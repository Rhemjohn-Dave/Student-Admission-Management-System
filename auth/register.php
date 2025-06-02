<?php
// Start session
session_start();

// Include database connection and other required files
require_once "../config/database.php";
require_once "../includes/register_handler.php";
require_once "../includes/get_programs.php";

// Debug: Print POST data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "<!-- Debug POST Data: ";
    print_r($_POST);
    echo " -->";
}

// Check if user is already logged in
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: ../index.php");
    exit;
}

// Initialize variables
$username = $password = $confirm_password = $email = "";
$first_name = $middle_name = $last_name = $birth_date = $gender = $mobile_number = "";
$address_lot = $address_street = $address_town = $address_city = $address_zipcode = "";
$mother_maiden_name = $father_name = "";
$elementary_school = $elementary_year_graduated = "";
$high_school = $high_school_year_graduated = "";
$primary_program_id = $secondary_program_id = "";

$username_err = $password_err = $confirm_password_err = $email_err = "";
$personal_info_err = $education_err = $program_err = "";

// Debug: Print current variables
echo "<!-- Debug Variables: ";
echo "Gender: " . (isset($_POST['gender']) ? $_POST['gender'] : 'not set');
echo " | Primary Program: " . (isset($_POST['primary_program_id']) ? $_POST['primary_program_id'] : 'not set');
echo " | Secondary Program: " . (isset($_POST['secondary_program_id']) ? $_POST['secondary_program_id'] : 'not set');
echo " -->";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{
        $sql = "SELECT user_id FROM users WHERE username = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST["username"]);
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have at least 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }

    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter an email.";
    } else{
        $email = trim($_POST["email"]);
    }

    // Validate personal information
    if(empty(trim($_POST["first_name"])) || empty(trim($_POST["last_name"])) || 
       empty(trim($_POST["birth_date"])) || empty(trim($_POST["gender"])) || 
       empty(trim($_POST["mobile_number"]))){
        $personal_info_err = "Please fill in all required personal information.";
    } else{
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
    }

    // Validate education information
    if(empty(trim($_POST["elementary_school"])) || empty(trim($_POST["elementary_year_graduated"])) || 
       empty(trim($_POST["high_school"])) || empty(trim($_POST["high_school_year_graduated"]))){
        $education_err = "Please fill in all required education information.";
    } else{
        $elementary_school = trim($_POST["elementary_school"]);
        $elementary_year_graduated = trim($_POST["elementary_year_graduated"]);
        $high_school = trim($_POST["high_school"]);
        $high_school_year_graduated = trim($_POST["high_school_year_graduated"]);
    }

    // Validate program choices
    if(empty(trim($_POST["primary_program_id"])) || empty(trim($_POST["secondary_program_id"]))){
        $program_err = "Please select both primary and secondary program choices.";
    } else{
        $primary_program_id = trim($_POST["primary_program_id"]);
        $secondary_program_id = trim($_POST["secondary_program_id"]);
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && 
       empty($email_err) && empty($personal_info_err) && empty($education_err) && empty($program_err)){
        
        // Start transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Insert into users table
            $sql = "INSERT INTO users (username, password, email, user_type, status) VALUES (?, ?, ?, 'applicant', 'pending')";
            if($stmt = mysqli_prepare($conn, $sql)){
                mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_password, $param_email);
                $param_username = $username;
                $param_password = password_hash($password, PASSWORD_DEFAULT);
                $param_email = $email;
                
                if(mysqli_stmt_execute($stmt)){
                    $user_id = mysqli_insert_id($conn);
                    
                    // Insert into applicants table
                    $sql = "INSERT INTO applicants (user_id, first_name, middle_name, last_name, birth_date, gender, 
                            mobile_number, address_lot, address_street, address_town, address_city, address_zipcode,
                            mother_maiden_name, father_name, elementary_school, elementary_year_graduated,
                            high_school, high_school_year_graduated, primary_program_id, secondary_program_id) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    
                    if($stmt = mysqli_prepare($conn, $sql)){
                        mysqli_stmt_bind_param($stmt, "isssssssssssssssssii", 
                            $user_id, $first_name, $middle_name, $last_name, $birth_date, $gender,
                            $mobile_number, $address_lot, $address_street, $address_town, $address_city, $address_zipcode,
                            $mother_maiden_name, $father_name, $elementary_school, $elementary_year_graduated,
                            $high_school, $high_school_year_graduated, $primary_program_id, $secondary_program_id);
                        
                        if(mysqli_stmt_execute($stmt)){
                            mysqli_commit($conn);
                            header("location: login.php");
                            exit;
                        }
                    }
                }
            }
            throw new Exception("Error in database operation");
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "Something went wrong. Please try again later.";
        }
    }
}

