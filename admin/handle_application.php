<?php
require_once "../config/database.php";

// Check if user is logged in and is admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Get the action and user_id
$action = $_POST['action'] ?? '';
$user_id = $_POST['user_id'] ?? 0;

if (empty($action) || empty($user_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters']);
    exit();
}

// Handle the action
switch ($action) {
    case 'approve_application':
        // First get the applicant's name data
        $get_applicant_sql = "SELECT first_name, middle_name, last_name FROM applicants WHERE user_id = ?";
        if ($stmt = mysqli_prepare($conn, $get_applicant_sql)) {
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $applicant_data = mysqli_fetch_assoc($result);
            
            if ($applicant_data) {
                // Update user status and name data
                $sql = "UPDATE users SET 
                        status = 'active',
                        first_name = ?,
                        middle_name = ?,
                        last_name = ?
                        WHERE user_id = ? AND user_type = 'applicant'";
                
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "sssi", 
                        $applicant_data['first_name'],
                        $applicant_data['middle_name'],
                        $applicant_data['last_name'],
                        $user_id
                    );
                    
                    if (mysqli_stmt_execute($stmt)) {
                        echo json_encode(['success' => true, 'message' => 'Application approved successfully']);
                    } else {
                        http_response_code(500);
                        echo json_encode(['error' => 'Error approving application: ' . mysqli_error($conn)]);
                    }
                }
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Applicant data not found']);
            }
        }
        break;

    case 'reject_application':
        $sql = "UPDATE users SET status = 'inactive' WHERE user_id = ? AND user_type = 'applicant'";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => true, 'message' => 'Application rejected successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error rejecting application: ' . mysqli_error($conn)]);
            }
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
?> 