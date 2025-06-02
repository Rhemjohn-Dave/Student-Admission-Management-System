<?php
// Include header


// Include database connection
require_once "../config/database.php";

// Check database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch all programs with college information
$programs_query = "
    SELECT p.*, c.college_name, c.college_code,
           CONCAT(u.first_name, ' ', u.last_name) as program_head_name
    FROM programs p 
    LEFT JOIN colleges c ON p.college_id = c.college_id 
    LEFT JOIN program_heads ph ON p.program_head_id = ph.program_head_id
    LEFT JOIN users u ON ph.user_id = u.user_id
    ORDER BY c.college_name, p.program_name
";

// Debug: Check if the query is valid
if (!$programs_query) {
    $_SESSION['error'] = "Invalid query";
} else {
    $programs = mysqli_query($conn, $programs_query);
    
    // Debug: Check for query errors
    if (!$programs) {
        $_SESSION['error'] = "Error fetching programs: " . mysqli_error($conn);
        $programs = array();
    } else {
        // Debug: Check number of rows
        $num_rows = mysqli_num_rows($programs);
        if ($num_rows === 0) {
            $_SESSION['error'] = "No programs found in the database.";
        } else {
            // Debug: Check for programs with missing colleges
            $programs_data = array();
            while ($row = mysqli_fetch_assoc($programs)) {
                if ($row['college_id'] === null) {
                    $_SESSION['error'] = "Some programs are not associated with any college. Please check the college associations.";
                }
                $programs_data[] = $row;
            }
            // Reset the pointer for the main display
            mysqli_data_seek($programs, 0);
        }
    }
}

// Fetch all colleges for the dropdown
$colleges_query = "SELECT * FROM colleges ORDER BY college_name";
$colleges = mysqli_query($conn, $colleges_query);
if (!$colleges) {
    $_SESSION['error'] = "Error fetching colleges: " . mysqli_error($conn);
    $colleges = array();
} else {
    $num_colleges = mysqli_num_rows($colleges);
    if ($num_colleges === 0) {
        $_SESSION['error'] = "No colleges found in the database. Please add colleges first.";
    }
}

// Fetch all program heads for the dropdown
$program_heads_query = "
    SELECT ph.*, u.first_name, u.last_name 
    FROM program_heads ph 
    JOIN users u ON ph.user_id = u.user_id 
    ORDER BY u.last_name, u.first_name
";
$program_heads = mysqli_query($conn, $program_heads_query);

// Debug: Check database connection
if (!$conn) {
    $_SESSION['error'] = "Database connection failed";
}
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Programs Management</h1>
    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addProgramModal">
        <i class="fas fa-plus"></i> Add Program
    </button>
</div>

