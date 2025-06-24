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

// Build the overall rankings query (exam scores only)
$overall_rankings_query = "
    SELECT 
        a.applicant_id,
        a.first_name,
        a.middle_name,
        a.last_name,
        p1.program_name as primary_program,
        p2.program_name as secondary_program,
        esc.score as exam_score,
        RANK() OVER (ORDER BY esc.score DESC) as overall_rank
    FROM applicants a
    JOIN users u ON a.user_id = u.user_id
    JOIN programs p1 ON a.primary_program_id = p1.program_id
    LEFT JOIN programs p2 ON a.secondary_program_id = p2.program_id
    LEFT JOIN exam_registrations reg ON a.applicant_id = reg.applicant_id
    LEFT JOIN exam_scores esc ON reg.registration_id = esc.registration_id
    WHERE esc.score IS NOT NULL
    ORDER BY overall_rank
";

// Build the program-specific rankings query (based on combined scores)
$program_rankings_query = "
    SELECT * FROM (
        SELECT 
            a.applicant_id,
            a.first_name,
            a.middle_name,
            a.last_name,
            p.program_name,
            p1.program_name as primary_program,
            p2.program_name as secondary_program,
            esc.score as exam_score,
            i.score as interview_score,
            ROUND(
                (COALESCE(esc.score, 0) * 0.75) + 
                ((COALESCE(i.score, 0) / 25 * 100) * 0.25), 
                2
            ) as combined_score,
            RANK() OVER (
                PARTITION BY p.program_id 
                ORDER BY 
                    (COALESCE(esc.score, 0) * 0.75) + 
                    ((COALESCE(i.score, 0) / 25 * 100) * 0.25) DESC
            ) as program_rank,
            pc.start_rank, pc.end_rank
        FROM applicants a
        JOIN users u ON a.user_id = u.user_id
        JOIN programs p ON a.primary_program_id = p.program_id
        LEFT JOIN programs p1 ON a.primary_program_id = p1.program_id
        LEFT JOIN programs p2 ON a.secondary_program_id = p2.program_id
        LEFT JOIN exam_registrations reg ON a.applicant_id = reg.applicant_id
        LEFT JOIN exam_scores esc ON reg.registration_id = esc.registration_id
        LEFT JOIN applications app ON a.user_id = app.user_id
        LEFT JOIN interviews i ON app.application_id = i.application_id AND i.status = 'completed'
        LEFT JOIN program_cutoffs pc ON p.program_id = pc.program_id
        WHERE esc.score IS NOT NULL
        " . ($program_filter ? " AND p.program_id = $program_filter" : "") . "
    ) ranked
    ORDER BY ranked.program_name, ranked.program_rank
";

// Execute the appropriate query based on ranking type
$rankings = mysqli_query($conn, $ranking_type === 'overall' ? $overall_rankings_query : $program_rankings_query);
if (!$rankings) {
    echo '<div class="alert alert-danger">Query failed: ' . mysqli_error($conn) . '</div>';
    echo '<pre>' . htmlspecialchars($ranking_type === 'overall' ? $overall_rankings_query : $program_rankings_query) . '</pre>';
    exit;
}
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
                                            <?php if ($ranking['program_rank'] >= $ranking['start_rank'] && $ranking['program_rank'] <= $ranking['end_rank']): ?>
                                                <span class="badge bg-success">Eligible</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Not Eligible</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo isset($ranking['start_rank'], $ranking['end_rank']) ? $ranking['start_rank'] . '–' . $ranking['end_rank'] : 'Not Set'; ?></td>
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

<!-- DataTables CSS -->
<link href="../assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css" rel="stylesheet">

<!-- Core DataTables JS -->
<script src="../assets/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- DataTables Buttons -->
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

<!-- JSZip -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

<!-- PDFMake -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

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