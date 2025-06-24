<?php
require_once '../config/database.php';

// Check if user is logged in and is an interviewer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'interviewer') {
    header("location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get the assigned program for the current user (program head)
$assigned_program = null;
$program_head_query = "
    SELECT p.program_name, p.program_id
    FROM program_heads ph
    JOIN programs p ON ph.program_id = p.program_id
    WHERE ph.user_id = ? AND ph.status = 'active'
    LIMIT 1
";
if ($stmt = mysqli_prepare($conn, $program_head_query)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $assigned_program = mysqli_fetch_assoc($result);
}

// Default values
$upcoming_interviews_count = 0;
$completed_interviews_count = 0;
$total_applicants = 0;
$upcoming_interviews = [];

if ($assigned_program && isset($assigned_program['program_id'])) {
    $program_id = $assigned_program['program_id'];
    // 1. Upcoming Interviews: scheduled interviews not yet scored
    $upcoming_query = "
        SELECT i.*, s.interview_date, s.time_window, p.program_name, s.current_applicants
        FROM interviews i
        JOIN applications a ON i.application_id = a.application_id
        JOIN interview_schedules s ON s.program_id = a.program_id AND s.interview_date = i.scheduled_date AND s.time_window = i.scheduled_time
        JOIN programs p ON a.program_id = p.program_id
        WHERE a.program_id = ? AND i.status = 'scheduled' AND (i.score IS NULL OR i.score = '')
        ORDER BY i.scheduled_date ASC
    ";
    if ($stmt = mysqli_prepare($conn, $upcoming_query)) {
        mysqli_stmt_bind_param($stmt, "i", $program_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $upcoming_interviews = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $upcoming_interviews_count = count($upcoming_interviews);
    }
    // 2. Completed Interviews: interviews finished and graded by the interviewer
    $completed_query = "
        SELECT COUNT(*) as total
        FROM interviews i
        JOIN applications a ON i.application_id = a.application_id
        WHERE a.program_id = ? AND i.status = 'completed' AND (i.score IS NOT NULL AND i.score != '')
    ";
    if ($stmt = mysqli_prepare($conn, $completed_query)) {
        mysqli_stmt_bind_param($stmt, "i", $program_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $completed_interviews_count = $row ? (int)$row['total'] : 0;
    }
    // 3. Total Applicants: unique applicants scheduled for interview in this program
    $applicants_query = "
        SELECT COUNT(DISTINCT a.user_id) as total
        FROM interviews i
        JOIN applications a ON i.application_id = a.application_id
        WHERE a.program_id = ?
    ";
    if ($stmt = mysqli_prepare($conn, $applicants_query)) {
        mysqli_stmt_bind_param($stmt, "i", $program_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $total_applicants = $row && $row['total'] !== null ? (int)$row['total'] : 0;
    }
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
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                            Assigned Program</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo ($assigned_program && isset($assigned_program['program_name'])) ? $assigned_program['program_name'] : 'Not Assigned'; ?>
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
                            <?php echo $upcoming_interviews_count; ?>
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
                            <?php echo $completed_interviews_count; ?>
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
                            <?php echo $total_applicants; ?>
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
                                    <td><?php echo isset($interview['interview_date']) ? date('F d, Y', strtotime($interview['interview_date'])) : ''; ?></td>
                                    <td><?php echo isset($interview['time_window']) ? $interview['time_window'] : ''; ?></td>
                                    <td><?php echo isset($interview['program_name']) ? $interview['program_name'] : ''; ?></td>
                                    <td><?php echo isset($interview['current_applicants']) ? $interview['current_applicants'] : ''; ?></td>
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