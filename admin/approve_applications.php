<?php
// Start session and include database connection

require_once "../config/database.php";

// Check database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch all pending applications with their details
$applications_query = "
    SELECT 
        u.user_id,
        a.first_name,
        a.middle_name,
        a.last_name,
        CONCAT(
            a.first_name,
            IF(a.middle_name IS NOT NULL AND a.middle_name != '', CONCAT(' ', a.middle_name), ''),
            ' ',
            a.last_name
        ) as full_name,
        u.email,
        u.status,
        a.applicant_id,
        a.birth_date,
        a.gender,
        a.mobile_number,
        p1.program_name as primary_program,
        p2.program_name as secondary_program
    FROM users u
    JOIN applicants a ON u.user_id = a.user_id
    LEFT JOIN programs p1 ON a.primary_program_id = p1.program_id
    LEFT JOIN programs p2 ON a.secondary_program_id = p2.program_id
    WHERE u.user_type = 'applicant' AND u.status = 'inactive'
    ORDER BY u.created_at DESC
";

$applications_result = mysqli_query($conn, $applications_query);
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Approve Applications</h1>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Pending Applications</h6>
            <div class="bulk-actions" style="display: none;">
                <button type="button" class="btn btn-success btn-sm" id="bulkApproveBtn">
                    <i class="fas fa-check"></i> Approve Selected
                </button>
                <button type="button" class="btn btn-danger btn-sm" id="bulkRejectBtn">
                    <i class="fas fa-times"></i> Reject Selected
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="applicationsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="30">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="selectAll">
                                    <label class="custom-control-label" for="selectAll"></label>
                                </div>
                            </th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Birth Date</th>
                            <th>Gender</th>
                            <th>Mobile Number</th>
                            <th>Primary Program</th>
                            <th>Secondary Program</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($application = mysqli_fetch_assoc($applications_result)): ?>
                            <tr>
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input application-checkbox" 
                                               id="app_<?php echo $application['user_id']; ?>"
                                               data-user-id="<?php echo $application['user_id']; ?>"
                                               data-name="<?php echo htmlspecialchars($application['full_name']); ?>">
                                        <label class="custom-control-label" for="app_<?php echo $application['user_id']; ?>"></label>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($application['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($application['email']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($application['birth_date'])); ?></td>
                                <td><?php echo ucfirst($application['gender']); ?></td>
                                <td><?php echo htmlspecialchars($application['mobile_number']); ?></td>
                                <td><?php echo htmlspecialchars($application['primary_program']); ?></td>
                                <td><?php echo htmlspecialchars($application['secondary_program']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm approve-btn" 
                                            data-user-id="<?php echo $application['user_id']; ?>"
                                            data-name="<?php echo htmlspecialchars($application['full_name']); ?>">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm reject-btn"
                                            data-user-id="<?php echo $application['user_id']; ?>"
                                            data-name="<?php echo htmlspecialchars($application['full_name']); ?>">
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

<script>
$(document).ready(function() {
    // Initialize DataTables
    var table = $('#applicationsTable').DataTable({
        "order": [[1, "asc"]], // Sort by name column
        "pageLength": 10,
        "columnDefs": [
            { "orderable": false, "targets": [0, -1] } // Disable sorting for checkbox and actions columns
        ]
    });

    // Handle select all checkbox
    $('#selectAll').change(function() {
        $('.application-checkbox').prop('checked', $(this).prop('checked'));
        updateBulkActionsVisibility();
    });

    // Handle individual checkboxes
    $(document).on('change', '.application-checkbox', function() {
        updateBulkActionsVisibility();
        // Update select all checkbox
        var allChecked = $('.application-checkbox:checked').length === $('.application-checkbox').length;
        $('#selectAll').prop('checked', allChecked);
    });

    // Show/hide bulk action buttons based on selection
    function updateBulkActionsVisibility() {
        var selectedCount = $('.application-checkbox:checked').length;
        $('.bulk-actions').toggle(selectedCount > 0);
    }

    // Bulk Approve
    $('#bulkApproveBtn').click(function() {
        var selectedIds = [];
        var selectedNames = [];
        
        $('.application-checkbox:checked').each(function() {
            selectedIds.push($(this).data('user-id'));
            selectedNames.push($(this).data('name'));
        });

        if (selectedIds.length === 0) return;

        Swal.fire({
            title: 'Approve Applications',
            html: `Are you sure you want to approve the following applications?<br><br>
                   <div class="text-left">
                   ${selectedNames.map(name => `<div>• ${name}</div>`).join('')}
                   </div>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, approve all!',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            backdrop: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Create a form and submit it
                var form = $('<form>', {
                    'method': 'POST',
                    'action': window.location.href
                });
                
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'action',
                    'value': 'bulk_approve'
                }));
                
                selectedIds.forEach(function(id) {
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'user_ids[]',
                        'value': id
                    }));
                });
                
                $('body').append(form);
                form.submit();
            }
        });
    });

    // Bulk Reject
    $('#bulkRejectBtn').click(function() {
        var selectedIds = [];
        var selectedNames = [];
        
        $('.application-checkbox:checked').each(function() {
            selectedIds.push($(this).data('user-id'));
            selectedNames.push($(this).data('name'));
        });

        if (selectedIds.length === 0) return;

        Swal.fire({
            title: 'Reject Applications',
            html: `Are you sure you want to reject the following applications?<br><br>
                   <div class="text-left">
                   ${selectedNames.map(name => `<div>• ${name}</div>`).join('')}
                   </div><br>
                   <span class="text-danger">This action cannot be undone.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, reject all!',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            backdrop: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Create a form and submit it
                var form = $('<form>', {
                    'method': 'POST',
                    'action': window.location.href
                });
                
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'action',
                    'value': 'bulk_reject'
                }));
                
                selectedIds.forEach(function(id) {
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'user_ids[]',
                        'value': id
                    }));
                });
                
                $('body').append(form);
                form.submit();
            }
        });
    });

    // Approve Application
    $(document).on('click', '.approve-btn', function() {
        var userId = $(this).data('user-id');
        var userName = $(this).data('name');

        Swal.fire({
            title: 'Approve Application',
            html: `Are you sure you want to approve the application of <strong>${userName}</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, approve it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            backdrop: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Create a form and submit it
                var form = $('<form>', {
                    'method': 'POST',
                    'action': window.location.href
                });
                
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'action',
                    'value': 'approve_application'
                }));
                
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'user_id',
                    'value': userId
                }));
                
                $('body').append(form);
                form.submit();
            }
        });
    });

    // Reject Application
    $(document).on('click', '.reject-btn', function() {
        var userId = $(this).data('user-id');
        var userName = $(this).data('name');

        Swal.fire({
            title: 'Reject Application',
            html: `Are you sure you want to reject the application of <strong>${userName}</strong>?<br><br><span class="text-danger">This action cannot be undone.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, reject it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            backdrop: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Create a form and submit it
                var form = $('<form>', {
                    'method': 'POST',
                    'action': window.location.href
                });
                
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'action',
                    'value': 'reject_application'
                }));
                
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'user_id',
                    'value': userId
                }));
                
                $('body').append(form);
                form.submit();
            }
        });
    });

    // Check for success message in session and display SweetAlert
    <?php if (isset($_SESSION['success']) && !empty($_SESSION['success'])): ?>
        Swal.fire({
            title: 'Success!',
            text: '<?php echo addslashes($_SESSION['success']); ?>',
            icon: 'success',
            confirmButtonText: 'OK'
        });
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
});
</script>

