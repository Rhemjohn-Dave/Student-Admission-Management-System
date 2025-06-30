<?php
// Check if session is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

// Get all completed interviews for all applicants
$interviews_query = "
    SELECT 
        i.*,
        a.first_name,
        a.last_name,
        u.email,
        p.program_name,
        CONCAT(a.first_name, ' ', a.last_name) as applicant_name,
        CASE 
            WHEN i.result = 'passed' THEN 1
            ELSE 2
        END as result_order
    FROM interviews i
    JOIN applications app ON i.application_id = app.application_id
    JOIN applicants a ON app.user_id = a.user_id
    JOIN users u ON a.user_id = u.user_id
    JOIN programs p ON app.program_id = p.program_id
    WHERE i.status = 'completed'
    ORDER BY 
        p.program_name ASC,
        result_order ASC,
        i.completed_date DESC,
        i.score DESC
";

$interviews = null;
if ($stmt = mysqli_prepare($conn, $interviews_query)) {
    mysqli_stmt_execute($stmt);
    $interviews = mysqli_stmt_get_result($stmt);
}

$interview_data = array(); // Always initialize interview_data as an array
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Interview Results</h1>
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
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">All Completed Interviews</h6>
    </div>
    <div class="card-body">
        <?php if ($interviews && $interviews->num_rows == 0): ?>
            <div class="alert alert-info">
                No completed interviews found.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Program</th>
                            <th>Applicant</th>
                            <th>Email</th>
                            <th>Date</th>
                            <th>Result</th>
                            <th>Total Score</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        while ($interview = $interviews->fetch_assoc()): 
                            $interview_data[] = $interview; // Store interview data
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($interview['program_name']); ?></td>
                                <td><?php echo htmlspecialchars($interview['applicant_name']); ?></td>
                                <td><?php echo htmlspecialchars($interview['email']); ?></td>
                                <td><?php echo date('F d, Y', strtotime($interview['completed_date'])); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $interview['result'] == 'passed' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst($interview['result']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    // Extract total score from notes
                                    $notes = $interview['notes'];
                                    if (preg_match('/Total Score: (\d+)\/25/', $notes, $matches)) {
                                        echo $matches[1] . '/25';
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewModal<?php echo $interview['interview_id']; ?>">
                                        <i class="fas fa-eye"></i> View Details
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modals -->
<?php foreach ($interview_data as $interview): ?>
    <!-- View Details Modal -->
    <div class="modal fade" id="viewModal<?php echo $interview['interview_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel<?php echo $interview['interview_id']; ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel<?php echo $interview['interview_id']; ?>">Interview Evaluation Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Program:</strong> <?php echo htmlspecialchars($interview['program_name']); ?></p>
                            <p><strong>Applicant:</strong> <?php echo htmlspecialchars($interview['applicant_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($interview['email']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Date:</strong> <?php echo date('F d, Y', strtotime($interview['completed_date'])); ?></p>
                            <p><strong>Result:</strong> 
                                <span class="badge badge-<?php echo $interview['result'] == 'passed' ? 'success' : 'danger'; ?>">
                                    <?php echo ucfirst($interview['result']); ?>
                                </span>
                            </p>
                        </div>
                    </div>

                    <h6 class="font-weight-bold">Evaluation Scores</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Criteria</th>
                                    <th>Score</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $notes = $interview['notes'];
                                $criteria = [
                                    'Interest and Motivation' => [
                                        '5' => 'Outstanding interest and motivation',
                                        '4' => 'Noticeable interest and motivation',
                                        '3' => 'Reasonable interest and motivation',
                                        '2' => 'Somewhat lacking interest and motivation',
                                        '1' => 'Minimal interest and motivation'
                                    ],
                                    'Communication Skills' => [
                                        '5' => 'Exceptional clarity and precision',
                                        '4' => 'Clear expression and meaningful conversation',
                                        '3' => 'Adequate communication with occasional lapses',
                                        '2' => 'Often unclear communication',
                                        '1' => 'Struggles to express thoughts clearly'
                                    ],
                                    'Comprehension and Critical Thinking' => [
                                        '5' => 'Exceptional comprehension and critical thinking',
                                        '4' => 'Clear comprehension and critical thinking',
                                        '3' => 'Basic comprehension and analysis',
                                        '2' => 'Limited comprehension',
                                        '1' => 'Struggles with comprehension and analysis'
                                    ],
                                    'Program-Relevant Skills' => [
                                        '5' => 'Proficient in program-relevant skills',
                                        '4' => 'Above average competence',
                                        '3' => 'Fundamental skills present',
                                        '2' => 'Evident shortcomings in skills',
                                        '1' => 'Deficient in essential skills'
                                    ],
                                    'Financial Capacity' => [
                                        '5' => 'Serious financial struggles',
                                        '4' => 'Significant financial difficulties',
                                        '3' => 'Manages some financial responsibilities',
                                        '2' => 'Some financial capability',
                                        '1' => 'Generally able to manage finances'
                                    ]
                                ];
                                
                                foreach ($criteria as $criterion => $descriptions) {
                                    if (preg_match("/$criterion: (\d+)\/5/", $notes, $matches)) {
                                        $score = $matches[1];
                                        echo "<tr>";
                                        echo "<td>$criterion</td>";
                                        echo "<td>{$score}/5</td>";
                                        echo "<td>{$descriptions[$score]}</td>";
                                        echo "</tr>";
                                    }
                                }
                                
                                if (preg_match('/Total Score: (\d+)\/25/', $notes, $matches)) {
                                    echo "<tr class='table-primary'>";
                                    echo "<td colspan='2'><strong>Total Score</strong></td>";
                                    echo "<td><strong>{$matches[1]}/25</strong></td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <h6 class="font-weight-bold mt-4">Additional Notes</h6>
                    <?php
                    // Extract additional notes
                    if (preg_match('/Additional Notes:\n(.*)/s', $notes, $matches)) {
                        echo "<div class='border p-3'>" . nl2br(htmlspecialchars(trim($matches[1]))) . "</div>";
                    }
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?> 