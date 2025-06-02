<?php



// Include database connection
require_once "../config/database.php";

// Set page title
$page_title = "Exam Schedules - Student Admissions Management System";



// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_schedule'])) {
    $date = $_POST['date'];
    $time = $_POST['time'];
    $venue = $_POST['venue'];
    $max_participants = $_POST['max_participants'];
    
    $stmt = $conn->prepare("INSERT INTO exam_schedules (exam_date, exam_time, venue, max_participants, status) VALUES (?, ?, ?, ?, 'scheduled')");
    $stmt->bind_param("sssi", $date, $time, $venue, $max_participants);
    
    if ($stmt->execute()) {
        // Log activity
        $action = "Added new exam schedule for $date at $time";
        $stmt = $conn->prepare("INSERT INTO activity_log (user_id, action) VALUES (?, ?)");
        $stmt->bind_param("is", $_SESSION['user_id'], $action);
        $stmt->execute();
        
        echo '<div class="alert alert-success">Schedule added successfully.</div>';
    } else {
        echo '<div class="alert alert-danger">Error adding schedule.</div>';
    }
}

// Handle schedule closure
if (isset($_POST['close_schedule'])) {
    $schedule_id = $_POST['schedule_id'];
    $stmt = $conn->prepare("UPDATE exam_schedules SET status = 'cancelled' WHERE exam_id = ?");
    $stmt->bind_param("i", $schedule_id);
    
    if ($stmt->execute()) {
        // Log activity
        $action = "Closed exam schedule ID: $schedule_id";
        $stmt = $conn->prepare("INSERT INTO activity_log (user_id, action) VALUES (?, ?)");
        $stmt->bind_param("is", $_SESSION['user_id'], $action);
        $stmt->execute();
        
        echo '<div class="alert alert-success">Schedule closed successfully.</div>';
    } else {
        echo '<div class="alert alert-danger">Error closing schedule.</div>';
    }
}

