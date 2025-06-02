<?php
require_once "../config/database.php";

// Check if user is logged in and is an applicant
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'applicant') {
    header("location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$registration = null; // Initialize registration variable

// Get applicant's exam registration
$registration_query = "
    SELECT 
        er.*,
        es.exam_date,
        es.exam_time,
        es.venue,
        es.status as schedule_status,
        CONCAT(a.first_name, 
            IF(a.middle_name IS NOT NULL AND a.middle_name != '', CONCAT(' ', a.middle_name), ''),
            ' ', a.last_name) as full_name
    FROM exam_registrations er
    LEFT JOIN exam_schedules es ON er.exam_schedule_id = es.exam_id
    LEFT JOIN applicants a ON er.applicant_id = a.applicant_id
    WHERE a.user_id = ?
    ORDER BY er.registration_date DESC
    LIMIT 1
";

if ($stmt = mysqli_prepare($conn, $registration_query)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $registration = mysqli_fetch_assoc($result);
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Exam Schedule</h1>

    <?php if ($registration): ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Your Exam Schedule</h6>
            </div>
            <div class="card-body">
                <?php if ($registration['schedule_status'] === 'scheduled'): ?>
                    <div class="alert alert-success">
                        <h4 class="alert-heading">Exam Details</h4>
                        <p>
                            <strong>Name:</strong> <?php echo $registration['full_name']; ?><br>
                            <strong>Exam Date:</strong> <?php echo date('F d, Y', strtotime($registration['exam_date'])); ?><br>
                            <strong>Time:</strong> <?php echo date('h:i A', strtotime($registration['exam_time'])); ?><br>
                            <strong>Venue:</strong> <?php echo $registration['venue']; ?><br>
                            <strong>Status:</strong> <?php echo ucfirst($registration['status']); ?>
                        </p>
                        <hr>
                        <p class="mb-0">
                            <strong>Important Notes:</strong><br>
                            1. Please arrive at least 30 minutes before your scheduled time.<br>
                            2. Bring your valid ID and exam permit.<br>
                            3. No electronic devices are allowed during the exam.<br>
                            4. Follow all health and safety protocols.
                        </p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <h4 class="alert-heading">Schedule Not Available</h4>
                        <p>Your exam schedule is currently not available. Please check back later or contact the admissions office.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">No Exam Registration</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h4 class="alert-heading">You are not registered for any exam</h4>
                    <p>Please register for an exam to view your schedule.</p>
                    <a href="exam_registration.php" class="btn btn-primary">Register for Exam</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div> 