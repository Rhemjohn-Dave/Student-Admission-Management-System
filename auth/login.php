<?php
session_start();
require_once "../config/database.php";

// Check if already logged in
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    // Redirect based on user type
    if($_SESSION["user_type"] === "admin"){
        header("location: ../admin/index.php");
    } else if($_SESSION["user_type"] === "interviewer") {
        header("location: ../interviewer/index.php");
    } else if($_SESSION["user_type"] === "applicant") {
        header("location: ../applicant/index.php");
    } else {
        header("location: ../index.php");
    }
    exit;
}

$username = $password = "";
$username_err = $password_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        $sql = "SELECT user_id, username, password, user_type, status FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $user_type, $status);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            if($status === 'active'){
                                session_start();
                                
                                $_SESSION["loggedin"] = true;
                                $_SESSION["user_id"] = $id;
                                $_SESSION["username"] = $username;
                                $_SESSION["user_type"] = $user_type;
                                $_SESSION["status"] = $status;
                                
                                // Redirect based on user type
                                if($user_type === "admin"){
                                    header("location: ../admin/index.php");
                                } else if($user_type === "applicant") {
                                    header("location: ../applicant/index.php");
                                } else if($user_type === "interviewer") {
                                    header("location: ../interviewer/index.php");
                                } else {
                                    header("location: ../index.php");
                                }
                                exit;
                            } else {
                                $login_err = "Your account is not yet activated. Please wait for admin approval.";
                            }
                        } else {
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else {
                    $login_err = "Invalid username or password.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Set page title for header
$page_title = "Login - Student Admissions Management System";

// Include auth header
include '../includes/components/auth_header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-9">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-login-image">
                            <div class="d-flex justify-content-center align-items-center h-100">
                                <img src="../assets/images/tuplogo.png" alt="TUP Visayas Logo" style="max-width: 80%; height: auto;">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h5 text-gray-900 mb-4 ">Technological University of the Philippines Visayas</h1>
                                    <h2 class="h3 text-gray-900 mb-4 font-weight-bold">STUDENT ADMISSIONS MANAGEMENT SYSTEM</h2>
                                    <p class="mb-4">Please login to your account.</p>
                                </div>
                                
                                <?php 
                                if(!empty($login_err)){
                                    echo '<div class="alert alert-danger">' . $login_err . '</div>';
                                }        
                                ?>

                                <form class="user" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                    <div class="form-group">
                                        <input type="text" name="username" class="form-control form-control-user <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" 
                                            value="<?php echo $username; ?>" placeholder="Enter Username...">
                                        <span class="invalid-feedback"><?php echo $username_err; ?></span>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="password" class="form-control form-control-user <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" 
                                            placeholder="Password">
                                        <span class="invalid-feedback"><?php echo $password_err; ?></span>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox small">
                                            <input type="checkbox" class="custom-control-input" id="customCheck">
                                            <label class="custom-control-label" for="customCheck">Remember Me</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        Login
                                    </button>
                                </form>
                                <hr>
                                <div class="text-center">
                                    <a class="small" href="forgot-password.php">Forgot Password?</a>
                                </div>
                                <div class="text-center">
                                    <a class="small" href="register.php">Create an Account!</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/components/auth_footer.php'; ?> 