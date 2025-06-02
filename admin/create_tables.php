<?php
require_once "../config/database.php";

// Drop existing tables if they exist
$tables = [
    'exam_schedules',
    'exam_registrations',
    'activity_log',
    'interviews'
];

foreach ($tables as $table) {
    mysqli_query($conn, "DROP TABLE IF EXISTS $table");
}

// Create exam_schedules table
$create_exam_schedules = "CREATE TABLE exam_schedules (
    exam_id INT PRIMARY KEY AUTO_INCREMENT,
    exam_date DATE NOT NULL,
    exam_time TIME NOT NULL,
    venue VARCHAR(255) NOT NULL,
    max_participants INT NOT NULL,
    status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

// Create exam_registrations table
$create_exam_registrations = "CREATE TABLE exam_registrations (
    registration_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    exam_id INT NOT NULL,
    status ENUM('registered', 'attended', 'cancelled') DEFAULT 'registered',
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (exam_id) REFERENCES exam_schedules(exam_id)
)";

// Create activity_log table
$create_activity_log = "CREATE TABLE activity_log (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
)";

// Create interviews table
$create_interviews = "CREATE TABLE interviews (
    interview_id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    interviewer_id INT NOT NULL,
    program_head_id INT NOT NULL,
    scheduled_date DATE NOT NULL,
    scheduled_time TIME NOT NULL,
    status ENUM('scheduled', 'completed', 'cancelled') NOT NULL DEFAULT 'scheduled',
    result ENUM('passed', 'failed') DEFAULT NULL,
    score DECIMAL(5,2) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    completed_date DATETIME DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(application_id) ON DELETE CASCADE,
    FOREIGN KEY (interviewer_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (program_head_id) REFERENCES program_heads(program_head_id) ON DELETE CASCADE
)";

// Execute table creation
if (mysqli_query($conn, $create_exam_schedules)) {
    echo "Table exam_schedules created successfully<br>";
} else {
    echo "Error creating exam_schedules table: " . mysqli_error($conn) . "<br>";
}

if (mysqli_query($conn, $create_exam_registrations)) {
    echo "Table exam_registrations created successfully<br>";
} else {
    echo "Error creating exam_registrations table: " . mysqli_error($conn) . "<br>";
}

if (mysqli_query($conn, $create_activity_log)) {
    echo "Table activity_log created successfully<br>";
} else {
    echo "Error creating activity_log table: " . mysqli_error($conn) . "<br>";
}

if (mysqli_query($conn, $create_interviews)) {
    echo "Table interviews created successfully<br>";
} else {
    echo "Error creating interviews table: " . mysqli_error($conn) . "<br>";
}

mysqli_close($conn);
?> 