<?php
require_once "../config/database.php";

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch all exam registrations with user and exam details
$query = "SELECT er.*, 
          u.first_name, u.last_name, u.email,
          es.exam_date, es.exam_time, es.venue
          FROM exam_registrations er
          JOIN users u ON er.user_id = u.user_id
          JOIN exam_schedules es ON er.exam_id = es.exam_id
          ORDER BY er.registration_date DESC";
$result = mysqli_query($conn, $query);
?>

<div class="container mt-4">
    <h2>Exam Registrations</h2>

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
                    <th>Registration Date</th>
                    <th>Applicant Name</th>
                    <th>Email</th>
                    <th>Exam Date</th>
                    <th>Exam Time</th>
                    <th>Venue</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($registration = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo date('F d, Y h:i A', strtotime($registration['registration_date'])); ?></td>
                        <td><?php echo htmlspecialchars($registration['first_name'] . ' ' . $registration['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($registration['email']); ?></td>
                        <td><?php echo date('F d, Y', strtotime($registration['exam_date'])); ?></td>
                        <td><?php echo date('h:i A', strtotime($registration['exam_time'])); ?></td>
                        <td><?php echo htmlspecialchars($registration['venue']); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $registration['status'] === 'registered' ? 'success' : 'secondary'; ?>">
                                <?php echo ucfirst($registration['status']); ?>
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $registration['registration_id']; ?>">
                                View Details
                            </button>
                            <?php if ($registration['status'] === 'registered'): ?>
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal<?php echo $registration['registration_id']; ?>">
                                    Cancel
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- View Details Modal -->
                    <div class="modal fade" id="viewModal<?php echo $registration['registration_id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Registration Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <dl class="row">
                                        <dt class="col-sm-4">Applicant Name</dt>
                                        <dd class="col-sm-8"><?php echo htmlspecialchars($registration['first_name'] . ' ' . $registration['last_name']); ?></dd>

                                        <dt class="col-sm-4">Email</dt>
                                        <dd class="col-sm-8"><?php echo htmlspecialchars($registration['email']); ?></dd>

                                        <dt class="col-sm-4">Exam Date</dt>
                                        <dd class="col-sm-8"><?php echo date('F d, Y', strtotime($registration['exam_date'])); ?></dd>

                                        <dt class="col-sm-4">Exam Time</dt>
                                        <dd class="col-sm-8"><?php echo date('h:i A', strtotime($registration['exam_time'])); ?></dd>

                                        <dt class="col-sm-4">Venue</dt>
                                        <dd class="col-sm-8"><?php echo htmlspecialchars($registration['venue']); ?></dd>

                                        <dt class="col-sm-4">Registration Date</dt>
                                        <dd class="col-sm-8"><?php echo date('F d, Y h:i A', strtotime($registration['registration_date'])); ?></dd>

                                        <dt class="col-sm-4">Status</dt>
                                        <dd class="col-sm-8">
                                            <span class="badge bg-<?php echo $registration['status'] === 'registered' ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($registration['status']); ?>
                                            </span>
                                        </dd>
                                    </dl>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cancel Registration Modal -->
                    <div class="modal fade" id="cancelModal<?php echo $registration['registration_id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Cancel Registration</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to cancel this registration?</p>
                                    <dl class="row">
                                        <dt class="col-sm-4">Applicant</dt>
                                        <dd class="col-sm-8"><?php echo htmlspecialchars($registration['first_name'] . ' ' . $registration['last_name']); ?></dd>

                                        <dt class="col-sm-4">Exam Date</dt>
                                        <dd class="col-sm-8"><?php echo date('F d, Y', strtotime($registration['exam_date'])); ?></dd>

                                        <dt class="col-sm-4">Exam Time</dt>
                                        <dd class="col-sm-8"><?php echo date('h:i A', strtotime($registration['exam_time'])); ?></dd>
                                    </dl>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep</button>
                                    <form action="handle_exam_registration.php" method="POST">
                                        <input type="hidden" name="registration_id" value="<?php echo $registration['registration_id']; ?>">
                                        <button type="submit" name="cancel_registration" class="btn btn-danger">Yes, Cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div> 