<?php
// Function to validate username
function validateUsername($username, $conn) {
    if(empty(trim($username))){
        return "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($username))){
        return "Username can only contain letters, numbers, and underscores.";
    } else {
        $sql = "SELECT user_id FROM users WHERE username = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($username);
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    return "This username is already taken.";
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
    return "";
}

// Function to validate password
function validatePassword($password, $confirm_password) {
    if(empty(trim($password))){
        return "Please enter a password.";
    } elseif(strlen(trim($password)) < 6){
        return "Password must have at least 6 characters.";
    } elseif($password !== $confirm_password){
        return "Passwords do not match.";
    }
    return "";
}

// Function to validate email
function validateEmail($email, $conn) {
    if(empty(trim($email))){
        return "Please enter an email.";
    } elseif(!filter_var(trim($email), FILTER_VALIDATE_EMAIL)){
        return "Please enter a valid email address.";
    } else {
        $sql = "SELECT user_id FROM users WHERE email = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = trim($email);
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    return "This email is already registered.";
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
    return "";
}

// Function to insert user data
function insertUserData($conn, $username, $password, $email, $user_type = 'applicant', $status = 'pending') {
    $sql = "INSERT INTO users (username, password, email, user_type, status) VALUES (?, ?, ?, ?, ?)";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "sssss", $param_username, $param_password, $param_email, $param_user_type, $param_status);
        $param_username = $username;
        $param_password = password_hash($password, PASSWORD_DEFAULT);
        $param_email = $email;
        $param_user_type = $user_type;
        $param_status = $status;
        
        if(mysqli_stmt_execute($stmt)){
            return mysqli_insert_id($conn);
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}

// Function to insert personal information
function insertPersonalInfo($conn, $user_id, $data) {
    $sql = "INSERT INTO personal_information (user_id, first_name, middle_name, last_name, birth_date, gender, mobile_number, 
            address_lot, address_street, address_town, address_city, address_zipcode, mother_maiden_name, father_name) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "isssssssssssss", 
            $user_id,
            $data['first_name'],
            $data['middle_name'],
            $data['last_name'],
            $data['birth_date'],
            $data['gender'],
            $data['mobile_number'],
            $data['address_lot'],
            $data['address_street'],
            $data['address_town'],
            $data['address_city'],
            $data['address_zipcode'],
            $data['mother_maiden_name'],
            $data['father_name']
        );
        
        return mysqli_stmt_execute($stmt);
    }
    return false;
}

// Function to insert education information
function insertEducationInfo($conn, $user_id, $data) {
    $sql = "INSERT INTO education_information (user_id, elementary_school, elementary_year_graduated, 
            high_school, high_school_year_graduated) VALUES (?, ?, ?, ?, ?)";
            
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "issss", 
            $user_id,
            $data['elementary_school'],
            $data['elementary_year_graduated'],
            $data['high_school'],
            $data['high_school_year_graduated']
        );
        
        return mysqli_stmt_execute($stmt);
    }
    return false;
}

// Function to insert program choices
function insertProgramChoices($conn, $user_id, $primary_program_id, $secondary_program_id) {
    $sql = "INSERT INTO program_choices (user_id, primary_program_id, secondary_program_id) VALUES (?, ?, ?)";
            
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "iii", $user_id, $primary_program_id, $secondary_program_id);
        return mysqli_stmt_execute($stmt);
    }
    return false;
}
?> 