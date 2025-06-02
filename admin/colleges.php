<?php


// Include database connection
require_once "../config/database.php";

// Check database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch all colleges
$colleges_query = "SELECT * FROM colleges ORDER BY college_name";
$colleges = mysqli_query($conn, $colleges_query);
if (!$colleges) {
    die("Error fetching colleges: " . mysqli_error($conn));
}
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Colleges Management</h1>
    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addCollegeModal">
        <i class="fas fa-plus"></i> Add College
    </button>
</div>

<!-- Content Row -->
<div class="row">
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <!-- Card Header -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">List of Colleges</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="collegesTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>College Name</th>
                                <th>College Code</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($colleges)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['college_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['college_code']); ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm edit-college" 
                                            data-toggle="modal" data-target="#editCollegeModal"
                                            data-id="<?php echo $row['college_id']; ?>"
                                            data-name="<?php echo htmlspecialchars($row['college_name']); ?>"
                                            data-code="<?php echo htmlspecialchars($row['college_code']); ?>"
                                            data-description="<?php echo htmlspecialchars($row['description']); ?>"
                                            title="Edit College">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm delete-college"
                                            data-id="<?php echo $row['college_id']; ?>"
                                            data-name="<?php echo htmlspecialchars($row['college_name']); ?>"
                                            title="Delete College">
                                        <i class="fas fa-trash"></i>
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
</div>

<!-- Add College Modal -->
<div class="modal fade" id="addCollegeModal" tabindex="-1" role="dialog" aria-labelledby="addCollegeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCollegeModalLabel">Add New College</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="handle_college.php" id="addCollegeForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_college">
                    <div class="form-group">
                        <label for="college_name">College Name</label>
                        <input type="text" class="form-control" id="college_name" name="college_name" required>
                    </div>
                    <div class="form-group">
                        <label for="college_code">College Code</label>
                        <input type="text" class="form-control" id="college_code" name="college_code" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add College</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit College Modal -->
<div class="modal fade" id="editCollegeModal" tabindex="-1" role="dialog" aria-labelledby="editCollegeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCollegeModalLabel">Edit College</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="handle_college.php" id="editCollegeForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_college">
                    <input type="hidden" name="college_id" id="edit_college_id">
                    <div class="form-group">
                        <label for="edit_college_name">College Name</label>
                        <input type="text" class="form-control" id="edit_college_name" name="college_name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_college_code">College Code</label>
                        <input type="text" class="form-control" id="edit_college_code" name="college_code" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_description">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update College</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Initialize DataTables and SweetAlert2 -->
<script>
$(document).ready(function() {
    // Initialize DataTables
    var table = $('#collegesTable').DataTable({
        "order": [[0, "asc"]], // Sort by college name
        "pageLength": 10,
        "columnDefs": [
            { "orderable": false, "targets": -1 } // Make actions column non-sortable
        ],
        "language": {
            "emptyTable": "No colleges found",
            "info": "Showing _START_ to _END_ of _TOTAL_ colleges",
            "infoEmpty": "Showing 0 to 0 of 0 colleges",
            "infoFiltered": "(filtered from _MAX_ total colleges)",
            "lengthMenu": "Show _MENU_ colleges per page",
            "search": "Search colleges:",
            "zeroRecords": "No matching colleges found"
        }
    });

    // Add College Modal
    $('#addCollegeModal').on('show.bs.modal', function () {
        $('#addCollegeForm')[0].reset();
        $('#addCollegeForm .is-invalid').removeClass('is-invalid');
    });

    // Edit College Modal
    $(document).on('click', '.edit-college', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var code = $(this).data('code');
        var description = $(this).data('description');

        $('#edit_college_id').val(id);
        $('#edit_college_name').val(name);
        $('#edit_college_code').val(code);
        $('#edit_description').val(description);
        
        $('#editCollegeModal').modal('show');
    });

    // Delete College with SweetAlert2
    $(document).on('click', '.delete-college', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');

        Swal.fire({
            title: 'Delete College',
            html: `Are you sure you want to delete the college "<strong>${name}</strong>"?<br><br>
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
                    url: 'handle_college.php',
                    type: 'POST',
                    data: {
                        action: 'delete_college',
                        college_id: id
                    },
                    success: function(response) {
                        processingAlert.close();
                        if (response.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'College has been deleted successfully.',
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
                                text: response.message || 'An error occurred while deleting the college.',
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

    // Form validation with SweetAlert2
    $('#addCollegeForm, #editCollegeForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var required = form.find('[required]');
        var valid = true;
        var errorMessage = '';

        required.each(function() {
            if (!$(this).val()) {
                valid = false;
                $(this).addClass('is-invalid');
                errorMessage = 'Please fill in all required fields.';
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!valid) {
            Swal.fire({
                title: 'Validation Error',
                text: errorMessage,
                icon: 'error',
                confirmButtonColor: '#3085d6',
                allowOutsideClick: false,
                allowEscapeKey: false,
                backdrop: true,
                showConfirmButton: true,
                showCloseButton: true
            });
            return false;
        }

        // Show processing alert
        let processingAlert = Swal.fire({
            title: 'Processing...',
            text: 'Please wait while we process your request.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            showConfirmButton: false,
            backdrop: true,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Submit form via AJAX
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            beforeSend: function() {
                // Ensure processing alert is shown
                if (!Swal.isVisible()) {
                    processingAlert = Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while we process your request.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                        showConfirmButton: false,
                        backdrop: true,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                }
            },
            success: function(response) {
                // Close processing alert
                Swal.close();
                
                // Show success message
                Swal.fire({
                    title: 'Success!',
                    text: 'Operation completed successfully',
                    icon: 'success',
                    confirmButtonColor: '#3085d6',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    backdrop: true,
                    showConfirmButton: true,
                    showCloseButton: true
                }).then(() => {
                    // Reload page after success message is closed
                    window.location.reload();
                });
            },
            error: function() {
                // Close processing alert
                Swal.close();
                
                // Show error message
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while processing your request.',
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    backdrop: true,
                    showConfirmButton: true,
                    showCloseButton: true
                });
            }
        });
    });

    // Show success/error messages from PHP using SweetAlert2
    <?php if (isset($_SESSION['success'])): ?>
        Swal.fire({
            title: 'Success!',
            text: '<?php echo $_SESSION['success']; ?>',
            icon: 'success',
            confirmButtonColor: '#3085d6',
            allowOutsideClick: false,
            allowEscapeKey: false,
            backdrop: true,
            showConfirmButton: true,
            showCloseButton: true
        });
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        Swal.fire({
            title: 'Error!',
            text: '<?php echo $_SESSION['error']; ?>',
            icon: 'error',
            confirmButtonColor: '#3085d6',
            allowOutsideClick: false,
            allowEscapeKey: false,
            backdrop: true,
            showConfirmButton: true,
            showCloseButton: true
        });
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    // Initialize tooltips
    $('[title]').tooltip();
});
</script> 