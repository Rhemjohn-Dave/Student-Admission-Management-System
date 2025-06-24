<?php
// Only start session if it hasn't been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an applicant
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'applicant') {
    header("Location: ../login.php");
    exit();
}

// Include database connection
require_once "../config/database.php";

// Get available exam schedules
$schedules_query = "SELECT * FROM exam_schedules WHERE status = 'scheduled' AND exam_date >= CURDATE() ORDER BY exam_date ASC, exam_time ASC";
$schedules_result = mysqli_query($conn, $schedules_query);

// Get user's existing registration
$user_id = $_SESSION['user_id'];

// First get the applicant_id for this user
$applicant_query = "SELECT applicant_id FROM applicants WHERE user_id = ?";
$applicant_stmt = mysqli_prepare($conn, $applicant_query);

if (!$applicant_stmt) {
    error_log("Error preparing applicant query: " . mysqli_error($conn));
    $has_registration = false;
} else {
    mysqli_stmt_bind_param($applicant_stmt, "i", $user_id);
    mysqli_stmt_execute($applicant_stmt);
    $applicant_result = mysqli_stmt_get_result($applicant_stmt);
    $applicant = mysqli_fetch_assoc($applicant_result);
    mysqli_stmt_close($applicant_stmt);

    if ($applicant) {
        // Now check for existing registration using applicant_id
        $registration_query = "SELECT * FROM exam_registrations WHERE applicant_id = ? AND status = 'registered'";
        $registration_stmt = mysqli_prepare($conn, $registration_query);
        
        if (!$registration_stmt) {
            error_log("Error preparing registration query: " . mysqli_error($conn));
            $has_registration = false;
        } else {
            mysqli_stmt_bind_param($registration_stmt, "i", $applicant['applicant_id']);
            mysqli_stmt_execute($registration_stmt);
            $registration_result = mysqli_stmt_get_result($registration_stmt);
            $has_registration = mysqli_num_rows($registration_result) > 0;
            mysqli_stmt_close($registration_stmt);
        }
    } else {
        $has_registration = false;
    }
}

// Store schedules in an array for calendar view
$schedules = [];
while ($schedule = mysqli_fetch_assoc($schedules_result)) {
    // Count registered participants for each schedule
    $count_query = "SELECT COUNT(*) as registered FROM exam_registrations WHERE exam_schedule_id = ? AND status = 'registered'";
    $count_stmt = mysqli_prepare($conn, $count_query);
    
    if ($count_stmt) {
        mysqli_stmt_bind_param($count_stmt, "i", $schedule['exam_id']);
        mysqli_stmt_execute($count_stmt);
        $count_result = mysqli_stmt_get_result($count_stmt);
        $count = mysqli_fetch_assoc($count_result)['registered'];
        mysqli_stmt_close($count_stmt);
        
        // Add available slots to the schedule data
        $schedule['available_slots'] = $schedule['max_participants'] - $count;
    } else {
        $schedule['available_slots'] = $schedule['max_participants'];
        error_log("Error preparing count statement: " . mysqli_error($conn));
    }
    
    $schedules[] = $schedule;
}

// Prepare calendar events data
$calendar_events = [];
foreach ($schedules as $schedule) {
    $calendar_events[] = [
        'title' => htmlspecialchars($schedule['venue']) . ' (' . $schedule['available_slots'] . ' slots)',
        'start' => $schedule['exam_date'] . 'T' . $schedule['exam_time'],
        'color' => $schedule['available_slots'] > 0 ? '#28a745' : '#dc3545',
        'extendedProps' => [
            'exam_id' => $schedule['exam_id'],
            'available_slots' => $schedule['available_slots']
        ]
    ];
}

// Store data in session for the parent page to use
$_SESSION['exam_registration_data'] = [
    'schedules' => $schedules,
    'calendar_events' => $calendar_events,
    'has_registration' => $has_registration,
    'success' => $_SESSION['success'] ?? null,
    'error' => $_SESSION['error'] ?? null
];

// Clear session messages
unset($_SESSION['success']);
unset($_SESSION['error']);
?>

<!-- Add FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<!-- Add SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">

<div class="container mt-4">
    <h2>Select Exam Schedule</h2>
    
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

    <?php if ($has_registration): ?>
        <div class="alert alert-info">
            You have already registered for an exam. Please check your dashboard for details.
        </div>
    <?php else: ?>
        <!-- Calendar View -->
        <div class="calendar-container mb-4">
            <div id="calendar" style="height: 600px;"></div>
        </div>

        <!-- Table View -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Venue</th>
                        <th>Available Slots</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $schedule): ?>
                        <tr>
                            <td><?php echo date('F d, Y', strtotime($schedule['exam_date'])); ?></td>
                            <td><?php echo date('h:i A', strtotime($schedule['exam_time'])); ?></td>
                            <td><?php echo htmlspecialchars($schedule['venue']); ?></td>
                            <td>
                                <span id="slots-<?php echo $schedule['exam_id']; ?>" class="<?php echo $schedule['available_slots'] > 0 ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo $schedule['available_slots']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($schedule['available_slots'] > 0): ?>
                                    <button class="btn btn-primary btn-sm register-btn" data-exam-id="<?php echo $schedule['exam_id']; ?>">Register</button>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled>Full</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Add FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<!-- Add SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

<script>
$(document).ready(function() {
    var calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: <?php echo json_encode($calendar_events); ?>,
            eventClick: function(info) {
                var examId = info.event.extendedProps.exam_id;
                console.log("Calendar click, examId:", examId);
                var availableSlots = info.event.extendedProps.available_slots;
                
                if (availableSlots > 0) {
                    confirmRegistration(examId);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Schedule Full',
                        text: 'This exam schedule is already full.'
                    });
                }
            }
        });
        calendar.render();
    }

    // Handle registration from table view
    $('.register-btn').on('click', function() {
        var examId = $(this).attr('data-exam-id');
        console.log("Table button click, examId:", examId);
        confirmRegistration(examId);
    });

    function confirmRegistration(examId) {
        console.log("Confirming registration for examId:", examId);
        Swal.fire({
            title: 'Confirm Registration',
            text: "Are you sure you want to register for this exam schedule?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, register!'
        }).then((result) => {
            if (result.isConfirmed) {
                registerForExam(examId);
            }
        });
    }

    function registerForExam(examId) {
        console.log("Sending AJAX for examId:", examId);
        // Use AJAX to submit the registration
        $.ajax({
            url: 'handle_exam_registration.php',
            type: 'POST',
            data: { exam_id: examId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registration Successful',
                        text: response.message,
                    }).then(() => {
                        // Update UI
                        $('#slots-' + examId).text(response.available_slots);
                        // Optionally disable the button or reload the page
                        location.reload(); 
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Registration Failed',
                        text: response.message,
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'An Error Occurred',
                    text: 'An unexpected error occurred. Please try again later.',
                });
            }
        });
    }
});
</script> 