<?php
require_once "../config/database.php";

// Get all programs for dropdown
$programs_query = "SELECT program_id, program_name, college_id FROM programs ORDER BY program_name";
$programs_result = mysqli_query($conn, $programs_query);

// Get all colleges for grouping
$colleges_query = "SELECT college_id, college_name FROM colleges ORDER BY college_name";
$colleges_result = mysqli_query($conn, $colleges_query);

// Fetch all students with their current program choices and exam results
$students_query = "
    SELECT 
        a.applicant_id,
        a.user_id,
        a.first_name,
        a.middle_name,
        a.last_name,
        CONCAT(a.last_name, ', ', 
            CASE WHEN a.middle_name IS NOT NULL AND a.middle_name != '' 
                THEN CONCAT(a.middle_name, ' ') 
                ELSE '' 
            END,
            a.first_name) as full_name,
        u.email,
        a.primary_program_id,
        a.secondary_program_id,
        p1.program_name as primary_program_name,
        p2.program_name as secondary_program_name,
        c1.college_name as primary_college,
        c2.college_name as secondary_college,
        es.score as exam_score,
        i.score as interview_score,
        i.status as interview_status,
        i.result as interview_result,
        pr.rank_position,
        pr.is_eligible,
        pc.start_rank,
        pc.end_rank,
        CASE 
            WHEN pr.rank_position >= pc.start_rank AND pr.rank_position <= pc.end_rank THEN 'Eligible'
            WHEN pr.rank_position IS NOT NULL THEN 'Not Eligible'
            ELSE 'Pending'
        END as cutoff_status
    FROM applicants a
    JOIN users u ON a.user_id = u.user_id
    LEFT JOIN programs p1 ON a.primary_program_id = p1.program_id
    LEFT JOIN programs p2 ON a.secondary_program_id = p2.program_id
    LEFT JOIN colleges c1 ON p1.college_id = c1.college_id
    LEFT JOIN colleges c2 ON p2.college_id = c2.college_id
    LEFT JOIN exam_registrations er ON a.applicant_id = er.applicant_id
    LEFT JOIN exam_scores es ON er.registration_id = es.registration_id
    LEFT JOIN applications app ON a.user_id = app.user_id
    LEFT JOIN interviews i ON app.application_id = i.application_id
    LEFT JOIN program_rankings pr ON a.applicant_id = pr.applicant_id AND a.primary_program_id = pr.program_id
    LEFT JOIN program_cutoffs pc ON a.primary_program_id = pc.program_id
    WHERE u.user_type = 'applicant' AND u.status = 'active'
    ORDER BY a.last_name, a.first_name ASC
";

