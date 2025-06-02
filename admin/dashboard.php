<?php
// Include database connection
require_once "../config/database.php";

// Get counts for dashboard
$pending_interviewers = $conn->query("SELECT COUNT(*) FROM users WHERE user_type = 'interviewer' AND status = 'pending'")->fetch_row()[0];
$upcoming_exams = $conn->query("SELECT COUNT(*) FROM exam_schedules WHERE exam_date >= CURDATE() AND status = 'open'")->fetch_row()[0];
$upcoming_interviews = $conn->query("SELECT COUNT(*) FROM interview_schedules WHERE interview_date >= CURDATE() AND status = 'open'")->fetch_row()[0];
$total_applicants = $conn->query("SELECT COUNT(*) FROM users WHERE user_type = 'applicant'")->fetch_row()[0];

// Get recent activity
$recent_activity = $conn->query("
    SELECT 
        u.username,
        u.user_type,
        a.action,
        a.created_at
    FROM activity_log a
    JOIN users u ON a.user_id = u.user_id
    ORDER BY a.created_at DESC
    LIMIT 10
");
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    <div>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" id="exportBtn">
            <i class="fas fa-download fa-sm text-white-50"></i> Export Report
        </a>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" id="printBtn">
            <i class="fas fa-print fa-sm text-white-50"></i> Print
        </a>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Pending Interviewers Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pending Interviewers</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pending_interviewers; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Exams Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Upcoming Exams</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $upcoming_exams; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $upcoming_interviews; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Applicants Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Applicants</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_applicants; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Registrations Card -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Exam Registrations</h5>
                <p class="card-text">View and manage exam registrations</p>
                <a href="index.php?page=exam_schedules" class="btn btn-danger">View Registrations</a>
            </div>
        </div>
    </div>

    <!-- Exam Results Card -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Exam Results</h5>
                <p class="card-text">Encode and manage exam results</p>
                <a href="index.php?page=exam_results" class="btn btn-danger">Encode Results</a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Type</th>
                                <th>Action</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $recent_activity->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['user_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['action']); ?></td>
                                <td><?php echo date('M d, Y h:i A', strtotime($row['created_at'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Export button click handler
    $('#exportBtn').click(function() {
        // Add export functionality here
        alert('Export functionality will be implemented');
    });

    // Print button click handler
    $('#printBtn').click(function() {
        window.print();
    });
});
</script> 