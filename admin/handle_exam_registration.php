<?php
require_once "../config/database.php";

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle registration cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_registration'])) {
    $registration_id = $_POST['registration_id'] ?? null;
    
    if ($registration_id) {
        // Start transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Update registration status
            $update_query = "UPDATE exam_registrations SET status = 'cancelled' WHERE registration_id = ?";
            $stmt = mysqli_prepare($conn, $update_query);
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $registration_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Log the action
                    $log_query = "INSERT INTO activity_log (user_id, action, details) VALUES (?, 'cancel_exam_registration', ?)";
                    $log_stmt = mysqli_prepare($conn, $log_query);
                    
                    if ($log_stmt) {
                        $details = "Cancelled exam registration ID: " . $registration_id;
                        mysqli_stmt_bind_param($log_stmt, "is", $_SESSION['user_id'], $details);
                        mysqli_stmt_execute($log_stmt);
                        mysqli_stmt_close($log_stmt);
                    }
                    
                    mysqli_commit($conn);
                    $_SESSION['success'] = "Registration cancelled successfully";
                } else {
                    throw new Exception("Error cancelling registration: " . mysqli_error($conn));
                }
                mysqli_stmt_close($stmt);
            } else {
                throw new Exception("Error preparing statement: " . mysqli_error($conn));
            }
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $_SESSION['error'] = $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Invalid registration ID";
    }
    
    header("Location: view_exam_registrations.php");
    exit();
}

// If no valid action was performed, redirect back
header("Location: view_exam_registrations.php");
exit(); 