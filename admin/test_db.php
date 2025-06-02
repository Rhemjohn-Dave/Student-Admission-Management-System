<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../config/database.php";

echo "<h2>Database Connection Test</h2>";

// Test database connection
if ($conn) {
    echo "Database connection successful<br>";
    
    // Check if exam_schedules table exists
    $result = mysqli_query($conn, "SHOW TABLES LIKE 'exam_schedules'");
    if (mysqli_num_rows($result) > 0) {
        echo "exam_schedules table exists<br>";
        
        // Show table structure
        $result = mysqli_query($conn, "DESCRIBE exam_schedules");
        echo "<h3>Table Structure:</h3>";
        echo "<pre>";
        while ($row = mysqli_fetch_assoc($result)) {
            print_r($row);
        }
        echo "</pre>";
        
        // Try to insert a test record
        $test_date = date('Y-m-d');
        $test_time = date('H:i:s');
        $test_venue = "Test Venue";
        $test_participants = 50;
        
        $insert_query = "INSERT INTO exam_schedules (exam_date, exam_time, venue, max_participants, status) 
                        VALUES (?, ?, ?, ?, 'scheduled')";
        $stmt = mysqli_prepare($conn, $insert_query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssi", $test_date, $test_time, $test_venue, $test_participants);
            
            if (mysqli_stmt_execute($stmt)) {
                echo "Test record inserted successfully<br>";
                echo "Last inserted ID: " . mysqli_insert_id($conn) . "<br>";
            } else {
                echo "Error inserting test record: " . mysqli_error($conn) . "<br>";
            }
        } else {
            echo "Error preparing statement: " . mysqli_error($conn) . "<br>";
        }
        
        // Show existing records
        $result = mysqli_query($conn, "SELECT * FROM exam_schedules ORDER BY exam_id DESC LIMIT 5");
        echo "<h3>Last 5 Records:</h3>";
        echo "<pre>";
        while ($row = mysqli_fetch_assoc($result)) {
            print_r($row);
        }
        echo "</pre>";
        
    } else {
        echo "exam_schedules table does not exist<br>";
        
        // Create the table
        $create_table = "CREATE TABLE IF NOT EXISTS exam_schedules (
            exam_id INT PRIMARY KEY AUTO_INCREMENT,
            exam_date DATE NOT NULL,
            exam_time TIME NOT NULL,
            venue VARCHAR(255) NOT NULL,
            max_participants INT NOT NULL,
            status ENUM('scheduled', 'cancelled') DEFAULT 'scheduled',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        if (mysqli_query($conn, $create_table)) {
            echo "Table created successfully<br>";
        } else {
            echo "Error creating table: " . mysqli_error($conn) . "<br>";
        }
    }
} else {
    echo "Database connection failed: " . mysqli_connect_error() . "<br>";
}

mysqli_close($conn);
?> 