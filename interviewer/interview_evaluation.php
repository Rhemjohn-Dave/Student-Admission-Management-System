<?php
require_once '../config/database.php';

// Check if user is logged in and is an interviewer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'interviewer') {
    header("location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// If no interview ID is provided, redirect to interview schedules
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Please select an interview to evaluate.";
    header("location: index.php?page=interview_schedules");
    exit();
}

// Get interview details
$interview = null;
$sql = "SELECT i.*, u.first_name, u.last_name, p.program_name, s.time_window
        FROM interviews i
        JOIN applications a ON i.application_id = a.application_id
        JOIN users u ON a.user_id = u.user_id
        JOIN programs p ON a.program_id = p.program_id
        JOIN interview_schedules s ON s.program_id = a.program_id AND s.interview_date = i.scheduled_date
        WHERE i.interview_id = ? AND i.interviewer_id = ?";
        
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "ii", $_GET['id'], $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $interview = mysqli_fetch_assoc($result);
}

if (!$interview) {
    $_SESSION['error'] = "Interview not found or you don't have permission to access it.";
    header("location: index.php?page=interview_schedules");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_evaluation'])) {
    $interview_id = $_POST['interview_id'];
    $applicant_id = $_POST['applicant_id'];
    
    // Get scores
    $interest_motivation = $_POST['interest_motivation'];
    $communication_skills = $_POST['communication_skills'];
    $comprehension_thinking = $_POST['comprehension_thinking'];
    $program_skills = $_POST['program_skills'];
    $financial_capacity = $_POST['financial_capacity'];
    
    // Calculate total score
    $total_score = $interest_motivation + $communication_skills + $comprehension_thinking + $program_skills + $financial_capacity;
    
    // Determine recommendation
    $recommendation = ($total_score >= 20) ? 'recommended' : 'not_recommended';
    
    // Update interview record
    $sql = "UPDATE interviews 
            SET status = 'completed',
                result = ?,
                notes = ?,
                completed_date = NOW()
            WHERE interview_id = ? AND interviewer_id = ?";
            
    if ($stmt = mysqli_prepare($conn, $sql)) {
        $notes = "Interest & Motivation: $interest_motivation/5\n" .
                 "Communication Skills: $communication_skills/5\n" .
                 "Comprehension & Critical Thinking: $comprehension_thinking/5\n" .
                 "Program-Relevant Skills: $program_skills/5\n" .
                 "Financial Capacity: $financial_capacity/5\n" .
                 "Total Score: $total_score/25";
                 
        mysqli_stmt_bind_param($stmt, "ssii", $recommendation, $notes, $interview_id, $user_id);
        mysqli_stmt_execute($stmt);
        
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $_SESSION['success'] = "Interview evaluation submitted successfully.";
            header("location: index.php?page=interview_results");
            exit();
        } else {
            $_SESSION['error'] = "Error submitting evaluation.";
        }
    }
}
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Interview Evaluation</h1>
</div>

