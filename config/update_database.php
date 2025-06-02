<?php
require_once 'database.php';

// Read the SQL file
$sql = file_get_contents('database.sql');

// Split the SQL file into individual statements
$statements = array_filter(array_map('trim', explode(';', $sql)));

// Execute each statement
foreach ($statements as $statement) {
    if (!empty($statement)) {
        if (mysqli_query($conn, $statement)) {
            echo "Successfully executed: " . substr($statement, 0, 50) . "...<br>";
        } else {
            echo "Error executing statement: " . mysqli_error($conn) . "<br>";
            echo "Statement: " . $statement . "<br>";
        }
    }
}

echo "Database update completed.";
?> 