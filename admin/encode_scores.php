<?php
// Only start session if it hasn't been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_type"] !== "admin"){
    // For AJAX requests, return JSON error
    if (isset($_POST['update_single_score'])) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
        exit;
    }
    // For regular requests, redirect
    header("location: ../auth/login.php");
    exit;
}

// Include database connection
require_once "../config/database.php";

// Set page title
$page_title = "Encode Exam Scores - Student Admissions Management System";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['test_ajax'])) {
        // Simple test endpoint
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'AJAX is working']);
        exit;
    } elseif (isset($_POST['update_single_score'])) {
        // Debug: Log the request
        error_log("Single score update request received: " . print_r($_POST, true));
        
        $applicant_id = trim($_POST['applicant_id']);
        $score = trim($_POST['score']);
        
        try {
            // Validate inputs
            if (empty($applicant_id) || !is_numeric($score) || $score < 0 || $score > 100) {
                throw new Exception("Invalid score. Please enter a number between 0 and 100.");
            }

            // Debug: Log the values
            error_log("Processing score update - Applicant ID: $applicant_id, Score: $score");

            // Get applicant details
            $user_query = "SELECT user_id, first_name, last_name FROM applicants WHERE applicant_id = ?";
            $user_stmt = $conn->prepare($user_query);
            if (!$user_stmt) {
                throw new Exception("Error preparing user query: " . $conn->error);
            }
            
            $user_stmt->bind_param("i", $applicant_id);
            if (!$user_stmt->execute()) {
                throw new Exception("Error executing user query: " . $user_stmt->error);
            }
            
            $user_result = $user_stmt->get_result();
            if (!$user = $user_result->fetch_assoc()) {
                throw new Exception("Applicant not found");
            }
            
            $user_id = $user['user_id'];
            $applicant_name = $user['first_name'] . ' ' . $user['last_name'];
            
            // Get the exam registration for this applicant
            $reg_query = "SELECT reg.registration_id, reg.exam_schedule_id, es.exam_id
                         FROM exam_registrations reg 
                         JOIN exam_schedules es ON reg.exam_schedule_id = es.exam_id 
                         WHERE reg.applicant_id = ? 
                         ORDER BY reg.registration_date DESC LIMIT 1";
            $reg_stmt = $conn->prepare($reg_query);
            if (!$reg_stmt) {
                throw new Exception("Error preparing registration query: " . $conn->error);
            }
            
            $reg_stmt->bind_param("i", $applicant_id);
            if (!$reg_stmt->execute()) {
                throw new Exception("Error executing registration query: " . $reg_stmt->error);
            }
            
            $reg_result = $reg_stmt->get_result();
            if (!$registration = $reg_result->fetch_assoc()) {
                throw new Exception("No exam registration found for this applicant");
            }
            
            $registration_id = $registration['registration_id'];
            $exam_id = $registration['exam_id'];
            
            // Check if score already exists for this registration
            $check_query = "SELECT score_id FROM exam_scores WHERE registration_id = ?";
            $check_stmt = $conn->prepare($check_query);
            if (!$check_stmt) {
                throw new Exception("Error preparing check query: " . $conn->error);
            }
            
            $check_stmt->bind_param("i", $registration_id);
            if (!$check_stmt->execute()) {
                throw new Exception("Error executing check query: " . $check_stmt->error);
            }
            
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                // Update existing score
                $update_query = "UPDATE exam_scores SET 
                    score = ?, 
                    status = CASE 
                        WHEN ? >= 75 THEN 'qualified' 
                        ELSE 'not_qualified' 
                    END,
                    updated_at = NOW() 
                    WHERE registration_id = ?";
                $update_stmt = $conn->prepare($update_query);
                if (!$update_stmt) {
                    throw new Exception("Error preparing update query: " . $conn->error);
                }
                
                $update_stmt->bind_param("ddi", $score, $score, $registration_id);
                if (!$update_stmt->execute()) {
                    throw new Exception("Error executing update query: " . $update_stmt->error);
                }
            } else {
                // Insert new score
                $insert_query = "INSERT INTO exam_scores (
                    registration_id,
                    score, 
                    status
                ) VALUES (
                    ?,
                    ?, 
                    CASE 
                        WHEN ? >= 75 THEN 'qualified' 
                        ELSE 'not_qualified' 
                    END
                )";
                $insert_stmt = $conn->prepare($insert_query);
                if (!$insert_stmt) {
                    throw new Exception("Error preparing insert query: " . $conn->error);
                }
                
                $insert_stmt->bind_param("idd", $registration_id, $score, $score);
                if (!$insert_stmt->execute()) {
                    // Get more detailed error information
                    $error_msg = $insert_stmt->error;
                    $errno = $insert_stmt->errno;
                    
                    // Check if it's an auto-increment issue
                    if ($errno == 1062) { // Duplicate entry error
                        throw new Exception("Database error: Primary key auto-increment issue. Please check the exam_scores table structure. Error: " . $error_msg);
                    } else {
                        throw new Exception("Error executing insert query: " . $error_msg . " (Error Code: " . $errno . ")");
                    }
                }
            }
            
            // Return success response for AJAX
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Score updated successfully for ' . $applicant_name]);
            exit;
            
        } catch (Exception $e) {
            // Return error response for AJAX
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    } elseif (isset($_POST['batch_update'])) {
        $scores = $_POST['scores'];
        $success_count = 0;
        $error_count = 0;
        $error_details = array();
        
        foreach ($scores as $applicant_id => $score) {
            try {
                // Skip empty scores
                if (empty($score)) {
                    continue;
                }

                // Validate score
                if (!is_numeric($score) || $score < 0 || $score > 100) {
                    throw new Exception("Invalid score value: $score");
                }

                // Get applicant details
                $user_query = "SELECT user_id, first_name, last_name FROM applicants WHERE applicant_id = ?";
                $user_stmt = $conn->prepare($user_query);
                if (!$user_stmt) {
                    throw new Exception("Error preparing user query: " . $conn->error);
                }
                
                $user_stmt->bind_param("i", $applicant_id);
                if (!$user_stmt->execute()) {
                    throw new Exception("Error executing user query: " . $user_stmt->error);
                }
                
                $user_result = $user_stmt->get_result();
                if (!$user = $user_result->fetch_assoc()) {
                    throw new Exception("Applicant not found");
                }
                
                $user_id = $user['user_id'];
                $applicant_name = $user['first_name'] . ' ' . $user['last_name'];
                
                // Get the exam registration for this applicant
                $reg_query = "SELECT reg.registration_id, reg.exam_schedule_id, es.exam_id
                             FROM exam_registrations reg 
                             JOIN exam_schedules es ON reg.exam_schedule_id = es.exam_id 
                             WHERE reg.applicant_id = ? 
                             ORDER BY reg.registration_date DESC LIMIT 1";
                $reg_stmt = $conn->prepare($reg_query);
                if (!$reg_stmt) {
                    throw new Exception("Error preparing registration query: " . $conn->error);
                }
                
                $reg_stmt->bind_param("i", $applicant_id);
                if (!$reg_stmt->execute()) {
                    throw new Exception("Error executing registration query: " . $reg_stmt->error);
                }
                
                $reg_result = $reg_stmt->get_result();
                if (!$registration = $reg_result->fetch_assoc()) {
                    throw new Exception("No exam registration found for this applicant");
                }
                
                $registration_id = $registration['registration_id'];
                $exam_id = $registration['exam_id'];
                
                // Debug: Log the values for troubleshooting
                error_log("Debug - Applicant ID: $applicant_id, Registration ID: $registration_id, Exam ID: $exam_id, Score: $score");
                
                // Check if score already exists for this registration
                $check_query = "SELECT score_id FROM exam_scores WHERE registration_id = ?";
                $check_stmt = $conn->prepare($check_query);
                if (!$check_stmt) {
                    throw new Exception("Error preparing check query: " . $conn->error);
                }
                
                $check_stmt->bind_param("i", $registration_id);
                if (!$check_stmt->execute()) {
                    throw new Exception("Error executing check query: " . $check_stmt->error);
                }
                
                $check_result = $check_stmt->get_result();
                
                if ($check_result->num_rows > 0) {
                    // Update existing score
                    $update_query = "UPDATE exam_scores SET 
                        score = ?, 
                        status = CASE 
                            WHEN ? >= 75 THEN 'qualified' 
                            ELSE 'not_qualified' 
                        END,
                        updated_at = NOW() 
                        WHERE registration_id = ?";
                    $update_stmt = $conn->prepare($update_query);
                    if (!$update_stmt) {
                        throw new Exception("Error preparing update query: " . $conn->error);
                    }
                    
                    $update_stmt->bind_param("ddi", $score, $score, $registration_id);
                    if (!$update_stmt->execute()) {
                        throw new Exception("Error executing update query: " . $update_stmt->error);
                    }
                } else {
                    // Insert new score
                    $insert_query = "INSERT INTO exam_scores (
                        registration_id,
                        score, 
                        status
                    ) VALUES (
                        ?,
                        ?, 
                        CASE 
                            WHEN ? >= 75 THEN 'qualified' 
                            ELSE 'not_qualified' 
                        END
                    )";
                    $insert_stmt = $conn->prepare($insert_query);
                    if (!$insert_stmt) {
                        throw new Exception("Error preparing insert query: " . $conn->error);
                    }
                    
                    $insert_stmt->bind_param("idd", $registration_id, $score, $score);
                    if (!$insert_stmt->execute()) {
                        // Get more detailed error information
                        $error_msg = $insert_stmt->error;
                        $errno = $insert_stmt->errno;
                        
                        // Check if it's an auto-increment issue
                        if ($errno == 1062) { // Duplicate entry error
                            throw new Exception("Database error: Primary key auto-increment issue. Please check the exam_scores table structure. Error: " . $error_msg);
                        } else {
                            throw new Exception("Error executing insert query: " . $error_msg . " (Error Code: " . $errno . ")");
                        }
                    }
                }
                
                $success_count++;
                
            } catch (Exception $e) {
                $error_count++;
                $error_details[] = "Applicant ID $applicant_id" . 
                    (isset($applicant_name) ? " ($applicant_name)" : "") . 
                    ": " . $e->getMessage();
            }
        }
        
        if ($success_count > 0) {
            $message = '<div class="alert alert-success">Successfully updated ' . $success_count . ' scores.';
            if ($error_count > 0) {
                $message .= '<br>Failed to update ' . $error_count . ' scores:<br>';
                $message .= '<ul><li>' . implode('</li><li>', $error_details) . '</li></ul>';
            }
            $message .= '</div>';
            echo $message;
        } else {
            echo '<div class="alert alert-danger">No scores were updated. Errors:<br><ul><li>' . 
                 implode('</li><li>', $error_details) . '</li></ul></div>';
        }
    }
}

