<?php
// Start session
session_start();

// Include database connection
require_once "../config/database.php";

// Check if user is admin
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["action"])) {
        switch ($_POST["action"]) {
            case "add_program":
                $program_name = trim($_POST["program_name"]);
                $program_code = trim($_POST["program_code"]);
                $college_id = $_POST["college_id"];
                $program_head_id = $_POST["program_head_id"];
                
                if (empty($program_name) || empty($program_code) || empty($college_id) || empty($program_head_id)) {
                    $_SESSION['error'] = "All fields are required";
                } else {
                    // Start transaction
                    mysqli_begin_transaction($conn);
                    
                    try {
                        // Insert program
                        $sql = "INSERT INTO programs (program_name, program_code, college_id, program_head_id, status) VALUES (?, ?, ?, ?, 'active')";
                        if ($stmt = mysqli_prepare($conn, $sql)) {
                            mysqli_stmt_bind_param($stmt, "ssis", $program_name, $program_code, $college_id, $program_head_id);
                            if (!mysqli_stmt_execute($stmt)) {
                                throw new Exception("Error adding program: " . mysqli_error($conn));
                            }
                            $program_id = mysqli_insert_id($conn);
                        }

                        // Insert program head
                        $sql = "INSERT INTO program_heads (user_id, program_id) VALUES (?, ?)";
                        if ($stmt = mysqli_prepare($conn, $sql)) {
                            mysqli_stmt_bind_param($stmt, "ii", $program_head_id, $program_id);
                            if (!mysqli_stmt_execute($stmt)) {
                                throw new Exception("Error adding program head: " . mysqli_error($conn));
                            }
                        }

                        mysqli_commit($conn);
                        $_SESSION['success'] = "Program added successfully";
                    } catch (Exception $e) {
                        mysqli_rollback($conn);
                        $_SESSION['error'] = $e->getMessage();
                    }
                }
                break;

            case "update_program":
                $program_id = $_POST["program_id"];
                $program_name = trim($_POST["program_name"]);
                $program_code = trim($_POST["program_code"]);
                $college_id = $_POST["college_id"];
                $new_program_head_id = $_POST["program_head_id"];
                
                if (empty($program_name) || empty($program_code) || empty($college_id) || empty($new_program_head_id)) {
                    $_SESSION['error'] = "All fields are required";
                } else {
                    // Start transaction
                    mysqli_begin_transaction($conn);
                    
                    try {
                        // Get current program head
                        $sql = "SELECT user_id FROM program_heads WHERE program_id = ?";
                        if ($stmt = mysqli_prepare($conn, $sql)) {
                            mysqli_stmt_bind_param($stmt, "i", $program_id);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            $current_program_head = mysqli_fetch_assoc($result);
                            $current_program_head_id = $current_program_head['user_id'];
                        }

                        // Update program
                        $sql = "UPDATE programs SET program_name = ?, program_code = ?, college_id = ?, program_head_id = ? WHERE program_id = ?";
                        if ($stmt = mysqli_prepare($conn, $sql)) {
                            mysqli_stmt_bind_param($stmt, "ssisi", $program_name, $program_code, $college_id, $new_program_head_id, $program_id);
                            if (!mysqli_stmt_execute($stmt)) {
                                throw new Exception("Error updating program: " . mysqli_error($conn));
                            }
                        }

                        // Update program head
                        $sql = "UPDATE program_heads SET user_id = ? WHERE program_id = ?";
                        if ($stmt = mysqli_prepare($conn, $sql)) {
                            mysqli_stmt_bind_param($stmt, "ii", $new_program_head_id, $program_id);
                            if (!mysqli_stmt_execute($stmt)) {
                                throw new Exception("Error updating program head: " . mysqli_error($conn));
                            }
                        }

                        // If program head changed, update interview schedules
                        if ($current_program_head_id != $new_program_head_id) {
                            // Update open interview schedules
                            $sql = "UPDATE interview_schedules SET interviewer_id = ? WHERE program_id = ? AND status = 'open'";
                            if ($stmt = mysqli_prepare($conn, $sql)) {
                                mysqli_stmt_bind_param($stmt, "ii", $new_program_head_id, $program_id);
                                if (!mysqli_stmt_execute($stmt)) {
                                    throw new Exception("Error updating interview schedules: " . mysqli_error($conn));
                                }
                            }

                            // Update scheduled interviews
                            $sql = "UPDATE interviews i 
                                   JOIN applications a ON i.application_id = a.application_id 
                                   SET i.interviewer_id = ? 
                                   WHERE a.program_id = ? AND i.status = 'scheduled'";
                            if ($stmt = mysqli_prepare($conn, $sql)) {
                                mysqli_stmt_bind_param($stmt, "ii", $new_program_head_id, $program_id);
                                if (!mysqli_stmt_execute($stmt)) {
                                    throw new Exception("Error updating interviews: " . mysqli_error($conn));
                                }
                            }
                        }

                        mysqli_commit($conn);
                        $_SESSION['success'] = "Program updated successfully";
                    } catch (Exception $e) {
                        mysqli_rollback($conn);
                        $_SESSION['error'] = $e->getMessage();
                    }
                }
                break;

            case "delete_program":
                $program_id = $_POST["program_id"];
                
                // Start transaction
                mysqli_begin_transaction($conn);
                
                try {
                    // Update program status
                    $sql = "UPDATE programs SET status = 'inactive' WHERE program_id = ?";
                    if ($stmt = mysqli_prepare($conn, $sql)) {
                        mysqli_stmt_bind_param($stmt, "i", $program_id);
                        if (!mysqli_stmt_execute($stmt)) {
                            throw new Exception("Error deleting program: " . mysqli_error($conn));
                        }
                    }

                    // Close any open interview schedules
                    $sql = "UPDATE interview_schedules SET status = 'closed' WHERE program_id = ? AND status = 'open'";
                    if ($stmt = mysqli_prepare($conn, $sql)) {
                        mysqli_stmt_bind_param($stmt, "i", $program_id);
                        if (!mysqli_stmt_execute($stmt)) {
                            throw new Exception("Error closing interview schedules: " . mysqli_error($conn));
                        }
                    }

                    // Cancel any scheduled interviews
                    $sql = "UPDATE interviews i 
                           JOIN applications a ON i.application_id = a.application_id 
                           SET i.status = 'cancelled' 
                           WHERE a.program_id = ? AND i.status = 'scheduled'";
                    if ($stmt = mysqli_prepare($conn, $sql)) {
                        mysqli_stmt_bind_param($stmt, "i", $program_id);
                        if (!mysqli_stmt_execute($stmt)) {
                            throw new Exception("Error cancelling interviews: " . mysqli_error($conn));
                        }
                    }

                    mysqli_commit($conn);
                    $_SESSION['success'] = "Program deleted successfully";
                } catch (Exception $e) {
                    mysqli_rollback($conn);
                    $_SESSION['error'] = $e->getMessage();
                }
                break;
        }
    }
}

// Redirect back to programs page
header("Location: index.php?page=programs");
exit(); 