$students_result = mysqli_query($conn, $students_query);
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Manage Student Programs</h1>
    
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
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Student Program Management</h6>
            <small class="text-muted">Edit primary and secondary program choices for students who don't make the cutoff</small>
        </div>
        <div class="card-body">
            <!-- Filter Controls -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="statusFilter">Filter by Status:</label>
                    <select class="form-control" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="Eligible">Eligible</option>
                        <option value="Not Eligible">Not Eligible</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="programFilter">Filter by Program:</label>
                    <select class="form-control" id="programFilter">
                        <option value="">All Programs</option>
                        <?php 
                        mysqli_data_seek($programs_result, 0);
                        while ($program = mysqli_fetch_assoc($programs_result)): 
                        ?>
                            <option value="<?php echo htmlspecialchars($program['program_name']); ?>">
                                <?php echo htmlspecialchars($program['program_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="scoreFilter">Filter by Exam Score:</label>
                    <select class="form-control" id="scoreFilter">
                        <option value="">All Scores</option>
                        <option value="high">High (80-100)</option>
                        <option value="medium">Medium (60-79)</option>
                        <option value="low">Low (0-59)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-secondary btn-block" id="clearFilters">
                        <i class="fas fa-times"></i> Clear Filters
                    </button>
                </div>
            </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="studentProgramsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Current Primary Program</th>
                            <th>Current Secondary Program</th>
                            <th>Exam Score</th>
                            <th>Interview Score</th>
                            <th>Rank Position</th>
                            <th>Cutoff Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($student = mysqli_fetch_assoc($students_result)): ?>
                            <tr class="<?php echo $student['cutoff_status'] == 'Not Eligible' ? 'table-warning' : ($student['cutoff_status'] == 'Eligible' ? 'table-success' : ''); ?>">
                                <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td>
                                    <?php if ($student['primary_program_name']): ?>
                                        <strong><?php echo htmlspecialchars($student['primary_program_name']); ?></strong>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($student['primary_college'] ?? 'N/A'); ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">Not set</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($student['secondary_program_name']): ?>
                                        <?php echo htmlspecialchars($student['secondary_program_name']); ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($student['secondary_college'] ?? 'N/A'); ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">Not set</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($student['exam_score']): ?>
                                        <?php echo number_format($student['exam_score'], 2); ?>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($student['interview_score']): ?>
                                        <?php echo number_format($student['interview_score'], 2); ?>/25
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($student['rank_position']): ?>
                                        #<?php echo $student['rank_position']; ?>
                                        <?php if ($student['start_rank'] && $student['end_rank']): ?>
                                            <br><small class="text-muted">Cutoff: #<?php echo $student['start_rank']; ?> - #<?php echo $student['end_rank']; ?></small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?php 
                                        echo $student['cutoff_status'] == 'Eligible' ? 'success' : 
                                            ($student['cutoff_status'] == 'Not Eligible' ? 'danger' : 'warning'); 
                                    ?>">
                                        <?php echo $student['cutoff_status']; ?>
                                    </span>
                                </td>
                                                                 <td>
                                     <button type="button" class="btn btn-primary btn-sm" 
                                             data-toggle="modal" 
                                             data-target="#editProgramsModal"
                                             data-applicant-id="<?php echo $student['applicant_id']; ?>"
                                             data-primary-program="<?php echo $student['primary_program_id'] ?? ''; ?>"
                                             data-secondary-program="<?php echo $student['secondary_program_id'] ?? ''; ?>"
                                             data-student-name="<?php echo htmlspecialchars($student['full_name']); ?>">
                                         <i class="fas fa-edit"></i> Edit Programs
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

<!-- Edit Programs Modal -->
<div class="modal fade" id="editProgramsModal" tabindex="-1" role="dialog" aria-labelledby="editProgramsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProgramsModalLabel">Edit Student Programs</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="handlers/manage_student_programs_handler.php">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_programs">
                    <input type="hidden" name="applicant_id" id="editApplicantId">
                    
                    <div class="form-group">
                        <label><strong>Student:</strong> <span id="editStudentName"></span></label>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="primary_program_id">Primary Program <span class="text-danger">*</span></label>
                                <select class="form-control" id="primary_program_id" name="primary_program_id" required>
                                    <option value="">Select Primary Program</option>
                                    <?php 
                                    mysqli_data_seek($programs_result, 0);
                                    while ($program = mysqli_fetch_assoc($programs_result)): 
                                    ?>
                                        <option value="<?php echo $program['program_id']; ?>">
                                            <?php echo htmlspecialchars($program['program_name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="secondary_program_id">Secondary Program</label>
                                <select class="form-control" id="secondary_program_id" name="secondary_program_id">
                                    <option value="">Select Secondary Program (Optional)</option>
                                    <?php 
                                    mysqli_data_seek($programs_result, 0);
                                    while ($program = mysqli_fetch_assoc($programs_result)): 
                                    ?>
                                        <option value="<?php echo $program['program_id']; ?>">
                                            <?php echo htmlspecialchars($program['program_name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> Changing a student's program choices will affect their ranking and eligibility for different programs. 
                        Make sure to consider the student's exam scores and interview results when making changes.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Programs</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#studentProgramsTable').DataTable({
        order: [[0, 'asc']],
        pageLength: 25,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    
    // Handle edit programs modal
    $('#editProgramsModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var applicantId = button.data('applicant-id');
        var primaryProgram = button.data('primary-program');
        var secondaryProgram = button.data('secondary-program');
        var studentName = button.data('student-name');
        
        var modal = $(this);
        modal.find('#editApplicantId').val(applicantId);
        modal.find('#editStudentName').text(studentName);
        modal.find('#primary_program_id').val(primaryProgram);
        modal.find('#secondary_program_id').val(secondaryProgram);
    });
    
    // Prevent selecting same program for primary and secondary
    $('#primary_program_id, #secondary_program_id').on('change', function() {
        var primary = $('#primary_program_id').val();
        var secondary = $('#secondary_program_id').val();
        
        if (primary && secondary && primary === secondary) {
            alert('Primary and Secondary programs cannot be the same.');
            $(this).val('');
        }
    });
    
    // Filter functionality
    $('#statusFilter').on('change', function() {
        var status = $(this).val();
        if (status) {
            table.column(7).search(status).draw();
        } else {
            table.column(7).search('').draw();
        }
    });
    
    $('#programFilter').on('change', function() {
        var program = $(this).val();
        if (program) {
            table.column(2).search(program).draw();
        } else {
            table.column(2).search('').draw();
        }
    });
    
    $('#scoreFilter').on('change', function() {
        var score = $(this).val();
        if (score) {
            var minScore, maxScore;
            switch(score) {
                case 'high':
                    minScore = 80;
                    maxScore = 100;
                    break;
                case 'medium':
                    minScore = 60;
                    maxScore = 79;
                    break;
                case 'low':
                    minScore = 0;
                    maxScore = 59;
                    break;
            }
            
            // Custom filtering for score range
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var score = parseFloat(data[4]) || 0; // Column 4 is exam score
                return score >= minScore && score <= maxScore;
            });
            table.draw();
            
            // Remove the custom filter
            $.fn.dataTable.ext.search.pop();
        } else {
            table.draw();
        }
    });
    
    // Clear all filters
    $('#clearFilters').on('click', function() {
        $('#statusFilter, #programFilter, #scoreFilter').val('');
        table.search('').columns().search('').draw();
    });
    
    // Add custom filtering for score ranges
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        var scoreFilter = $('#scoreFilter').val();
        if (!scoreFilter) return true;
        
        var score = parseFloat(data[4]) || 0; // Column 4 is exam score
        switch(scoreFilter) {
            case 'high':
                return score >= 80 && score <= 100;
            case 'medium':
                return score >= 60 && score <= 79;
            case 'low':
                return score >= 0 && score <= 59;
            default:
                return true;
        }
    });
});
</script> 