<?php
// Only start session if it hasn't been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_type"] !== "admin"){
    header("location: ../auth/login.php");
    exit;
}

// Include database connection
require_once "../config/database.php";

// Set page title
$page_title = "Exam Rankings - Student Admissions Management System";

// Handle program cutoff updates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debug: Log the POST data
    error_log("POST data: " . print_r($_POST, true));
    
    if (isset($_POST['update_cutoff'])) {
        // Debug: Log specific fields
        error_log("program_id exists: " . (isset($_POST['program_id']) ? 'yes' : 'no'));
        error_log("cutoff_rank exists: " . (isset($_POST['cutoff_rank']) ? 'yes' : 'no'));
        
        // Check if both required fields exist
        if (!isset($_POST['program_id']) || !isset($_POST['cutoff_rank'])) {
            echo '<div class="alert alert-danger">Missing required fields. POST data: ' . htmlspecialchars(print_r($_POST, true)) . '</div>';
        } else {
            $program_id = trim($_POST['program_id']);
            $cutoff_rank = trim($_POST['cutoff_rank']);
            
            // Debug: Log the values
            error_log("program_id value: " . $program_id);
            error_log("cutoff_rank value: " . $cutoff_rank);
            
            // Validate inputs
            if (empty($program_id) || empty($cutoff_rank)) {
                echo '<div class="alert alert-danger">Program ID and cutoff rank are required.</div>';
            } else {
                // Check if cutoff exists
                $check_stmt = $conn->prepare("SELECT cutoff_id FROM program_cutoffs WHERE program_id = ?");
                $check_stmt->bind_param("i", $program_id);
                $check_stmt->execute();
                $result = $check_stmt->get_result();
                
                if ($result->num_rows > 0) {
                    // Update existing cutoff
                    $stmt = $conn->prepare("UPDATE program_cutoffs SET cutoff_rank = ? WHERE program_id = ?");
                    $stmt->bind_param("ii", $cutoff_rank, $program_id);
                } else {
                    // Insert new cutoff
                    $stmt = $conn->prepare("INSERT INTO program_cutoffs (program_id, cutoff_rank) VALUES (?, ?)");
                    $stmt->bind_param("ii", $program_id, $cutoff_rank);
                }
                
                if ($stmt->execute()) {
                    // Recalculate rankings
                    recalculateRankings($conn);
                    echo '<div class="alert alert-success">Cutoff updated and rankings recalculated successfully.</div>';
                } else {
                    echo '<div class="alert alert-danger">Error updating cutoff: ' . $conn->error . '</div>';
                }
            }
        }
    }
}

// Function to recalculate rankings
function recalculateRankings($conn) {
    // Clear existing rankings
    $conn->query("TRUNCATE TABLE program_rankings");
    
    // Get all programs
    $programs = $conn->query("SELECT program_id FROM programs");
    
    while ($program = $programs->fetch_assoc()) {
        $program_id = $program['program_id'];
        
        // Get cutoff rank for this program
        $cutoff_query = "SELECT cutoff_rank FROM program_cutoffs WHERE program_id = ? AND is_active = 1";
        $cutoff_stmt = $conn->prepare($cutoff_query);
        $cutoff_stmt->bind_param("i", $program_id);
        $cutoff_stmt->execute();
        $cutoff_result = $cutoff_stmt->get_result();
        $cutoff_rank = $cutoff_result->fetch_assoc()['cutoff_rank'] ?? PHP_INT_MAX;
        
        // Get all applicants with exam scores for this program
        $rank_query = "
            INSERT INTO program_rankings (applicant_id, program_id, exam_score, rank_position, is_eligible)
            SELECT 
                a.applicant_id,
                ?,
                er.score,
                RANK() OVER (ORDER BY er.score DESC),
                CASE WHEN RANK() OVER (ORDER BY er.score DESC) <= ? THEN TRUE ELSE FALSE END
            FROM applicants a
            JOIN exam_results er ON a.user_id = er.user_id
            WHERE a.primary_program_id = ? OR a.secondary_program_id = ?
            AND er.score IS NOT NULL
        ";
        
        $rank_stmt = $conn->prepare($rank_query);
        $rank_stmt->bind_param("iiii", $program_id, $cutoff_rank, $program_id, $program_id);
        $rank_stmt->execute();
    }
    
    // Assign programs based on rankings
    assignPrograms($conn);
}

