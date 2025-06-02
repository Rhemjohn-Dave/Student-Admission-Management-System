<?php
// Include database connection
require_once "../config/database.php";

// Check if schedule_id is provided
if (isset($_POST['schedule_id'])) {
    $schedule_id = $_POST['schedule_id'];
    
    // Query to get applicants for the selected schedule
    $query = "
        SELECT 
            er.registration_id,
            a.first_name,
            a.last_name,
            u.email,
            p.program_name,
            er.registration_date
        FROM exam_registrations er
        JOIN applicants a ON er.applicant_id = a.applicant_id
        JOIN users u ON a.user_id = u.user_id
        JOIN programs p ON a.primary_program_id = p.program_id
        WHERE er.exam_schedule_id = ?
        ORDER BY er.registration_date DESC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $schedule_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Output the results
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td data-name='" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "'>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</td>";
        echo "<td data-email='" . htmlspecialchars($row['email']) . "'>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td data-program='" . htmlspecialchars($row['program_name']) . "'>" . htmlspecialchars($row['program_name']) . "</td>";
        echo "<td data-date='" . date('Y-m-d H:i:s', strtotime($row['registration_date'])) . "'>" . date('M d, Y h:i A', strtotime($row['registration_date'])) . "</td>";
        echo "</tr>";
    }
    
    // If no applicants found
    if ($result->num_rows === 0) {
        echo "<tr><td colspan='4' class='text-center'>No applicants registered for this schedule</td></tr>";
    }
    
    $stmt->close();
} else {
    echo "<tr><td colspan='4' class='text-center text-danger'>Invalid request</td></tr>";
}

$conn->close();
?> 