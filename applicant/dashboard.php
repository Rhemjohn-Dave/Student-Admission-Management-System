<?php
require_once "../config/database.php";

// Check if user is logged in and is an applicant
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'applicant') {
    header("location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get applicant's program information
$applicant_query = "
    SELECT 
        a.primary_program_id,
        a.secondary_program_id,
        p1.program_name as primary_program_name,
        p2.program_name as secondary_program_name
    FROM applicants a
    LEFT JOIN programs p1 ON a.primary_program_id = p1.program_id
    LEFT JOIN programs p2 ON a.secondary_program_id = p2.program_id
    WHERE a.user_id = ?
";

$applicant = null;
if ($stmt = mysqli_prepare($conn, $applicant_query)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $applicant = mysqli_fetch_assoc($result);
}

// Get applicant's interview details
$interview_query = "
    SELECT 
        i.*,
        p.program_name,
        CONCAT(u.first_name, ' ', u.last_name) as interviewer_name,
        s.time_window
    FROM interviews i
    JOIN applications a ON i.application_id = a.application_id
    JOIN programs p ON a.program_id = p.program_id
    JOIN program_heads ph ON i.program_head_id = ph.program_head_id
    JOIN users u ON ph.user_id = u.user_id
    JOIN interview_schedules s ON s.interview_date = i.scheduled_date AND s.program_id = p.program_id
    WHERE a.user_id = ?
    ORDER BY i.scheduled_date DESC, i.scheduled_time DESC
    LIMIT 1
";

$interview = null;
if ($stmt = mysqli_prepare($conn, $interview_query)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $interview = mysqli_fetch_assoc($result);
}

// Get exam registration status
$exam_registration = null;
$exam_registration_query = "
    SELECT 
        er.*,
        es.exam_date,
        es.exam_time,
        es.venue,
        es.status as schedule_status,
        esc.score,
        esc.status as result_status
    FROM exam_registrations er
    LEFT JOIN exam_schedules es ON er.exam_schedule_id = es.exam_id
    LEFT JOIN applicants a ON er.applicant_id = a.applicant_id
    LEFT JOIN exam_scores esc ON er.registration_id = esc.registration_id
    WHERE a.user_id = ?
    ORDER BY er.registration_date DESC
    LIMIT 1
";

// Debug: Log the query and user_id
error_log("Exam Registration Query: " . $exam_registration_query);
error_log("User ID: " . $user_id);

if ($stmt = mysqli_prepare($conn, $exam_registration_query)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    // Debug: Log the number of rows found
    error_log("Number of exam registrations found: " . mysqli_num_rows($result));
    
    $exam_registration = mysqli_fetch_assoc($result);
    
    // Debug: Log the registration data
    error_log("Exam Registration Data: " . print_r($exam_registration, true));
} else {
    // Debug: Log any query preparation errors
    error_log("Error preparing exam registration query: " . mysqli_error($conn));
}
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?php 
        echo $_SESSION['success'];
        unset($_SESSION['success']);
        ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?php 
        echo $_SESSION['error'];
        unset($_SESSION['error']);
        ?>
    </div>
<?php endif; ?>

<!-- Program Information Card -->
<div class="row">
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Primary Program</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo htmlspecialchars($applicant['primary_program_name'] ?? 'Not selected'); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Secondary Program</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo htmlspecialchars($applicant['secondary_program_name'] ?? 'Not selected'); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Interview Information Card -->
<div class="row">
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Interview Information</h6>
            </div>
            <div class="card-body">
                <?php if ($interview): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <tbody>
                                <tr>
                                    <th width="30%">Program</th>
                                    <td><?php echo htmlspecialchars($interview['program_name']); ?></td>
                                </tr>
                                <tr>
                                    <th>Interviewer</th>
                                    <td><?php echo htmlspecialchars($interview['interviewer_name']); ?></td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <td><?php echo date('F d, Y', strtotime($interview['scheduled_date'])); ?></td>
                                </tr>
                                <tr>
                                    <th>Time</th>
                                    <td><?php echo $interview['time_window']; ?></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge badge-<?php 
                                            echo $interview['status'] == 'scheduled' ? 'primary' : 
                                                ($interview['status'] == 'completed' ? 'success' : 'danger'); 
                                        ?>">
                                            <?php echo ucfirst($interview['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        You haven't scheduled an interview yet. Please go to the <a href="index.php?page=select_interview">Select Interview</a> page to schedule one.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Application Status Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Application Status</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo ucfirst($_SESSION['status']); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Registration/Result Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <?php if ($exam_registration === null): ?>
                            <!-- No Registration -->
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Exam Registration</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Not Registered
                            </div>
                        <?php elseif (isset($exam_registration['score'])): ?>
                            <!-- Has Exam Result -->
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Exam Result</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo number_format($exam_registration['score'], 2); ?>%
                            </div>
                            <div class="text-sm text-gray-800 mt-0">
                                <?php echo date('F d, Y', strtotime($exam_registration['exam_date'])); ?> at 
                                <?php echo date('h:i A', strtotime($exam_registration['exam_time'])); ?>
                            </div>
                            <div class="text-lg text-gray-800 mt-0">
                                Venue: <?php echo htmlspecialchars($exam_registration['venue']); ?>
                            </div>
                        <?php else: ?>
                            <!-- Has Registration but No Result -->
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Exam Registration</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo ucfirst($exam_registration['status']); ?>
                            </div>
                            <div class="text-lg text-gray-800 mt-0">
                                <?php echo date('F d, Y', strtotime($exam_registration['exam_date'])); ?> at 
                                <?php echo date('h:i A', strtotime($exam_registration['exam_time'])); ?>
                            </div>
                            <div class="text-sm text-gray-800 mt-0">
                                Venue: <?php echo htmlspecialchars($exam_registration['venue']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-auto">
                        <?php if (isset($exam_registration['score'])): ?>
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        <?php else: ?>
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="?page=profile" class="list-group-item list-group-item-action">
                        <i class="fas fa-user fa-fw mr-2"></i> Update Profile
                    </a>
                    <a href="?page=exam_registration" class="list-group-item list-group-item-action">
                        <i class="fas fa-calendar-alt fa-fw mr-2"></i> Register for Exam
                    </a>
                    <a href="?page=exam_schedule" class="list-group-item list-group-item-action">
                        <i class="fas fa-clock fa-fw mr-2"></i> View Exam Schedule
                    </a>
                    <a href="?page=results" class="list-group-item list-group-item-action">
                        <i class="fas fa-chart-bar fa-fw mr-2"></i> View Results
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>