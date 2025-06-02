<?php

require_once "../config/database.php";

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch all active students with their details
$students_query = "
    SELECT 
        u.user_id,
        a.first_name,
        a.middle_name,
        a.last_name,
        CONCAT(a.last_name, 
        ',',
            CASE WHEN a.middle_name IS NOT NULL AND a.middle_name != '' 
                THEN CONCAT(' ', a.middle_name) 
                ELSE '' 
            END,
            ' ', a.first_name) as full_name,
        u.email,
        u.status,
        a.applicant_id,
        a.birth_date,
        a.gender,
        a.mobile_number,
        p1.program_name as primary_program,
        p2.program_name as secondary_program,
        es.exam_id,
        es.exam_date,
        es.exam_time,
        es.status as exam_status
    FROM users u
    JOIN applicants a ON u.user_id = a.user_id
    LEFT JOIN programs p1 ON a.primary_program_id = p1.program_id
    LEFT JOIN programs p2 ON a.secondary_program_id = p2.program_id
    LEFT JOIN exam_registrations er ON a.applicant_id = er.applicant_id
    LEFT JOIN exam_schedules es ON er.exam_schedule_id = es.exam_id
    WHERE u.user_type = 'applicant' AND u.status = 'active'
    ORDER BY a.last_name, a.first_name ASC
";

$students_result = mysqli_query($conn, $students_query);
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Student Records</h1>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Active Students</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="studentsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Program</th>
                            <th>Contact</th>
                            <th>Exam Schedule</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($student = mysqli_fetch_assoc($students_result)): ?>
                            <tr>
                                <td>
                                    <?php 
                                        echo htmlspecialchars($student['full_name']); 
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td>
                                    <?php 
                                        echo htmlspecialchars($student['primary_program']);
                                        if (!empty($student['secondary_program'])) {
                                            echo '<br><small class="text-muted">Secondary: ' . htmlspecialchars($student['secondary_program']) . '</small>';
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                        echo htmlspecialchars($student['mobile_number']);
                                        echo '<br><small class="text-muted">' . ucfirst($student['gender']) . '</small>';
                                    ?>
                                </td>
                                <td>
                                    <?php if ($student['exam_id']): ?>
                                        <?php 
                                            echo date('M d, Y', strtotime($student['exam_date']));
                                            echo '<br><small class="text-muted">' . date('h:i A', strtotime($student['exam_time'])) . '</small>';
                                        ?>
                                    <?php else: ?>
                                        <span class="text-muted">Not scheduled</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($student['exam_id']): ?>
                                        <span class="badge badge-<?php 
                                            echo $student['exam_status'] === 'open' ? 'primary' : 
                                                ($student['exam_status'] === 'closed' ? 'success' : 'warning');
                                        ?>">
                                            <?php echo ucfirst($student['exam_status']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm view-details" 
                                            data-student-id="<?php echo $student['user_id']; ?>"
                                            data-toggle="modal" data-target="#studentDetailsModal">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <?php if (!$student['exam_id']): ?>
                                        <button type="button" class="btn btn-primary btn-sm schedule-exam" 
                                                data-student-id="<?php echo $student['user_id']; ?>"
                                                data-name="<?php echo htmlspecialchars($student['full_name']); ?>"
                                                data-toggle="modal" data-target="#scheduleExamModal">
                                            <i class="fas fa-calendar"></i> Schedule
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Student Details Modal -->
<div class="modal fade" id="studentDetailsModal" tabindex="-1" role="dialog" aria-labelledby="studentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentDetailsModalLabel">Student Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="studentDetailsContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Exam Modal -->
<div class="modal fade" id="scheduleExamModal" tabindex="-1" role="dialog" aria-labelledby="scheduleExamModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleExamModalLabel">Schedule Exam</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="scheduleExamForm">
                    <input type="hidden" name="student_id" id="studentId">
                    <div class="form-group">
                        <label for="examDate">Exam Date</label>
                        <input type="date" class="form-control" id="examDate" name="exam_date" required>
                    </div>
                    <div class="form-group">
                        <label for="examTime">Exam Time</label>
                        <select class="form-control" id="examTime" name="exam_time" required>
                            <option value="">Select Time Window</option>
                            <option value="9:00 AM - 12:00 PM">9:00 AM - 12:00 PM</option>
                            <option value="1:00 PM - 4:00 PM">1:00 PM - 4:00 PM</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmSchedule">Schedule Exam</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTables
    $('#studentsTable').DataTable({
        "order": [[0, "asc"]],
        "pageLength": 10,
        "columnDefs": [
            { "orderable": false, "targets": -1 }
        ]
    });

    // View Student Details
    $(document).on('click', '.view-details', function() {
        var studentId = $(this).data('student-id');
        
        // Show loading state
        $('#studentDetailsContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');
        
        // Load student details via AJAX
        $.ajax({
            url: 'get_student_details.php',
            type: 'GET',
            data: { student_id: studentId },
            success: function(response) {
                $('#studentDetailsContent').html(response);
            },
            error: function() {
                $('#studentDetailsContent').html('<div class="alert alert-danger">Error loading student details.</div>');
            }
        });
    });

    // Schedule Exam
    $(document).on('click', '.schedule-exam', function() {
        var studentId = $(this).data('student-id');
        var studentName = $(this).data('name');
        
        $('#studentId').val(studentId);
        $('#scheduleExamModalLabel').text('Schedule Exam for ' + studentName);
    });

    // Confirm Schedule
    $('#confirmSchedule').click(function() {
        var formData = $('#scheduleExamForm').serialize();
        
        Swal.fire({
            title: 'Confirm Schedule',
            text: 'Are you sure you want to schedule this exam?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, schedule it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show processing alert
                let processingAlert = Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we schedule the exam.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit form via AJAX
                $.ajax({
                    url: 'handle_exam_schedule.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        // Close processing alert
                        Swal.close();
                        
                        // Show success message
                        Swal.fire({
                            title: 'Success!',
                            text: 'Exam scheduled successfully',
                            icon: 'success'
                        }).then(() => {
                            // Reload page
                            window.location.reload();
                        });
                    },
                    error: function() {
                        // Close processing alert
                        Swal.close();
                        
                        // Show error message
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while scheduling the exam.',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });
});
</script> 