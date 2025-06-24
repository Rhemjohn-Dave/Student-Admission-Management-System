<?php
// Start session and include database connection
require_once "../config/database.php";

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("location: ../auth/login.php");
    exit();
}

// Set page title
$page_title = "Reports - Student Admissions Management System";

// Get all programs for the dropdown
$programs_query = "SELECT program_id, program_name FROM programs ORDER BY program_name";
$programs = mysqli_query($conn, $programs_query);

// Get interview schedules for the dropdown
$interviews_query = "
    SELECT 
        s.schedule_id,
        s.interview_date,
        s.time_window,
        s.max_applicants,
        s.current_applicants,
        s.status as schedule_status,
        p.program_name,
        CONCAT(u.first_name, ' ', u.last_name) as interviewer_name
    FROM interview_schedules s
    JOIN programs p ON s.program_id = p.program_id
    JOIN users u ON s.created_by = u.user_id
    ORDER BY s.interview_date ASC, s.time_window ASC
";
$interviews_result = mysqli_query($conn, $interviews_query);

// Get exam schedules for the dropdown
$schedules_query = "
    SELECT 
        es.exam_id,
        es.exam_date,
        es.exam_time,
        es.venue,
        es.max_participants,
        es.status,
        (SELECT COUNT(*) FROM exam_registrations er WHERE er.exam_schedule_id = es.exam_id AND er.status = 'registered') as registered_count
    FROM exam_schedules es
    ORDER BY es.exam_date DESC, es.exam_time DESC
";
$schedules_result = mysqli_query($conn, $schedules_query);
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Reports</h1>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Generate Reports</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Exam Schedule Report -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Exam Schedule Report</h6>
                        </div>
                        <div class="card-body">
                            <form id="examScheduleForm" method="post" action="../admin/handlers/generate_report.php" target="_blank">
                                <input type="hidden" name="action" value="exam_schedule">
                                <div class="form-group">
                                    <label for="schedule_id">Select Exam Schedule</label>
                                    <select class="form-control" id="schedule_id" name="schedule_id" required>
                                        <option value="">Choose a schedule...</option>
                                        <?php while ($schedule = mysqli_fetch_assoc($schedules_result)): ?>
                                            <option value="<?php echo $schedule['exam_id']; ?>">
                                                <?php 
                                                    echo date('F d, Y', strtotime($schedule['exam_date'])) . ' - ' . 
                                                         date('h:i A', strtotime($schedule['exam_time'])) . ' - ' . 
                                                         htmlspecialchars($schedule['venue']) . 
                                                         ' (' . $schedule['registered_count'] . ' registered) - ' .
                                                         ucfirst($schedule['status']);
                                                ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-print"></i> Generate Report
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Exam Rankings Report -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Exam Rankings Report</h6>
                        </div>
                        <div class="card-body">
                            <form id="examRankingsForm" method="post" action="../admin/handlers/generate_report.php" target="_blank">
                                <input type="hidden" name="action" value="exam_rankings">
                                <div class="form-group">
                                    <label for="program_id">Select Program (Optional)</label>
                                    <select class="form-control" id="program_id" name="program_id">
                                        <option value="">All Programs</option>
                                        <?php while ($program = mysqli_fetch_assoc($programs)): ?>
                                            <option value="<?php echo $program['program_id']; ?>">
                                                <?php echo htmlspecialchars($program['program_name']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="ranking_type">Ranking Type</label>
                                    <select class="form-control" id="ranking_type" name="ranking_type" required>
                                        <option value="overall">Overall Rankings</option>
                                        <option value="program">Program-Specific Rankings</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-print"></i> Generate Rankings Report
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Interview Schedule Report -->
                <div class="col-md-6 mt-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Interview Schedule Report</h6>
                        </div>
                        <div class="card-body">
                            <form id="interviewScheduleForm" method="post" action="../admin/handlers/generate_report.php" target="_blank">
                                <input type="hidden" name="action" value="interview_schedule">
                                <div class="form-group">
                                    <label for="program_id">Select Program (Optional)</label>
                                    <select class="form-control" id="interview_program_id" name="program_id">
                                        <option value="">All Programs</option>
                                        <?php 
                                        mysqli_data_seek($programs, 0); // Reset program results pointer
                                        while ($program = mysqli_fetch_assoc($programs)): 
                                        ?>
                                            <option value="<?php echo $program['program_id']; ?>">
                                                <?php echo htmlspecialchars($program['program_name']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="interview_schedule">Select Interview Schedule</label>
                                    <select class="form-control" id="interview_schedule" name="interview_schedule" required>
                                        <option value="">Choose a schedule...</option>
                                        <?php while ($interview = mysqli_fetch_assoc($interviews_result)): ?>
                                            <option value="<?php echo $interview['schedule_id']; ?>">
                                                <?php 
                                                    echo date('F d, Y', strtotime($interview['interview_date'])) . ' - ' . 
                                                         $interview['time_window'] . ' - ' . 
                                                         htmlspecialchars($interview['program_name']) . ' - ' .
                                                         htmlspecialchars($interview['interviewer_name']) . 
                                                         ' (' . $interview['current_applicants'] . '/' . $interview['max_applicants'] . ' slots)';
                                                ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-print"></i> Generate Interview Report
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Final Rankings Report -->
                <div class="col-md-6 mt-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Final Rankings Report</h6>
                        </div>
                        <div class="card-body">
                            <form id="finalRankingsForm" method="post" action="../admin/handlers/generate_report.php" target="_blank">
                                <input type="hidden" name="action" value="final_rankings">
                                <div class="form-group">
                                    <label for="final_rankings_program">Select Program</label>
                                    <select class="form-control" id="final_rankings_program" name="program_id" required>
                                        <option value="">Choose a program...</option>
                                        <?php 
                                        mysqli_data_seek($programs, 0); // Reset program results pointer
                                        while ($program = mysqli_fetch_assoc($programs)): 
                                        ?>
                                            <option value="<?php echo $program['program_id']; ?>">
                                                <?php echo htmlspecialchars($program['program_name']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-print"></i> Generate Final Rankings Report
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize select2 for better dropdown experience
    $('#schedule_id, #program_id, #interview_program_id, #interview_schedule, #final_rankings_program').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Initialize DataTables with export buttons
    $('.datatable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        pageLength: 25,
        order: [[0, 'asc']]
    });
});
</script> 