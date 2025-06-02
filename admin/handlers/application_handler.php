<?php

// Include database connection if not already included
// require_once "../../config/database.php"; // Uncomment if database connection is not available via index.php include

// This file handles POST requests for application approval/rejection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the action is set
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        // Assuming $conn is the database connection and $_SESSION is available
        // You might need to include necessary files here if this handler is called directly

        switch ($action) {
            case "approve_application":
                // Check for required user_id
                if (isset($_POST["user_id"])) {
                    $user_id = $_POST["user_id"];

                    // Update user status to active
                    $sql = "UPDATE users SET status = 'active' WHERE user_id = ? AND user_type = 'applicant'";
                    if ($stmt = mysqli_prepare($conn, $sql)) {
                        mysqli_stmt_bind_param($stmt, "i", $user_id);
                        if (mysqli_stmt_execute($stmt)) {
                            $_SESSION['success'] = "Application approved successfully";
                            
                            // Redirect removed as per user request
                            // if (headers_sent()) {
                            //     error_log("Headers already sent before redirect in approve_application.");
                            //     // Optionally, output buffer content for more info
                            //     // error_log("Output buffer: " . ob_get_contents()); 
                            // } else {
                            //     error_log("Executing redirect to student_records.php");
                               // header("Location: student_records.php");
                               // exit();
                            // }
                            

                        } else {
                            $_SESSION['error'] = "Error approving application: " . mysqli_error($conn);
                             error_log("Database execute error in approve_application: " . mysqli_error($conn));
                        }
                         mysqli_stmt_close($stmt);
                    } else {
                         $_SESSION['error'] = "Database query error: " . mysqli_error($conn);
                          error_log("Database prepare error in approve_application: " . mysqli_error($conn));
                    }
                } else {
                     $_SESSION['error'] = "User ID not provided for approval.";
                      error_log("User ID not provided for approval in application_handler.");
                }
                break;

            case "reject_application":
                 // Check for required user_id
                if (isset($_POST["user_id"])) {
                    $user_id = $_POST["user_id"];

                    $sql = "UPDATE users SET status = 'inactive' WHERE user_id = ? AND user_type = 'applicant'";
                    if ($stmt = mysqli_prepare($conn, $sql)) {
                        mysqli_stmt_bind_param($stmt, "i", $user_id);
                         if (mysqli_stmt_execute($stmt)) {
                            $_SESSION['success'] = "Application rejected successfully";
                             error_log("Application rejected successfully for user ID: " . $user_id);
                        } else {
                            $_SESSION['error'] = "Error rejecting application: " . mysqli_error($conn);
                             error_log("Database execute error in reject_application: " . mysqli_error($conn));
                        }
                         mysqli_stmt_close($stmt);
                    } else {
                         $_SESSION['error'] = "Database query error: " . mysqli_error(conn);
                          error_log("Database prepare error in reject_application: " . mysqli_error($conn));
                    }
                } else {
                     $_SESSION['error'] = "User ID not provided for rejection.";
                      error_log("User ID not provided for rejection in application_handler.");
                }
                break;

            case "bulk_approve":
                if (isset($_POST["user_ids"]) && is_array($_POST["user_ids"])) {
                    $user_ids = array_map('intval', $_POST["user_ids"]);
                    $success_count = 0;
                    $error_count = 0;
                    
                    // Start transaction
                    mysqli_begin_transaction($conn);
                    
                    try {
                        foreach ($user_ids as $user_id) {
                            // Get applicant data
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
                                    
                                    if ($update_stmt = mysqli_prepare($conn, $sql)) {
                                        mysqli_stmt_bind_param($update_stmt, "sssi", 
                                            $applicant_data['first_name'],
                                            $applicant_data['middle_name'],
                                            $applicant_data['last_name'],
                                            $user_id
                                        );
                                        
                                        if (mysqli_stmt_execute($update_stmt)) {
                                            $success_count++;
                                        } else {
                                            $error_count++;
                                            error_log("Error updating user $user_id: " . mysqli_error($conn));
                                        }
                                        mysqli_stmt_close($update_stmt);
                                    }
                                }
                                mysqli_stmt_close($stmt);
                            }
                        }
                        
                        if ($success_count > 0) {
                            mysqli_commit($conn);
                            $_SESSION['success'] = "Successfully approved $success_count application(s)." . 
                                                  ($error_count > 0 ? " Failed to approve $error_count application(s)." : "");
                        } else {
                            mysqli_rollback($conn);
                            $_SESSION['error'] = "Failed to approve any applications.";
                        }
                    } catch (Exception $e) {
                        mysqli_rollback($conn);
                        $_SESSION['error'] = "Error processing bulk approval: " . $e->getMessage();
                        error_log("Bulk approval error: " . $e->getMessage());
                    }
                } else {
                    $_SESSION['error'] = "No applications selected for approval.";
                }
                break;

            case "bulk_reject":
                if (isset($_POST["user_ids"]) && is_array($_POST["user_ids"])) {
                    $user_ids = array_map('intval', $_POST["user_ids"]);
                    $success_count = 0;
                    $error_count = 0;
                    
                    // Start transaction
                    mysqli_begin_transaction($conn);
                    
                    try {
                        foreach ($user_ids as $user_id) {
                            $sql = "UPDATE users SET status = 'rejected' WHERE user_id = ? AND user_type = 'applicant'";
                            if ($stmt = mysqli_prepare($conn, $sql)) {
                                mysqli_stmt_bind_param($stmt, "i", $user_id);
                                if (mysqli_stmt_execute($stmt)) {
                                    $success_count++;
                                } else {
                                    $error_count++;
                                    error_log("Error rejecting user $user_id: " . mysqli_error($conn));
                                }
                                mysqli_stmt_close($stmt);
                            }
                        }
                        
                        if ($success_count > 0) {
                            mysqli_commit($conn);
                            $_SESSION['success'] = "Successfully rejected $success_count application(s)." . 
                                                  ($error_count > 0 ? " Failed to reject $error_count application(s)." : "");
                        } else {
                            mysqli_rollback($conn);
                            $_SESSION['error'] = "Failed to reject any applications.";
                        }
                    } catch (Exception $e) {
                        mysqli_rollback($conn);
                        $_SESSION['error'] = "Error processing bulk rejection: " . $e->getMessage();
                        error_log("Bulk rejection error: " . $e->getMessage());
                    }
                } else {
                    $_SESSION['error'] = "No applications selected for rejection.";
                }
                break;
        }
         // Redirect to prevent form resubmission if no specific redirect happened
        // This redirect should be conditional or handled carefully to avoid infinite loops
        // Given the approve action redirects, this might only be needed for reject or other actions
         // Redirect removed as per user request
        // if (!isset($_SESSION['success']) && !isset($_SESSION['error']) && !headers_sent()) {
         //      error_log("Performing general redirect to self.");
         //     header("Location: " . $_SERVER['PHP_SELF']);
         //     exit();
         // } else if (headers_sent()) {
         //      error_log("Headers already sent, cannot perform general redirect.");
         // }
    }
     else {
          error_log("Action not set in POST request to application_handler.");
     }
}
 else {
      error_log("Request method is not POST in application_handler.");
 }

// Note: The rest of the file (fetching and displaying data) will remain in approve_applications.php

?> 