<!-- Interview Details Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Applicant Information</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Applicant Name:</strong> <?php echo htmlspecialchars($interview['first_name'] . ' ' . $interview['last_name']); ?></p>
                <p><strong>Program:</strong> <?php echo htmlspecialchars($interview['program_name']); ?></p>
                <p><strong>Interview Date:</strong> <?php echo date('F d, Y', strtotime($interview['scheduled_date'])); ?></p>
                <p><strong>Interview Time:</strong> <?php echo $interview['time_window']; ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Evaluation Form -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Evaluation Criteria</h6>
    </div>
    <div class="card-body">
        <form method="post" class="needs-validation" novalidate>
            <input type="hidden" name="interview_id" value="<?php echo $interview['interview_id']; ?>">
            <input type="hidden" name="applicant_id" value="<?php echo $interview['application_id']; ?>">
            
            <!-- Interest and Motivation -->
            <div class="form-group mb-4">
                <h5>1. Interest and Motivation</h5>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="interest_motivation" value="5" required>
                    <label class="form-check-label">
                        5 POINTS - Outstanding interest and motivation, deep understanding of program's significance
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="interest_motivation" value="4">
                    <label class="form-check-label">
                        4 POINTS - Noticeable interest and motivation, clear reasons for pursuing program
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="interest_motivation" value="3">
                    <label class="form-check-label">
                        3 POINTS - Reasonable interest and motivation, some genuine enthusiasm
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="interest_motivation" value="2">
                    <label class="form-check-label">
                        2 POINTS - Somewhat lacking interest and motivation
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="interest_motivation" value="1">
                    <label class="form-check-label">
                        1 POINT - Minimal interest and motivation
                    </label>
                </div>
            </div>

            <!-- Communication Skills -->
            <div class="form-group mb-4">
                <h5>2. Communication Skills</h5>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="communication_skills" value="5" required>
                    <label class="form-check-label">
                        5 POINTS - Exceptional clarity and precision, fluent articulation
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="communication_skills" value="4">
                    <label class="form-check-label">
                        4 POINTS - Clear expression and meaningful conversation
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="communication_skills" value="3">
                    <label class="form-check-label">
                        3 POINTS - Adequate communication with occasional lapses
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="communication_skills" value="2">
                    <label class="form-check-label">
                        2 POINTS - Often unclear communication
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="communication_skills" value="1">
                    <label class="form-check-label">
                        1 POINT - Struggles to express thoughts clearly
                    </label>
                </div>
            </div>

            <!-- Comprehension and Critical Thinking -->
            <div class="form-group mb-4">
                <h5>3. Comprehension and Critical Thinking</h5>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="comprehension_thinking" value="5" required>
                    <label class="form-check-label">
                        5 POINTS - Exceptional comprehension and critical thinking abilities
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="comprehension_thinking" value="4">
                    <label class="form-check-label">
                        4 POINTS - Clear demonstration of comprehension and critical thinking
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="comprehension_thinking" value="3">
                    <label class="form-check-label">
                        3 POINTS - Basic comprehension skills with some analysis ability
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="comprehension_thinking" value="2">
                    <label class="form-check-label">
                        2 POINTS - Limited comprehension and critical thinking
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="comprehension_thinking" value="1">
                    <label class="form-check-label">
                        1 POINT - Struggles to understand or analyze information
                    </label>
                </div>
            </div>

            <!-- Program-Relevant Skills -->
            <div class="form-group mb-4">
                <h5>4. Program-Relevant Skills</h5>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="program_skills" value="5" required>
                    <label class="form-check-label">
                        5 POINTS - Proficiency closely corresponding to program demands
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="program_skills" value="4">
                    <label class="form-check-label">
                        4 POINTS - Competence above average level
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="program_skills" value="3">
                    <label class="form-check-label">
                        3 POINTS - Fundamental skills pertinent to program
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="program_skills" value="2">
                    <label class="form-check-label">
                        2 POINTS - Evident shortcomings in skills
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="program_skills" value="1">
                    <label class="form-check-label">
                        1 POINT - Deficient in essential skills
                    </label>
                </div>
            </div>

            <!-- Financial Capacity -->
            <div class="form-group mb-4">
                <h5>5. Financial Capacity</h5>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="financial_capacity" value="5" required>
                    <label class="form-check-label">
                        5 POINTS - Serious financial struggles with little to no ability to manage expenses
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="financial_capacity" value="4">
                    <label class="form-check-label">
                        4 POINTS - Significant financial difficulties
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="financial_capacity" value="3">
                    <label class="form-check-label">
                        3 POINTS - Manages some financial responsibilities with challenges
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="financial_capacity" value="2">
                    <label class="form-check-label">
                        2 POINTS - Some financial capability with occasional struggles
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="financial_capacity" value="1">
                    <label class="form-check-label">
                        1 POINT - Generally able to manage finances
                    </label>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" name="submit_evaluation" class="btn btn-primary">Submit Evaluation</button>
                <a href="index.php?page=interview_schedules" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
// Form validation
(function() {
    'use strict';
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script> 