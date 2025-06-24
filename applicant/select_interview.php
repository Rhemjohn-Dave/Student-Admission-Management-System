<?php
require_once '../config/database.php';

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
        p2.program_name as secondary_program_name,
        u.first_name,
        u.last_name
    FROM applicants a
    LEFT JOIN programs p1 ON a.primary_program_id = p1.program_id
    LEFT JOIN programs p2 ON a.secondary_program_id = p2.program_id
    JOIN users u ON a.user_id = u.user_id
    WHERE a.user_id = ?
";

$applicant = null;
if ($stmt = mysqli_prepare($conn, $applicant_query)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $applicant = mysqli_fetch_assoc($result);
}

// Handle interview selection
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['schedule_id'])) {
    $schedule_id = $_POST['schedule_id'];
    
    // Get the schedule details (to know which program this is for)
    $schedule_query = "
        SELECT s.*, p.program_name, p.program_id, CONCAT(u.first_name, ' ', u.last_name) as interviewer_name
        FROM interview_schedules s
        JOIN programs p ON s.program_id = p.program_id
        JOIN program_heads ph ON p.program_id = ph.program_id
        JOIN users u ON ph.user_id = u.user_id
        WHERE s.schedule_id = ? AND s.status = 'open'
    ";
    if ($stmt = mysqli_prepare($conn, $schedule_query)) {
        mysqli_stmt_bind_param($stmt, "i", $schedule_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $schedule = mysqli_fetch_assoc($result);
        
        if ($schedule) {
            // Check if applicant already has an interview for this program
            $check_query = "
                SELECT COUNT(*) as count
                FROM interviews i
                JOIN applications a ON i.application_id = a.application_id
                WHERE a.user_id = ? AND a.program_id = ?
            ";
            if ($stmt = mysqli_prepare($conn, $check_query)) {
                mysqli_stmt_bind_param($stmt, "ii", $user_id, $schedule['program_id']);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $count = mysqli_fetch_assoc($result)['count'];
                
                if ($count > 0) {
                    $_SESSION['error'] = "You already have an interview scheduled for this program.";
                } else {
                    // Get or create the application ID
                    $application_query = "SELECT application_id FROM applications WHERE user_id = ? AND program_id = ?";
                    if ($stmt = mysqli_prepare($conn, $application_query)) {
                        mysqli_stmt_bind_param($stmt, "ii", $user_id, $schedule['program_id']);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        $application = mysqli_fetch_assoc($result);
                        
                        if (!$application) {
                            // Create new application if it doesn't exist
                            $create_application_query = "INSERT INTO applications (user_id, program_id, status) VALUES (?, ?, 'pending')";
                            if ($stmt = mysqli_prepare($conn, $create_application_query)) {
                                mysqli_stmt_bind_param($stmt, "ii", $user_id, $schedule['program_id']);
                                if (mysqli_stmt_execute($stmt)) {
                                    $application_id = mysqli_insert_id($conn);
                                    $application = ['application_id' => $application_id];
                                } else {
                                    throw new Exception("Error creating application record.");
                                }
                            }
                        }
                        
                        if ($application) {
                            // Get the program head ID
                            $programhead_query = "SELECT ph.program_head_id FROM program_heads ph WHERE ph.program_id = ?";
                            if ($stmt2 = mysqli_prepare($conn, $programhead_query)) {
                                mysqli_stmt_bind_param($stmt2, "i", $schedule['program_id']);
                                mysqli_stmt_execute($stmt2);
                                $result = mysqli_stmt_get_result($stmt2);
                                $program = mysqli_fetch_assoc($result);
                                
                                // Start transaction
                                mysqli_begin_transaction($conn);
                                
                                try {
                                    // Create the interview record
                                    $insert_query = "INSERT INTO interviews (application_id, interviewer_id, program_head_id, scheduled_date, scheduled_time, status) VALUES (?, ?, ?, ?, ?, 'scheduled')";
                                    if ($stmt = mysqli_prepare($conn, $insert_query)) {
                                        mysqli_stmt_bind_param($stmt, "iiiss", 
                                            $application['application_id'],
                                            $program['program_head_id'],
                                            $program['program_head_id'],
                                            $schedule['interview_date'],
                                            $schedule['time_window']
                                        );
                                        
                                        if (mysqli_stmt_execute($stmt)) {
                                            // Update schedule count
                                            $update_query = "UPDATE interview_schedules SET current_applicants = current_applicants + 1 WHERE schedule_id = ?";
                                            if ($stmt = mysqli_prepare($conn, $update_query)) {
                                                mysqli_stmt_bind_param($stmt, "i", $schedule_id);
                                                mysqli_stmt_execute($stmt);
                                            }
                                            
                                            // Create notification for interviewer
                                            $notification_query = "INSERT INTO notifications (user_id, title, message, type) VALUES (?, 'New Interview Scheduled', ?, 'interview')";
                                            if ($stmt = mysqli_prepare($conn, $notification_query)) {
                                                $message = "New interview scheduled with " . $applicant['first_name'] . " " . $applicant['last_name'] . 
                                                          " for " . $schedule['program_name'] . " on " . 
                                                          date('F d, Y', strtotime($schedule['interview_date'])) . " " . $schedule['time_window'];
                                                mysqli_stmt_bind_param($stmt, "is", $program['program_head_id'], $message);
                                                mysqli_stmt_execute($stmt);
                                            }
                                            
                                            mysqli_commit($conn);
                                            $_SESSION['success'] = "Interview scheduled successfully!";
                                            echo "<script>window.location.href = 'index.php?page=dashboard';</script>";
                                            exit();
                                        } else {
                                            throw new Exception("Error scheduling interview.");
                                        }
                                    }
                                } catch (Exception $e) {
                                    mysqli_rollback($conn);
                                    $_SESSION['error'] = $e->getMessage();
                                }
                            }
                        } else {
                            $_SESSION['error'] = "Error creating application record.";
                        }
                    }
                }
            }
        } else {
            $_SESSION['error'] = "Schedule not available.";
        }
    }
}

// Get available interview schedules for the applicant's programs
$schedules_query = "
    SELECT 
        s.*,
        p.program_name,
        CONCAT(u.first_name, ' ', u.last_name) as interviewer_name
    FROM interview_schedules s
    JOIN programs p ON s.program_id = p.program_id
    JOIN program_heads ph ON p.program_id = ph.program_id
    JOIN users u ON ph.user_id = u.user_id
    WHERE s.status = 'open'
    AND s.program_id IN (?, ?)
    AND s.current_applicants < s.max_applicants
    ORDER BY s.interview_date ASC
";

$schedules = [];
if ($stmt = mysqli_prepare($conn, $schedules_query)) {
    mysqli_stmt_bind_param($stmt, "ii", $applicant['primary_program_id'], $applicant['secondary_program_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $schedules = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Select Interview Schedule</h1>
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

<!-- Available Schedules -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Available Interview Schedules</h6>
    </div>
    <div class="card-body">
        <?php if (empty($schedules)): ?>
            <div class="alert alert-info">
                No available interview schedules for your programs at the moment. Please check back later.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered" id="schedulesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Program</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Interviewer</th>
                            <th>Available Slots</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedules as $schedule): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($schedule['program_name']); ?></td>
                                <td><?php echo date('F d, Y', strtotime($schedule['interview_date'])); ?></td>
                                <td><?php echo $schedule['time_window']; ?></td>
                                <td><?php echo htmlspecialchars($schedule['interviewer_name']); ?></td>
                                <td><?php echo $schedule['max_applicants'] - $schedule['current_applicants']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#confirmModal<?php echo $schedule['schedule_id']; ?>">
                                        Select
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Confirmation Modal -->
                            <div class="modal fade" id="confirmModal<?php echo $schedule['schedule_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel<?php echo $schedule['schedule_id']; ?>" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="confirmModalLabel<?php echo $schedule['schedule_id']; ?>">Confirm Interview Selection</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to schedule an interview for:</p>
                                            <ul>
                                                <li><strong>Program:</strong> <?php echo htmlspecialchars($schedule['program_name']); ?></li>
                                                <li><strong>Date:</strong> <?php echo date('F d, Y', strtotime($schedule['interview_date'])); ?></li>
                                                <li><strong>Time:</strong> <?php echo $schedule['time_window']; ?></li>
                                                <li><strong>Interviewer:</strong> <?php echo htmlspecialchars($schedule['interviewer_name']); ?></li>
                                            </ul>
                                            <p>Once confirmed, you cannot change your selection.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="schedule_id" value="<?php echo $schedule['schedule_id']; ?>">
                                                <button type="submit" class="btn btn-primary">Confirm Selection</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#schedulesTable').DataTable({
        "order": [[1, "asc"]], // Sort by date
        "pageLength": 10
    });
});
</script> 