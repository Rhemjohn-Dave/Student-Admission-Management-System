<?php
require_once 'database.php';

// Drop tables in correct order to handle foreign key constraints
$tables = [
    'interviews',
    'applications',
    'programs',
    'colleges',
    'users'
];

foreach ($tables as $table) {
    $sql = "DROP TABLE IF EXISTS `$table`";
    if (mysqli_query($conn, $sql)) {
        echo "Dropped table: $table<br>";
    } else {
        echo "Error dropping table $table: " . mysqli_error($conn) . "<br>";
    }
}

// Now create the tables
$sql = file_get_contents('database.sql');
$statements = array_filter(array_map('trim', explode(';', $sql)));

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

echo "Database reset completed.";
?> 