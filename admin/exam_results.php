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
$page_title = "Exam Results - Student Admissions Management System";

// Get program filter if set
$program_filter = isset($_GET['program_id']) ? (int)$_GET['program_id'] : null;

// Get all programs for the filter dropdown
$programs_query = "SELECT program_id, program_name FROM programs ORDER BY program_name";
$programs = mysqli_query($conn, $programs_query);

// Build the exam results query
$results_query = "
    SELECT 
        reg.registration_id,
        reg.applicant_id,
        reg.exam_schedule_id,
        reg.status as registration_status,
        a.first_name,
        a.middle_name,
        a.last_name,
        u.email,
        p.program_name,
        es.exam_date,
        es.exam_time,
        es.venue,
        es.exam_id,
        es.status as exam_status,
        er.score as exam_score,
        CASE 
            WHEN er.score IS NULL THEN 'pending'
            WHEN er.score >= 75 THEN 'passed'
            ELSE 'failed'
        END as result_status,
        er.created_at,
        RANK() OVER (
            PARTITION BY p.program_id 
            ORDER BY er.score DESC
        ) as program_rank
    FROM exam_registrations reg
    JOIN applicants a ON reg.applicant_id = a.applicant_id
    JOIN users u ON a.user_id = u.user_id
    JOIN programs p ON a.primary_program_id = p.program_id
    JOIN exam_schedules es ON reg.exam_schedule_id = es.exam_id
    LEFT JOIN exam_results er ON u.user_id = er.user_id AND es.exam_id = er.exam_id
    " . ($program_filter ? "WHERE p.program_id = " . $program_filter : "") . "
    ORDER BY p.program_name, program_rank
";

$results = mysqli_query($conn, $results_query);

// Get statistics
$stats_query = "
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN er.score IS NULL THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN er.score >= 75 THEN 1 ELSE 0 END) as passed,
        SUM(CASE WHEN er.score < 75 AND er.score IS NOT NULL THEN 1 ELSE 0 END) as failed
    FROM applicants a
    LEFT JOIN exam_results er ON a.user_id = er.user_id
    " . ($program_filter ? "WHERE a.primary_program_id = " . $program_filter : "");
$stats = mysqli_fetch_assoc(mysqli_query($conn, $stats_query));
?>

<!-- Main Content -->
<div class="container-fluid">
    <div class="row">
        <!-- Main Content Area -->
        <main class="col-md-12 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Exam Results</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <form method="GET" class="form-inline">
                        <input type="hidden" name="page" value="exam_results">
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

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Applicants</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['total']; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Passed</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['passed']; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Failed</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['failed']; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['pending']; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exam Results Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Exam Results</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="examResultsTable">
                            <thead>
                                <tr>
                                    <th>Program</th>
                                    <th>Program Rank</th>
                                    <th>Student Name</th>
                                    <th>Email</th>
                                    <th>Exam Date</th>
                                    <th>Exam Time</th>
                                    <th>Venue</th>
                                    <th>Exam Score</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($result = mysqli_fetch_assoc($results)): ?>
                                <tr class="<?php echo $result['result_status'] == 'passed' ? 'table-success' : ($result['result_status'] == 'failed' ? 'table-danger' : ''); ?>">
                                    <td><?php echo htmlspecialchars($result['program_name']); ?></td>
                                    <td><?php echo $result['program_rank']; ?></td>
                                    <td>
                                        <?php 
                                        echo htmlspecialchars($result['last_name'] . ', ' . 
                                            $result['first_name'] . 
                                            ($result['middle_name'] ? ' ' . $result['middle_name'] : '')); 
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($result['email']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($result['exam_date'])); ?></td>
                                    <td><?php echo date('h:i A', strtotime($result['exam_time'])); ?></td>
                                    <td><?php echo htmlspecialchars($result['venue']); ?></td>
                                    <td>
                                        <?php 
                                        if ($result['exam_score'] !== null) {
                                            echo number_format($result['exam_score'], 2);
                                        } else {
                                            echo '<span class="text-muted">Not yet rated</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($result['result_status'] == 'passed'): ?>
                                            <span class="badge bg-success">Passed</span>
                                        <?php elseif ($result['result_status'] == 'failed'): ?>
                                            <span class="badge bg-danger">Failed</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php endif; ?>
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
    $('#examResultsTable').DataTable({
        order: [[0, 'asc'], [1, 'asc']],
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