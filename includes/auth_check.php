<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../auth/login.php");
    exit;
}

// Check if user type is set
if (!isset($_SESSION["user_type"])) {
    header("location: ../auth/login.php");
    exit;
}

// Check if user's status is active
if(isset($_SESSION["status"]) && $_SESSION["status"] !== 'active'){
    // Logout the user
    session_destroy();
    header("location: auth/login.php?error=inactive");
    exit;
}

// Check if user has access to the current page based on user type
$current_page = basename($_SERVER['PHP_SELF']);
$user_type = $_SESSION["user_type"] ?? '';

// Define allowed pages for each user type
$allowed_pages = [
    'admin' => ['dashboard', 'interviewers', 'colleges', 'exam_schedules', 'interview_schedules', 
                'exam_results', 'programs', 'encode_scores', 'exam_rankings', 'program_cutoffs', 
                'student_rankings', 'student_records.php', 'profile.php', 'reports.php'],
    'interviewer' => ['dashboard', 'interview_schedules', 'interview_evaluation', 'interview_results', 'profile'],
    'applicant' => ['dashboard', 'exam_registration', 'exam_schedule', 'exam_result', 'profile']
];

// Check if the current page is allowed for the user type
if (!in_array($current_page, $allowed_pages[$user_type] ?? [])) {
    $page = 'dashboard';
}
?> 