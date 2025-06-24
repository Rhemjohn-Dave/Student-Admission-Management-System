<?php
// Only start session if it hasn't been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_type"] !== "admin"){
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

// Include database connection
require_once "../../config/database.php";

// Debug: Check database connection
if (!$conn) {
    error_log("Handler: Database connection failed");
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

// Handle test request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['test_ajax'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Handler is working']);
    exit;
}

// Handle single score update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_single_score'])) {
    // Debug: Log the request
    error_log("Handler: Single score update request received: " . print_r($_POST, true));
    
    $applicant_id = trim($_POST['applicant_id']);
    $score = trim($_POST['score']);
    
    try {
        // Validate inputs
        if (empty($applicant_id) || !is_numeric($score) || $score < 0 || $score > 100) {
            throw new Exception("Invalid score. Please enter a number between 0 and 100.");
        }

        // Debug: Log the values
        error_log("Handler: Processing score update - Applicant ID: $applicant_id, Score: $score");

        // Get applicant details
        $user_query = "SELECT user_id, first_name, last_name FROM applicants WHERE applicant_id = ?";
        $user_stmt = $conn->prepare($user_query);
        if (!$user_stmt) {
            throw new Exception("Error preparing user query: " . $conn->error);
        }
        
        $user_stmt->bind_param("i", $applicant_id);
        if (!$user_stmt->execute()) {
            throw new Exception("Error executing user query: " . $user_stmt->error);
        }
        
        $user_result = $user_stmt->get_result();
        if (!$user = $user_result->fetch_assoc()) {
            throw new Exception("Applicant not found");
        }
        
        $user_id = $user['user_id'];
        $applicant_name = $user['first_name'] . ' ' . $user['last_name'];
        
        // Get the exam registration for this applicant
        $reg_query = "SELECT reg.registration_id, reg.exam_schedule_id, es.exam_id
                     FROM exam_registrations reg 
                     JOIN exam_schedules es ON reg.exam_schedule_id = es.exam_id 
                     WHERE reg.applicant_id = ? 
                     ORDER BY reg.registration_date DESC LIMIT 1";
        $reg_stmt = $conn->prepare($reg_query);
        if (!$reg_stmt) {
            throw new Exception("Error preparing registration query: " . $conn->error);
        }
        
        $reg_stmt->bind_param("i", $applicant_id);
        if (!$reg_stmt->execute()) {
            throw new Exception("Error executing registration query: " . $reg_stmt->error);
        }
        
        $reg_result = $reg_stmt->get_result();
        if (!$registration = $reg_result->fetch_assoc()) {
            throw new Exception("No exam registration found for this applicant");
        }
        
        $registration_id = $registration['registration_id'];
        $exam_id = $registration['exam_id'];
        
        // Check if score already exists for this registration
        $check_query = "SELECT score_id FROM exam_scores WHERE registration_id = ?";
        $check_stmt = $conn->prepare($check_query);
        if (!$check_stmt) {
            throw new Exception("Error preparing check query: " . $conn->error);
        }
        
        $check_stmt->bind_param("i", $registration_id);
        if (!$check_stmt->execute()) {
            throw new Exception("Error executing check query: " . $check_stmt->error);
        }
        
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            // Update existing score
            $update_query = "UPDATE exam_scores SET 
                score = ?, 
                status = CASE 
                    WHEN ? >= 75 THEN 'qualified' 
                    ELSE 'not_qualified' 
                END,
                updated_at = NOW() 
                WHERE registration_id = ?";
            $update_stmt = $conn->prepare($update_query);
            if (!$update_stmt) {
                throw new Exception("Error preparing update query: " . $conn->error);
            }
            
            $update_stmt->bind_param("ddi", $score, $score, $registration_id);
            if (!$update_stmt->execute()) {
                throw new Exception("Error executing update query: " . $update_stmt->error);
            }
        } else {
            // Insert new score
            $insert_query = "INSERT INTO exam_scores (
                registration_id,
                score, 
                status
            ) VALUES (
                ?,
                ?, 
                CASE 
                    WHEN ? >= 75 THEN 'qualified' 
                    ELSE 'not_qualified' 
                END
            )";
            $insert_stmt = $conn->prepare($insert_query);
            if (!$insert_stmt) {
                throw new Exception("Error preparing insert query: " . $conn->error);
            }
            
            $insert_stmt->bind_param("idd", $registration_id, $score, $score);
            if (!$insert_stmt->execute()) {
                // Get more detailed error information
                $error_msg = $insert_stmt->error;
                $errno = $insert_stmt->errno;
                
                // Check if it's an auto-increment issue
                if ($errno == 1062) { // Duplicate entry error
                    throw new Exception("Database error: Primary key auto-increment issue. Please check the exam_scores table structure. Error: " . $error_msg);
                } else {
                    throw new Exception("Error executing insert query: " . $error_msg . " (Error Code: " . $errno . ")");
                }
            }
        }
        
        // Return success response
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Score updated successfully for ' . $applicant_name]);
        
    } catch (Exception $e) {
        // Return error response
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    // Invalid request
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?> 