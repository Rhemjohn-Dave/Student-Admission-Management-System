<?php
// Start the session
session_start();

// Check if user is logged in and is an admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_type"] !== "admin"){
    die("Unauthorized access");
}

// Include database connection
require_once "../config/database.php";

if(isset($_POST['schedule_id'])) {
    $schedule_id = $_POST['schedule_id'];
    
    // Debug: Log the schedule_id
    error_log("Fetching applicants for schedule_id: " . $schedule_id);
    
    // Fetch applicants for this interview schedule
    $sql = "SELECT 
                i.*,
                a.first_name,
                a.middle_name,
                a.last_name,
                u.email,
                p.program_name,
                app.application_id,
                app.status as application_status
            FROM interviews i
            INNER JOIN applications app ON i.application_id = app.application_id
            INNER JOIN applicants a ON app.user_id = a.user_id
            INNER JOIN users u ON app.user_id = u.user_id
            INNER JOIN programs p ON app.program_id = p.program_id
            WHERE i.scheduled_date = (
                SELECT interview_date 
                FROM interview_schedules 
                WHERE schedule_id = ?
            )
            ORDER BY i.created_at DESC";
            
    // Debug: Log the SQL query
    error_log("SQL Query: " . $sql);
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $schedule_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        // Debug: Log the number of rows found
        error_log("Number of applicants found: " . mysqli_num_rows($result));
        
        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                // Debug: Log each row
                error_log("Applicant data: " . print_r($row, true));
                
                echo "<tr>";
                // Format name with middle initial if available
                $fullName = $row['first_name'];
                if (!empty($row['middle_name'])) {
                    $fullName .= ' ' . substr($row['middle_name'], 0, 1) . '.';
                }
                $fullName .= ' ' . $row['last_name'];
                echo "<td>" . htmlspecialchars($fullName) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['program_name']) . "</td>";
                echo "<td>";
                switch($row['status']) {
                    case 'scheduled':
                        echo "<span class='badge bg-warning'>Scheduled</span>";
                        break;
                    case 'completed':
                        if($row['result'] == 'passed') {
                            echo "<span class='badge bg-success'>Done</span>";
                        } else if($row['result'] == 'failed') {
                            echo "<span class='badge bg-danger'>Failed</span>";
                        } else {
                            echo "<span class='badge bg-info'>Completed</span>";
                        }
                        break;
                    case 'cancelled':
                        echo "<span class='badge bg-secondary'>Cancelled</span>";
                        break;
                    default:
                        echo "<span class='badge bg-secondary'>Unknown</span>";
                }
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4' class='text-center'>No applicants found for this schedule.</td></tr>";
        }
    } else {
        error_log("Error preparing statement: " . mysqli_error($conn));
        echo "<tr><td colspan='4' class='text-center text-danger'>Error loading applicants: " . mysqli_error($conn) . "</td></tr>";
    }
} else {
    echo "<tr><td colspan='4' class='text-center text-danger'>Invalid request.</td></tr>";
}
?> 