// Function to assign programs based on rankings
function assignPrograms($conn) {
    // Get all applicants
    $applicants = $conn->query("
        SELECT DISTINCT applicant_id 
        FROM program_rankings 
        ORDER BY exam_score DESC
    ");
    
    while ($applicant = $applicants->fetch_assoc()) {
        $applicant_id = $applicant['applicant_id'];
        
        // Get applicant's rankings
        $rankings = $conn->query("
            SELECT pr.*, p.program_name 
            FROM program_rankings pr
            JOIN programs p ON pr.program_id = p.program_id
            WHERE pr.applicant_id = $applicant_id
            ORDER BY pr.rank_position ASC
        ");
        
        $assigned = false;
        while ($ranking = $rankings->fetch_assoc()) {
            if ($ranking['is_eligible']) {
                // Update assigned program
                $update = $conn->prepare("
                    UPDATE program_rankings 
                    SET assigned_program_id = ? 
                    WHERE applicant_id = ?
                ");
                $update->bind_param("ii", $ranking['program_id'], $applicant_id);
                $update->execute();
                $assigned = true;
                break;
            }
        }
        
        // If no eligible program found, assign based on best rank
        if (!$assigned) {
            $best_rank = $conn->query("
                SELECT program_id 
                FROM program_rankings 
                WHERE applicant_id = $applicant_id 
                ORDER BY rank_position ASC 
                LIMIT 1
            ")->fetch_assoc();
            
            if ($best_rank) {
                $update = $conn->prepare("
                    UPDATE program_rankings 
                    SET assigned_program_id = ? 
                    WHERE applicant_id = ?
                ");
                $update->bind_param("ii", $best_rank['program_id'], $applicant_id);
                $update->execute();
            }
        }
    }
}

// Get all programs with their cutoffs
$programs_query = "
    SELECT 
        p.program_id,
        p.program_name,
        pc.cutoff_rank,
        pc.is_active
    FROM programs p
    LEFT JOIN program_cutoffs pc ON p.program_id = pc.program_id
    ORDER BY p.program_name
";
$programs = mysqli_query($conn, $programs_query);

// Get all rankings
$rankings_query = "
    SELECT 
        pr.*,
        a.first_name,
        a.middle_name,
        a.last_name,
        p1.program_name as primary_program,
        p2.program_name as secondary_program,
        p3.program_name as assigned_program,
        p.program_name,
        pc.cutoff_rank
    FROM program_rankings pr
    JOIN applicants a ON pr.applicant_id = a.applicant_id
    JOIN programs p ON pr.program_id = p.program_id
    JOIN programs p1 ON a.primary_program_id = p1.program_id
    LEFT JOIN programs p2 ON a.secondary_program_id = p2.program_id
    LEFT JOIN programs p3 ON pr.assigned_program_id = p3.program_id
    LEFT JOIN program_cutoffs pc ON pr.program_id = pc.program_id
    ORDER BY pr.program_id, pr.rank_position
";
$rankings = mysqli_query($conn, $rankings_query);
?>

<!-- Main Content -->
<div class="container-fluid">
    <div class="row">
        <!-- Main Content Area -->
        <main class="col-md-12 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Exam Rankings Management</h1>
            </div>

            <!-- Program Cutoffs Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Program Cutoffs</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="cutoffsTable">
                            <thead>
                                <tr>
                                    <th>Program</th>
                                    <th>Cutoff Rank</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($program = mysqli_fetch_assoc($programs)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($program['program_name']); ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="program_id" value="<?php echo $program['program_id']; ?>">
                                            <div class="input-group">
                                                <input type="number" 
                                                       name="cutoff_rank" 
                                                       class="form-control" 
                                                       value="<?php echo $program['cutoff_rank'] ?? ''; ?>"
                                                       min="1"
                                                       required>
                                                <div class="input-group-append">
                                                    <button type="submit" 
                                                            name="update_cutoff" 
                                                            class="btn btn-primary">
                                                        Update
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </td>
                                    <td>
                                        <?php echo $program['is_active'] ? 
                                            '<span class="badge bg-success">Active</span>' : 
                                            '<span class="badge bg-danger">Inactive</span>'; ?>
                                    </td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-info btn-sm view-rankings"
                                                data-program-id="<?php echo $program['program_id']; ?>"
                                                data-program-name="<?php echo htmlspecialchars($program['program_name']); ?>">
                                            View Rankings
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Rankings Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Student Rankings</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="rankingsTable">
                            <thead>
                                <tr>
                                    <th>Program</th>
                                    <th>Rank</th>
                                    <th>Student Name</th>
                                    <th>Primary Program</th>
                                    <th>Secondary Program</th>
                                    <th>Exam Score</th>
                                    <th>Status</th>
                                    <th>Assigned Program</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($ranking = mysqli_fetch_assoc($rankings)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($ranking['program_name']); ?></td>
                                    <td><?php echo $ranking['rank_position']; ?></td>
                                    <td>
                                        <?php 
                                        echo htmlspecialchars($ranking['last_name'] . ', ' . 
                                            $ranking['first_name'] . 
                                            ($ranking['middle_name'] ? ' ' . $ranking['middle_name'] : '')); 
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($ranking['primary_program']); ?></td>
                                    <td><?php echo htmlspecialchars($ranking['secondary_program'] ?? 'N/A'); ?></td>
                                    <td><?php echo number_format($ranking['exam_score'], 2); ?></td>
                                    <td>
                                        <?php if ($ranking['is_eligible']): ?>
                                            <span class="badge bg-success">Eligible</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Not Eligible</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($ranking['assigned_program'] ?? 'Pending'); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Initialize DataTables -->
<script>
$(document).ready(function() {
    $('#cutoffsTable').DataTable({
        order: [[0, 'asc']],
        pageLength: 25
    });
    
    $('#rankingsTable').DataTable({
        order: [[0, 'asc'], [1, 'asc']],
        pageLength: 25
    });
});
</script> 