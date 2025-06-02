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
$page_title = "Student Rankings - Student Admissions Management System";

// Get program filter if set
$program_filter = isset($_GET['program_id']) ? (int)$_GET['program_id'] : null;
$ranking_type = isset($_GET['type']) ? $_GET['type'] : 'overall'; // 'overall' or 'program'

// Get all programs for the filter dropdown
$programs_query = "SELECT program_id, program_name FROM programs ORDER BY program_name";
$programs = mysqli_query($conn, $programs_query);

// Build the overall rankings query (based on exam scores only)
$overall_rankings_query = "
    SELECT 
        a.applicant_id,
        a.first_name,
        a.middle_name,
        a.last_name,
        p1.program_name as primary_program,
        p2.program_name as secondary_program,
        er.score as exam_score,
        RANK() OVER (ORDER BY er.score DESC) as overall_rank
    FROM applicants a
    JOIN users u ON a.user_id = u.user_id
    JOIN programs p1 ON a.primary_program_id = p1.program_id
    LEFT JOIN programs p2 ON a.secondary_program_id = p2.program_id
    LEFT JOIN exam_results er ON u.user_id = er.user_id
    WHERE er.score IS NOT NULL
    ORDER BY overall_rank
";

// Build the program-specific rankings query (based on combined scores)
$program_rankings_query = "
    SELECT 
        a.applicant_id,
        a.first_name,
        a.middle_name,
        a.last_name,
        p.program_name,
        p1.program_name as primary_program,
        p2.program_name as secondary_program,
        er.score as exam_score,
        i.score as interview_score,
        ROUND(
            (COALESCE(er.score, 0) * 0.75) + 
            ((COALESCE(i.score, 0) / 25 * 100) * 0.25), 
            2
        ) as combined_score,
        RANK() OVER (
            PARTITION BY p.program_id 
            ORDER BY 
                (COALESCE(er.score, 0) * 0.75) + 
                ((COALESCE(i.score, 0) / 25 * 100) * 0.25) DESC
        ) as program_rank,
        pc.cutoff_rank,
        CASE 
            WHEN RANK() OVER (
                PARTITION BY p.program_id 
                ORDER BY 
                    (COALESCE(er.score, 0) * 0.75) + 
                    ((COALESCE(i.score, 0) / 25 * 100) * 0.25) DESC
            ) <= COALESCE(pc.cutoff_rank, 999999) THEN 1
            ELSE 0
        END as is_eligible
    FROM applicants a
    JOIN users u ON a.user_id = u.user_id
    JOIN programs p ON a.primary_program_id = p.program_id
    LEFT JOIN programs p1 ON a.primary_program_id = p1.program_id
    LEFT JOIN programs p2 ON a.secondary_program_id = p2.program_id
    LEFT JOIN exam_results er ON u.user_id = er.user_id
    LEFT JOIN applications app ON a.user_id = app.user_id
    LEFT JOIN interviews i ON app.application_id = i.application_id AND i.status = 'completed'
    LEFT JOIN program_cutoffs pc ON p.program_id = pc.program_id
    WHERE er.score IS NOT NULL
";

if ($program_filter) {
    $program_rankings_query .= " AND p.program_id = " . $program_filter;
}

$program_rankings_query .= " ORDER BY p.program_name, program_rank";

// Execute the appropriate query based on ranking type
$rankings = mysqli_query($conn, $ranking_type === 'overall' ? $overall_rankings_query : $program_rankings_query);
?>

<!-- Main Content -->
<div class="container-fluid">
    <div class="row">
        <!-- Main Content Area -->
        <main class="col-md-12 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Student Rankings</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group mr-2">
                        <a href="?page=student_rankings&type=overall<?php echo $program_filter ? '&program_id=' . $program_filter : ''; ?>" 
                           class="btn btn-sm btn-outline-primary <?php echo $ranking_type === 'overall' ? 'active' : ''; ?>">
                            Overall Rankings
                        </a>
                        <a href="?page=student_rankings&type=program<?php echo $program_filter ? '&program_id=' . $program_filter : ''; ?>" 
                           class="btn btn-sm btn-outline-primary <?php echo $ranking_type === 'program' ? 'active' : ''; ?>">
                            Program Rankings
                        </a>
                    </div>
                    <form method="GET" class="form-inline">
                        <input type="hidden" name="page" value="student_rankings">
                        <input type="hidden" name="type" value="<?php echo $ranking_type; ?>">
                        <select name="program_id" class="form-control mr-2" onchange="this.form.submit()">
                            <option value="">All Programs</option>
                            <?php while ($program = mysqli_fetch_assoc($programs)): ?>
                            <option value="<?php echo $program['program_id']; ?>" 
                                    <?php echo ($program_filter == $program['program_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($program['program_name']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Rankings Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <?php echo $ranking_type === 'overall' ? 'Overall Exam Rankings' : 'Program-Specific Rankings'; ?>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="rankingsTable">
                            <thead>
                                <tr>
                                    <?php if ($ranking_type === 'overall'): ?>
                                        <th>Overall Rank</th>
                                        <th>Student Name</th>
                                        <th>Primary Program</th>
                                        <th>Secondary Program</th>
                                        <th>Exam Score</th>
                                    <?php else: ?>
                                        <th>Program</th>
                                        <th>Program Rank</th>
                                        <th>Student Name</th>
                                        <th>Primary Program</th>
                                        <th>Secondary Program</th>
                                        <th>Exam Score (75%)</th>
                                        <th>Interview Score (25%)</th>
                                        <th>Combined Score</th>
                                        <th>Status</th>
                                        <th>Cutoff Rank</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($ranking = mysqli_fetch_assoc($rankings)): ?>
                                <tr class="<?php echo ($ranking_type === 'program' && isset($ranking['is_eligible']) && $ranking['is_eligible']) ? 'table-success' : ''; ?>">
                                    <?php if ($ranking_type === 'overall'): ?>
                                        <td><?php echo $ranking['overall_rank']; ?></td>
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
                                    <?php else: ?>
                                        <td><?php echo htmlspecialchars($ranking['program_name']); ?></td>
                                        <td><?php echo $ranking['program_rank']; ?></td>
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
                                        <td><?php echo $ranking['interview_score'] ? number_format(($ranking['interview_score'] / 25 * 100), 2) : 'N/A'; ?></td>
                                        <td><?php echo number_format($ranking['combined_score'], 2); ?></td>
                                        <td>
                                            <?php if ($ranking['is_eligible']): ?>
                                                <span class="badge bg-success">Eligible</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Not Eligible</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $ranking['cutoff_rank'] ?? 'Not Set'; ?></td>
                                    <?php endif; ?>
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
    $('#rankingsTable').DataTable({
        order: [[0, 'asc']],
        pageLength: 25,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
});
</script>

<!-- Add DataTables Buttons CSS and JS -->
<link href="../assets/vendor/datatables-buttons/css/buttons.bootstrap4.min.css" rel="stylesheet">
<script src="../assets/vendor/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../assets/vendor/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../assets/vendor/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../assets/vendor/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../assets/vendor/jszip/jszip.min.js"></script>
<script src="../assets/vendor/pdfmake/pdfmake.min.js"></script>
<script src="../assets/vendor/pdfmake/vfs_fonts.js"></script> 