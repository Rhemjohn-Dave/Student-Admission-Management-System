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
$page_title = "Interview Results - Student Admissions Management System";

// Get program filter if set
$program_filter = isset($_GET['program_id']) ? (int)$_GET['program_id'] : null;

// Get all programs for the filter dropdown
$programs_query = "SELECT program_id, program_name FROM programs ORDER BY program_name";
$programs = mysqli_query($conn, $programs_query);
if (!$programs) {
    die("Error fetching programs: " . mysqli_error($conn));
}

// Build the interview results query
$results_query = "
    SELECT *,
        CASE WHEN program_rank >= start_rank AND program_rank <= end_rank THEN 1 ELSE 0 END as is_eligible
    FROM (
        SELECT 
            a.applicant_id,
            a.first_name,
            a.middle_name,
            a.last_name,
            p.program_id,
            p.program_name,
            p1.program_name as primary_program,
            p2.program_name as secondary_program,
            esc.score as exam_score,
            i.score as interview_score,
            i.status as interview_status,
            i.scheduled_date,
            i.scheduled_time,
            CONCAT(u2.first_name, ' ', u2.last_name) as interviewer_name,
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
        LEFT JOIN interviews i ON app.application_id = i.application_id
        LEFT JOIN program_heads ph ON p.program_id = ph.program_id
        LEFT JOIN users u2 ON ph.user_id = u2.user_id
        LEFT JOIN program_cutoffs pc ON p.program_id = pc.program_id
        WHERE (i.status = 'completed' OR i.status IS NULL)
    ) ranked
";

if ($program_filter) {
    $results_query .= " WHERE ranked.program_id = " . intval($program_filter);
}

$results_query .= " ORDER BY ranked.program_name, ranked.program_rank";

// Execute the query with error handling
$results = mysqli_query($conn, $results_query);
if (!$results) {
    die("Error executing interview results query: " . mysqli_error($conn));
}

// Get statistics with error handling
$stats_query = "
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN i.status = 'completed' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN i.status = 'scheduled' THEN 1 ELSE 0 END) as scheduled,
        SUM(CASE WHEN i.status = 'pending' OR i.status IS NULL THEN 1 ELSE 0 END) as pending
    FROM applicants a
    LEFT JOIN applications app ON a.user_id = app.user_id
    LEFT JOIN interviews i ON app.application_id = i.application_id
    " . ($program_filter ? "WHERE a.primary_program_id = " . intval($program_filter) : "");

$stats_result = mysqli_query($conn, $stats_query);
if (!$stats_result) {
    die("Error executing statistics query: " . mysqli_error($conn));
}
$stats = mysqli_fetch_assoc($stats_result);
?>

<!-- Main Content -->
<div class="container-fluid">
    <!-- Add DataTables CSS and JS -->
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

    <div class="row">
        <!-- Main Content Area -->
        <main class="col-md-12 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Interview Results</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <form method="GET" class="form-inline">
                        <input type="hidden" name="page" value="interview_results">
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
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed Interviews</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['completed']; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Scheduled Interviews</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['scheduled']; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Interviews</div>
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

            <!-- Interview Results Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Interview Results</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="interviewResultsTable">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Interview Score</th>
                                    <th>Interview Date</th>
                                    <th>Interviewer</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($result = mysqli_fetch_assoc($results)): ?>
                                <tr class="<?php echo $result['is_eligible'] ? 'table-success' : ''; ?>">
                                    <td>
                                        <?php 
                                        echo htmlspecialchars($result['last_name'] . ', ' . 
                                            $result['first_name'] . 
                                            ($result['middle_name'] ? ' ' . $result['middle_name'] : '')); 
                                        ?>
                                    </td>
                                    <td><?php echo $result['interview_score'] ? number_format(($result['interview_score'] / 25 * 100), 2) : 'N/A'; ?></td>
                                    <td>
                                        <?php 
                                        if ($result['scheduled_date']) {
                                            // Get the time window from interview schedule
                                            $time_query = "SELECT s.time_window 
                                                          FROM interview_schedules s 
                                                          JOIN applications app ON s.program_id = app.program_id 
                                                          WHERE app.user_id = (SELECT user_id FROM applicants WHERE applicant_id = ?) 
                                                          AND s.interview_date = ?";
                                            $time_stmt = mysqli_prepare($conn, $time_query);
                                            mysqli_stmt_bind_param($time_stmt, "is", $result['applicant_id'], $result['scheduled_date']);
                                            mysqli_stmt_execute($time_stmt);
                                            $time_result = mysqli_stmt_get_result($time_stmt);
                                            $time_row = mysqli_fetch_assoc($time_result);
                                            
                                            $time_display = $time_row ? $time_row['time_window'] : 'N/A';
                                            echo date('M d, Y', strtotime($result['scheduled_date'])) . ' ' . $time_display;
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($result['interviewer_name'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if ($result['is_eligible']): ?>
                                            <span class="badge bg-success">Eligible</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Not Eligible</span>
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
    $('#interviewResultsTable').DataTable({
        order: [[0, 'asc'], [1, 'asc']],
        pageLength: 25,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
});
</script>

<!-- Remove DataTables Buttons CSS and JS -->
<!-- <link href="../assets/vendor/datatables-buttons/css/buttons.bootstrap4.min.css" rel="stylesheet">
<script src="../assets/vendor/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../assets/vendor/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../assets/vendor/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../assets/vendor/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../assets/vendor/jszip/jszip.min.js"></script>
<script src="../assets/vendor/pdfmake/pdfmake.min.js"></script>
<script src="../assets/vendor/pdfmake/vfs_fonts.js"></script> --> 