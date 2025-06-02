<?php
require_once "../config/database.php";

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'encode_result') {
    $registration_id = $_POST['registration_id'] ?? null;
    $result = $_POST['result'] ?? null;
    $remarks = $_POST['remarks'] ?? '';
    
    if ($registration_id && $result) {
        // Start transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Update registration with result
            $update_query = "UPDATE exam_registrations SET result = ?, remarks = ? WHERE registration_id = ?";
            $stmt = mysqli_prepare($conn, $update_query);
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssi", $result, $remarks, $registration_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Log the action
                    $log_query = "INSERT INTO activity_log (user_id, action, details) VALUES (?, 'encode_exam_result', ?)";
                    $log_stmt = mysqli_prepare($conn, $log_query);
                    
                    if ($log_stmt) {
                        $details = "Encoded result for registration ID: " . $registration_id . " - Result: " . $result;
                        mysqli_stmt_bind_param($log_stmt, "is", $_SESSION['user_id'], $details);
                        mysqli_stmt_execute($log_stmt);
                        mysqli_stmt_close($log_stmt);
                    }
                    
                    mysqli_commit($conn);
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Exam result has been saved successfully'
                    ]);
                } else {
                    throw new Exception("Error saving exam result: " . mysqli_error($conn));
                }
                mysqli_stmt_close($stmt);
            } else {
                throw new Exception("Error preparing statement: " . mysqli_error($conn));
            }
        } catch (Exception $e) {
            mysqli_rollback($conn);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    }
    exit();
}

// If no valid action was performed
header('Content-Type: application/json');
echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
exit(); 