// Get filter parameters
$program_filter = isset($_GET['program']) ? (int)$_GET['program'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get all programs for the filter
$programs_query = "SELECT program_id, program_name FROM programs ORDER BY program_name";
$programs = mysqli_query($conn, $programs_query);

// Build the applicants query with filters
$applicants_query = "
    SELECT 
        a.applicant_id,
        a.first_name,
        a.middle_name,
        a.last_name,
        p.program_name as primary_program,
        p.program_id,
        esc.score,
        esc.status as score_status,
        sch.exam_date,
        reg.registration_id,
        sch.exam_id,
        CASE 
            WHEN esc.score IS NULL THEN 'pending'
            WHEN esc.score >= 75 THEN 'qualified'
            ELSE 'not_qualified'
        END as result_status
    FROM applicants a
    LEFT JOIN programs p ON a.primary_program_id = p.program_id
    LEFT JOIN exam_registrations reg ON a.applicant_id = reg.applicant_id
    LEFT JOIN exam_schedules sch ON reg.exam_schedule_id = sch.exam_id
    LEFT JOIN exam_scores esc ON reg.registration_id = esc.registration_id
    WHERE 1=1
";

if ($program_filter) {
    $applicants_query .= " AND p.program_id = " . $program_filter;
}

if ($status_filter) {
    $applicants_query .= " AND CASE 
        WHEN esc.score IS NULL THEN 'pending'
        WHEN esc.score >= 75 THEN 'qualified'
        ELSE 'not_qualified'
    END = '" . $status_filter . "'";
}

if ($search) {
    $search = $conn->real_escape_string($search);
    $applicants_query .= " AND (
        a.first_name LIKE '%$search%' OR 
        a.middle_name LIKE '%$search%' OR 
        a.last_name LIKE '%$search%' OR 
        p.program_name LIKE '%$search%'
    )";
}

