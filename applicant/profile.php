<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/database.php";

// Robust session check for applicant
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'applicant') {
    echo '<div class="alert alert-danger">Session lost or unauthorized access. Please <a href="../auth/login.php">log in again</a>.</div>';
    exit();
}

$user_id = $_SESSION['user_id'];
$success = $error = "";

// Handle form submission before any output
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['profile_update'])) {
    // Get form data
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
    $address_country = trim($_POST["address_country"]);
    $address_zipcode = trim($_POST["address_zipcode"]);
    $mother_maiden_name = trim($_POST["mother_maiden_name"]);
    $father_name = trim($_POST["father_name"]);
    $elementary_school = trim($_POST["elementary_school"]);
    $elementary_year_graduated = trim($_POST["elementary_year_graduated"]);
    $high_school = trim($_POST["high_school"]);
    $high_school_year_graduated = trim($_POST["high_school_year_graduated"]);
    $primary_program_id = trim($_POST["primary_program_id"]);
    $secondary_program_id = trim($_POST["secondary_program_id"]);

    // Update applicant information
    $sql = "UPDATE applicants SET 
            first_name = ?,
            middle_name = ?,
            last_name = ?,
            birth_date = ?,
            gender = ?,
            mobile_number = ?,
            address_lot = ?,
            address_street = ?,
            address_town = ?,
            address_city = ?,
            address_country = ?,
            address_zipcode = ?,
            mother_maiden_name = ?,
            father_name = ?,
            elementary_school = ?,
            elementary_year_graduated = ?,
            high_school = ?,
            high_school_year_graduated = ?,
            primary_program_id = ?,
            secondary_program_id = ?
            WHERE user_id = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Corrected parameter types: 16 strings, 5 integers
        if (!mysqli_stmt_bind_param($stmt, "ssssssssssssssssiiiii", 
            $first_name, $middle_name, $last_name, $birth_date, $gender,
            $mobile_number, $address_lot, $address_street, $address_town,
            $address_city, $address_country, $address_zipcode,
            $mother_maiden_name, $father_name, $elementary_school,
            $elementary_year_graduated, $high_school, $high_school_year_graduated,
            $primary_program_id, $secondary_program_id, $user_id
        )) {
            $error = "Parameter binding failed: " . mysqli_stmt_error($stmt);
        } else if (mysqli_stmt_execute($stmt)) {
            $success = "Profile updated successfully.";
        } else {
            $error = "Error updating profile: " . mysqli_stmt_error($stmt);
        }
    } else {
        $error = "Statement preparation failed: " . mysqli_error($conn);
    }
}

// Show success message if redirected
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success = "Profile updated successfully.";
}

// Get current applicant information
$applicant_query = "
    SELECT 
        a.*,
        p1.program_name as primary_program,
        p2.program_name as secondary_program
    FROM applicants a
    LEFT JOIN programs p1 ON a.primary_program_id = p1.program_id
    LEFT JOIN programs p2 ON a.secondary_program_id = p2.program_id
    WHERE a.user_id = ?
";

if ($stmt = mysqli_prepare($conn, $applicant_query)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $applicant = mysqli_fetch_assoc($result);
}

// Get all programs for dropdown
$programs_query = "SELECT program_id, program_name FROM programs ORDER BY program_name";
$programs_result = mysqli_query($conn, $programs_query);
$programs = [];
while ($row = mysqli_fetch_assoc($programs_result)) {
    $programs[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Applicant Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Profile Management</h1>
    <script>
    <?php if ($success): ?>
        Swal.fire({icon: 'success', title: 'Success', text: <?php echo json_encode($success); ?>});
    <?php endif; ?>
    <?php if ($error): ?>
        Swal.fire({icon: 'error', title: 'Error', text: <?php echo json_encode($error); ?>});
    <?php endif; ?>
    </script>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Personal Information</h6>
        </div>
        <div class="card-body">
            <form method="post" action="index.php?page=profile">
                <input type="hidden" name="profile_update" value="1">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" class="form-control" 
                                   value="<?php echo $applicant['first_name']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Middle Name</label>
                            <input type="text" name="middle_name" class="form-control" 
                                   value="<?php echo $applicant['middle_name']; ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" class="form-control" 
                                   value="<?php echo $applicant['last_name']; ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Birth Date</label>
                            <input type="date" name="birth_date" class="form-control" 
                                   value="<?php echo ($applicant['birth_date'] && $applicant['birth_date'] !== '0000-00-00') ? $applicant['birth_date'] : ''; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Gender</label>
                            <select name="gender" class="form-control" required>
                                <option value="male" <?php echo $applicant['gender'] == 'male' ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo $applicant['gender'] == 'female' ? 'selected' : ''; ?>>Female</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Mobile Number</label>
                            <input type="text" name="mobile_number" class="form-control" 
                                   value="<?php echo $applicant['mobile_number']; ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Address (Lot/Unit)</label>
                            <input type="text" name="address_lot" class="form-control" 
                                   value="<?php echo $applicant['address_lot']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Street</label>
                            <input type="text" name="address_street" class="form-control" 
                                   value="<?php echo $applicant['address_street']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Town/Barangay</label>
                            <input type="text" name="address_town" class="form-control" 
                                   value="<?php echo $applicant['address_town']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="address_city" class="form-control" 
                                   value="<?php echo $applicant['address_city']; ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" name="address_country" class="form-control" 
                                   value="<?php echo $applicant['address_country']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>ZIP Code</label>
                            <input type="text" name="address_zipcode" class="form-control" 
                                   value="<?php echo $applicant['address_zipcode']; ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Mother's Maiden Name</label>
                            <input type="text" name="mother_maiden_name" class="form-control" 
                                   value="<?php echo $applicant['mother_maiden_name']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Father's Name</label>
                            <input type="text" name="father_name" class="form-control" 
                                   value="<?php echo $applicant['father_name']; ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Elementary School</label>
                            <input type="text" name="elementary_school" class="form-control" 
                                   value="<?php echo $applicant['elementary_school']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Year Graduated (Elementary)</label>
                            <input type="number" name="elementary_year_graduated" class="form-control" 
                                   value="<?php echo $applicant['elementary_year_graduated']; ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>High School</label>
                            <input type="text" name="high_school" class="form-control" 
                                   value="<?php echo $applicant['high_school']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Year Graduated (High School)</label>
                            <input type="number" name="high_school_year_graduated" class="form-control" 
                                   value="<?php echo $applicant['high_school_year_graduated']; ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Primary Program</label>
                            <select name="primary_program_id" class="form-control" required>
                                <option value="">Select Program</option>
                                <?php foreach ($programs as $program): ?>
                                    <option value="<?php echo $program['program_id']; ?>" <?php echo $applicant['primary_program_id'] == $program['program_id'] ? 'selected' : ''; ?>>
                                        <?php echo $program['program_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Secondary Program</label>
                            <select name="secondary_program_id" class="form-control">
                                <option value="">Select Program</option>
                                <?php foreach ($programs as $program): ?>
                                    <option value="<?php echo $program['program_id']; ?>" <?php echo $applicant['secondary_program_id'] == $program['program_id'] ? 'selected' : ''; ?>>
                                        <?php echo $program['program_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
</div>
</body>
</html> 