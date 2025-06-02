<?php
session_start();
require_once "../config/database.php";

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch existing exam schedules
$schedules_query = "SELECT * FROM exam_schedules ORDER BY exam_date DESC, exam_time DESC";
$schedules_result = mysqli_query($conn, $schedules_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Schedule Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Manage Exam Schedules</h2>
        
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

        <!-- Add Schedule Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Add New Exam Schedule</h4>
            </div>
            <div class="card-body">
                <form id="addScheduleForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date" class="form-label">Exam Date</label>
                            <input type="date" class="form-control" id="date" name="date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="time" class="form-label">Exam Time</label>
                            <input type="time" class="form-control" id="time" name="time" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="venue" class="form-label">Venue</label>
                            <input type="text" class="form-control" id="venue" name="venue" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="max_participants" class="form-label">Maximum Participants</label>
                            <input type="number" class="form-control" id="max_participants" name="max_participants" required min="1">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Schedule</button>
                </form>
            </div>
        </div>

        <!-- Existing Schedules Table -->
        <div class="card">
            <div class="card-header">
                <h4>Existing Exam Schedules</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Venue</th>
                                <th>Max Participants</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($schedule = mysqli_fetch_assoc($schedules_result)): ?>
                                <tr>
                                    <td><?php echo date('F d, Y', strtotime($schedule['exam_date'])); ?></td>
                                    <td><?php echo date('h:i A', strtotime($schedule['exam_time'])); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['venue']); ?></td>
                                    <td><?php echo $schedule['max_participants']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $schedule['status'] === 'scheduled' ? 'success' : ($schedule['status'] === 'completed' ? 'info' : 'danger'); ?>">
                                            <?php echo ucfirst($schedule['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($schedule['status'] === 'scheduled'): ?>
                                            <button class="btn btn-danger btn-sm" onclick="closeSchedule(<?php echo $schedule['exam_id']; ?>)">
                                                Close Schedule
                                            </button>
                                            <button class="btn btn-success btn-sm" onclick="completeSchedule(<?php echo $schedule['exam_id']; ?>)">
                                                Mark as Completed
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    
    <script>
    document.getElementById('addScheduleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(this);
        
        // Debug: Log form data
        console.log('Form data being sent:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        // Show loading state
        Swal.fire({
            title: 'Processing...',
            text: 'Please wait while we add the schedule',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Send AJAX request
        fetch('handle_exam_schedule.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Raw response:', response);
            return response.text().then(text => {
                console.log('Raw response text:', text);
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    throw new Error('Invalid JSON response from server');
                }
            });
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    // Reload the page to show the new schedule
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message || 'An error occurred while adding the schedule.'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An error occurred while processing your request. Check console for details.'
            });
        });
    });

    function closeSchedule(scheduleId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will close the exam schedule and prevent new registrations.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, close it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create form data
                const formData = new FormData();
                formData.append('action', 'close_schedule');
                formData.append('schedule_id', scheduleId);

                // Send AJAX request
                fetch('handle_exam_schedule.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            // Reload the page to show the updated status
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message
                        });
                    }
                })
            }
        });
    }

    function completeSchedule(scheduleId) {
        Swal.fire({
            title: 'Mark Exam as Completed?',
            text: "This will allow you to encode exam grades for this schedule.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, mark as completed!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create form data
                const formData = new FormData();
                formData.append('action', 'complete_schedule');
                formData.append('schedule_id', scheduleId);

                // Send AJAX request
                fetch('handle_exam_schedule.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            // Reload the page to show the updated status
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message
                        });
                    }
                })
            }
        });
    }
    </script>
</body>
</html> 