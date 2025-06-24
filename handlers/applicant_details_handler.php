<?php
// Include database connection
require_once "../config/database.php";

if (isset($_GET['applicant_id']) && !empty($_GET['applicant_id'])) {
    $applicant_id = filter_var($_GET['applicant_id'], FILTER_VALIDATE_INT);

    if ($applicant_id === false) {
        echo '<div class="alert alert-danger">Invalid applicant ID.</div>';
        exit;
    }

    // Query to get applicant details
    $query = "
        SELECT 
            a.applicant_id,
            a.user_id,
            a.first_name,
            a.middle_name,
            a.last_name,
            a.birth_date, 
            a.gender,
            a.mobile_number,  
            a.address_lot,
            a.address_street,
            a.address_town,
            a.address_city,
            a.address_country,
            a.address_zipcode,
            u.username, 
            u.email, 
            u.created_at as user_created_at, 
            u.status as user_account_status, 
            p_primary.program_name as primary_program_name,
            p_secondary.program_name as secondary_program_name
        FROM applicants a
        JOIN users u ON a.user_id = u.user_id
        LEFT JOIN programs p_primary ON a.primary_program_id = p_primary.program_id
        LEFT JOIN programs p_secondary ON a.secondary_program_id = p_secondary.program_id
        WHERE a.applicant_id = ?
    ";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $applicant_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($applicant = $result->fetch_assoc()) {
            // Personal Information Section
            echo '<div class="card mb-4">';
            echo '  <div class="card-header bg-primary text-white">';
            echo '    <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>Personal Information</h5>';
            echo '  </div>';
            echo '  <div class="card-body">';
            echo '    <div class="row">';
            echo '      <div class="col-md-6">';
            echo '        <div class="mb-3">';
            echo '          <label class="text-muted small">Full Name</label>';
            echo '          <p class="mb-0 fw-bold">' . htmlspecialchars($applicant['first_name'] . ' ' . $applicant['middle_name'] . ' ' . $applicant['last_name']) . '</p>';
            echo '        </div>';
            echo '        <div class="mb-3">';
            echo '          <label class="text-muted small">Date of Birth</label>';
            echo '          <p class="mb-0 fw-bold">' . htmlspecialchars($applicant['birth_date'] ? date('F d, Y', strtotime($applicant['birth_date'])) : 'N/A') . '</p>';
            echo '        </div>';
            echo '        <div class="mb-3">';
            echo '          <label class="text-muted small">Gender</label>';
            echo '          <p class="mb-0 fw-bold">' . htmlspecialchars(ucfirst($applicant['gender']) ?? 'N/A') . '</p>';
            echo '        </div>';
            echo '      </div>';
            echo '      <div class="col-md-6">';
            echo '        <div class="mb-3">';
            echo '          <label class="text-muted small">Contact Number</label>';
            echo '          <p class="mb-0 fw-bold">' . htmlspecialchars($applicant['mobile_number'] ?? 'N/A') . '</p>';
            echo '        </div>';
            echo '        <div class="mb-3">';
            echo '          <label class="text-muted small">Email Address</label>';
            echo '          <p class="mb-0 fw-bold">' . htmlspecialchars($applicant['email']) . '</p>';
            echo '        </div>';
            echo '        <div class="mb-3">';
            echo '          <label class="text-muted small">Account Status</label>';
            echo '          <p class="mb-0"><span class="badge bg-' . ($applicant['user_account_status'] == 'active' ? 'success' : 'warning') . '">' . 
                htmlspecialchars(ucfirst($applicant['user_account_status'])) . '</span></p>';
            echo '        </div>';
            echo '      </div>';
            echo '    </div>';
            echo '  </div>';
            echo '</div>';

            // Address Section
            echo '<div class="card mb-4">';
            echo '  <div class="card-header bg-info text-white">';
            echo '    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Address Information</h5>';
            echo '  </div>';
            echo '  <div class="card-body">';
            $full_address = [];
            if (!empty($applicant['address_lot'])) $full_address[] = $applicant['address_lot'];
            if (!empty($applicant['address_street'])) $full_address[] = $applicant['address_street'];
            if (!empty($applicant['address_town'])) $full_address[] = $applicant['address_town'];
            if (!empty($applicant['address_city'])) $full_address[] = $applicant['address_city'];
            if (!empty($applicant['address_country'])) $full_address[] = $applicant['address_country'];
            if (!empty($applicant['address_zipcode'])) $full_address[] = $applicant['address_zipcode'];
            
            echo '    <div class="mb-3">';
            echo '      <label class="text-muted small">Complete Address</label>';
            echo '      <p class="mb-0 fw-bold">' . htmlspecialchars(implode(', ', $full_address) ?: 'N/A') . '</p>';
            echo '    </div>';
            echo '  </div>';
            echo '</div>';

            // Program Preferences Section
            echo '<div class="card mb-4">';
            echo '  <div class="card-header bg-success text-white">';
            echo '    <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Program Preferences</h5>';
            echo '  </div>';
            echo '  <div class="card-body">';
            echo '    <div class="row">';
            echo '      <div class="col-md-6">';
            echo '        <div class="mb-3">';
            echo '          <label class="text-muted small">Primary Program</label>';
            echo '          <p class="mb-0 fw-bold">' . htmlspecialchars($applicant['primary_program_name'] ?: 'N/A') . '</p>';
            echo '        </div>';
            echo '      </div>';
            echo '      <div class="col-md-6">';
            echo '        <div class="mb-3">';
            echo '          <label class="text-muted small">Secondary Program</label>';
            echo '          <p class="mb-0 fw-bold">' . htmlspecialchars($applicant['secondary_program_name'] ?: 'N/A') . '</p>';
            echo '        </div>';
            echo '      </div>';
            echo '    </div>';
            echo '  </div>';
            echo '</div>';

            // Exam Results Section
            $exam_result_query = "
                SELECT 
                    esc.score, 
                    esc.status as result_status,
                    es.exam_date,
                    es.exam_time,
                    es.venue
                FROM exam_registrations reg
                JOIN exam_scores esc ON reg.registration_id = esc.registration_id
                JOIN exam_schedules es ON reg.exam_schedule_id = es.exam_id
                WHERE reg.applicant_id = ?
                ORDER BY es.exam_date DESC LIMIT 1
            ";
            if ($exam_stmt = $conn->prepare($exam_result_query)) {
                $exam_stmt->bind_param("i", $applicant_id);
                $exam_stmt->execute();
                $exam_result = $exam_stmt->get_result();
                
                echo '<div class="card mb-4">';
                echo '  <div class="card-header bg-warning text-dark">';
                echo '    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Latest Exam Result</h5>';
                echo '  </div>';
                echo '  <div class="card-body">';
                
                if ($exam_data = $exam_result->fetch_assoc()) {
                    echo '    <div class="row">';
                    echo '      <div class="col-md-6">';
                    echo '        <div class="mb-3">';
                    echo '          <label class="text-muted small">Exam Grade</label>';
                    echo '          <h3 class="mb-0">' . htmlspecialchars($exam_data['score'] ?? 'N/A') . '%</h3>';
                    echo '        </div>';
                    echo '      </div>';
                    echo '      <div class="col-md-6">';
                    echo '        <div class="mb-3">';
                    echo '          <label class="text-muted small">Exam Details</label>';
                    echo '          <p class="mb-1"><i class="fas fa-calendar-alt me-2"></i>' . 
                        htmlspecialchars($exam_data['exam_date'] ? date('F d, Y', strtotime($exam_data['exam_date'])) : 'N/A') . '</p>';
                    echo '          <p class="mb-1"><i class="fas fa-clock me-2"></i>' . 
                        htmlspecialchars($exam_data['exam_time'] ? date('h:i A', strtotime($exam_data['exam_time'])) : 'N/A') . '</p>';
                    echo '          <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>' . 
                        htmlspecialchars($exam_data['venue'] ?? 'N/A') . '</p>';
                    echo '        </div>';
                    echo '      </div>';
                    echo '    </div>';
                } else {
                    echo '    <div class="alert alert-info mb-0">';
                    echo '      <i class="fas fa-info-circle me-2"></i>No exam results found.';
                    echo '    </div>';
                }
                echo '  </div>';
                echo '</div>';
                $exam_stmt->close();
            }

            // Account Information Section
            echo '<div class="card">';
            echo '  <div class="card-header bg-secondary text-white">';
            echo '    <h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>Account Information</h5>';
            echo '  </div>';
            echo '  <div class="card-body">';
            echo '    <div class="row">';
            echo '      <div class="col-md-6">';
            echo '        <div class="mb-3">';
            echo '          <label class="text-muted small">Username</label>';
            echo '          <p class="mb-0 fw-bold">' . htmlspecialchars($applicant['username']) . '</p>';
            echo '        </div>';
            echo '      </div>';
            echo '      <div class="col-md-6">';
            echo '        <div class="mb-3">';
            echo '          <label class="text-muted small">Registration Date</label>';
            echo '          <p class="mb-0 fw-bold">' . htmlspecialchars($applicant['user_created_at'] ? date('F d, Y', strtotime($applicant['user_created_at'])) : 'N/A') . '</p>';
            echo '        </div>';
            echo '      </div>';
            echo '    </div>';
            echo '  </div>';
            echo '</div>';

        } else {
            echo '<div class="alert alert-danger">';
            echo '  <i class="fas fa-exclamation-circle me-2"></i>Applicant details not found.';
            echo '</div>';
        }
        $stmt->close();
    } else {
        echo '<div class="alert alert-danger">Error preparing query: ' . $conn->error . '</div>';
    }
} else {
    echo '<div class="alert alert-danger">No applicant ID provided.</div>';
}
$conn->close();
?> 