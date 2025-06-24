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

// Handle different actions
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add_schedule':
            // Get POST data for new schedule
            $exam_date = $_POST['date'] ?? null;
            $exam_time = $_POST['time'] ?? null;
            $venue = $_POST['venue'] ?? null;
            $max_participants = $_POST['max_participants'] ?? null;

            // Log received data
            file_put_contents($log_file, "Received data for new schedule:\n", FILE_APPEND);
            file_put_contents($log_file, "exam_date: $exam_date\n", FILE_APPEND);
            file_put_contents($log_file, "exam_time: $exam_time\n", FILE_APPEND);
            file_put_contents($log_file, "venue: $venue\n", FILE_APPEND);
            file_put_contents($log_file, "max_participants: $max_participants\n", FILE_APPEND);

            // Validate required fields for new schedule
            if (!$exam_date || !$exam_time || !$venue || !$max_participants) {
                file_put_contents($log_file, "Validation failed - missing required fields for new schedule\n", FILE_APPEND);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'All fields are required for new schedule'
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

                // Log the action
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
                mysqli_rollback($conn);
                file_put_contents($log_file, "Error occurred: " . $e->getMessage() . "\n", FILE_APPEND);
                echo json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }
            break;

        case 'close_schedule':
            if (!isset($_POST['schedule_id']) || empty($_POST['schedule_id'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Schedule ID is required.'
                ]);
                exit();
            }

            $schedule_id = intval($_POST['schedule_id']);
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
            break;

        case 'complete_schedule':
            if (!isset($_POST['schedule_id']) || empty($_POST['schedule_id'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Schedule ID is required.'
                ]);
                exit();
            }

            $schedule_id = intval($_POST['schedule_id']);

            // Debug log
            file_put_contents($log_file, "Checking schedule ID: $schedule_id\n", FILE_APPEND);

            // First check if schedule exists
            $schedule_query = "SELECT * FROM exam_schedules WHERE exam_id = ?";
            $schedule_stmt = $conn->prepare($schedule_query);
            if (!$schedule_stmt) {
                file_put_contents($log_file, "Error preparing schedule query: " . $conn->error . "\n", FILE_APPEND);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error preparing schedule query: ' . $conn->error
                ]);
                exit();
            }

            $schedule_stmt->bind_param("i", $schedule_id);
            $schedule_stmt->execute();
            $schedule_result = $schedule_stmt->get_result();
            $schedule = $schedule_result->fetch_assoc();
            $schedule_stmt->close();

            if (!$schedule) {
                file_put_contents($log_file, "Schedule not found for ID: $schedule_id\n", FILE_APPEND);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Exam schedule not found.'
                ]);
                exit();
            }

            file_put_contents($log_file, "Schedule found: " . print_r($schedule, true) . "\n", FILE_APPEND);

            // Then check for registered applicants
            $registrations_query = "
                SELECT COUNT(*) as registered_count
                FROM exam_registrations er
                WHERE er.exam_schedule_id = ?
            ";
            
            $registrations_stmt = $conn->prepare($registrations_query);
            if (!$registrations_stmt) {
                file_put_contents($log_file, "Error preparing registrations query: " . $conn->error . "\n", FILE_APPEND);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error preparing registrations query: ' . $conn->error
                ]);
                exit();
            }

            $registrations_stmt->bind_param("i", $schedule_id);
            $registrations_stmt->execute();
            $registrations_result = $registrations_stmt->get_result();
            $registrations = $registrations_result->fetch_assoc();
            $registrations_stmt->close();

            file_put_contents($log_file, "Registration count: " . print_r($registrations, true) . "\n", FILE_APPEND);

            if ($registrations['registered_count'] == 0) {
                file_put_contents($log_file, "No registrations found for schedule ID: $schedule_id\n", FILE_APPEND);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Cannot mark schedule as completed. No registrations found for this exam schedule.'
                ]);
                exit();
            }

            // Start transaction
            $conn->begin_transaction();
            try {
                // Mark the schedule as completed
                $update_schedule = $conn->prepare("UPDATE exam_schedules SET status = 'completed' WHERE exam_id = ?");
                $update_schedule->bind_param("i", $schedule_id);
                if (!$update_schedule->execute()) {
                    throw new Exception('Failed to update schedule status: ' . $update_schedule->error);
                }
                $update_schedule->close();

                // Mark all related registrations as completed
                $update_registrations = $conn->prepare("UPDATE exam_registrations SET status = 'completed' WHERE exam_schedule_id = ? AND status = 'registered'");
                $update_registrations->bind_param("i", $schedule_id);
                if (!$update_registrations->execute()) {
                    throw new Exception('Failed to update registrations: ' . $update_registrations->error);
                }
                $update_registrations->close();

                $conn->commit();
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Exam schedule marked as completed and all registrations updated.'
                ]);
            } catch (Exception $e) {
                $conn->rollback();
                echo json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }
            exit();

        default:
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid action specified.'
            ]);
            break;
    }
    exit();
}

// If no action specified, return error
echo json_encode([
    'status' => 'error',
    'message' => 'No action specified.'
]);
exit();

// Close the database connection
mysqli_close($conn);
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Request completed\n", FILE_APPEND);
?> 