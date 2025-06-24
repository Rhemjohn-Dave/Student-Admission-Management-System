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
$page_title = "Program Cutoffs - Student Admissions Management System";

// Handle program cutoff updates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_cutoff'])) {
        if (!isset($_POST['program_id']) || !isset($_POST['start_rank']) || !isset($_POST['end_rank'])) {
            echo '<div class="alert alert-danger">Missing required fields.</div>';
        } else {
            $program_id = trim($_POST['program_id']);
            $start_rank = trim($_POST['start_rank']);
            $end_rank = trim($_POST['end_rank']);
            if (empty($program_id) || empty($start_rank) || empty($end_rank)) {
                echo '<div class="alert alert-danger">All fields are required.</div>';
            } else {
                $check_stmt = $conn->prepare("SELECT cutoff_id FROM program_cutoffs WHERE program_id = ?");
                $check_stmt->bind_param("i", $program_id);
                $check_stmt->execute();
                $result = $check_stmt->get_result();
                if ($result->num_rows > 0) {
                    $stmt = $conn->prepare("UPDATE program_cutoffs SET start_rank = ?, end_rank = ? WHERE program_id = ?");
                    $stmt->bind_param("iii", $start_rank, $end_rank, $program_id);
                } else {
                    $stmt = $conn->prepare("INSERT INTO program_cutoffs (program_id, start_rank, end_rank) VALUES (?, ?, ?)");
                    $stmt->bind_param("iii", $program_id, $start_rank, $end_rank);
                }
                if ($stmt->execute()) {
                    require_once "handlers/ranking_handler.php";
                    recalculateRankings($conn);
                    echo '<div class="alert alert-success">Cutoff updated and rankings recalculated successfully.</div>';
                } else {
                    echo '<div class="alert alert-danger">Error updating cutoff: ' . $conn->error . '</div>';
                }
            }
        }
    }
}

// Get all programs with their cutoffs
$programs_query = "
    SELECT 
        p.program_id,
        p.program_name,
        pc.start_rank,
        pc.end_rank,
        pc.is_active
    FROM programs p
    LEFT JOIN program_cutoffs pc ON p.program_id = pc.program_id
    ORDER BY p.program_name
";
$programs = mysqli_query($conn, $programs_query);
?>

<!-- Main Content -->
<div class="container-fluid">
    <div class="row">
        <!-- Main Content Area -->
        <main class="col-md-12 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Program Cutoffs Management</h1>
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
                                    <th>Start Rank</th>
                                    <th>End Rank</th>
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
                                            <input type="number" name="start_rank" class="form-control" value="<?php echo $program['start_rank'] ?? ''; ?>" min="1" required placeholder="Start Rank">
                                    </td>
                                    <td>
                                            <input type="number" name="end_rank" class="form-control" value="<?php echo $program['end_rank'] ?? ''; ?>" min="1" required placeholder="End Rank">
                                    </td>
                                    <td>
                                        <?php echo $program['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'; ?>
                                    </td>
                                    <td>
                                            <button type="submit" name="update_cutoff" class="btn btn-primary">Update</button>
                                        </form>
                                        <a href="index.php?page=student_rankings&program_id=<?php echo $program['program_id']; ?>" class="btn btn-info btn-sm mt-2">View Rankings</a>
                                    </td>
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
});
</script> 