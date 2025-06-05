<?php
// Only start session if it hasn't been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_type"] !== "admin"){
    header("location: ../auth/login.php");
    exit;
}

// Include database connection
require_once "../config/database.php";

// Set page title
$page_title = "Encode Exam Scores - Student Admissions Management System";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_score'])) {
        $applicant_id = trim($_POST['applicant_id']);
        $score = trim($_POST['score']);
        
        // Validate inputs
        if (empty($applicant_id) || !is_numeric($score) || $score < 0 || $score > 100) {
            echo '<div class="alert alert-danger">Invalid score. Please enter a number between 0 and 100.</div>';
        } else {
            // Get user_id from applicant_id
            $user_query = "SELECT user_id FROM applicants WHERE applicant_id = ?";
            $user_stmt = $conn->prepare($user_query);
            $user_stmt->bind_param("i", $applicant_id);
            $user_stmt->execute();
            $user_result = $user_stmt->get_result();
            
            if ($user = $user_result->fetch_assoc()) {
                $user_id = $user['user_id'];
                
                // Check if exam result already exists
                $check_query = "SELECT result_id FROM exam_results WHERE user_id = ?";
                $check_stmt = $conn->prepare($check_query);
                $check_stmt->bind_param("i", $user_id);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                
                if ($check_result->num_rows > 0) {
                    // Update existing score
                    $update_query = "UPDATE exam_results SET 
                        score = ?, 
                        status = CASE 
                            WHEN ? >= 75 THEN 'passed' 
                            ELSE 'failed' 
                        END,
                        updated_at = NOW() 
                        WHERE user_id = ?";
                    $update_stmt = $conn->prepare($update_query);
                    $update_stmt->bind_param("ddi", $score, $score, $user_id);
                    
                    if ($update_stmt->execute()) {
                        // Recalculate rankings after score update
                        require_once "handlers/ranking_handler.php";
                        recalculateRankings($conn);
                        echo '<div class="alert alert-success">Score updated successfully.</div>';
                    } else {
                        echo '<div class="alert alert-danger">Error updating score: ' . $conn->error . '</div>';
                    }
                } else {
                    // Insert new score
                    $insert_query = "INSERT INTO exam_results (
                        user_id, 
                        score, 
                        status, 
                        created_at, 
                        updated_at
                    ) VALUES (
                        ?, 
                        ?, 
                        CASE 
                            WHEN ? >= 75 THEN 'passed' 
                            ELSE 'failed' 
                        END,
                        NOW(), 
                        NOW()
                    )";
                    $insert_stmt = $conn->prepare($insert_query);
                    $insert_stmt->bind_param("idd", $user_id, $score, $score);
                    
                    if ($insert_stmt->execute()) {
                        // Recalculate rankings after new score
                        require_once "handlers/ranking_handler.php";
                        recalculateRankings($conn);
                        echo '<div class="alert alert-success">Score added successfully.</div>';
                    } else {
                        echo '<div class="alert alert-danger">Error adding score: ' . $conn->error . '</div>';
                    }
                }
            } else {
                echo '<div class="alert alert-danger">Applicant not found.</div>';
            }
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

                // Get user_id from applicant_id
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
                
                // Get the current exam ID for this applicant
                $exam_query = "SELECT es.exam_id 
                             FROM exam_registrations reg 
                             JOIN exam_schedules es ON reg.exam_schedule_id = es.exam_id 
                             WHERE reg.applicant_id = ? AND es.status = 'completed' 
                             ORDER BY es.exam_date DESC LIMIT 1";
                $exam_stmt = $conn->prepare($exam_query);
                if (!$exam_stmt) {
                    throw new Exception("Error preparing exam query: " . $conn->error);
                }
                
                $exam_stmt->bind_param("i", $applicant_id);
                if (!$exam_stmt->execute()) {
                    throw new Exception("Error executing exam query: " . $exam_stmt->error);
                }
                
                $exam_result = $exam_stmt->get_result();
                if (!$exam = $exam_result->fetch_assoc()) {
                    throw new Exception("No completed exam found for this applicant");
                }
                
                $current_exam_id = $exam['exam_id'];
                
                // Check if exam result already exists
                $check_query = "SELECT result_id FROM exam_results WHERE user_id = ? AND exam_id = ?";
                $check_stmt = $conn->prepare($check_query);
                if (!$check_stmt) {
                    throw new Exception("Error preparing check query: " . $conn->error);
                }
                
                $check_stmt->bind_param("ii", $user_id, $current_exam_id);
                if (!$check_stmt->execute()) {
                    throw new Exception("Error executing check query: " . $check_stmt->error);
                }
                
                $check_result = $check_stmt->get_result();
                
                if ($check_result->num_rows > 0) {
                    // Update existing score
                    $update_query = "UPDATE exam_results SET 
                        score = ?, 
                        status = CASE 
                            WHEN ? >= 75 THEN 'passed' 
                            ELSE 'failed' 
                        END,
                        updated_at = NOW() 
                        WHERE user_id = ? AND exam_id = ?";
                    $update_stmt = $conn->prepare($update_query);
                    if (!$update_stmt) {
                        throw new Exception("Error preparing update query: " . $conn->error);
                    }
                    
                    $update_stmt->bind_param("ddii", $score, $score, $user_id, $current_exam_id);
                    if (!$update_stmt->execute()) {
                        throw new Exception("Error executing update query: " . $update_stmt->error);
                    }
                } else {
                    // Insert new score
                    $insert_query = "INSERT INTO exam_results (
                        user_id, 
                        exam_id,
                        score, 
                        status, 
                        created_at, 
                        updated_at
                    ) VALUES (
                        ?, 
                        ?,
                        ?, 
                        CASE 
                            WHEN ? >= 75 THEN 'passed' 
                            ELSE 'failed' 
                        END,
                        NOW(), 
                        NOW()
                    )";
                    $insert_stmt = $conn->prepare($insert_query);
                    if (!$insert_stmt) {
                        throw new Exception("Error preparing insert query: " . $conn->error);
                    }
                    
                    $insert_stmt->bind_param("iidd", $user_id, $current_exam_id, $score, $score);
                    if (!$insert_stmt->execute()) {
                        throw new Exception("Error executing insert query: " . $insert_stmt->error);
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
            // Recalculate rankings after batch update
            require_once "handlers/ranking_handler.php";
            recalculateRankings($conn);
            
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
        er.score,
        er.status,
        es.exam_date,
        er.exam_id,
        es.exam_id as current_exam_id,
        CASE 
            WHEN er.score IS NULL THEN 'pending'
            WHEN er.score >= 75 THEN 'passed'
            ELSE 'failed'
        END as result_status
    FROM applicants a
    LEFT JOIN programs p ON a.primary_program_id = p.program_id
    LEFT JOIN exam_registrations reg ON a.applicant_id = reg.applicant_id
    LEFT JOIN exam_schedules es ON reg.exam_schedule_id = es.exam_id AND es.status = 'completed'
    LEFT JOIN exam_results er ON a.user_id = er.user_id AND er.exam_id = es.exam_id
    WHERE 1=1
";

if ($program_filter) {
    $applicants_query .= " AND p.program_id = " . $program_filter;
}

if ($status_filter) {
    $applicants_query .= " AND CASE 
        WHEN er.score IS NULL THEN 'pending'
        WHEN er.score >= 75 THEN 'passed'
        ELSE 'failed'
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
        COUNT(*) as total,
        SUM(CASE WHEN er.score IS NULL THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN er.score >= 75 THEN 1 ELSE 0 END) as passed,
        SUM(CASE WHEN er.score < 75 AND er.score IS NOT NULL THEN 1 ELSE 0 END) as failed
    FROM applicants a
    LEFT JOIN exam_results er ON a.user_id = er.user_id
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
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['total']; ?></div>
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
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['pending']; ?></div>
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
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Passed</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['passed']; ?></div>
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
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Failed</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['failed']; ?></div>
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
                                <option value="passed" <?php echo $status_filter == 'passed' ? 'selected' : ''; ?>>Passed</option>
                                <option value="failed" <?php echo $status_filter == 'failed' ? 'selected' : ''; ?>>Failed</option>
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
                                                    ($applicant['result_status'] == 'passed' ? 'table-success' : 'table-danger'); ?>">
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
                                                       value="<?php echo $applicant['score'] ?? ''; ?>"
                                                       min="0"
                                                       max="100"
                                                       step="0.01"
                                                       data-applicant-id="<?php echo $applicant['applicant_id']; ?>"
                                                       onchange="updateStatus(this)">
                                                <div class="input-group-append">
                                                    <button type="button" 
                                                            class="btn btn-outline-secondary clear-score" 
                                                            onclick="clearScore(this)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $applicant['result_status'] == 'pending' ? 'warning' : 
                                                    ($applicant['result_status'] == 'passed' ? 'success' : 'danger'); 
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

<!-- Initialize DataTables -->
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
                row.find('.badge').removeClass('bg-warning bg-danger').addClass('bg-success').text('Passed');
            } else {
                row.removeClass('table-warning table-success').addClass('table-danger');
                row.find('.badge').removeClass('bg-warning bg-success').addClass('bg-danger').text('Failed');
            }
        }
    });

    // Handle clear score button
    $('.clear-score').click(function() {
        var input = $(this).closest('.input-group').find('input');
        input.val('');
        var row = $(this).closest('tr');
        row.removeClass('table-success table-danger').addClass('table-warning');
        row.find('.badge').removeClass('bg-success bg-danger').addClass('bg-warning').text('Pending');
    });

    // Handle view details button
    $('.view-details').click(function() {
        var applicantId = $(this).data('applicant-id');
        // Implement view details functionality
        // You can show a modal with more information about the applicant
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
            badge.textContent = 'Passed';
        } else {
            row.className = 'table-danger';
            badge.className = 'badge bg-danger';
            badge.textContent = 'Failed';
        }
    }
}

// Function to clear score
function clearScore(button) {
    var input = button.closest('.input-group').querySelector('input');
    input.value = '';
    updateStatus(input);
}
</script> 