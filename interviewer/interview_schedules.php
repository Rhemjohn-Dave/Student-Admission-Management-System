<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an interviewer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'interviewer') {
    header("location: ../auth/login.php");
    exit();
}

require_once '../config/database.php';

$user_id = $_SESSION['user_id'];

// Get interviewer's program information
$interviewer_query = "
    SELECT 
        ph.program_head_id,
        p.program_id,
        p.program_name
    FROM program_heads ph
    JOIN programs p ON ph.program_id = p.program_id
    WHERE ph.user_id = ?
";

$interviewer = null;
if ($stmt = mysqli_prepare($conn, $interviewer_query)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $interviewer = mysqli_fetch_assoc($result);
}

// Handle status update (move this before any output or includes)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $interview_id = $_POST['interview_id'];
    if ($_POST['action'] == 'complete') {
        $result = $_POST['result'];
        $notes = $_POST['notes'];
        $interest_motivation = $_POST['interest_motivation'];
        $communication_skills = $_POST['communication_skills'];
        $comprehension_thinking = $_POST['comprehension_thinking'];
        $program_skills = $_POST['program_skills'];
        $financial_capacity = $_POST['financial_capacity'];
        // Calculate total score (out of 25)
        $total_score = $interest_motivation + $communication_skills + $comprehension_thinking + $program_skills + $financial_capacity;
        // Format the notes to include evaluation scores
        $evaluation_notes = "Evaluation Scores:\n";
        $evaluation_notes .= "Interest and Motivation: $interest_motivation/5\n";
        $evaluation_notes .= "Communication Skills: $communication_skills/5\n";
        $evaluation_notes .= "Comprehension and Critical Thinking: $comprehension_thinking/5\n";
        $evaluation_notes .= "Program-Relevant Skills: $program_skills/5\n";
        $evaluation_notes .= "Financial Capacity: $financial_capacity/5\n";
        $evaluation_notes .= "Total Score: $total_score/25\n\n";
        $evaluation_notes .= "Additional Notes:\n" . $notes;
        $stmt = $conn->prepare("
            UPDATE interviews 
            SET status = 'completed', 
                result = ?, 
                score = ?,
                notes = ?, 
                completed_date = NOW() 
            WHERE interview_id = ? AND program_head_id = ?
        ");
        $stmt->bind_param("sdsii", $result, $total_score, $evaluation_notes, $interview_id, $interviewer['program_head_id']);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Interview evaluation submitted successfully.";
        } else {
            $_SESSION['error'] = "Failed to submit interview evaluation.";
        }
        // Redirect to refresh the page
        if (!headers_sent()) {
            header("location: index.php?page=interview_schedules");
            exit();
        } else {
            echo '<script>window.location.href = "index.php?page=interview_schedules";</script>';
            echo '<noscript><meta http-equiv="refresh" content="0;url=index.php?page=interview_schedules"></noscript>';
            echo '<div class="alert alert-warning">Redirecting... If you are not redirected, <a href="index.php?page=interview_schedules">click here</a>.</div>';
            exit();
        }
    }
}

// Get all interviews for this program head
$interviews_query = "
    SELECT 
        i.*,
        a.first_name,
        a.last_name,
        u.email,
        p.program_name,
        CONCAT(a.first_name, ' ', a.last_name) as applicant_name,
        CASE 
            WHEN i.status = 'scheduled' THEN 1
            WHEN i.status = 'completed' THEN 2
            ELSE 3
        END as status_order
    FROM interviews i
    JOIN applications app ON i.application_id = app.application_id
    JOIN applicants a ON app.user_id = a.user_id
    JOIN users u ON a.user_id = u.user_id
    JOIN programs p ON app.program_id = p.program_id
    WHERE i.program_head_id = ?
    ORDER BY 
        status_order ASC,
        i.scheduled_date DESC,
        i.scheduled_time DESC
";

