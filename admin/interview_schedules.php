<?php
// Check if user is logged in and is an admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_type"] !== "admin"){
    header("location: ../auth/login.php");
    exit;
}

// Include database connection
require_once "../config/database.php";

// Verify database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debug: Print POST data
    error_log("POST data: " . print_r($_POST, true));
    
    $date = $_POST["date"];
    $time_window = $_POST["time_window"];
    $max_applicants = $_POST["max_applicants"];
    $program_id = $_POST["program_id"];
    
    // Debug: Print variables
    error_log("Variables: date=$date, time_window=$time_window, max_applicants=$max_applicants, program_id=$program_id");
    
    // Get the program head's user_id for the selected program
    $sql = "SELECT ph.user_id 
            FROM program_heads ph 
            WHERE ph.program_id = ?";
            
    // Debug: Print SQL query
    error_log("Program head query: " . $sql);
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $program_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $program_head = mysqli_fetch_assoc($result);
        
        if ($program_head === null) {
            echo "<script>alert('No program head found for the selected program.'); window.location.href='interview_schedules.php';</script>";
            exit;
        }
        
        $interviewer_id = $program_head['user_id'];
        
        // Debug: Print interviewer_id
        error_log("Interviewer ID: " . $interviewer_id);
        
        // Insert the new schedule
        $sql = "INSERT INTO interview_schedules (interview_date, time_window, max_applicants, created_by, program_id, status) VALUES (?, ?, ?, ?, ?, 'open')";
        
        // Debug: Print SQL and parameters
        error_log("Insert SQL: " . $sql);
        error_log("Parameters: date=$date, time_window=$time_window, max_applicants=$max_applicants, created_by=" . $_SESSION['user_id'] . ", program_id=$program_id");
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            if (!mysqli_stmt_bind_param($stmt, "ssiii", $date, $time_window, $max_applicants, $_SESSION['user_id'], $program_id)) {
                echo "<script>alert('Error binding parameters: " . mysqli_stmt_error($stmt) . "');</script>";
                exit;
            }
            
            if(!mysqli_stmt_execute($stmt)) {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Error executing statement: " . mysqli_stmt_error($stmt) . "'
                    });
                </script>";
                exit;
            }
            
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Schedule added successfully!',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = 'index.php?page=interview_schedules';
                });
            </script>";
        } else {
            $error = mysqli_error($conn);
            error_log("Database error: " . $error);
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Error preparing statement: " . $error . "'
                });
            </script>";
        }
    } else {
        $error = mysqli_error($conn);
        error_log("Database error: " . $error);
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Error preparing program head query: " . $error . "'
            });
        </script>";
    }
}

// Fetch programs with their program heads
$sql = "SELECT p.program_id, p.program_name, 
               uh.user_id as program_head_id, uh.first_name as program_head_first_name, uh.last_name as program_head_last_name
        FROM programs p
        JOIN program_heads ph ON p.program_id = ph.program_id
        JOIN users uh ON ph.user_id = uh.user_id
        WHERE uh.status = 'active'
        ORDER BY p.program_name";
$programs = mysqli_query($conn, $sql);

// Fetch interview schedules with program and program head info
$sql = "SELECT s.*, p.program_name, 
               uh.first_name as program_head_first_name, uh.last_name as program_head_last_name
        FROM interview_schedules s
        JOIN programs p ON s.program_id = p.program_id
        JOIN program_heads ph ON p.program_id = ph.program_id
        JOIN users uh ON ph.user_id = uh.user_id
        ORDER BY s.interview_date DESC";
$interview_schedules = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Interview Schedules</title>
    <!-- Add SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <!-- Add SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    
    <script>
    // Define functions globally
    function showSuccessMessage() {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Schedule added successfully!',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            window.location.href = 'interview_schedules.php';
        });
    }

    function showErrorMessage(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: message
        });
    }

    function validateForm() {
        var date = document.getElementById('date').value;
        var timeWindow = document.getElementById('time_window').value;
        var programId = document.getElementById('program_id').value;
        var maxApplicants = document.getElementById('max_applicants').value;
        
        if (!date || !timeWindow || !programId || !maxApplicants) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please fill in all required fields.'
            });
            return false;
        }
        
        if (maxApplicants < 1) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Maximum applicants must be at least 1.'
            });
            return false;
        }
        
        return true;
    }
    </script>
