<?php
// Start the session
session_start();

// Check if user is logged in and is an admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_type"] !== "admin"){
    die(json_encode(['success' => false, 'message' => 'Unauthorized access']));
}

// Include database connection
require_once "../config/database.php";

if(isset($_POST['schedule_id'])) {
    $schedule_id = $_POST['schedule_id'];
    
    // Update the schedule status to closed
    $sql = "UPDATE interview_schedules SET status = 'closed' WHERE schedule_id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $schedule_id);
        
        if(mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'message' => 'Schedule closed successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error closing schedule: ' . mysqli_error($conn)]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error preparing statement: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?> 