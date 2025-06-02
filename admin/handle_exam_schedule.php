<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start logging
$log_file = __DIR__ . '/exam_schedule_debug.log';
file_put_contents($log_file, "\n\n" . date('Y-m-d H:i:s') . " - New request started\n", FILE_APPEND);
file_put_contents($log_file, "POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);

// Include database connection
require_once "../config/database.php";

// Check database connection
if (!$conn) {
    file_put_contents($log_file, "Database connection failed: " . mysqli_connect_error() . "\n", FILE_APPEND);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . mysqli_connect_error()
    ]);
    exit();
}

// Set header to return JSON
header('Content-Type: application/json');

// Check if user is logged in and is an admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    file_put_contents($log_file, "Unauthorized access attempt\n", FILE_APPEND);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access. Please log in as an admin.'
    ]);
    exit();
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    file_put_contents($log_file, "Invalid request method: " . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
    exit();
}

// Get POST data - Updated to match form field names
$exam_date = $_POST['date'] ?? null;
$exam_time = $_POST['time'] ?? null;
$venue = $_POST['venue'] ?? null;
$max_participants = $_POST['max_participants'] ?? null;

// Log received data
file_put_contents($log_file, "Received data:\n", FILE_APPEND);
file_put_contents($log_file, "exam_date: $exam_date\n", FILE_APPEND);
file_put_contents($log_file, "exam_time: $exam_time\n", FILE_APPEND);
file_put_contents($log_file, "venue: $venue\n", FILE_APPEND);
file_put_contents($log_file, "max_participants: $max_participants\n", FILE_APPEND);

// Validate required fields
if (!$exam_date || !$exam_time || !$venue || !$max_participants) {
    file_put_contents($log_file, "Validation failed - missing required fields\n", FILE_APPEND);
    echo json_encode([
        'status' => 'error',
        'message' => 'All fields are required'
    ]);
    exit();
}

// Validate exam date (must not be in the past)
if (strtotime($exam_date) < strtotime(date('Y-m-d'))) {
    file_put_contents($log_file, "Validation failed - date in past\n", FILE_APPEND);
    echo json_encode([
        'status' => 'error',
        'message' => 'Exam date cannot be in the past'
    ]);
    exit();
}

// Validate max participants
if (!is_numeric($max_participants) || $max_participants < 1) {
    file_put_contents($log_file, "Validation failed - invalid max participants\n", FILE_APPEND);
    echo json_encode([
        'status' => 'error',
        'message' => 'Maximum participants must be a positive number'
    ]);
    exit();
}

try {
    // Start transaction
    mysqli_begin_transaction($conn);

    // Insert new exam schedule
    $insert_query = "INSERT INTO exam_schedules (exam_date, exam_time, venue, max_participants, status) VALUES (?, ?, ?, ?, 'scheduled')";
    file_put_contents($log_file, "Insert query: $insert_query\n", FILE_APPEND);
    
    $stmt = mysqli_prepare($conn, $insert_query);
    
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "sssi", $exam_date, $exam_time, $venue, $max_participants);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error adding exam schedule: " . mysqli_error($conn));
    }
    
    $exam_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // Log the action - Updated to match table structure
    $log_query = "INSERT INTO activity_log (user_id, action) VALUES (?, ?)";
    $log_stmt = mysqli_prepare($conn, $log_query);
    
    if (!$log_stmt) {
        throw new Exception("Error preparing log statement: " . mysqli_error($conn));
    }
    
    $action = "Added new exam schedule ID: " . $exam_id;
    mysqli_stmt_bind_param($log_stmt, "is", $_SESSION['user_id'], $action);
    
    if (!mysqli_stmt_execute($log_stmt)) {
        throw new Exception("Error logging action: " . mysqli_error($conn));
    }
    mysqli_stmt_close($log_stmt);

    // Commit transaction
    mysqli_commit($conn);

    file_put_contents($log_file, "Successfully added exam schedule with ID: $exam_id\n", FILE_APPEND);

    echo json_encode([
        'status' => 'success',
        'message' => 'Exam schedule added successfully',
        'exam_id' => $exam_id
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    
    file_put_contents($log_file, "Error occurred: " . $e->getMessage() . "\n", FILE_APPEND);
    
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

// Handle schedule closure
if (isset($_POST['action']) && $_POST['action'] === 'close_schedule') {
    $schedule_id = $_POST['schedule_id'];
    $stmt = $conn->prepare("UPDATE exam_schedules SET status = 'cancelled' WHERE exam_id = ?");
    $stmt->bind_param("i", $schedule_id);
    
    if ($stmt->execute()) {
        // Log activity
        $action = "Closed exam schedule ID: $schedule_id";
        $stmt = $conn->prepare("INSERT INTO activity_log (user_id, action) VALUES (?, ?)");
        $stmt->bind_param("is", $_SESSION['user_id'], $action);
        $stmt->execute();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Schedule closed successfully.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error closing schedule: ' . $conn->error
        ]);
    }
    exit();
}

// Handle schedule completion
if (isset($_POST['action']) && $_POST['action'] === 'complete_schedule') {
    $schedule_id = $_POST['schedule_id'];
    $stmt = $conn->prepare("UPDATE exam_schedules SET status = 'completed' WHERE exam_id = ?");
    $stmt->bind_param("i", $schedule_id);
    
    if ($stmt->execute()) {
        // Log activity
        $action = "Marked exam schedule ID: $schedule_id as completed";
        $stmt = $conn->prepare("INSERT INTO activity_log (user_id, action) VALUES (?, ?)");
        $stmt->bind_param("is", $_SESSION['user_id'], $action);
        $stmt->execute();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Schedule marked as completed. You can now encode exam grades.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error completing schedule: ' . $conn->error
        ]);
    }
    exit();
}

// Close the database connection
mysqli_close($conn);
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Request completed\n", FILE_APPEND);
?> 