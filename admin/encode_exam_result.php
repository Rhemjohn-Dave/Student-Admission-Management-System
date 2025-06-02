<?php
require_once "../config/database.php";

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch exam registrations that need results
$query = "SELECT er.*, 
          u.first_name, u.last_name, u.email,
          es.exam_date, es.exam_time, es.venue
          FROM exam_registrations er
          JOIN users u ON er.user_id = u.user_id
          JOIN exam_schedules es ON er.exam_id = es.exam_id
          WHERE er.status = 'registered'
          ORDER BY es.exam_date DESC, es.exam_time ASC";
$result = mysqli_query($conn, $query);
?>

<div class="container mt-4">
    <h2>Encode Exam Results</h2>

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

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Applicant Name</th>
                    <th>Email</th>
                    <th>Exam Date</th>
                    <th>Exam Time</th>
                    <th>Venue</th>
                    <th>Result</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($registration = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($registration['first_name'] . ' ' . $registration['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($registration['email']); ?></td>
                        <td><?php echo date('F d, Y', strtotime($registration['exam_date'])); ?></td>
                        <td><?php echo date('h:i A', strtotime($registration['exam_time'])); ?></td>
                        <td><?php echo htmlspecialchars($registration['venue']); ?></td>
                        <td>
                            <?php if (isset($registration['result'])): ?>
                                <span class="badge bg-<?php echo $registration['result'] === 'passed' ? 'success' : 'danger'; ?>">
                                    <?php echo ucfirst($registration['result']); ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-warning">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#resultModal<?php echo $registration['registration_id']; ?>">
                                Encode Result
                            </button>
                        </td>
                    </tr>

                    <!-- Result Modal -->
                    <div class="modal fade" id="resultModal<?php echo $registration['registration_id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Encode Exam Result</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="resultForm<?php echo $registration['registration_id']; ?>" class="result-form">
                                        <input type="hidden" name="registration_id" value="<?php echo $registration['registration_id']; ?>">
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Applicant</label>
                                            <p class="form-control-static">
                                                <?php echo htmlspecialchars($registration['first_name'] . ' ' . $registration['last_name']); ?>
                                            </p>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Exam Details</label>
                                            <p class="form-control-static">
                                                <?php echo date('F d, Y', strtotime($registration['exam_date'])); ?> at 
                                                <?php echo date('h:i A', strtotime($registration['exam_time'])); ?><br>
                                                Venue: <?php echo htmlspecialchars($registration['venue']); ?>
                                            </p>
                                        </div>

                                        <div class="mb-3">
                                            <label for="result<?php echo $registration['registration_id']; ?>" class="form-label">Result</label>
                                            <select class="form-select" id="result<?php echo $registration['registration_id']; ?>" name="result" required>
                                                <option value="">Select Result</option>
                                                <option value="passed">Passed</option>
                                                <option value="failed">Failed</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="remarks<?php echo $registration['registration_id']; ?>" class="form-label">Remarks</label>
                                            <textarea class="form-control" id="remarks<?php echo $registration['registration_id']; ?>" name="remarks" rows="3"></textarea>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" onclick="submitResult(<?php echo $registration['registration_id']; ?>)">Save Result</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function submitResult(registrationId) {
    const form = document.getElementById('resultForm' + registrationId);
    const formData = new FormData(form);
    
    // Add the action
    formData.append('action', 'encode_result');
    
    fetch('handle_exam_result.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: data.message
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'An error occurred while saving the result.'
        });
    });
}
</script> 