// Get programs for dropdown
$programs = getActivePrograms($conn);

// Debug: Print programs array
echo "<!-- Debug Programs: ";
print_r($programs);
echo " -->";

// Set page title
$page_title = "Register - Student Admissions Management System";

// Include auth header
include '../includes/components/auth_header.php';
?>

<!-- Site Header -->
<header class="site-header bg-white border-bottom">
    <div class="container d-flex align-items-center justify-content-center py-2">
        <a class="navbar-brand d-flex align-items-center m-0 p-0" href="../index.php">
            <img src="../assets/images/tuplogo.png" alt="TUP Visayas Logo" height="60" class="mr-3">
            <span class="font-weight-bold text-gray-800">Student Admissions Management System</span>
        </a>
    </div>
</header>


<!-- Main Content -->
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-9">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h2 class="h5 text-gray-900">Create an Account</h2>
                        <p class="text-muted small">Please fill out all required information</p>
                    </div>
                    
                    <?php 
                    if(!empty($username_err) || !empty($password_err) || !empty($confirm_password_err) || 
                       !empty($email_err) || !empty($personal_info_err) || !empty($education_err) || 
                       !empty($program_err)){
                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle mr-2"></i> Please check the form for errors.
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                              </div>';
                    }        
                    ?>

                    <form class="user" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="registrationForm">
                        <!-- Navigation Tabs -->
                        <ul class="nav nav-tabs nav-justified mb-4" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="account-tab" data-toggle="tab" href="#account" role="tab">
                                    <i class="fas fa-user-circle fa-fw"></i> Account
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="personal-tab" data-toggle="tab" href="#personal" role="tab">
                                    <i class="fas fa-user fa-fw"></i> Personal
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="education-tab" data-toggle="tab" href="#education" role="tab">
                                    <i class="fas fa-graduation-cap fa-fw"></i> Education
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="program-tab" data-toggle="tab" href="#program" role="tab">
                                    <i class="fas fa-book fa-fw"></i> Program
                                </a>
                            </li>
                        </ul>
                        
                        <!-- Tab Content -->
                        <div class="tab-content" id="registrationTabContent">
                            <!-- Account Information Tab -->
                            <div class="tab-pane fade show active" id="account" role="tabpanel">
                                        <div class="form-group">
                                            <label for="username" class="small text-gray-600">Username</label>
                                            <input type="text" id="username" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" 
                                                value="<?php echo $username; ?>" placeholder="Enter your username" autocomplete="username">
                                                        <span class="invalid-feedback"><?php echo $username_err; ?></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="email" class="small text-gray-600">Email Address</label>
                                            <input type="email" id="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" 
                                                value="<?php echo $email; ?>" placeholder="Enter your email address" autocomplete="email">
                                                        <span class="invalid-feedback"><?php echo $email_err; ?></span>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6 mb-3 mb-sm-0">
                                                <label for="password" class="small text-gray-600">Password</label>
                                                <input type="password" id="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" 
                                                    placeholder="Enter your password" autocomplete="new-password">
                                                        <span class="invalid-feedback"><?php echo $password_err; ?></span>
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="confirm_password" class="small text-gray-600">Confirm Password</label>
                                                <input type="password" id="confirm_password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" 
                                                    placeholder="Confirm your password" autocomplete="new-password">
                                                        <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                                            </div>
                                        </div>
                                <div class="text-right mt-4">
                                                            <a href="#personal" class="btn btn-primary btn-user next-tab" data-toggle="tab" data-target="#personal-tab">
                                                                Next <i class="fas fa-arrow-right ml-1"></i>
                                                            </a>
                                                        </div>
                            </div>
                            
                            <!-- Personal Information Tab -->
                            <div class="tab-pane fade" id="personal" role="tabpanel">
                                        <div class="form-group row">
                                            <div class="col-sm-4 mb-3 mb-sm-0">
                                                <label for="first_name" class="small text-gray-600">First Name</label>
                                                <input type="text" id="first_name" name="first_name" class="form-control" 
                                                    value="<?php echo $first_name; ?>" placeholder="Enter first name" autocomplete="given-name">
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="middle_name" class="small text-gray-600">Middle Name</label>
                                                <input type="text" id="middle_name" name="middle_name" class="form-control" 
                                                    value="<?php echo $middle_name; ?>" placeholder="Enter middle name" autocomplete="additional-name">
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="last_name" class="small text-gray-600">Last Name</label>
                                                <input type="text" id="last_name" name="last_name" class="form-control" 
                                                    value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>" 
                                                    placeholder="Enter last name" autocomplete="family-name" required>
                                                <div class="invalid-feedback">Please enter your last name</div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6 mb-3 mb-sm-0">
                                                <label for="birth_date" class="small text-gray-600">Birth Date</label>
                                                <input type="date" id="birth_date" name="birth_date" class="form-control" 
                                                    value="<?php echo isset($_POST['birth_date']) ? htmlspecialchars($_POST['birth_date']) : ''; ?>" autocomplete="bday">
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="gender" class="small text-gray-600">Gender</label>
                                                <select id="gender" name="gender" class="form-control" required autocomplete="sex">
                                                    <option value="" disabled <?php echo empty($_POST['gender']) ? 'selected' : ''; ?>>Select Gender</option>
                                                                                    <option value="male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                                                                                    <option value="female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                                                                                    <option value="other" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'other') ? 'selected' : ''; ?>>Other</option>
                                                                                </select>
                                                <div class="invalid-feedback">Please select your gender</div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="mobile_number" class="small text-gray-600">Mobile Number</label>
                                            <input type="text" id="mobile_number" name="mobile_number" class="form-control" 
                                                value="<?php echo $mobile_number; ?>" placeholder="Enter mobile number" autocomplete="tel">
                                        </div>
                                        <div class="form-group">
                                            <label for="address_lot" class="small text-gray-600">Lot/Unit Number</label>
                                            <input type="text" id="address_lot" name="address_lot" class="form-control" 
                                                value="<?php echo $address_lot; ?>" placeholder="Enter lot/unit number" autocomplete="address-line1">
                                        </div>
                                        <div class="form-group">
                                            <label for="address_street" class="small text-gray-600">Street Name</label>
                                            <input type="text" id="address_street" name="address_street" class="form-control" 
                                                value="<?php echo $address_street; ?>" placeholder="Enter street name" autocomplete="address-line2">
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6 mb-3 mb-sm-0">
                                                <label for="address_town" class="small text-gray-600">Town/Barangay</label>
                                                <input type="text" id="address_town" name="address_town" class="form-control" 
                                                    value="<?php echo $address_town; ?>" placeholder="Enter town/barangay" autocomplete="address-level3">
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="address_city" class="small text-gray-600">City</label>
                                                <input type="text" id="address_city" name="address_city" class="form-control" 
                                                    value="<?php echo $address_city; ?>" placeholder="Enter city" autocomplete="address-level2">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="address_zipcode" class="small text-gray-600">ZIP Code</label>
                                            <input type="text" id="address_zipcode" name="address_zipcode" class="form-control" 
                                                value="<?php echo $address_zipcode; ?>" placeholder="Enter ZIP code" autocomplete="postal-code">
                                        </div>
                                        <div class="form-group">
                                            <label for="mother_maiden_name" class="small text-gray-600">Mother's Maiden Name</label>
                                            <input type="text" id="mother_maiden_name" name="mother_maiden_name" class="form-control" 
                                                value="<?php echo $mother_maiden_name; ?>" placeholder="Enter mother's maiden name" autocomplete="additional-name">
                                        </div>
                                        <div class="form-group">
                                            <label for="father_name" class="small text-gray-600">Father's Name</label>
                                            <input type="text" id="father_name" name="father_name" class="form-control" 
                                                value="<?php echo $father_name; ?>" placeholder="Enter father's name" autocomplete="additional-name">
                                        </div>
                                <div class="text-right mt-4">
                                                            <a href="#account" class="btn btn-secondary btn-user mr-2" data-toggle="tab" data-target="#account-tab">
                                                                <i class="fas fa-arrow-left mr-1"></i> Previous
                                                            </a>
                                                            <a href="#education" class="btn btn-primary btn-user next-tab" data-toggle="tab" data-target="#education-tab">
                                                                Next <i class="fas fa-arrow-right ml-1"></i>
                                                            </a>
                                                        </div>
                            </div>
                            
                            <!-- Education Information Tab -->
                            <div class="tab-pane fade" id="education" role="tabpanel">
                                        <div class="form-group">
                                            <label for="elementary_school" class="small text-gray-600">Elementary School</label>
                                            <input type="text" id="elementary_school" name="elementary_school" class="form-control" 
                                                value="<?php echo $elementary_school; ?>" placeholder="Enter elementary school name" autocomplete="organization">
                                        </div>
                                        <div class="form-group">
                                            <label for="elementary_year_graduated" class="small text-gray-600">Elementary Year Graduated</label>
                                            <input type="text" id="elementary_year_graduated" name="elementary_year_graduated" class="form-control" 
                                                value="<?php echo $elementary_year_graduated; ?>" placeholder="Enter year graduated" autocomplete="off">
                                        </div>
                                        <div class="form-group">
                                            <label for="high_school" class="small text-gray-600">High School</label>
                                            <input type="text" id="high_school" name="high_school" class="form-control" 
                                                value="<?php echo $high_school; ?>" placeholder="Enter high school name" autocomplete="organization">
                                        </div>
                                        <div class="form-group">
                                            <label for="high_school_year_graduated" class="small text-gray-600">High School Year Graduated</label>
                                            <input type="text" id="high_school_year_graduated" name="high_school_year_graduated" class="form-control" 
                                                value="<?php echo $high_school_year_graduated; ?>" placeholder="Enter year graduated" autocomplete="off">
                                        </div>
                                <div class="text-right mt-4">
                                                            <a href="#personal" class="btn btn-secondary btn-user mr-2" data-toggle="tab" data-target="#personal-tab">
                                                                <i class="fas fa-arrow-left mr-1"></i> Previous
                                                            </a>
                                                            <a href="#program" class="btn btn-primary btn-user next-tab" data-toggle="tab" data-target="#program-tab">
                                                                Next <i class="fas fa-arrow-right ml-1"></i>
                                                            </a>
                                                        </div>
                            </div>
                            
                            <!-- Program Choices Tab -->
                            <div class="tab-pane fade" id="program" role="tabpanel">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle mr-2"></i> Please select your primary and secondary program choices
                                        </div>
                                        <div class="form-group">
                                            <label for="primary_program_id" class="small text-gray-600">Primary Program Choice</label>
                                            <select id="primary_program_id" name="primary_program_id" class="form-control" required autocomplete="off">
                                                <option value="" disabled <?php echo empty($_POST['primary_program_id']) ? 'selected' : ''; ?>>Select Primary Program</option>
                                                <?php 
                                                if(isset($programs) && is_array($programs)) {
                                                    foreach($programs as $program): 
                                                ?>
                                                            <option value="<?php echo $program['program_id']; ?>" 
                                                                <?php echo (isset($_POST['primary_program_id']) && $_POST['primary_program_id'] == $program['program_id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($program['program_name']); ?>
                                                            </option>
                                                <?php 
                                                    endforeach;
                                                }
                                                ?>
                                            </select>
                                            <div class="invalid-feedback">Please select your primary program</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="secondary_program_id" class="small text-gray-600">Secondary Program Choice</label>
                                            <select id="secondary_program_id" name="secondary_program_id" class="form-control" required autocomplete="off">
                                                <option value="" disabled <?php echo empty($_POST['secondary_program_id']) ? 'selected' : ''; ?>>Select Secondary Program</option>
                                                <?php 
                                                if(isset($programs) && is_array($programs)) {
                                                    foreach($programs as $program): 
                                                ?>
                                                            <option value="<?php echo $program['program_id']; ?>" 
                                                                <?php echo (isset($_POST['secondary_program_id']) && $_POST['secondary_program_id'] == $program['program_id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($program['program_name']); ?>
                                                            </option>
                                                <?php 
                                                    endforeach;
                                                }
                                                ?>
                                            </select>
                                            <div class="invalid-feedback">Please select your secondary program</div>
                                        </div>
                                <div class="text-right mt-4">
                                                            <a href="#education" class="btn btn-secondary btn-user mr-2" data-toggle="tab" data-target="#education-tab">
                                                                <i class="fas fa-arrow-left mr-1"></i> Previous
                                                            </a>
                                                            <button type="submit" class="btn btn-success btn-user">
                                                                <i class="fas fa-check mr-1"></i> Submit Registration
                                                            </button>
                                                        </div>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <div class="text-center">
                        <a class="small" href="forgot-password.php">Forgot Password?</a>
                    </div>
                    <div class="text-center">
                        <a class="small" href="login.php">Already have an account? Login!</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to validate select elements
    function validateSelect(select) {
        if (!select || select.value === '') {
            if (select) {
                select.classList.add('is-invalid');
                const feedback = select.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.textContent = 'Please select a value';
                }
            }
            return false;
        } else {
            select.classList.remove('is-invalid');
            return true;
        }
    }

    // Function to validate input fields
    function validateInput(input) {
        if (!input || !input.value.trim()) {
            if (input) {
                input.classList.add('is-invalid');
                const feedback = input.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.textContent = 'This field is required';
                }
            }
            return false;
        } else {
            input.classList.remove('is-invalid');
            return true;
        }
    }

    // Function to validate email format
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Function to validate password strength
    function validatePassword(password) {
        return password.length >= 6;
    }

    // Function to show error message
    function showError(field, message) {
        field.classList.add('is-invalid');
        const feedback = field.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.textContent = message;
        }
    }

    // Function to validate current tab
    function validateCurrentTab(tabId) {
        const currentTab = document.querySelector(`#${tabId}`);
        if (!currentTab) return true;

        let isValid = true;
        let firstInvalidInput = null;
        let errorMessages = [];

        // Account Tab Validation
        if (tabId === 'account') {
            const username = currentTab.querySelector('input[name="username"]');
            const email = currentTab.querySelector('input[name="email"]');
            const password = currentTab.querySelector('input[name="password"]');
            const confirmPassword = currentTab.querySelector('input[name="confirm_password"]');

            // Validate username
            if (!validateInput(username)) {
                isValid = false;
                if (!firstInvalidInput) firstInvalidInput = username;
                errorMessages.push('Username is required');
            }

            // Validate email
            if (!validateInput(email)) {
                isValid = false;
                if (!firstInvalidInput) firstInvalidInput = email;
                errorMessages.push('Email is required');
            } else if (!validateEmail(email.value)) {
                isValid = false;
                if (!firstInvalidInput) firstInvalidInput = email;
                showError(email, 'Please enter a valid email address');
                errorMessages.push('Please enter a valid email address');
            }

            // Validate password
            if (!validateInput(password)) {
                isValid = false;
                if (!firstInvalidInput) firstInvalidInput = password;
                errorMessages.push('Password is required');
            } else if (!validatePassword(password.value)) {
                isValid = false;
                if (!firstInvalidInput) firstInvalidInput = password;
                showError(password, 'Password must be at least 6 characters long');
                errorMessages.push('Password must be at least 6 characters long');
            }

            // Validate password match
            if (!validateInput(confirmPassword)) {
                isValid = false;
                if (!firstInvalidInput) firstInvalidInput = confirmPassword;
                errorMessages.push('Please confirm your password');
            } else if (password.value !== confirmPassword.value) {
                isValid = false;
                if (!firstInvalidInput) firstInvalidInput = confirmPassword;
                showError(confirmPassword, 'Passwords do not match');
                errorMessages.push('Passwords do not match');
            }
        }

        // Personal Information Tab Validation
        else if (tabId === 'personal') {
            const requiredFields = currentTab.querySelectorAll('input[required], select[required]');
            requiredFields.forEach(field => {
                if (field.tagName === 'SELECT') {
                    if (!validateSelect(field)) {
                        isValid = false;
                        if (!firstInvalidInput) firstInvalidInput = field;
                        errorMessages.push(`${field.previousElementSibling.textContent.trim()} is required`);
                    }
                } else {
                    if (!validateInput(field)) {
                        isValid = false;
                        if (!firstInvalidInput) firstInvalidInput = field;
                        errorMessages.push(`${field.previousElementSibling.textContent.trim()} is required`);
                    }
                }
            });
        }

        // Education Information Tab Validation
        else if (tabId === 'education') {
            const requiredFields = currentTab.querySelectorAll('input[required]');
            requiredFields.forEach(field => {
                if (!validateInput(field)) {
                    isValid = false;
                    if (!firstInvalidInput) firstInvalidInput = field;
                    errorMessages.push(`${field.previousElementSibling.textContent.trim()} is required`);
                }
            });
        }

        // Program Choices Tab Validation
        else if (tabId === 'program') {
            const primaryProgram = currentTab.querySelector('select[name="primary_program_id"]');
            const secondaryProgram = currentTab.querySelector('select[name="secondary_program_id"]');

            if (!validateSelect(primaryProgram)) {
                isValid = false;
                if (!firstInvalidInput) firstInvalidInput = primaryProgram;
                errorMessages.push('Please select your primary program');
            }

            if (!validateSelect(secondaryProgram)) {
                isValid = false;
                if (!firstInvalidInput) firstInvalidInput = secondaryProgram;
                errorMessages.push('Please select your secondary program');
            }

            // Check if both programs are the same
            if (primaryProgram.value === secondaryProgram.value && primaryProgram.value !== '') {
                isValid = false;
                if (!firstInvalidInput) firstInvalidInput = secondaryProgram;
                showError(secondaryProgram, 'Primary and secondary programs cannot be the same');
                errorMessages.push('Primary and secondary programs cannot be the same');
            }
        }

        if (!isValid && firstInvalidInput) {
            firstInvalidInput.focus();
            Swal.fire({
                title: 'Validation Error',
                html: errorMessages.map(msg => `<div class="text-left">• ${msg}</div>`).join(''),
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }

        return isValid;
    }

    // Real-time validation for username
    const usernameInput = document.querySelector('input[name="username"]');
    if (usernameInput) {
        usernameInput.addEventListener('blur', function() {
            if (this.value.trim()) {
                // Check username availability via AJAX
                fetch('check_username.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'username=' + encodeURIComponent(this.value)
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.available) {
                        showError(this, 'Username is already taken');
                    } else {
                        this.classList.remove('is-invalid');
                    }
                });
            }
        });
    }

    // Real-time validation for email
    const emailInput = document.querySelector('input[name="email"]');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            if (this.value.trim() && !validateEmail(this.value)) {
                showError(this, 'Please enter a valid email address');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    }

    // Real-time validation for password match
    const passwordInput = document.querySelector('input[name="password"]');
    const confirmPasswordInput = document.querySelector('input[name="confirm_password"]');
    
    if (passwordInput && confirmPasswordInput) {
        const validatePasswordMatch = () => {
            if (passwordInput.value && confirmPasswordInput.value) {
                if (passwordInput.value.length < 6) {
                    showError(passwordInput, 'Password must be at least 6 characters long');
                } else if (passwordInput.value !== confirmPasswordInput.value) {
                    showError(confirmPasswordInput, 'Passwords do not match');
                } else {
                    passwordInput.classList.remove('is-invalid');
                    confirmPasswordInput.classList.remove('is-invalid');
                }
            }
        };

        passwordInput.addEventListener('input', validatePasswordMatch);
        confirmPasswordInput.addEventListener('input', validatePasswordMatch);
    }

    // Handle next button clicks
    const nextButtons = document.querySelectorAll('.next-tab');
    nextButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const currentTab = this.closest('.tab-pane');
            if (!currentTab) return;

            const currentTabId = currentTab.id;
            const nextTabId = this.getAttribute('href').substring(1);

            if (validateCurrentTab(currentTabId)) {
                document.querySelector(`#${currentTabId}-tab`).classList.remove('active');
                currentTab.classList.remove('show', 'active');

                const nextTab = document.querySelector(`#${nextTabId}`);
                const nextTabLink = document.querySelector(`#${nextTabId}-tab`);
                
                if (nextTab && nextTabLink) {
                    nextTabLink.classList.add('active');
                    nextTab.classList.add('show', 'active');
                }
            }
        });
    });

    // Handle previous button clicks
    const prevButtons = document.querySelectorAll('.prev-tab');
    prevButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const currentTab = this.closest('.tab-pane');
            if (!currentTab) return;

            const currentTabId = currentTab.id;
            const prevTabId = this.getAttribute('href').substring(1);

            document.querySelector(`#${currentTabId}-tab`).classList.remove('active');
            currentTab.classList.remove('show', 'active');

            const prevTab = document.querySelector(`#${prevTabId}`);
            const prevTabLink = document.querySelector(`#${prevTabId}-tab`);
            
            if (prevTab && prevTabLink) {
                prevTabLink.classList.add('active');
                prevTab.classList.add('show', 'active');
            }
        });
    });

    // Handle form submission
    const registrationForm = document.getElementById('registrationForm');
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate all tabs before submission
            const tabs = ['account', 'personal', 'education', 'program'];
            let isValid = true;
            let errorMessages = [];

            for (const tab of tabs) {
                if (!validateCurrentTab(tab)) {
                    isValid = false;
                    break;
                }
            }

            if (isValid) {
                // Submit the form via AJAX
                const formData = new FormData(this);
                
                fetch('process_registration.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        Swal.fire({
                            title: 'Registration Successful!',
                            text: 'Your account has been created successfully. You will be redirected to the login page.',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 3000
                        }).then(() => {
                            // Redirect to login page
                            window.location.href = 'login.php';
                        });
                    } else {
                        // Show error message
                        Swal.fire({
                            title: 'Registration Failed',
                            text: data.message || 'An error occurred during registration. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred while processing your registration. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            }
        });
    }
});
</script>

<?php include '../includes/components/auth_footer.php'; ?>
</body>
</html>