<!-- Content Row -->
<div class="row">
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <!-- Card Header -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">List of Programs</h6>
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
                    <table class="table table-bordered" id="programsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Program Name</th>
                                <th>Program Code</th>
                                <th>College</th>
                                <th>Program Head</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($programs) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($programs)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['program_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['program_code']); ?></td>
                                    <td><?php echo htmlspecialchars($row['college_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['program_head_name']); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm edit-program" 
                                                data-toggle="modal" data-target="#editProgramModal"
                                                data-id="<?php echo $row['program_id']; ?>"
                                                data-name="<?php echo htmlspecialchars($row['program_name']); ?>"
                                                data-code="<?php echo htmlspecialchars($row['program_code']); ?>"
                                                data-college="<?php echo $row['college_id']; ?>"
                                                data-head="<?php echo $row['program_head_id']; ?>"
                                                title="Edit Program">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm delete-program"
                                                data-id="<?php echo $row['program_id']; ?>"
                                                data-name="<?php echo htmlspecialchars($row['program_name']); ?>"
                                                title="Delete Program">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No programs found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Program Modal -->
<div class="modal fade" id="addProgramModal" tabindex="-1" role="dialog" aria-labelledby="addProgramModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProgramModalLabel">Add New Program</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="handle_program.php" id="addProgramForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_program">
                    <div class="form-group">
                        <label for="program_name">Program Name</label>
                        <input type="text" class="form-control" id="program_name" name="program_name" required>
                    </div>
                    <div class="form-group">
                        <label for="program_code">Program Code</label>
                        <input type="text" class="form-control" id="program_code" name="program_code" required>
                    </div>
                    <div class="form-group">
                        <label for="college_id">College</label>
                        <select class="form-control" id="college_id" name="college_id" required>
                            <option value="">Select College</option>
                            <?php while($row = mysqli_fetch_assoc($colleges)): ?>
                                <option value="<?php echo $row['college_id']; ?>">
                                    <?php echo htmlspecialchars($row['college_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="program_head_id">Program Head</label>
                        <select class="form-control" id="program_head_id" name="program_head_id" required>
                            <option value="">Select Program Head</option>
                            <?php 
                            mysqli_data_seek($program_heads, 0);
                            while($row = mysqli_fetch_assoc($program_heads)): 
                            ?>
                                <option value="<?php echo $row['program_head_id']; ?>">
                                    <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Program</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Program Modal -->
<div class="modal fade" id="editProgramModal" tabindex="-1" role="dialog" aria-labelledby="editProgramModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProgramModalLabel">Edit Program</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="handle_program.php" id="editProgramForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_program">
                    <input type="hidden" name="program_id" id="edit_program_id">
                    <div class="form-group">
                        <label for="edit_program_name">Program Name</label>
                        <input type="text" class="form-control" id="edit_program_name" name="program_name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_program_code">Program Code</label>
                        <input type="text" class="form-control" id="edit_program_code" name="program_code" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_college_id">College</label>
                        <select class="form-control" id="edit_college_id" name="college_id" required>
                            <option value="">Select College</option>
                            <?php 
                            mysqli_data_seek($colleges, 0);
                            while($row = mysqli_fetch_assoc($colleges)): 
                            ?>
                                <option value="<?php echo $row['college_id']; ?>">
                                    <?php echo htmlspecialchars($row['college_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_program_head_id">Program Head</label>
                        <select class="form-control" id="edit_program_head_id" name="program_head_id" required>
                            <option value="">Select Program Head</option>
                            <?php 
                            mysqli_data_seek($program_heads, 0);
                            while($row = mysqli_fetch_assoc($program_heads)): 
                            ?>
                                <option value="<?php echo $row['program_head_id']; ?>">
                                    <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Program</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Initialize DataTables and SweetAlert2 -->
<script>
$(document).ready(function() {
    // Initialize DataTables
    var table = $('#programsTable').DataTable({
        "order": [[0, "asc"]], // Sort by program name
        "pageLength": 10,
        "columnDefs": [
            { "orderable": false, "targets": -1 } // Make actions column non-sortable
        ],
        "language": {
            "emptyTable": "No programs found",
            "info": "Showing _START_ to _END_ of _TOTAL_ programs",
            "infoEmpty": "Showing 0 to 0 of 0 programs",
            "infoFiltered": "(filtered from _MAX_ total programs)",
            "lengthMenu": "Show _MENU_ programs per page",
            "search": "Search programs:",
            "zeroRecords": "No matching programs found"
        }
    });

    // Add Program Modal
    $('#addProgramModal').on('show.bs.modal', function () {
        $('#addProgramForm')[0].reset();
        $('#addProgramForm .is-invalid').removeClass('is-invalid');
    });

    // Edit Program Modal
    $(document).on('click', '.edit-program', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var code = $(this).data('code');
        var college = $(this).data('college');
        var head = $(this).data('head');

        $('#edit_program_id').val(id);
        $('#edit_program_name').val(name);
        $('#edit_program_code').val(code);
        $('#edit_college_id').val(college);
        $('#edit_program_head_id').val(head);
        
        $('#editProgramModal').modal('show');
    });

    // Delete Program with SweetAlert2
    $(document).on('click', '.delete-program', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');

        Swal.fire({
            title: 'Delete Program',
            html: `Are you sure you want to delete the program "<strong>${name}</strong>"?<br><br>
                  <span class="text-danger">This action cannot be undone.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            backdrop: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show processing alert
                let processingAlert = Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait while we process your request.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    backdrop: true,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit form via AJAX
                $.ajax({
                    url: 'handle_program.php',
                    type: 'POST',
                    data: {
                        action: 'delete_program',
                        program_id: id
                    },
                    success: function(response) {
                        processingAlert.close();
                        if (response.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Program has been deleted successfully.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'An error occurred while deleting the program.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function() {
                        processingAlert.close();
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while processing your request.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    });
});
</script> 