$interviews = null;
if ($stmt = mysqli_prepare($conn, $interviews_query)) {
    mysqli_stmt_bind_param($stmt, "i", $interviewer['program_head_id']);
    mysqli_stmt_execute($stmt);
    $interviews = mysqli_stmt_get_result($stmt);
}

// Get interview schedules for this program
$schedules_stmt = $conn->prepare("
    SELECT 
        s.*,
        p.program_name
    FROM interview_schedules s
    JOIN programs p ON s.program_id = p.program_id
    WHERE s.program_id = ?
    ORDER BY s.interview_date ASC
");
$schedules_stmt->bind_param("i", $interviewer['program_id']);
$schedules_stmt->execute();
$schedules = $schedules_stmt->get_result();

// Move this line to after all form handling and redirects:
// include '../includes/navbar.php';

// Just before the first HTML output:
include '../includes/navbar.php';

?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Interview Schedules</h1>
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

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Scheduled Interviews for <?php echo htmlspecialchars($interviewer['program_name']); ?></h6>
    </div>
    <div class="card-body">
        <?php if ($interviews->num_rows == 0): ?>
            <div class="alert alert-info">
                No interviews scheduled at the moment.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Email</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($interview = $interviews->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($interview['first_name'] . ' ' . $interview['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($interview['email']); ?></td>
                                <td><?php echo date('F d, Y', strtotime($interview['scheduled_date'])); ?></td>
                                <td><?php echo date('h:i A', strtotime($interview['scheduled_time'])); ?></td>
                                <td>
                                    <span class="badge badge-<?php 
                                        echo $interview['status'] == 'scheduled' ? 'primary' : 
                                            ($interview['status'] == 'completed' ? 'success' : 'danger'); 
                                    ?>">
                                        <?php echo ucfirst($interview['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($interview['status'] == 'scheduled'): ?>
                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#completeModal<?php echo $interview['interview_id']; ?>">
                                            Complete Interview
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewModal<?php echo $interview['interview_id']; ?>">
                                            View Details
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <!-- Complete Interview Modal -->
                            <div class="modal fade" id="completeModal<?php echo $interview['interview_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="completeModalLabel<?php echo $interview['interview_id']; ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="completeModalLabel<?php echo $interview['interview_id']; ?>">Complete Interview</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="action" value="complete">
                                                <input type="hidden" name="interview_id" value="<?php echo $interview['interview_id']; ?>">
                                                
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Interest and Motivation</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="interest_motivation" value="5" required>
                                                        <label class="form-check-label">5 POINTS - Outstanding interest and motivation</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="interest_motivation" value="4">
                                                        <label class="form-check-label">4 POINTS - Noticeable interest and motivation</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="interest_motivation" value="3">
                                                        <label class="form-check-label">3 POINTS - Reasonable interest and motivation</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="interest_motivation" value="2">
                                                        <label class="form-check-label">2 POINTS - Somewhat lacking interest and motivation</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="interest_motivation" value="1">
                                                        <label class="form-check-label">1 POINT - Minimal interest and motivation</label>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="font-weight-bold">Communication Skills</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="communication_skills" value="5" required>
                                                        <label class="form-check-label">5 POINTS - Exceptional clarity and precision</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="communication_skills" value="4">
                                                        <label class="form-check-label">4 POINTS - Clear expression and meaningful conversation</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="communication_skills" value="3">
                                                        <label class="form-check-label">3 POINTS - Adequate communication with occasional lapses</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="communication_skills" value="2">
                                                        <label class="form-check-label">2 POINTS - Often unclear communication</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="communication_skills" value="1">
                                                        <label class="form-check-label">1 POINT - Struggles to express thoughts clearly</label>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="font-weight-bold">Comprehension and Critical Thinking</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="comprehension_thinking" value="5" required>
                                                        <label class="form-check-label">5 POINTS - Exceptional comprehension and critical thinking</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="comprehension_thinking" value="4">
                                                        <label class="form-check-label">4 POINTS - Clear comprehension and critical thinking</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="comprehension_thinking" value="3">
                                                        <label class="form-check-label">3 POINTS - Basic comprehension and analysis</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="comprehension_thinking" value="2">
                                                        <label class="form-check-label">2 POINTS - Limited comprehension</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="comprehension_thinking" value="1">
                                                        <label class="form-check-label">1 POINT - Struggles with comprehension and analysis</label>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="font-weight-bold">Program-Relevant Skills</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="program_skills" value="5" required>
                                                        <label class="form-check-label">5 POINTS - Proficient in program-relevant skills</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="program_skills" value="4">
                                                        <label class="form-check-label">4 POINTS - Above average competence</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="program_skills" value="3">
                                                        <label class="form-check-label">3 POINTS - Fundamental skills present</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="program_skills" value="2">
                                                        <label class="form-check-label">2 POINTS - Evident shortcomings in skills</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="program_skills" value="1">
                                                        <label class="form-check-label">1 POINT - Deficient in essential skills</label>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="font-weight-bold">Financial Capacity</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="financial_capacity" value="5" required>
                                                        <label class="form-check-label">5 POINTS - Serious financial struggles</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="financial_capacity" value="4">
                                                        <label class="form-check-label">4 POINTS - Significant financial difficulties</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="financial_capacity" value="3">
                                                        <label class="form-check-label">3 POINTS - Manages some financial responsibilities</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="financial_capacity" value="2">
                                                        <label class="form-check-label">2 POINTS - Some financial capability</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="financial_capacity" value="1">
                                                        <label class="form-check-label">1 POINT - Generally able to manage finances</label>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label>Overall Result</label>
                                                    <select class="form-control" name="result" required>
                                                        <option value="passed">Passed</option>
                                                        <option value="failed">Failed</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label>Additional Notes</label>
                                                    <textarea class="form-control" name="notes" rows="3" placeholder="Enter additional notes about the interview..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Submit Evaluation</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- View Details Modal -->
                            <div class="modal fade" id="viewModal<?php echo $interview['interview_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel<?php echo $interview['interview_id']; ?>" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewModalLabel<?php echo $interview['interview_id']; ?>">Interview Details</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Applicant:</strong> <?php echo htmlspecialchars($interview['first_name'] . ' ' . $interview['last_name']); ?></p>
                                            <p><strong>Email:</strong> <?php echo htmlspecialchars($interview['email']); ?></p>
                                            <p><strong>Date:</strong> <?php echo date('F d, Y', strtotime($interview['scheduled_date'])); ?></p>
                                            <p><strong>Time:</strong> <?php echo date('h:i A', strtotime($interview['scheduled_time'])); ?></p>
                                            <p><strong>Result:</strong> 
                                                <span class="badge badge-<?php echo $interview['result'] == 'passed' ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($interview['result']); ?>
                                                </span>
                                            </p>
                                            <?php if ($interview['notes']): ?>
                                                <p><strong>Notes:</strong></p>
                                                <p><?php echo nl2br(htmlspecialchars($interview['notes'])); ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Available Schedules -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Available Interview Schedules</h6>
    </div>
    <div class="card-body">
        <?php if ($schedules->num_rows == 0): ?>
            <div class="alert alert-info">
                No available interview schedules at the moment.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered" id="schedulesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time Window</th>
                            <th>Available Slots</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($schedule = $schedules->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('F d, Y', strtotime($schedule['interview_date'])); ?></td>
                                <td><?php echo $schedule['time_window']; ?></td>
                                <td><?php echo $schedule['max_applicants'] - $schedule['current_applicants']; ?></td>
                                <td>
                                    <span class="badge badge-<?php 
                                        echo $schedule['status'] == 'open' ? 'success' : 
                                            ($schedule['status'] == 'closed' ? 'warning' : 'danger'); 
                                    ?>">
                                        <?php echo ucfirst($schedule['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div> 