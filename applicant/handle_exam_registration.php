<?php
session_start();
require_once "../config/database.php";

// Debugging: Log POST data
file_put_contents('exam_registration_debug.log', print_r($_POST, true), FILE_APPEND);

// Check if user is logged in and is an applicant
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'applicant') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access. Please log in as an applicant.'
    ]);
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
    exit();
}

// Get exam ID from POST data
$exam_id = $_POST['exam_id'] ?? null;

if (!$exam_id) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No exam schedule selected'
    ]);
    exit();
}

try {
    // Start transaction
    mysqli_begin_transaction($conn);

    // Get applicant_id for this user
    $applicant_query = "SELECT applicant_id FROM applicants WHERE user_id = ?";
    $applicant_stmt = mysqli_prepare($conn, $applicant_query);
    
    if (!$applicant_stmt) {
        throw new Exception("Error preparing applicant query: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($applicant_stmt, "i", $user_id);
    mysqli_stmt_execute($applicant_stmt);
    $applicant_result = mysqli_stmt_get_result($applicant_stmt);
    $applicant = mysqli_fetch_assoc($applicant_result);
    mysqli_stmt_close($applicant_stmt);

    if (!$applicant) {
        throw new Exception("Applicant record not found");
    }

    // Check if user already has a registration
    $check_query = "SELECT * FROM exam_registrations WHERE applicant_id = ? AND status = 'registered'";
    $check_stmt = mysqli_prepare($conn, $check_query);
    
    if (!$check_stmt) {
        throw new Exception("Error preparing check statement: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($check_stmt, "i", $applicant['applicant_id']);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($result) > 0) {
        throw new Exception("You have already registered for an exam");
    }
    mysqli_stmt_close($check_stmt);

    // Check if exam schedule exists and has available slots
    $schedule_query = "SELECT * FROM exam_schedules WHERE exam_id = ? AND status = 'scheduled'";
    $schedule_stmt = mysqli_prepare($conn, $schedule_query);
    
    if (!$schedule_stmt) {
        throw new Exception("Error preparing schedule statement: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($schedule_stmt, "i", $exam_id);
    mysqli_stmt_execute($schedule_stmt);
    $schedule = mysqli_fetch_assoc(mysqli_stmt_get_result($schedule_stmt));
    mysqli_stmt_close($schedule_stmt);

    if (!$schedule) {
        throw new Exception("Invalid exam schedule");
    }

    // Count registered participants
    $count_query = "SELECT COUNT(*) as registered FROM exam_registrations WHERE exam_schedule_id = ? AND status = 'registered'";
    $count_stmt = mysqli_prepare($conn, $count_query);
    
    if (!$count_stmt) {
        throw new Exception("Error preparing count statement: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($count_stmt, "i", $exam_id);
    mysqli_stmt_execute($count_stmt);
    $count = mysqli_fetch_assoc(mysqli_stmt_get_result($count_stmt))['registered'];
    mysqli_stmt_close($count_stmt);

    if ($count >= $schedule['max_participants']) {
        throw new Exception("This exam schedule is full");
    }

    // Register for exam
    $register_query = "INSERT INTO exam_registrations (applicant_id, exam_schedule_id, status, registration_date) VALUES (?, ?, 'registered', NOW())";
    $register_stmt = mysqli_prepare($conn, $register_query);
    
    if (!$register_stmt) {
        throw new Exception("Error preparing registration statement: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($register_stmt, "ii", $applicant['applicant_id'], $exam_id);
    
    if (!mysqli_stmt_execute($register_stmt)) {
        throw new Exception("Error registering for exam: " . mysqli_error($conn));
    }
    mysqli_stmt_close($register_stmt);

    // Log the action
    $log_query = "INSERT INTO activity_log (user_id, action) VALUES (?, 'exam_registration')";
    $log_stmt = mysqli_prepare($conn, $log_query);
    
    if (!$log_stmt) {
        throw new Exception("Error preparing log statement: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($log_stmt, "i", $user_id);
    
    if (!mysqli_stmt_execute($log_stmt)) {
        throw new Exception("Error logging action: " . mysqli_error($conn));
    }
    mysqli_stmt_close($log_stmt);

    // Commit transaction
    mysqli_commit($conn);

    // Calculate remaining slots
    $remaining_slots = $schedule['max_participants'] - ($count + 1);

    echo json_encode([
        'status' => 'success',
        'message' => 'Successfully registered for the exam on ' . date('F d, Y', strtotime($schedule['exam_date'])) . ' at ' . date('h:i A', strtotime($schedule['exam_time'])),
        'available_slots' => $remaining_slots
    ]);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 