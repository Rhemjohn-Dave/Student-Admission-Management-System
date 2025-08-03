<?php
session_start();
require_once "../../config/database.php";

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

// Handle form submission for updating programs
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'update_programs') {
        $applicant_id = $_POST['applicant_id'];
        $primary_program_id = $_POST['primary_program_id'];
        $secondary_program_id = $_POST['secondary_program_id'];
        
        // Update the applicant's program choices
        $update_query = "UPDATE applicants SET primary_program_id = ?, secondary_program_id = ? WHERE applicant_id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "iii", $primary_program_id, $secondary_program_id, $applicant_id);
        
        if (mysqli_stmt_execute($stmt)) {
            // Update program rankings for this student
            require_once 'update_program_rankings.php';
            updateProgramRankings($conn, $applicant_id);
            
            $_SESSION['success'] = "Student programs updated successfully. Rankings have been recalculated.";
        } else {
            $_SESSION['error'] = "Error updating student programs: " . mysqli_error($conn);
        }
        
        // Redirect to refresh the page
        header("Location: ../index.php?page=manage_student_programs");
        exit();
    }
}

// If not a POST request, redirect back
header("Location: ../index.php?page=manage_student_programs");
exit();
?> 