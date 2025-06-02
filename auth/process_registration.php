<?php
// Start session
session_start();

// Include database connection
require_once "../config/database.php";

// Set response header
header('Content-Type: application/json');

// Initialize response array
$response = array(
    'success' => false,
    'message' => ''
);

// Check if request is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    $email = trim($_POST["email"]);
    $first_name = trim($_POST["first_name"]);
    $middle_name = trim($_POST["middle_name"]);
    $last_name = trim($_POST["last_name"]);
    $birth_date = trim($_POST["birth_date"]);
    $gender = trim($_POST["gender"]);
    $mobile_number = trim($_POST["mobile_number"]);
    $address_lot = trim($_POST["address_lot"]);
    $address_street = trim($_POST["address_street"]);
    $address_town = trim($_POST["address_town"]);
    $address_city = trim($_POST["address_city"]);
    $address_zipcode = trim($_POST["address_zipcode"]);
    $mother_maiden_name = trim($_POST["mother_maiden_name"]);
    $father_name = trim($_POST["father_name"]);
    $elementary_school = trim($_POST["elementary_school"]);
    $elementary_year_graduated = trim($_POST["elementary_year_graduated"]);
    $high_school = trim($_POST["high_school"]);
    $high_school_year_graduated = trim($_POST["high_school_year_graduated"]);
    $primary_program_id = trim($_POST["primary_program_id"]);
    $secondary_program_id = trim($_POST["secondary_program_id"]);

    // Validate input
    $errors = array();

    // Validate username
    if (empty($username)) {
        $errors[] = "Username is required";
    } else {
        // Check if username exists
        $sql = "SELECT user_id FROM users WHERE username = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) > 0) {
                    $errors[] = "Username is already taken";
                }
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Validate password
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }

    // Validate confirm password
    if (empty($confirm_password)) {
        $errors[] = "Please confirm password";
    } elseif ($password != $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    // Validate email
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        // Start transaction
        mysqli_begin_transaction($conn);

        try {
            // Insert into users table
            $sql = "INSERT INTO users (username, password, email, user_type) VALUES (?, ?, ?, 'applicant')";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                mysqli_stmt_bind_param($stmt, "sss", $username, $hashed_password, $email);
                
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Error inserting user data: " . mysqli_error($conn));
                }
                
                $user_id = mysqli_insert_id($conn);
                mysqli_stmt_close($stmt);
            } else {
                throw new Exception("Error preparing user statement: " . mysqli_error($conn));
            }

            // Insert into applicants table
            $sql = "INSERT INTO applicants (user_id, first_name, middle_name, last_name, birth_date, gender, 
                    mobile_number, address_lot, address_street, address_town, address_city, address_zipcode,
                    mother_maiden_name, father_name, elementary_school, elementary_year_graduated,
                    high_school, high_school_year_graduated, primary_program_id, secondary_program_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "isssssssssssssssssii", 
                    $user_id, $first_name, $middle_name, $last_name, $birth_date, $gender,
                    $mobile_number, $address_lot, $address_street, $address_town, $address_city, $address_zipcode,
                    $mother_maiden_name, $father_name, $elementary_school, $elementary_year_graduated,
                    $high_school, $high_school_year_graduated, $primary_program_id, $secondary_program_id);
                
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Error inserting applicant data: " . mysqli_error($conn));
                }
                
                mysqli_stmt_close($stmt);
            } else {
                throw new Exception("Error preparing applicant statement: " . mysqli_error($conn));
            }

            // Commit transaction
            mysqli_commit($conn);

            $response['success'] = true;
            $response['message'] = "Registration successful!";

        } catch (Exception $e) {
            // Rollback transaction on error
            mysqli_rollback($conn);
            $response['message'] = "An error occurred: " . $e->getMessage();
            error_log("Registration error: " . $e->getMessage());
        }

    } else {
        $response['message'] = implode("<br>", $errors);
    }
}

// Close database connection
mysqli_close($conn);

// Return JSON response
echo json_encode($response);
?> 