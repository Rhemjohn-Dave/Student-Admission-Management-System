<?php
require_once "config/database.php";

// Test database connection
if($conn) {
    echo "Database connection successful<br>";
    
    // Check if programs table exists
    $table_check = mysqli_query($conn, "SHOW TABLES LIKE 'programs'");
    if(mysqli_num_rows($table_check) > 0) {
        echo "Programs table exists<br>";
        
        // Get all programs
        $sql = "SELECT program_id, program_name FROM programs WHERE status = 'active' ORDER BY program_name";
        $result = mysqli_query($conn, $sql);
        
        if($result) {
            echo "Number of programs found: " . mysqli_num_rows($result) . "<br>";
            echo "Programs:<br>";
            while($row = mysqli_fetch_assoc($result)) {
                echo "ID: " . $row['program_id'] . " - Name: " . $row['program_name'] . "<br>";
            }
        } else {
            echo "Error fetching programs: " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "Programs table does not exist<br>";
    }
} else {
    echo "Database connection failed<br>";
}
?> 