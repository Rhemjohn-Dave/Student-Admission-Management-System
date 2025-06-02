<?php
require_once '../config/database.php';

// Check if user is logged in and is an interviewer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'interviewer') {
    header("location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get interviewer's program information
$interviewer_query = "
    SELECT 
        u.*,
        p.program_name,
        p.program_id
    FROM users u
    JOIN interviewers i ON u.user_id = i.user_id
    JOIN programs p ON i.program_id = p.program_id
    WHERE u.user_id = ?
";

$interviewer = null;
if ($stmt = mysqli_prepare($conn, $interviewer_query)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $interviewer = mysqli_fetch_assoc($result);
}

// Get upcoming interviews
$interviews_query = "
    SELECT 
        s.*,
        u.first_name,
        u.last_name,
        p.program_name
    FROM interview_schedules s
    JOIN users u ON s.created_by = u.user_id
    JOIN programs p ON s.program_id = p.program_id
    WHERE s.created_by = ? AND s.status = 'open'
    ORDER BY s.interview_date ASC
    LIMIT 5
";

$upcoming_interviews = [];
if ($stmt = mysqli_prepare($conn, $interviews_query)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $upcoming_interviews = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Get recent interviews
$recent_interviews_query = "
    SELECT 
        s.*,
        u.first_name,
        u.last_name,
        p.program_name
    FROM interview_schedules s
    JOIN users u ON s.created_by = u.user_id
    JOIN programs p ON s.program_id = p.program_id
    WHERE s.created_by = ? AND s.status = 'completed'
    ORDER BY s.interview_date DESC
    LIMIT 5
";

$recent_interviews = [];
if ($stmt = mysqli_prepare($conn, $recent_interviews_query)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $recent_interviews = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Program Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Assigned Program</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $interviewer ? $interviewer['program_name'] : 'Not Assigned'; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Interviews Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Upcoming Interviews</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo count($upcoming_interviews); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Interviews Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Completed Interviews</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo count($recent_interviews); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Applicants Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Total Applicants</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php 
                            $total_applicants = 0;
                            foreach ($upcoming_interviews as $interview) {
                                $total_applicants += $interview['current_applicants'];
                            }
                            echo $total_applicants;
                            ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Upcoming Interviews Table -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Upcoming Interviews</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Program</th>
                                <th>Applicants</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($upcoming_interviews as $interview): ?>
                                <tr>
                                    <td><?php echo date('F d, Y', strtotime($interview['interview_date'])); ?></td>
                                    <td><?php echo date('h:i A', strtotime($interview['interview_time'])); ?></td>
                                    <td><?php echo $interview['program_name']; ?></td>
                                    <td><?php echo $interview['current_applicants']; ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $interview['status'] === 'open' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($interview['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($upcoming_interviews)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No upcoming interviews</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="?page=interview_schedules" class="list-group-item list-group-item-action">
                        <i class="fas fa-calendar fa-fw mr-2"></i> Manage Interview Schedules
                    </a>
                    <a href="?page=applicants" class="list-group-item list-group-item-action">
                        <i class="fas fa-users fa-fw mr-2"></i> View Applicants
                    </a>
                    <a href="?page=profile" class="list-group-item list-group-item-action">
                        <i class="fas fa-user fa-fw mr-2"></i> Update Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div> 