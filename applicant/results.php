<?php
require_once "../config/database.php";

// Check if user is logged in and is an applicant
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'applicant') {
    header("location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get applicant's exam results
$results_query = "
    SELECT 
        er.*,
        es.exam_date,
        es.time_window,
        CONCAT(a.first_name, 
            IF(a.middle_name IS NOT NULL AND a.middle_name != '', CONCAT(' ', a.middle_name), ''),
            ' ', a.last_name) as full_name,
        p1.program_name as primary_program,
        p2.program_name as secondary_program
    FROM exam_registrations er
    LEFT JOIN exam_schedules es ON er.exam_schedule_id = es.schedule_id
    LEFT JOIN applicants a ON er.user_id = a.user_id
    LEFT JOIN programs p1 ON a.primary_program_id = p1.program_id
    LEFT JOIN programs p2 ON a.secondary_program_id = p2.program_id
    WHERE er.user_id = ?
    ORDER BY er.created_at DESC
    LIMIT 1
";

if ($stmt = mysqli_prepare($conn, $results_query)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $exam_result = mysqli_fetch_assoc($result);
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Exam Results</h1>

    <?php if ($exam_result): ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Your Exam Results</h6>
            </div>
            <div class="card-body">
                <?php if ($exam_result['status'] === 'completed'): ?>
                    <div class="alert alert-info">
                        <h4 class="alert-heading">Exam Details</h4>
                        <p>
                            <strong>Name:</strong> <?php echo $exam_result['full_name']; ?><br>
                            <strong>Exam Date:</strong> <?php echo date('F d, Y', strtotime($exam_result['exam_date'])); ?><br>
                            <strong>Time:</strong> <?php echo $exam_result['time_window']; ?><br>
                            <strong>Primary Program:</strong> <?php echo $exam_result['primary_program']; ?><br>
                            <strong>Secondary Program:</strong> <?php echo $exam_result['secondary_program']; ?>
                        </p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Overall Score</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php echo number_format($exam_result['score'], 2); ?>%
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Status</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php 
                                                if ($exam_result['score'] >= 75) {
                                                    echo "Passed";
                                                } else {
                                                    echo "Failed";
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5>Score Breakdown</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Score</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Mathematics</td>
                                        <td><?php echo number_format($exam_result['math_score'], 2); ?>%</td>
                                        <td>
                                            <?php echo $exam_result['math_score'] >= 75 ? 
                                                '<span class="text-success">Passed</span>' : 
                                                '<span class="text-danger">Failed</span>'; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>English</td>
                                        <td><?php echo number_format($exam_result['english_score'], 2); ?>%</td>
                                        <td>
                                            <?php echo $exam_result['english_score'] >= 75 ? 
                                                '<span class="text-success">Passed</span>' : 
                                                '<span class="text-danger">Failed</span>'; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Science</td>
                                        <td><?php echo number_format($exam_result['science_score'], 2); ?>%</td>
                                        <td>
                                            <?php echo $exam_result['science_score'] >= 75 ? 
                                                '<span class="text-success">Passed</span>' : 
                                                '<span class="text-danger">Failed</span>'; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5>Next Steps</h5>
                        <?php if ($exam_result['score'] >= 75): ?>
                            <div class="alert alert-success">
                                <p>Congratulations! You have passed the entrance exam. Here are your next steps:</p>
                                <ol>
                                    <li>Wait for the official admission letter.</li>
                                    <li>Complete the enrollment requirements.</li>
                                    <li>Attend the orientation program.</li>
                                </ol>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <p>We regret to inform you that you did not meet the passing score. Here are your options:</p>
                                <ol>
                                    <li>You may retake the exam in the next available schedule.</li>
                                    <li>Contact the admissions office for more information.</li>
                                </ol>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <h4 class="alert-heading">Results Not Yet Available</h4>
                        <p>Your exam results are not yet available. Please check back later.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">No Exam Results</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h4 class="alert-heading">No Exam Results Found</h4>
                    <p>You have not taken any exam yet. Please register and take the entrance exam to view your results.</p>
                    <a href="exam_registration.php" class="btn btn-primary">Register for Exam</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div> 