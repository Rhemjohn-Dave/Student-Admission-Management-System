<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is not logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    // Redirect to login page
    header("location: auth/login.php");
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
    'admin' => ['index.php', 'dashboard.php', 'interviewers.php', 'colleges.php', 'programs.php', 'exam_schedules.php', 'interview_schedules.php', 'exam_results.php', 'student_records.php', 'approve_applications.php', 'profile.php', 'reports.php'],
    'interviewer' => ['index.php', 'dashboard.php', 'interview_schedules.php', 'interview_results.php', 'profile.php'],
    'applicant' => ['index.php', 'dashboard.php', 'application.php', 'exam_schedule.php', 'interview_schedule.php', 'results.php', 'profile.php']
];

// Check if the current page is allowed for the user type
if (!in_array($current_page, $allowed_pages[$user_type] ?? [])) {
    // Redirect to appropriate dashboard
    if ($user_type === 'admin') {
        header("location: admin/index.php");
    } else if ($user_type === 'interviewer') {
        header("location: interviewer/index.php");
    } else if ($user_type === 'applicant') {
        header("location: applicant/index.php");
    } else {
        header("location: index.php");
    }
    exit;
}
?> 