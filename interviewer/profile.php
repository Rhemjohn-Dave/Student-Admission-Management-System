<?php
// Only start session if it hasn't been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/auth_check.php';
require_once '../config/database.php';

// Check if user is logged in and is an interviewer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'interviewer') {
    header("location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get interviewer's information
$interviewer_query = "
    SELECT 
        u.*,
        ph.program_head_id,
        p.program_name,
        c.college_name
    FROM users u
    JOIN program_heads ph ON u.user_id = ph.user_id
    JOIN programs p ON ph.program_id = p.program_id
    JOIN colleges c ON p.college_id = c.college_id
    WHERE u.user_id = ?
";

$interviewer = null;
if ($stmt = mysqli_prepare($conn, $interviewer_query)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $interviewer = mysqli_fetch_assoc($result);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $current_password = trim($_POST["current_password"]);
    $new_password = trim($_POST["new_password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    
    $errors = [];
    
    // Validate current password if changing password
    if (!empty($current_password)) {
        if (!password_verify($current_password, $interviewer['password'])) {
            $errors[] = "Current password is incorrect.";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match.";
        } elseif (strlen($new_password) < 6) {
            $errors[] = "New password must be at least 6 characters long.";
        }
    }
    
    // Update profile if no errors
    if (empty($errors)) {
        $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?";
        $params = [$first_name, $last_name, $email, $phone];
        $types = "ssss";
        
        // Add password update if provided
        if (!empty($new_password)) {
            $sql .= ", password = ?";
            $params[] = password_hash($new_password, PASSWORD_DEFAULT);
            $types .= "s";
        }
        
        $sql .= " WHERE user_id = ?";
        $params[] = $user_id;
        $types .= "i";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Profile updated successfully.";
            // Refresh the page to show updated information
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $errors[] = "Something went wrong. Please try again later.";
        }
    }
}

// Include the template file
require_once 'templates/profile_template.php'; 