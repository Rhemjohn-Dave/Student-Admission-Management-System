<?php
// Include database connection
require_once "../config/database.php";

// Handle interviewer approval/rejection
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
    $interviewer_id = $_POST["interviewer_id"];
    $action = $_POST["action"];
    
    if ($action == "approve") {
        $sql = "UPDATE users SET status = 'active' WHERE user_id = ? AND user_type = 'interviewer'";
        $message = "Interviewer approved successfully";
    } elseif ($action == "reject") {
        $sql = "UPDATE users SET status = 'inactive' WHERE user_id = ? AND user_type = 'interviewer'";
        $message = "Interviewer rejected successfully";
    }
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $interviewer_id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = $message;
        } else {
            $_SESSION['error'] = "Error updating interviewer status";
        }
    }
    
    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch pending interviewers with detailed information
$sql = "SELECT u.user_id, u.username, u.email, u.status, u.first_name, u.last_name,
               i.qualifications, i.experience, u.created_at,
               p.program_name, c.college_name
        FROM users u
        JOIN interviewers i ON u.user_id = i.user_id
        LEFT JOIN programs p ON i.program_id = p.program_id
        LEFT JOIN colleges c ON p.college_id = c.college_id
        WHERE u.user_type = 'interviewer' AND u.status = 'pending'
        ORDER BY u.created_at DESC";
$pending_interviewers = mysqli_query($conn, $sql);

// Fetch active interviewers
$sql = "SELECT u.user_id, u.username, u.email, u.status, u.first_name, u.last_name,
               i.qualifications, i.experience, u.created_at,
               p.program_name, c.college_name
        FROM users u
        JOIN interviewers i ON u.user_id = i.user_id
        LEFT JOIN programs p ON i.program_id = p.program_id
        LEFT JOIN colleges c ON p.college_id = c.college_id
        WHERE u.user_type = 'interviewer' AND u.status = 'active'
        ORDER BY u.created_at DESC";
$active_interviewers = mysqli_query($conn, $sql);
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Interviewers Management</h1>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Pending Interviewers Card -->
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <!-- Card Header -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Pending Interviewers</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php 
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php 
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-bordered dataTable" id="pendingInterviewersTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>College</th>
                                <th>Program</th>
                                <th>Qualifications</th>
                                <th>Experience</th>
                                <th>Date Applied</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($pending_interviewers)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['college_name'] ?? 'Not assigned'); ?></td>
                                <td><?php echo htmlspecialchars($row['program_name'] ?? 'Not assigned'); ?></td>
                                <td><?php echo htmlspecialchars($row['qualifications']); ?></td>
                                <td><?php echo htmlspecialchars($row['experience']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm view-details" 
                                            data-toggle="modal" data-target="#viewDetailsModal"
                                            data-id="<?php echo $row['user_id']; ?>"
                                            data-name="<?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>"
                                            data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                            data-college="<?php echo htmlspecialchars($row['college_name'] ?? 'Not assigned'); ?>"
                                            data-program="<?php echo htmlspecialchars($row['program_name'] ?? 'Not assigned'); ?>"
                                            data-qualifications="<?php echo htmlspecialchars($row['qualifications']); ?>"
                                            data-experience="<?php echo htmlspecialchars($row['experience']); ?>">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm approve-interviewer" 
                                            data-toggle="modal" data-target="#approveModal"
                                            data-id="<?php echo $row['user_id']; ?>"
                                            data-name="<?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm reject-interviewer" 
                                            data-toggle="modal" data-target="#rejectModal"
                                            data-id="<?php echo $row['user_id']; ?>"
                                            data-name="<?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Interviewers Card -->
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <!-- Card Header -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Active Interviewers</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered dataTable" id="activeInterviewersTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>College</th>
                                <th>Program</th>
                                <th>Qualifications</th>
                                <th>Experience</th>
                                <th>Date Approved</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($active_interviewers)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['college_name'] ?? 'Not assigned'); ?></td>
                                <td><?php echo htmlspecialchars($row['program_name'] ?? 'Not assigned'); ?></td>
                                <td><?php echo htmlspecialchars($row['qualifications']); ?></td>
                                <td><?php echo htmlspecialchars($row['experience']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                <td><span class="badge badge-success">Active</span></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" role="dialog" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailsModalLabel">Interviewer Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Personal Information</h6>
                        <p><strong>Name:</strong> <span id="modal-name"></span></p>
                        <p><strong>Email:</strong> <span id="modal-email"></span></p>
                        <p><strong>College:</strong> <span id="modal-college"></span></p>
                        <p><strong>Program:</strong> <span id="modal-program"></span></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Professional Information</h6>
                        <p><strong>Qualifications:</strong></p>
                        <p id="modal-qualifications"></p>
                        <p><strong>Experience:</strong></p>
                        <p id="modal-experience"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Approve Interviewer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to approve <strong id="approve-name"></strong> as an interviewer?</p>
                <form id="approveForm" method="post">
                    <input type="hidden" name="interviewer_id" id="approve-id">
                    <input type="hidden" name="action" value="approve">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" form="approveForm" class="btn btn-success">Approve</button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Reject Interviewer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to reject <strong id="reject-name"></strong> as an interviewer?</p>
                <form id="rejectForm" method="post">
                    <input type="hidden" name="interviewer_id" id="reject-id">
                    <input type="hidden" name="action" value="reject">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" form="rejectForm" class="btn btn-danger">Reject</button>
            </div>
        </div>
    </div>
</div>

<!-- Initialize DataTables and Modals -->
<script>
    $(document).ready(function() {
        // Function to initialize DataTable
        function initDataTable(tableId, emptyMessage) {
            var table = $('#' + tableId);
            
            // Destroy existing instance if it exists
            if ($.fn.DataTable.isDataTable(table)) {
                table.DataTable().destroy();
            }
            
            // Initialize new instance
            return table.DataTable({
                "order": [[0, "desc"]],
                "pageLength": 10,
                "language": {
                    "emptyTable": emptyMessage,
                    "search": "Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "infoFiltered": "(filtered from _MAX_ total entries)",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                },
                "destroy": true,
                "responsive": true,
                "autoWidth": false,
                "columnDefs": [
                    { "orderable": false, "targets": -1 } // Make last column (Actions) not sortable
                ]
            });
        }

        // Initialize tables
        initDataTable('pendingInterviewersTable', 'No pending interviewers found');
        initDataTable('activeInterviewersTable', 'No active interviewers found');

        // View Details Modal
        $('.view-details').click(function() {
            var name = $(this).data('name');
            var email = $(this).data('email');
            var college = $(this).data('college');
            var program = $(this).data('program');
            var qualifications = $(this).data('qualifications');
            var experience = $(this).data('experience');

            $('#modal-name').text(name);
            $('#modal-email').text(email);
            $('#modal-college').text(college);
            $('#modal-program').text(program);
            $('#modal-qualifications').text(qualifications);
            $('#modal-experience').text(experience);
        });

        // Approve Modal
        $('.approve-interviewer').click(function() {
            var id = $(this).data('id');
            var name = $(this).data('name');

            $('#approve-id').val(id);
            $('#approve-name').text(name);
        });

        // Reject Modal
        $('.reject-interviewer').click(function() {
            var id = $(this).data('id');
            var name = $(this).data('name');

            $('#reject-id').val(id);
            $('#reject-name').text(name);
        });
    });
</script> 