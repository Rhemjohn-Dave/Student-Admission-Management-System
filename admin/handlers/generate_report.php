<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../../config/database.php";

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("location: ../../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'exam_schedule':
            if (!isset($_POST['schedule_id'])) {
                die("Schedule ID is required");
            }

            $schedule_id = intval($_POST['schedule_id']);

            // Get schedule details
            $schedule_query = "
                SELECT 
                    es.exam_id,
                    es.exam_date,
                    es.exam_time,
                    es.venue,
                    es.max_participants,
                    es.status
                FROM exam_schedules es
                WHERE es.exam_id = ?
            ";

            if ($stmt = mysqli_prepare($conn, $schedule_query)) {
                mysqli_stmt_bind_param($stmt, "i", $schedule_id);
                mysqli_stmt_execute($stmt);
                $schedule_result = mysqli_stmt_get_result($stmt);
                $schedule = mysqli_fetch_assoc($schedule_result);
                mysqli_stmt_close($stmt);

                if ($schedule) {
                    // Get registered students with their program information
                    $students_query = "
                        SELECT 
                            a.first_name,
                            a.middle_name,
                            a.last_name,
                            a.gender,
                            a.mobile_number,
                            u.email,
                            p1.program_name as primary_program,
                            p2.program_name as secondary_program,
                            er.registration_date
                        FROM exam_registrations er
                        JOIN applicants a ON er.applicant_id = a.applicant_id
                        JOIN users u ON a.user_id = u.user_id
                        LEFT JOIN programs p1 ON a.primary_program_id = p1.program_id
                        LEFT JOIN programs p2 ON a.secondary_program_id = p2.program_id
                        WHERE er.exam_schedule_id = ?
                        AND er.status = 'registered'
                        ORDER BY a.last_name ASC, a.first_name ASC
                    ";

                    if ($stmt = mysqli_prepare($conn, $students_query)) {
                        mysqli_stmt_bind_param($stmt, "i", $schedule_id);
                        mysqli_stmt_execute($stmt);
                        $students_result = mysqli_stmt_get_result($stmt);
                        $students = [];
                        while ($student = mysqli_fetch_assoc($students_result)) {
                            $students[] = $student;
                        }
                        mysqli_stmt_close($stmt);

                        // Generate the report HTML
                        ?>
                        <!DOCTYPE html>
                        <html lang="en">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Exam Schedule Report - <?php echo date('F d, Y', strtotime($schedule['exam_date'])); ?></title>
                            <style>
                                @media print {
                                    @page {
                                        size: legal;
                                        margin: 1cm;
                                    }
                                    .no-print {
                                        display: none;
                                    }
                                    .header {
                                        margin-top: 0px;
                                    }
                                    html, body {
                                        margin: 0 !important;
                                        padding: 0 !important;
                                        height: 100%;
                                    }
                                }
                                body {
                                    font-family: Arial, sans-serif;
                                    line-height: 1.6;
                                    margin: 0;
                                    padding: 20px;
                                }
                                .header {
                                    text-align: center;
                                    margin-bottom: 30px;
                                }
                                .header img {
                                    max-width: 100px;
                                    height: auto;
                                }
                                .header h1 {
                                    margin: 10px 0;
                                    font-size: 24px;
                                }
                                .header p {
                                    margin: 5px 0;
                                    font-size: 16px;
                                }
                                .schedule-info {
                                    margin-bottom: 20px;
                                    padding: 15px;
                                    border: 1px solid #ddd;
                                    border-radius: 5px;
                                }
                                table {
                                    width: 100%;
                                    border-collapse: collapse;
                                    margin-bottom: 20px;
                                }
                                th, td {
                                    border: 1px solid #ddd;
                                    padding: 8px;
                                    text-align: left;
                                }
                                th {
                                    background-color: #f5f5f5;
                                }
                                .footer {
                                    margin-top: 30px;
                                    text-align: center;
                                    font-size: 12px;
                                    color: #666;
                                }
                                .print-button {
                                    position: fixed;
                                    top: 20px;
                                    right: 20px;
                                    padding: 10px 20px;
                                    background-color: #4CAF50;
                                    color: white;
                                    border: none;
                                    border-radius: 5px;
                                    cursor: pointer;
                                }
                                .print-button:hover {
                                    background-color: #45a049;
                                }
                            </style>
                        </head>
                        <body>
                            <button onclick="window.print()" class="print-button no-print">
                                Print Report
                            </button>

                            <div class="header" style="display: flex; align-items: center; border: 1px solid #000; padding: 10px; font-family: Arial, sans-serif;">
    <!-- Left: TUP Logo -->
    <div style="flex: 1; text-align: center;">
        <img src="../../assets/images/tuplogo.png" alt="TUP Logo" style="width: 70px;">
    </div>

    <!-- Center: University Info -->
    <div style="flex: 4; text-align: center;">
        <h2 style="margin: 0; font-size: 16px;">TECHNOLOGICAL UNIVERSITY OF THE PHILIPPINES VISAYAS</h2>
        <p style="margin: 2px 0; font-size: 14px;"><strong>Office of the Assistant Director for Academic Affairs</strong></p>
        <p style="margin: 2px 0; font-size: 12px;">
            Capt. Sabi St., Brgy. Zone 12, City of Talisay, Negros Occidental<br>
            Tel. No. (034) 445-2177 | <a href="http://www.tupvisayas.edu.ph">http://www.tupvisayas.edu.ph</a> | Email: visayas@tup.edu.ph
        </p>
        <p style="margin: 2px 0; font-size: 11px;"><em>"Our Quality Management System is Certified according to ISO9001"</em></p>
    </div>

    <!-- Right: Form Info -->
    <!-- <div style="flex: 1; font-size: 12px; text-align: left;">
        <p style="margin: 2px 0;"><strong>Form No.</strong><br>F-QMR-45B</p>
        <p style="margin: 2px 0;"><strong>Rev</strong><br>0</p>
        <p style="margin: 2px 0;"><strong>Date</strong><br>01 Jan 24</p>
    </div> -->
</div>


                            <div class="schedule-info">
                                <h3>Exam Details</h3>
                                <p><strong>Date:</strong> <?php echo date('F d, Y', strtotime($schedule['exam_date'])); ?></p>
                                <p><strong>Time:</strong> <?php echo date('h:i A', strtotime($schedule['exam_time'])); ?></p>
                                <p><strong>Venue:</strong> <?php echo htmlspecialchars($schedule['venue']); ?></p>
                                <p><strong>Maximum Participants:</strong> <?php echo $schedule['max_participants']; ?></p>
                                <p><strong>Total Registered:</strong> <?php echo count($students); ?> students</p>
                            </div>

                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Gender</th>
                                        <th>Contact Number</th>
                                        <th>Email</th>
                                        <th>Primary Program</th>
                                        <th>Secondary Program</th>
                                        <th>Registration Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $index => $student): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td>
                                            <?php 
                                                echo htmlspecialchars($student['last_name'] . ', ' . 
                                                    $student['first_name'] . 
                                                    ($student['middle_name'] ? ' ' . $student['middle_name'] : ''));
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($student['gender']); ?></td>
                                        <td><?php echo htmlspecialchars($student['mobile_number']); ?></td>
                                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                                        <td><?php echo htmlspecialchars($student['primary_program'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($student['secondary_program'] ?? 'N/A'); ?></td>
                                        <td><?php echo date('M d, Y h:i A', strtotime($student['registration_date'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div class="footer">
                                <p>Generated on: <?php echo date('F d, Y h:i A'); ?></p>
                                <p>This is a computer-generated document. No signature is required.</p>
                            </div>
                        </body>
                        </html>
                        <?php
                    } else {
                        die("Error preparing students query: " . mysqli_error($conn));
                    }
                } else {
                    die("Schedule not found");
                }
            } else {
                die("Error preparing schedule query: " . mysqli_error($conn));
            }
            break;

        case 'exam_rankings':
            $program_id = isset($_POST['program_id']) ? intval($_POST['program_id']) : null;
            $ranking_type = $_POST['ranking_type'] ?? 'overall';

            // Build the rankings query based on type
            if ($ranking_type === 'overall') {
                $rankings_query = "
                    SELECT * FROM (
                        SELECT 
                            a.applicant_id,
                            a.first_name,
                            a.middle_name,
                            a.last_name,
                            p1.program_name as primary_program,
                            p2.program_name as secondary_program,
                            esc.score as exam_score,
                            RANK() OVER (ORDER BY esc.score DESC) as overall_rank
                        FROM applicants a
                        JOIN users u ON a.user_id = u.user_id
                        JOIN programs p1 ON a.primary_program_id = p1.program_id
                        LEFT JOIN programs p2 ON a.secondary_program_id = p2.program_id
                        LEFT JOIN exam_registrations reg ON a.applicant_id = reg.applicant_id
                        LEFT JOIN exam_scores esc ON reg.registration_id = esc.registration_id
                        WHERE esc.score IS NOT NULL
                        " . ($program_id ? "AND (a.primary_program_id = $program_id OR a.secondary_program_id = $program_id)" : "") . "
                    ) ranked
                    ORDER BY ranked.overall_rank
                ";
            } else {
                $rankings_query = "
                    SELECT 
                        a.applicant_id,
                        a.first_name,
                        a.middle_name,
                        a.last_name,
                        p.program_name,
                        p1.program_name as primary_program,
                        p2.program_name as secondary_program,
                        er.score as exam_score,
                        RANK() OVER (
                            PARTITION BY p.program_id 
                            ORDER BY er.score DESC
                        ) as program_rank,
                        pc.start_rank,
                        pc.end_rank,
                        CASE 
                            WHEN program_rank >= pc.start_rank AND program_rank <= pc.end_rank THEN 'Eligible'
                            ELSE 'Not Eligible'
                        END as status
                    FROM applicants a
                    JOIN users u ON a.user_id = u.user_id
                    JOIN programs p ON a.primary_program_id = p.program_id
                    LEFT JOIN programs p1 ON a.primary_program_id = p1.program_id
                    LEFT JOIN programs p2 ON a.secondary_program_id = p2.program_id
                    LEFT JOIN exam_results er ON u.user_id = er.user_id
                    LEFT JOIN program_cutoffs pc ON p.program_id = pc.program_id
                    WHERE er.score IS NOT NULL
                    " . ($program_id ? "AND p.program_id = $program_id" : "") . "
                    ORDER BY p.program_name, program_rank
                ";
            }

            $rankings_result = mysqli_query($conn, $rankings_query);
            if (!$rankings_result) {
                die("Error fetching rankings: " . mysqli_error($conn));
            }

            $rankings = [];
            while ($row = mysqli_fetch_assoc($rankings_result)) {
                $rankings[] = $row;
            }

            // Generate the report HTML
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Exam Rankings Report - <?php echo $ranking_type === 'overall' ? 'Overall Rankings' : 'Program Rankings'; ?></title>
                <style>
                    @media print {
                                    @page {
                                        size: legal;
                                        margin: 1cm;
                                    }
                                    .no-print {
                                        display: none;
                                    }
                                    .header {
                                        margin-top: 0px;
                                    }
                                    html, body {
                                        margin: 0 !important;
                                        padding: 0 !important;
                                        height: 100%;
                                    }
                                }
                    body {
                        font-family: Arial, sans-serif;
                        line-height: 1.6;
                        margin: 0;
                        padding: 20px;
                    }
                    .header {
                        text-align: center;
                        margin-bottom: 30px;
                    }
                    .header img {
                        max-width: 100px;
                        height: auto;
                    }
                    .header h1 {
                        margin: 10px 0;
                        font-size: 24px;
                    }
                    .header p {
                        margin: 5px 0;
                        font-size: 16px;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 20px;
                    }
                    th, td {
                        border: 1px solid #ddd;
                        padding: 8px;
                        text-align: left;
                    }
                    th {
                        background-color: #f5f5f5;
                    }
                    .footer {
                        margin-top: 30px;
                        text-align: center;
                        font-size: 12px;
                        color: #666;
                    }
                    .print-button {
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        padding: 10px 20px;
                        background-color: #4CAF50;
                        color: white;
                        border: none;
                        border-radius: 5px;
                        cursor: pointer;
                    }
                    .print-button:hover {
                        background-color: #45a049;
                    }
                    .status-eligible {
                        color: #28a745;
                        font-weight: bold;
                    }
                    .status-not-eligible {
                        color: #dc3545;
                        font-weight: bold;
                    }
                </style>
            </head>
            <body>
                <button onclick="window.print()" class="print-button no-print">
                    Print Report
                </button>

                <div class="header" style="display: flex; align-items: center; border: 1px solid #000; padding: 10px; font-family: Arial, sans-serif;">
                    <!-- Left: TUP Logo -->
                    <div style="flex: 1; text-align: center;">
                        <img src="../../assets/images/tuplogo.png" alt="TUP Logo" style="width: 70px;">
                    </div>

                    <!-- Center: University Info -->
                    <div style="flex: 4; text-align: center;">
                        <h2 style="margin: 0; font-size: 16px;">TECHNOLOGICAL UNIVERSITY OF THE PHILIPPINES VISAYAS</h2>
                        <p style="margin: 2px 0; font-size: 14px;"><strong>Office of the Assistant Director for Academic Affairs</strong></p>
                        <p style="margin: 2px 0; font-size: 12px;">
                            Capt. Sabi St., Brgy. Zone 12, City of Talisay, Negros Occidental<br>
                            Tel. No. (034) 445-2177 | <a href="http://www.tupvisayas.edu.ph">http://www.tupvisayas.edu.ph</a> | Email: visayas@tup.edu.ph
                        </p>
                        <p style="margin: 2px 0; font-size: 11px;"><em>"Our Quality Management System is Certified according to ISO9001"</em></p>
                    </div>
                </div>

                <h2 style="text-align: center; margin: 20px 0;">
                    <?php echo $ranking_type === 'overall' ? 'Overall Exam Rankings' : 'Program-Specific Exam Rankings'; ?>
                    <?php 
                    if ($program_id) {
                        $program_name = mysqli_fetch_assoc(mysqli_query($conn, "SELECT program_name FROM programs WHERE program_id = $program_id"))['program_name'];
                        echo " - " . htmlspecialchars($program_name);
                    }
                    ?>
                </h2>

                <table>
                    <thead>
                        <tr>
                            <?php if ($ranking_type === 'overall'): ?>
                                <th>Overall Rank</th>
                                <th>Student Name</th>
                                <th>Primary Program</th>
                                <th>Secondary Program</th>
                                <th>Exam Score</th>
                            <?php else: ?>
                                <th>Program</th>
                                <th>Program Rank</th>
                                <th>Student Name</th>
                                <th>Primary Program</th>
                                <th>Secondary Program</th>
                                <th>Exam Score</th>
                                <th>Status</th>
                                <th>Cutoff Rank</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rankings as $ranking): ?>
                        <tr>
                            <?php if ($ranking_type === 'overall'): ?>
                                <td><?php echo $ranking['overall_rank']; ?></td>
                                <td>
                                    <?php 
                                    echo htmlspecialchars($ranking['last_name'] . ', ' . 
                                        $ranking['first_name'] . 
                                        ($ranking['middle_name'] ? ' ' . $ranking['middle_name'] : '')); 
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($ranking['primary_program']); ?></td>
                                <td><?php echo htmlspecialchars($ranking['secondary_program'] ?? 'N/A'); ?></td>
                                <td><?php echo number_format($ranking['exam_score'], 2); ?></td>
                            <?php else: ?>
                                <td><?php echo htmlspecialchars($ranking['program_name']); ?></td>
                                <td><?php echo $ranking['program_rank']; ?></td>
                                <td>
                                    <?php 
                                    echo htmlspecialchars($ranking['last_name'] . ', ' . 
                                        $ranking['first_name'] . 
                                        ($ranking['middle_name'] ? ' ' . $ranking['middle_name'] : '')); 
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($ranking['primary_program']); ?></td>
                                <td><?php echo htmlspecialchars($ranking['secondary_program'] ?? 'N/A'); ?></td>
                                <td><?php echo number_format($ranking['exam_score'], 2); ?></td>
                                <td class="<?php echo $ranking['status'] === 'Eligible' ? 'status-eligible' : 'status-not-eligible'; ?>">
                                    <?php echo $ranking['status']; ?>
                                </td>
                                <td><?php echo isset($ranking['start_rank'], $ranking['end_rank']) ? $ranking['start_rank'] . '–' . $ranking['end_rank'] : 'N/A'; ?></td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="footer">
                    <p>Generated on: <?php echo date('F d, Y h:i A'); ?></p>
                    <p>This is a computer-generated document. No signature is required.</p>
                </div>
            </body>
            </html>
            <?php
            break;

        case 'interview_schedule':
            $program_id = isset($_POST['program_id']) ? intval($_POST['program_id']) : null;
            $schedule_id = isset($_POST['interview_schedule']) ? intval($_POST['interview_schedule']) : null;

            if (!$schedule_id) {
                die("Interview schedule ID is required");
            }

            // Get interview schedule details
            $schedule_query = "
                SELECT 
                    s.schedule_id,
                    s.interview_date,
                    s.time_window,
                    s.max_applicants,
                    s.current_applicants,
                    s.status as schedule_status,
                    p.program_name,
                    CONCAT(u.first_name, ' ', u.last_name) as interviewer_name,
                    u.email as interviewer_email
                FROM interview_schedules s
                JOIN programs p ON s.program_id = p.program_id
                JOIN program_heads ph ON p.program_id = ph.program_id
                JOIN users u ON ph.user_id = u.user_id
                WHERE s.schedule_id = ?
                LIMIT 1
            ";

            if ($stmt = mysqli_prepare($conn, $schedule_query)) {
                mysqli_stmt_bind_param($stmt, "i", $schedule_id);
                mysqli_stmt_execute($stmt);
                $schedule_result = mysqli_stmt_get_result($stmt);
                $schedule = mysqli_fetch_assoc($schedule_result);
                mysqli_stmt_close($stmt);

                if ($schedule) {
                    // Get all interviews for this schedule with results
                    $students_query = "
                        SELECT 
                            a.first_name,
                            a.middle_name,
                            a.last_name,
                            a.gender,
                            a.mobile_number,
                            u.email,
                            p.program_name,
                            i.created_at as scheduled_at
                        FROM interviews i
                        JOIN applications app ON i.application_id = app.application_id
                        JOIN applicants a ON app.user_id = a.user_id
                        JOIN users u ON a.user_id = u.user_id
                        JOIN programs p ON (a.primary_program_id = p.program_id OR a.secondary_program_id = p.program_id)
                        JOIN interview_schedules s ON s.program_id = p.program_id 
                            AND s.interview_date = i.scheduled_date
                            AND s.time_window = i.scheduled_time
                        WHERE s.schedule_id = ?
                        " . ($program_id ? "AND p.program_id = " . $program_id : "") . "
                        ORDER BY a.last_name ASC, a.first_name ASC
                    ";

                    if ($stmt = mysqli_prepare($conn, $students_query)) {
                        mysqli_stmt_bind_param($stmt, "i", $schedule_id);
                        mysqli_stmt_execute($stmt);
                        $students_result = mysqli_stmt_get_result($stmt);
                        $students = [];
                        while ($student = mysqli_fetch_assoc($students_result)) {
                            $students[] = $student;
                        }
                        mysqli_stmt_close($stmt);

                        // Debug: Log the query results
                        error_log("Query returned " . count($students) . " students");
                        if (!empty($students)) {
                            error_log("First student data: " . print_r($students[0], true));
                        }

                        // Generate the report HTML
                        ?>
                        <!DOCTYPE html>
                        <html lang="en">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Interview Schedule Report - <?php echo date('F d, Y', strtotime($schedule['interview_date'])); ?></title>
                            <style>
                                @media print {
                                    @page {
                                        size: legal;
                                        margin: 1cm;
                                    }
                                    .no-print {
                                        display: none;
                                    }
                                    .header {
                                        margin-top: 0px;
                                    }
                                    html, body {
                                        margin: 0 !important;
                                        padding: 0 !important;
                                        height: 100%;
                                    }
                                }
                                body {
                                    font-family: Arial, sans-serif;
                                    line-height: 1.6;
                                    margin: 0;
                                    padding: 20px;
                                }
                                .header {
                                    text-align: center;
                                    margin-bottom: 30px;
                                }
                                .header img {
                                    max-width: 100px;
                                    height: auto;
                                }
                                .header h1 {
                                    margin: 10px 0;
                                    font-size: 24px;
                                }
                                .header p {
                                    margin: 5px 0;
                                    font-size: 16px;
                                }
                                .schedule-info {
                                    margin-bottom: 20px;
                                    padding: 15px;
                                    border: 1px solid #ddd;
                                    border-radius: 5px;
                                }
                                table {
                                    width: 100%;
                                    border-collapse: collapse;
                                    margin-bottom: 20px;
                                    font-size: 14px;
                                }
                                th, td {
                                    border: 1px solid #ddd;
                                    padding: 8px;
                                    text-align: left;
                                }
                                th {
                                    background-color: #f5f5f5;
                                    font-weight: bold;
                                }
                                td {
                                    vertical-align: top;
                                }
                                .footer {
                                    margin-top: 30px;
                                    text-align: center;
                                    font-size: 12px;
                                    color: #666;
                                }
                                .print-button {
                                    position: fixed;
                                    top: 20px;
                                    right: 20px;
                                    padding: 10px 20px;
                                    background-color: #4CAF50;
                                    color: white;
                                    border: none;
                                    border-radius: 5px;
                                    cursor: pointer;
                                }
                                .print-button:hover {
                                    background-color: #45a049;
                                }
                                .schedule-status-open {
                                    color: #28a745;
                                    font-weight: bold;
                                }
                                .schedule-status-closed {
                                    color: #dc3545;
                                    font-weight: bold;
                                }
                                .schedule-status-completed {
                                    color: #17a2b8;
                                    font-weight: bold;
                                }
                            </style>
                        </head>
                        <body>
                            <button onclick="window.print()" class="print-button no-print">
                                Print Report
                            </button>

                            <div class="header" style="display: flex; align-items: center; border: 1px solid #000; padding: 10px; font-family: Arial, sans-serif;">
                                <!-- Left: TUP Logo -->
                                <div style="flex: 1; text-align: center;">
                                    <img src="../../assets/images/tuplogo.png" alt="TUP Logo" style="width: 70px;">
                                </div>

                                <!-- Center: University Info -->
                                <div style="flex: 4; text-align: center;">
                                    <h2 style="margin: 0; font-size: 16px;">TECHNOLOGICAL UNIVERSITY OF THE PHILIPPINES VISAYAS</h2>
                                    <p style="margin: 2px 0; font-size: 14px;"><strong>Office of the Assistant Director for Academic Affairs</strong></p>
                                    <p style="margin: 2px 0; font-size: 12px;">
                                        Capt. Sabi St., Brgy. Zone 12, City of Talisay, Negros Occidental<br>
                                        Tel. No. (034) 445-2177 | <a href="http://www.tupvisayas.edu.ph">http://www.tupvisayas.edu.ph</a> | Email: visayas@tup.edu.ph
                                    </p>
                                    <p style="margin: 2px 0; font-size: 11px;"><em>"Our Quality Management System is Certified according to ISO9001"</em></p>
                                </div>
                            </div>

                            <div class="schedule-info">
                                <h3>Interview Schedule Details</h3>
                                <p><strong>Date:</strong> <?php echo date('F d, Y', strtotime($schedule['interview_date'])); ?></p>
                                <p><strong>Time Window:</strong> <?php echo $schedule['time_window']; ?></p>
                                <p><strong>Program:</strong> <?php echo htmlspecialchars($schedule['program_name']); ?></p>
                                <p><strong>Interviewer:</strong> <?php echo htmlspecialchars($schedule['interviewer_name']); ?></p>
                                <p><strong>Interviewer Email:</strong> <?php echo htmlspecialchars($schedule['interviewer_email']); ?></p>
                                <p><strong>Schedule Status:</strong> 
                                    <span class="schedule-status-<?php echo strtolower($schedule['schedule_status']); ?>">
                                        <?php echo ucfirst($schedule['schedule_status']); ?>
                                    </span>
                                </p>
                                <p><strong>Capacity:</strong> <?php echo $schedule['current_applicants']; ?> / <?php echo $schedule['max_applicants']; ?> applicants</p>
                            </div>

                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">#</th>
                                            <th style="width: 25%;">Student Name</th>
                                            <th style="width: 10%;">Gender</th>
                                            <th style="width: 15%;">Contact Number</th>
                                            <th style="width: 20%;">Email</th>
                                            <th style="width: 15%;">Program</th>
                                            <th style="width: 10%;">Scheduled At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($students)): ?>
                                            <tr>
                                                <td colspan="7" style="text-align: center;">No students found for this schedule.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($students as $index => $student): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td>
                                                    <?php 
                                                        echo htmlspecialchars(
                                                            $student['last_name'] . ', ' . 
                                                            $student['first_name'] . 
                                                            ($student['middle_name'] ? ' ' . $student['middle_name'] : '')
                                                        );
                                                    ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($student['gender']); ?></td>
                                                <td><?php echo htmlspecialchars($student['mobile_number']); ?></td>
                                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                                <td><?php echo htmlspecialchars($student['program_name']); ?></td>
                                                <td><?php echo date('M d, Y h:i A', strtotime($student['scheduled_at'])); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="footer">
                                <p>Generated on: <?php echo date('F d, Y h:i A'); ?></p>
                                <p>This is a computer-generated document. No signature is required.</p>
                            </div>
                        </body>
                        </html>
                        <?php
                    } else {
                        die("Error preparing students query: " . mysqli_error($conn));
                    }
                } else {
                    die("Interview schedule not found");
                }
            } else {
                die("Error preparing schedule query: " . mysqli_error($conn));
            }
            break;

        case 'final_rankings':
            $program_id = isset($_POST['program_id']) ? intval($_POST['program_id']) : null;

            if (!$program_id) {
                die("Program ID is required");
            }

            // Get program details
            $program_query = "SELECT program_name FROM programs WHERE program_id = ?";
            if ($stmt = mysqli_prepare($conn, $program_query)) {
                mysqli_stmt_bind_param($stmt, "i", $program_id);
                mysqli_stmt_execute($stmt);
                $program_result = mysqli_stmt_get_result($stmt);
                $program = mysqli_fetch_assoc($program_result);
                mysqli_stmt_close($stmt);

                if ($program) {
                    // Get final rankings with both exam and interview scores, only one row per applicant
                    $rankings_query = "
                        SELECT * FROM (
                            SELECT 
                                a.applicant_id,
                                a.first_name,
                                a.middle_name,
                                a.last_name,
                                a.gender,
                                p.program_name,
                                esc.score as exam_score,
                                -- Only get the highest scored interview for this applicant/program
                                (
                                    SELECT i2.score FROM interviews i2
                                    JOIN applications app2 ON i2.application_id = app2.application_id
                                    WHERE app2.user_id = a.user_id AND app2.program_id = p.program_id AND i2.status = 'completed'
                                    ORDER BY i2.score DESC, i2.completed_date DESC LIMIT 1
                                ) as interview_score,
                                (
                                    SELECT i2.result FROM interviews i2
                                    JOIN applications app2 ON i2.application_id = app2.application_id
                                    WHERE app2.user_id = a.user_id AND app2.program_id = p.program_id AND i2.status = 'completed'
                                    ORDER BY i2.score DESC, i2.completed_date DESC LIMIT 1
                                ) as interview_result,
                                ROUND(
                                    (COALESCE(esc.score, 0) * 0.75) + 
                                    ((COALESCE((
                                        SELECT i2.score FROM interviews i2
                                        JOIN applications app2 ON i2.application_id = app2.application_id
                                        WHERE app2.user_id = a.user_id AND app2.program_id = p.program_id AND i2.status = 'completed'
                                        ORDER BY i2.score DESC, i2.completed_date DESC LIMIT 1
                                    ), 0) / 25 * 100) * 0.25), 
                                    2
                                ) as final_score,
                                RANK() OVER (
                                    ORDER BY (
                                        (COALESCE(esc.score, 0) * 0.75) + 
                                        ((COALESCE((
                                            SELECT i2.score FROM interviews i2
                                            JOIN applications app2 ON i2.application_id = app2.application_id
                                            WHERE app2.user_id = a.user_id AND app2.program_id = p.program_id AND i2.status = 'completed'
                                            ORDER BY i2.score DESC, i2.completed_date DESC LIMIT 1
                                        ), 0) / 25 * 100) * 0.25)
                                    ) DESC
                                ) as overall_rank,
                                pc.start_rank,
                                pc.end_rank,
                                'Qualified' as final_status
                            FROM applicants a
                            JOIN exam_registrations reg ON a.applicant_id = reg.applicant_id
                            JOIN programs p ON a.primary_program_id = p.program_id
                            LEFT JOIN exam_scores esc ON reg.registration_id = esc.registration_id
                            LEFT JOIN program_cutoffs pc ON p.program_id = pc.program_id
                            WHERE p.program_id = ?
                            AND esc.score IS NOT NULL
                        ) ranked
                        WHERE ranked.overall_rank >= ranked.start_rank AND ranked.overall_rank <= ranked.end_rank
                        ORDER BY ranked.final_score DESC, ranked.overall_rank ASC
                    ";

                    if ($stmt = mysqli_prepare($conn, $rankings_query)) {
                        mysqli_stmt_bind_param($stmt, "i", $program_id);
                        mysqli_stmt_execute($stmt);
                        $rankings_result = mysqli_stmt_get_result($stmt);
                        $rankings = [];
                        while ($ranking = mysqli_fetch_assoc($rankings_result)) {
                            $rankings[] = $ranking;
                        }
                        mysqli_stmt_close($stmt);

                        // Generate the report HTML
                        ?>
                        <!DOCTYPE html>
                        <html lang="en">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Final Rankings Report - <?php echo htmlspecialchars($program['program_name']); ?></title>
                            <style>
                                @media print {
                                    @page {
                                        size: legal;
                                        margin: 1cm;
                                    }
                                    .no-print {
                                        display: none;
                                    }
                                    .header {
                                        margin-top: 0px;
                                    }
                                    html, body {
                                        margin: 0 !important;
                                        padding: 0 !important;
                                        height: 100%;
                                    }
                                }
                                body {
                                    font-family: Arial, sans-serif;
                                    line-height: 1.6;
                                    margin: 0;
                                    padding: 20px;
                                }
                                .header {
                                    text-align: center;
                                    margin-bottom: 30px;
                                }
                                table {
                                    width: 100%;
                                    border-collapse: collapse;
                                    margin-bottom: 20px;
                                    font-size: 14px;
                                }
                                th, td {
                                    border: 1px solid #ddd;
                                    padding: 8px;
                                    text-align: left;
                                }
                                th {
                                    background-color: #f5f5f5;
                                    font-weight: bold;
                                }
                                .status-qualified {
                                    color: #28a745;
                                    font-weight: bold;
                                }
                                .status-not-qualified {
                                    color: #dc3545;
                                    font-weight: bold;
                                }
                                .result-passed {
                                    color: #28a745;
                                    font-weight: bold;
                                }
                                .result-failed {
                                    color: #dc3545;
                                    font-weight: bold;
                                }
                                .result-pending {
                                    color: #ffc107;
                                    font-weight: bold;
                                }
                                .print-button {
                                    position: fixed;
                                    top: 20px;
                                    right: 20px;
                                    padding: 10px 20px;
                                    background-color: #4CAF50;
                                    color: white;
                                    border: none;
                                    border-radius: 5px;
                                    cursor: pointer;
                                }
                                .print-button:hover {
                                    background-color: #45a049;
                                }
                                .stats-box {
                                    background-color: #f8f9fa;
                                    border: 1px solid #dee2e6;
                                    border-radius: 4px;
                                    padding: 15px;
                                    margin-bottom: 20px;
                                }
                                .stats-grid {
                                    display: grid;
                                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                                    gap: 15px;
                                    margin-top: 10px;
                                }
                                .stat-item {
                                    text-align: center;
                                    padding: 10px;
                                    background-color: white;
                                    border-radius: 4px;
                                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                                }
                                .stat-item .value {
                                    font-size: 24px;
                                    font-weight: bold;
                                    color: #2c3e50;
                                }
                                .stat-item .label {
                                    font-size: 14px;
                                    color: #6c757d;
                                }
                            </style>
                        </head>
                        <body>
                            <button onclick="window.print()" class="print-button no-print">
                                Print Report
                            </button>

                            <div class="header" style="display: flex; align-items: center; border: 1px solid #000; padding: 10px; font-family: Arial, sans-serif;">
                                <!-- Left: TUP Logo -->
                                <div style="flex: 1; text-align: center;">
                                    <img src="../../assets/images/tuplogo.png" alt="TUP Logo" style="width: 70px;">
                                </div>

                                <!-- Center: University Info -->
                                <div style="flex: 4; text-align: center;">
                                    <h2 style="margin: 0; font-size: 16px;">TECHNOLOGICAL UNIVERSITY OF THE PHILIPPINES VISAYAS</h2>
                                    <p style="margin: 2px 0; font-size: 14px;"><strong>Office of the Assistant Director for Academic Affairs</strong></p>
                                    <p style="margin: 2px 0; font-size: 12px;">
                                        Capt. Sabi St., Brgy. Zone 12, City of Talisay, Negros Occidental<br>
                                        Tel. No. (034) 445-2177 | <a href="http://www.tupvisayas.edu.ph">http://www.tupvisayas.edu.ph</a> | Email: visayas@tup.edu.ph
                                    </p>
                                    <p style="margin: 2px 0; font-size: 11px;"><em>"Our Quality Management System is Certified according to ISO9001"</em></p>
                                </div>
                            </div>

                            <h2 style="text-align: center; margin: 20px 0;">
                                Final Rankings Report - <?php echo htmlspecialchars($program['program_name']); ?>
                            </h2>

                            <div class="stats-box">
                                <h4>Program Statistics</h4>
                                <div class="stats-grid">
                                    <div class="stat-item">
                                        <div class="value"><?php echo count($rankings); ?></div>
                                        <div class="label">Total Applicants</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="value">
                                            <?php 
                                            echo count(array_filter($rankings, function($r) { 
                                                return $r['final_status'] === 'Qualified'; 
                                            }));
                                            ?>
                                        </div>
                                        <div class="label">Qualified Applicants</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="value">
                                            <?php 
                                            $exam_count = count($rankings);
                                            $avg_exam = $exam_count > 0 ? array_sum(array_column($rankings, 'exam_score')) / $exam_count : 0;
                                            echo $exam_count > 0 ? number_format($avg_exam, 2) : 'N/A';
                                            ?>
                                        </div>
                                        <div class="label">Average Exam Score</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="value">
                                            <?php 
                                            $avg_interview = 0;
                                            if ($exam_count > 0) {
                                                $interview_sum = array_sum(array_map(function($r) {
                                                    return $r['interview_score'] ? ($r['interview_score'] / 25 * 100) : 0;
                                                }, $rankings));
                                                $avg_interview = $interview_sum / $exam_count;
                                            }
                                            echo $exam_count > 0 ? number_format($avg_interview, 2) : 'N/A';
                                            ?>
                                        </div>
                                        <div class="label">Average Interview Score</div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">Rank</th>
                                            <th style="width: 25%;">Student Name</th>
                                            <th style="width: 10%;">Gender</th>
                                            <th style="width: 10%;">Exam Score</th>
                                            <th style="width: 10%;">Interview Score</th>
                                            <th style="width: 10%;">Final Score</th>
                                            <th style="width: 10%;">Interview Result</th>
                                            <th style="width: 10%;">Status</th>
                                            <th style="width: 10%;">Cutoff Rank</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rankings as $ranking): ?>
                                        <tr>
                                            <td><?php echo $ranking['overall_rank']; ?></td>
                                            <td>
                                                <?php 
                                                    echo htmlspecialchars($ranking['last_name'] . ', ' . 
                                                        $ranking['first_name'] . 
                                                        ($ranking['middle_name'] ? ' ' . $ranking['middle_name'] : ''));
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($ranking['gender']); ?></td>
                                            <td><?php echo number_format($ranking['exam_score'], 2); ?></td>
                                            <td>
                                                <?php 
                                                if ($ranking['interview_score'] !== null) {
                                                    echo number_format(($ranking['interview_score'] / 25 * 100), 2);
                                                } else {
                                                    echo 'Not yet rated';
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo number_format($ranking['final_score'], 2); ?></td>
                                            <td class="result-<?php echo strtolower($ranking['interview_result']); ?>">
                                                <?php echo $ranking['interview_result']; ?>
                                            </td>
                                            <td class="status-<?php echo strtolower(str_replace(' ', '-', $ranking['final_status'])); ?>">
                                                <?php echo $ranking['final_status']; ?>
                                            </td>
                                            <td><?php echo isset($ranking['start_rank'], $ranking['end_rank']) ? $ranking['start_rank'] . '–' . $ranking['end_rank'] : 'N/A'; ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="footer">
                                <p>Generated on: <?php echo date('F d, Y h:i A'); ?></p>
                                <p>This is a computer-generated document. No signature is required.</p>
                            </div>
                        </body>
                        </html>
                        <?php
                    } else {
                        die("Error preparing rankings query: " . mysqli_error($conn));
                    }
                } else {
                    die("Program not found");
                }
            } else {
                die("Error preparing program query: " . mysqli_error($conn));
            }
            break;

        default:
            die("Invalid action");
    }
} else {
    die("Invalid request");
}
?> 