</head>
<body>
<!-- Main Content -->
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Interview Schedules</h1>
    </div>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Add New Schedule -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Add New Schedule</h6>
        </div>
        <div class="card-body">
            <form method="post" class="needs-validation" novalidate onsubmit="return validateForm()">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="date">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                        <div class="invalid-feedback">
                            Please select a date.
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="time_window">Time Window</label>
                        <select class="form-control" id="time_window" name="time_window" required>
                            <option value="">Select time window</option>
                            <option value="AM">Morning (AM)</option>
                            <option value="PM">Afternoon (PM)</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a time window.
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="program_id">Program</label>
                        <select class="form-control" id="program_id" name="program_id" required>
                            <option value="">Select program</option>
                            <?php 
                            // Reset the programs result pointer
                            mysqli_data_seek($programs, 0);
                            while($row = mysqli_fetch_assoc($programs)): 
                            ?>
                                <option value="<?php echo $row['program_id']; ?>">
                                    <?php echo htmlspecialchars($row['program_name'] . ' - ' . $row['program_head_first_name'] . ' ' . $row['program_head_last_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <div class="invalid-feedback">
                            Please select a program.
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="max_applicants">Maximum Applicants</label>
                        <input type="number" class="form-control" id="max_applicants" name="max_applicants" min="1" required>
                        <div class="invalid-feedback">
                            Please enter the maximum number of applicants.
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">Add Schedule</button>
            </form>
        </div>
    </div>

    <!-- Interview Schedules List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Interview Schedules</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="interviewSchedulesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time Window</th>
                            <th>Program</th>
                            <th>Program Head (Interviewer)</th>
                            <th>Maximum Applicants</th>
                            <th>Current Applicants</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($interview_schedules)): ?>
                        <tr>
                            <td><?php echo date('F j, Y', strtotime($row['interview_date'])); ?></td>
                            <td><?php echo $row['time_window']; ?></td>
                            <td><?php echo htmlspecialchars($row['program_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['program_head_first_name'] . ' ' . $row['program_head_last_name']); ?></td>
                            <td><?php echo $row['max_applicants']; ?></td>
                            <td><?php echo $row['current_applicants']; ?></td>
                            <td>
                                <?php if($row['status'] == 'open'): ?>
                                    <span class="badge bg-success">Open</span>
                                <?php elseif($row['status'] == 'closed'): ?>
                                    <span class="badge bg-warning">Closed</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Completed</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm" onclick="viewApplicants(<?php echo $row['schedule_id']; ?>)">
                                    <i class="fas fa-eye"></i> View Applicants
                                </button>
                                <?php if($row['status'] == 'open'): ?>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="closeSchedule(<?php echo $row['schedule_id']; ?>)">
                                        <i class="fas fa-times"></i> Close
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Applicants Modal -->
<div class="modal fade" id="applicantsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registered Applicants</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table" id="applicantsTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Program</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Applicants will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// View applicants for a schedule
function viewApplicants(scheduleId) {
    // Show loading state
    Swal.fire({
        title: 'Loading...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: 'get_interview_applicants.php',
        method: 'POST',
        data: { schedule_id: scheduleId },
        success: function(response) {
            Swal.close();
            $('#applicantsTable tbody').html(response);
            $('#applicantsModal').modal('show');
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to load applicants. Please try again.'
            });
        }
    });
}

// Close a schedule
function closeSchedule(scheduleId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will close the interview schedule. This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, close it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Processing...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: 'close_interview_schedule.php',
                method: 'POST',
                data: { schedule_id: scheduleId },
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Schedule has been closed successfully.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: result.message || 'Failed to close schedule.'
                            });
                        }
                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An unexpected error occurred.'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to close schedule. Please try again.'
                    });
                }
            });
        }
    });
}

// Initialize DataTables
$(document).ready(function() {
    $('#interviewSchedulesTable').DataTable();
    $('#applicantsTable').DataTable();
});
</script>
</body>
</html> 