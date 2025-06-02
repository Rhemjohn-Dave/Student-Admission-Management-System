<?php
session_start();
require_once "../config/database.php";

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    die("Unauthorized access");
}

if (!isset($_GET['student_id'])) {
    die("Student ID is required");
}

$student_id = $_GET['student_id'];

// Fetch detailed student information
$query = "
    SELECT 
        u.user_id,
        a.first_name,
        a.middle_name,
        a.last_name,
        CONCAT(a.first_name, 
            CASE WHEN a.middle_name IS NOT NULL AND a.middle_name != '' 
                THEN CONCAT(' ', a.middle_name) 
                ELSE '' 
            END,
            ' ', a.last_name) as full_name,
        u.email,
        u.status,
        a.applicant_id,
        a.birth_date,
        a.gender,
        a.mobile_number,
        a.address_lot,
        a.address_street,
        a.address_town,
        a.address_city,
        a.address_zipcode,
        a.mother_maiden_name,
        a.father_name,
        a.elementary_school,
        a.elementary_year_graduated,
        a.high_school,
        a.high_school_year_graduated,
        p1.program_name as primary_program,
        p2.program_name as secondary_program,
        es.exam_id,
        es.exam_date,
        es.exam_time,
        es.status as exam_status
    FROM users u
    JOIN applicants a ON u.user_id = a.user_id
    LEFT JOIN programs p1 ON a.primary_program_id = p1.program_id
    LEFT JOIN programs p2 ON a.secondary_program_id = p2.program_id
    LEFT JOIN exam_registrations er ON a.applicant_id = er.applicant_id
    LEFT JOIN exam_schedules es ON er.exam_schedule_id = es.exam_id
    WHERE u.user_id = ?
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

if (!$student) {
    die("Student not found");
}

// Format the address
$address = [];
if (!empty($student['address_lot'])) $address[] = $student['address_lot'];
if (!empty($student['address_street'])) $address[] = $student['address_street'];
if (!empty($student['address_town'])) $address[] = $student['address_town'];
if (!empty($student['address_city'])) $address[] = $student['address_city'];
if (!empty($student['address_zipcode'])) $address[] = $student['address_zipcode'];
$formatted_address = implode(', ', $address);
?>

<div class="row">
    <div class="col-md-6">
        <h5 class="mb-3">Personal Information</h5>
        <table class="table table-bordered">
            <tr>
                <th width="30%">Full Name</th>
                <td><?php echo htmlspecialchars($student['full_name']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($student['email']); ?></td>
            </tr>
            <tr>
                <th>Mobile Number</th>
                <td><?php echo htmlspecialchars($student['mobile_number']); ?></td>
            </tr>
            <tr>
                <th>Gender</th>
                <td><?php echo ucfirst($student['gender']); ?></td>
            </tr>
            <tr>
                <th>Birth Date</th>
                <td><?php echo date('F d, Y', strtotime($student['birth_date'])); ?></td>
            </tr>
            <tr>
                <th>Address</th>
                <td><?php echo htmlspecialchars($formatted_address); ?></td>
            </tr>
            <tr>
                <th>Mother's Maiden Name</th>
                <td><?php echo htmlspecialchars($student['mother_maiden_name']); ?></td>
            </tr>
            <tr>
                <th>Father's Name</th>
                <td><?php echo htmlspecialchars($student['father_name']); ?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <h5 class="mb-3">Educational Background</h5>
        <table class="table table-bordered">
            <tr>
                <th>Elementary School</th>
                <td><?php echo htmlspecialchars($student['elementary_school']); ?></td>
            </tr>
            <tr>
                <th>Year Graduated</th>
                <td><?php echo $student['elementary_year_graduated']; ?></td>
            </tr>
            <tr>
                <th>High School</th>
                <td><?php echo htmlspecialchars($student['high_school']); ?></td>
            </tr>
            <tr>
                <th>Year Graduated</th>
                <td><?php echo $student['high_school_year_graduated']; ?></td>
            </tr>
        </table>

        <h5 class="mb-3 mt-4">Program Choices</h5>
        <table class="table table-bordered">
            <tr>
                <th>Primary Program</th>
                <td><?php echo htmlspecialchars($student['primary_program']); ?></td>
            </tr>
            <?php if (!empty($student['secondary_program'])): ?>
            <tr>
                <th>Secondary Program</th>
                <td><?php echo htmlspecialchars($student['secondary_program']); ?></td>
            </tr>
            <?php endif; ?>
        </table>

        <h5 class="mb-3 mt-4">Exam Information</h5>
        <table class="table table-bordered">
            <?php if ($student['exam_id']): ?>
                <tr>
                    <th width="30%">Exam Date</th>
                    <td><?php echo date('F d, Y', strtotime($student['exam_date'])); ?></td>
                </tr>
                <tr>
                    <th>Exam Time</th>
                    <td><?php echo date('h:i A', strtotime($student['exam_time'])); ?></td>
                </tr>
                <tr>
                    <th>Exam Status</th>
                    <td>
                        <span class="badge badge-<?php 
                            echo $student['exam_status'] === 'scheduled' ? 'primary' : 
                                ($student['exam_status'] === 'completed' ? 'success' : 'warning');
                        ?>">
                            <?php echo ucfirst($student['exam_status']); ?>
                        </span>
                    </td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="2" class="text-center text-muted">No exam scheduled yet</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div> 