// Fetch all schedules
$schedules = $conn->query("
    SELECT 
        es.*,
        COUNT(er.registration_id) as registered_count
    FROM exam_schedules es
    LEFT JOIN exam_registrations er ON es.exam_id = er.exam_schedule_id
    GROUP BY es.exam_id
    ORDER BY es.exam_date ASC
");

// Fetch applicants for each schedule
$applicants_by_schedule = [];
$schedule_ids = [];
foreach ($schedules as $schedule) {
    $schedule_ids[] = $schedule['exam_id'];
}
if ($schedule_ids) {
    $in = implode(',', array_fill(0, count($schedule_ids), '?'));
    $types = str_repeat('i', count($schedule_ids));
    $stmt = $conn->prepare("
        SELECT er.exam_schedule_id, a.first_name, a.last_name, u.email, p.program_name, er.registration_date
        FROM exam_registrations er
        JOIN applicants a ON er.applicant_id = a.applicant_id
        JOIN users u ON a.user_id = u.user_id
        JOIN programs p ON a.primary_program_id = p.program_id
        WHERE er.exam_schedule_id IN ($in)
        ORDER BY er.registration_date DESC
    ");
    $stmt->bind_param($types, ...$schedule_ids);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $applicants_by_schedule[$row['exam_schedule_id']][] = $row;
    }
}

$modals = '';
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Exam Schedules</h1>
        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addScheduleModal">
            <i class="fas fa-plus"></i> Add Schedule
        </button>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Content Column -->
        <div class="col-lg-12">
            <!-- Exam Schedules Card -->
            <div class="card shadow mb-4">
                <!-- Card Header -->
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Exam Schedules List</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="examSchedulesTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Venue</th>
                                    <th>Max Participants</th>
                                    <th>Registered</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($schedules as $schedule): ?>
                                <tr>
                                    <td><?php echo date('M d, Y', strtotime($schedule['exam_date'])); ?></td>
                                    <td><?php echo date('h:i A', strtotime($schedule['exam_time'])); ?></td>
                                    <td><?php echo $schedule['venue']; ?></td>
                                    <td><?php echo $schedule['max_participants']; ?></td>
                                    <td><?php echo $schedule['registered_count']; ?></td>
                                    <td>
                                        <?php if ($schedule['status'] == 'scheduled'): ?>
                                            <span class="badge badge-success">Open</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Closed</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewApplicantsModal<?php echo $schedule['exam_id']; ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if ($schedule['status'] == 'scheduled'): ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="schedule_id" value="<?php echo $schedule['exam_id']; ?>">
                                            <button type="submit" name="close_schedule" class="btn btn-danger btn-sm"
                                                    title="Close Schedule">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php
                                // Collect modal HTML in a variable
                                $modals .= '
<div class="modal fade" id="viewApplicantsModal' . $schedule['exam_id'] . '" tabindex="-1" role="dialog" aria-labelledby="viewApplicantsModalLabel' . $schedule['exam_id'] . '" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewApplicantsModalLabel' . $schedule['exam_id'] . '">Registered Applicants</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped applicantsTable" id="applicantsTable' . $schedule['exam_id'] . '">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Program</th>
                                <th>Registration Date</th>
                            </tr>
                        </thead>
                        <tbody>';
                                if (!empty($applicants_by_schedule[$schedule['exam_id']])) {
                                    foreach ($applicants_by_schedule[$schedule['exam_id']] as $applicant) {
                                        $modals .= '<tr>
                                            <td>' . htmlspecialchars($applicant['first_name'] . ' ' . $applicant['last_name']) . '</td>
                                            <td>' . htmlspecialchars($applicant['email']) . '</td>
                                            <td>' . htmlspecialchars($applicant['program_name']) . '</td>
                                            <td>' . date('M d, Y h:i A', strtotime($applicant['registration_date'])) . '</td>
                                        </tr>';
                                    }
                                } else {
                                    $modals .= '<tr><td colspan="4" class="text-center">No applicants registered for this schedule</td></tr>';
                                }
                                $modals .= '</tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>';
                                ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->
<?php echo $modals; ?>

<!-- Add Schedule Modal -->
<div class="modal fade" id="addScheduleModal" tabindex="-1" role="dialog" aria-labelledby="addScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addScheduleModalLabel">Add New Exam Schedule</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" id="addScheduleForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_schedule">
                    <div class="form-group">
                        <label for="date">Exam Date</label>
                        <input type="date" class="form-control" id="date" name="date" required 
                               min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="time">Exam Time</label>
                        <input type="time" class="form-control" id="time" name="time" required>
                    </div>
                    <div class="form-group">
                        <label for="venue">Venue</label>
                        <input type="text" class="form-control" id="venue" name="venue" required>
                    </div>
                    <div class="form-group">
                        <label for="max_participants">Maximum Participants</label>
                        <input type="number" class="form-control" id="max_participants" name="max_participants" 
                               min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTables for main table
    var table = $('#examSchedulesTable').DataTable({
        "order": [[0, "asc"]], // Sort by date
        "pageLength": 10,
        "columnDefs": [
            { "orderable": false, "targets": -1 } // Make actions column non-sortable
        ],
        "language": {
            "emptyTable": "No exam schedules found",
            "info": "Showing _START_ to _END_ of _TOTAL_ schedules",
            "infoEmpty": "Showing 0 to 0 of 0 schedules",
            "infoFiltered": "(filtered from _MAX_ total schedules)",
            "lengthMenu": "Show _MENU_ schedules per page",
            "search": "Search schedules:",
            "zeroRecords": "No matching schedules found"
        }
    });

    // Initialize DataTables for each applicants table when modal is shown
    $('div[id^="viewApplicantsModal"]').on('shown.bs.modal', function () {
        var tableId = $(this).find('table.applicantsTable').attr('id');
        if (!$.fn.DataTable.isDataTable('#' + tableId)) {
            $('#' + tableId).DataTable({
                "order": [[3, "desc"]], // Sort by registration date
                "pageLength": 10,
                "language": {
                    "emptyTable": "No applicants registered for this schedule",
                    "info": "Showing _START_ to _END_ of _TOTAL_ applicants",
                    "infoEmpty": "Showing 0 to 0 of 0 applicants",
                    "infoFiltered": "(filtered from _MAX_ total applicants)",
                    "lengthMenu": "Show _MENU_ applicants per page",
                    "search": "Search applicants:",
                    "zeroRecords": "No matching applicants found"
                }
            });
        }
    });

    // Add Schedule Modal
    $('#addScheduleModal').on('show.bs.modal', function () {
        $('#addScheduleForm')[0].reset();
        $('#addScheduleForm .is-invalid').removeClass('is-invalid');
    });

    $('#addScheduleModal').on('hidden.bs.modal', function () {
        $('#addScheduleForm')[0].reset();
        $('#addScheduleForm .is-invalid').removeClass('is-invalid');
    });

    // Handle form submission
    $('#addScheduleForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate form data
        let formData = $(this).serialize();
        let date = $('#date').val();
        let time = $('#time').val();
        let venue = $('#venue').val();
        let maxParticipants = $('#max_participants').val();

        if (!date || !time || !venue || !maxParticipants) {
            Swal.fire({
                title: 'Error!',
                text: 'Please fill in all required fields.',
                icon: 'error',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        // Show processing alert
        let processingAlert = Swal.fire({
            title: 'Processing...',
            text: 'Please wait while we add the schedule.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            showConfirmButton: false,
            backdrop: true,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Submit form via AJAX
        $.ajax({
            url: 'handle_exam_schedule.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                // Close processing alert
                Swal.close();
                
                // Close the modal
                $('#addScheduleModal').modal('hide');
                
                // Show success message
                Swal.fire({
                    title: 'Success!',
                    text: 'Exam schedule added successfully',
                    icon: 'success',
                    confirmButtonColor: '#3085d6',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    backdrop: true,
                    showConfirmButton: true,
                    showCloseButton: true
                }).then(() => {
                    // Reload page after success message is closed
                    window.location.reload();
                });
            },
            error: function(xhr) {
                // Close processing alert
                Swal.close();
                
                // Show error message
                let errorMessage = 'An error occurred while adding the schedule.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                
                Swal.fire({
                    title: 'Error!',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonColor: '#3085d6'
                });
            }
        });
    });

    // Handle close schedule
    $('form[action="close_schedule"]').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var scheduleId = form.find('input[name="schedule_id"]').val();
        
        Swal.fire({
            title: 'Are you sure?',
            text: "This will close the exam schedule. This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, close it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show processing alert
                let processingAlert = Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we close the schedule.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    backdrop: true,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit form via AJAX
                $.ajax({
                    url: 'handle_exam_schedule.php',
                    type: 'POST',
                    data: {
                        action: 'close_schedule',
                        schedule_id: scheduleId
                    },
                    success: function(response) {
                        // Close processing alert
                        Swal.close();
                        
                        // Show success message
                        Swal.fire({
                            title: 'Success!',
                            text: 'Exam schedule closed successfully',
                            icon: 'success',
                            confirmButtonColor: '#3085d6',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            backdrop: true,
                            showConfirmButton: true,
                            showCloseButton: true
                        }).then(() => {
                            // Reload page after success message is closed
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        // Close processing alert
                        Swal.close();
                        
                        // Show error message
                        let errorMessage = 'An error occurred while closing the schedule.';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        }
                        
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                });
            }
        });
    });

    // Initialize tooltips
    $('[title]').tooltip();
});
</script> 