$applicants_query .= " ORDER BY a.last_name, a.first_name";
$applicants = mysqli_query($conn, $applicants_query);

// Get statistics
$stats_query = "
    SELECT 
        COUNT(DISTINCT a.applicant_id) as total,
        SUM(CASE 
            WHEN esc.score IS NULL THEN 1 
            ELSE 0 
        END) as pending,
        SUM(CASE 
            WHEN esc.score >= 75 THEN 1 
            ELSE 0 
        END) as qualified,
        SUM(CASE 
            WHEN esc.score < 75 AND esc.score IS NOT NULL THEN 1 
            ELSE 0 
        END) as not_qualified
    FROM applicants a
    LEFT JOIN programs p ON a.primary_program_id = p.program_id
    LEFT JOIN exam_registrations reg ON a.applicant_id = reg.applicant_id
    LEFT JOIN exam_schedules sch ON reg.exam_schedule_id = sch.exam_id
    LEFT JOIN exam_scores esc ON reg.registration_id = esc.registration_id
    " . ($program_filter ? "WHERE a.primary_program_id = " . $program_filter : "");

$stats = mysqli_fetch_assoc(mysqli_query($conn, $stats_query));
?>

<!-- Main Content -->
<div class="container-fluid">
    <div class="row">
        <!-- Main Content Area -->
        <main class="col-md-12 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Encode Exam Scores</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-success" id="batchSaveBtn">
                        <i class="fas fa-save"></i> Save All Changes
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Applicants</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['total'] ?? 0); ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Scores</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['pending'] ?? 0); ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Qualified</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['qualified'] ?? 0); ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Not Qualified</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['not_qualified'] ?? 0); ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Filter Applicants</h6>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <input type="hidden" name="page" value="encode_scores">
                        <div class="col-md-4">
                            <label for="program" class="form-label">Program:</label>
                            <select name="program" id="program" class="form-control" onchange="this.form.submit()">
                                <option value="">All Programs</option>
                                <?php while ($program = mysqli_fetch_assoc($programs)): ?>
                                    <option value="<?php echo $program['program_id']; ?>" 
                                            <?php echo $program_filter == $program['program_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($program['program_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status:</label>
                            <select name="status" id="status" class="form-control" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="qualified" <?php echo $status_filter == 'qualified' ? 'selected' : ''; ?>>Qualified</option>
                                <option value="not_qualified" <?php echo $status_filter == 'not_qualified' ? 'selected' : ''; ?>>Not Qualified</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search:</label>
                            <div class="input-group">
                                <input type="text" name="search" id="search" class="form-control" 
                                       value="<?php echo htmlspecialchars($search); ?>" 
                                       placeholder="Search by name or program...">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Applicants Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Applicant Exam Scores</h6>
                    <div class="text-muted">
                        Showing <?php echo mysqli_num_rows($applicants); ?> applicants
                    </div>
                </div>
                <div class="card-body">
                    <form id="batchForm" method="POST">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="scoresTable">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Primary Program</th>
                                        <th>Exam Score</th>
                                        <th>Status</th>
                                        <th>Exam Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($applicant = mysqli_fetch_assoc($applicants)): ?>
                                    <tr class="<?php echo $applicant['result_status'] == 'pending' ? 'table-warning' : 
                                                    ($applicant['result_status'] == 'qualified' ? 'table-success' : 'table-danger'); ?>">
                                        <td>
                                            <?php 
                                            echo htmlspecialchars($applicant['last_name'] . ', ' . 
                                                $applicant['first_name'] . 
                                                ($applicant['middle_name'] ? ' ' . $applicant['middle_name'] : '')); 
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($applicant['primary_program']); ?></td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" 
                                                       name="scores[<?php echo $applicant['applicant_id']; ?>]" 
                                                       class="form-control score-input" 
                                                       value="<?php echo isset($applicant['score']) ? number_format($applicant['score'], 2) : ''; ?>"
                                                       min="0"
                                                       max="100"
                                                       step="0.01"
                                                       data-applicant-id="<?php echo $applicant['applicant_id']; ?>"
                                                       onchange="updateStatus(this)">
                                                <div class="input-group-append">
                                                    <button type="button" 
                                                            class="btn btn-success update-score" 
                                                            onclick="updateSingleScore(this)"
                                                            data-applicant-id="<?php echo $applicant['applicant_id']; ?>">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $applicant['result_status'] == 'pending' ? 'warning' : 
                                                    ($applicant['result_status'] == 'qualified' ? 'success' : 'danger'); 
                                            ?>">
                                                <?php echo ucfirst($applicant['result_status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo $applicant['exam_date'] ? date('M d, Y', strtotime($applicant['exam_date'])) : 'Not Scheduled'; ?>
                                        </td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-info btn-sm view-details" 
                                                    data-applicant-id="<?php echo $applicant['applicant_id']; ?>">
                                                <i class="fas fa-eye"></i> Details
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" name="batch_update" value="1">
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Applicant Details Modal -->
<div class="modal fade" id="applicantDetailsModal" tabindex="-1" role="dialog" aria-labelledby="applicantDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="applicantDetailsModalLabel">Applicant Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="modalApplicantDetails">
        <!-- Details will be loaded here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable with custom options
    var table = $('#scoresTable').DataTable({
        order: [[0, 'asc']],
        pageLength: 25,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        columnDefs: [
            { targets: [2], orderable: false } // Disable sorting on score column
        ]
    });

    // Test AJAX functionality
    function testAjax() {
        // First test simple file
        $.ajax({
            url: 'test_handler.php',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                console.log('Simple test successful:', response);
                // Now test the handler
                testHandler();
            },
            error: function(xhr, status, error) {
                console.error('Simple test failed:', xhr.responseText);
            }
        });
    }
    
    function testHandler() {
        $.ajax({
            url: 'handlers/update_single_score.php',
            type: 'POST',
            data: { test_ajax: 1 },
            dataType: 'json',
            success: function(response) {
                console.log('Handler test successful:', response);
            },
            error: function(xhr, status, error) {
                console.error('Handler test failed:', xhr.responseText);
            }
        });
    }
    
    // Run test on page load
    testAjax();

    // Handle batch save
    $('#batchSaveBtn').click(function() {
        if (confirm('Are you sure you want to save all changes?')) {
            $('#batchForm').submit();
        }
    });

    // Handle score input changes
    $('.score-input').on('input', function() {
        var value = $(this).val();
        var row = $(this).closest('tr');
        
        // Update status badge
        if (value === '') {
            row.removeClass('table-success table-danger').addClass('table-warning');
            row.find('.badge').removeClass('bg-success bg-danger').addClass('bg-warning').text('Pending');
        } else {
            var score = parseFloat(value);
            if (score >= 75) {
                row.removeClass('table-warning table-danger').addClass('table-success');
                row.find('.badge').removeClass('bg-warning bg-danger').addClass('bg-success').text('Qualified');
            } else {
                row.removeClass('table-warning table-success').addClass('table-danger');
                row.find('.badge').removeClass('bg-warning bg-success').addClass('bg-danger').text('Not Qualified');
            }
        }
    });

    // Handle view details button
    $('.view-details').click(function() {
        var applicantId = $(this).data('applicant-id');
        // Load applicant details via AJAX
        $.ajax({
            url: '../handlers/applicant_details_handler.php',
            type: 'GET',
            data: { applicant_id: applicantId },
            success: function(response) {
                $('#modalApplicantDetails').html(response);
                $('#applicantDetailsModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error("Error loading applicant details: " + error);
                $('#modalApplicantDetails').html('<div class="alert alert-danger">Error loading details. Please try again.</div>');
                $('#applicantDetailsModal').modal('show');
            }
        });
    });
});

// Function to update status based on score
function updateStatus(input) {
    var value = input.value;
    var row = input.closest('tr');
    var badge = row.querySelector('.badge');
    
    if (value === '') {
        row.className = 'table-warning';
        badge.className = 'badge bg-warning';
        badge.textContent = 'Pending';
    } else {
        var score = parseFloat(value);
        if (score >= 75) {
            row.className = 'table-success';
            badge.className = 'badge bg-success';
            badge.textContent = 'Qualified';
        } else {
            row.className = 'table-danger';
            badge.className = 'badge bg-danger';
            badge.textContent = 'Not Qualified';
        }
    }
}

// Function to update single score
function updateSingleScore(button) {
    var input = button.closest('.input-group').querySelector('input');
    var applicantId = button.getAttribute('data-applicant-id');
    var score = input.value;
    
    // Validate score
    if (score === '' || isNaN(score) || score < 0 || score > 100) {
        alert('Please enter a valid score between 0 and 100.');
        return;
    }
    
    // Disable button and show loading
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    // Log the request data
    console.log('Sending score update:', { applicant_id: applicantId, score: score });
    
    // Send AJAX request
    $.ajax({
        url: 'handlers/update_single_score.php',
        type: 'POST',
        data: {
            update_single_score: 1,
            applicant_id: applicantId,
            score: score
        },
        dataType: 'json',
        success: function(response) {
            console.log('Response received:', response);
            if (response.status === 'success') {
                // Show success message
                showAlert('success', response.message);
                
                // Update the status badge
                updateStatus(input);
                
                // Update button appearance
                button.innerHTML = '<i class="fas fa-check"></i>';
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-success');
                
                // Re-enable button after a delay
                setTimeout(function() {
                    button.disabled = false;
                    button.classList.remove('btn-outline-success');
                    button.classList.add('btn-success');
                }, 2000);
                
            } else {
                showAlert('danger', response.message);
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-check"></i>';
            }
        },
        error: function(xhr, status, error) {
            console.error('Handler failed, trying main file...');
            // Fallback to main file
            $.ajax({
                url: 'encode_scores.php',
                type: 'POST',
                data: {
                    update_single_score: 1,
                    applicant_id: applicantId,
                    score: score
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Main file response:', response);
                    if (response.status === 'success') {
                        showAlert('success', response.message);
                        updateStatus(input);
                        button.innerHTML = '<i class="fas fa-check"></i>';
                        button.classList.remove('btn-success');
                        button.classList.add('btn-outline-success');
                        setTimeout(function() {
                            button.disabled = false;
                            button.classList.remove('btn-outline-success');
                            button.classList.add('btn-success');
                        }, 2000);
                    } else {
                        showAlert('danger', response.message);
                        button.disabled = false;
                        button.innerHTML = '<i class="fas fa-check"></i>';
                    }
                },
                error: function(xhr2, status2, error2) {
                    console.error('AJAX Error Details:');
                    console.error('Status:', status2);
                    console.error('Error:', error2);
                    console.error('Response Text:', xhr2.responseText);
                    console.error('Status Code:', xhr2.status);
                    
                    var errorMessage = 'Error updating score. Please try again.';
                    if (xhr2.responseText) {
                        try {
                            var response = JSON.parse(xhr2.responseText);
                            errorMessage = response.message || errorMessage;
                        } catch (e) {
                            errorMessage += ' Server response: ' + xhr2.responseText.substring(0, 100);
                        }
                    }
                    
                    showAlert('danger', errorMessage);
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-check"></i>';
                }
            });
        }
    });
}

// Function to show alerts
function showAlert(type, message) {
    var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                    message +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span>' +
                    '</button>' +
                    '</div>';
    
    // Remove existing alerts
    $('.alert').remove();
    
    // Add new alert at the top of the main content
    $('main').prepend(alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script> 