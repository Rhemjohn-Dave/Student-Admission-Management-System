-- Create database
CREATE DATABASE IF NOT EXISTS tup_admissions;
USE tup_admissions;

-- Create users table
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `user_type` enum('admin','interviewer','applicant') NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create colleges table
CREATE TABLE IF NOT EXISTS `colleges` (
  `college_id` int(11) NOT NULL AUTO_INCREMENT,
  `college_name` varchar(100) NOT NULL,
  `college_code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`college_id`),
  UNIQUE KEY `college_code` (`college_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create programs table (without program_head_id for now)
CREATE TABLE IF NOT EXISTS `programs` (
  `program_id` int(11) NOT NULL AUTO_INCREMENT,
  `college_id` int(11) NOT NULL,
  `program_name` varchar(100) NOT NULL,
  `program_code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`program_id`),
  KEY `college_id` (`college_id`),
  CONSTRAINT `programs_ibfk_1` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create program_heads table
CREATE TABLE IF NOT EXISTS `program_heads` (
  `program_head_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`program_head_id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `program_id` (`program_id`),
  CONSTRAINT `program_heads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `program_heads_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add program_head_id to programs table
ALTER TABLE `programs` 
ADD COLUMN `program_head_id` int(11) DEFAULT NULL AFTER `college_id`,
ADD CONSTRAINT `programs_ibfk_2` FOREIGN KEY (`program_head_id`) REFERENCES `program_heads` (`program_head_id`) ON DELETE SET NULL;

-- Create applications table
CREATE TABLE IF NOT EXISTS `applications` (
  `application_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`application_id`),
  KEY `user_id` (`user_id`),
  KEY `program_id` (`program_id`),
  CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create interviews table
CREATE TABLE IF NOT EXISTS `interviews` (
  `interview_id` int(11) NOT NULL AUTO_INCREMENT,
  `application_id` int(11) NOT NULL,
  `interviewer_id` int(11) NOT NULL,
  `program_head_id` int(11) NOT NULL,
  `scheduled_date` date NOT NULL,
  `scheduled_time` time NOT NULL,
  `status` enum('scheduled','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `result` enum('passed','failed') DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `completed_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`interview_id`),
  KEY `application_id` (`application_id`),
  KEY `interviewer_id` (`interviewer_id`),
  KEY `program_head_id` (`program_head_id`),
  CONSTRAINT `interviews_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`application_id`) ON DELETE CASCADE,
  CONSTRAINT `interviews_ibfk_2` FOREIGN KEY (`interviewer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `interviews_ibfk_3` FOREIGN KEY (`program_head_id`) REFERENCES `program_heads` (`program_head_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create exam_schedules table
CREATE TABLE IF NOT EXISTS `exam_schedules` (
  `exam_id` int(11) NOT NULL AUTO_INCREMENT,
  `program_id` int(11) NOT NULL,
  `exam_date` date NOT NULL,
  `exam_time` time NOT NULL,
  `venue` varchar(255) NOT NULL,
  `max_participants` int(11) NOT NULL,
  `status` enum('scheduled','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`exam_id`),
  KEY `program_id` (`program_id`),
  CONSTRAINT `exam_schedules_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create exam_results table
CREATE TABLE IF NOT EXISTS `exam_results` (
  `result_id` int(11) NOT NULL AUTO_INCREMENT,
  `exam_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `status` enum('passed','failed') NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`result_id`),
  KEY `exam_id` (`exam_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `exam_results_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exam_schedules` (`exam_id`) ON DELETE CASCADE,
  CONSTRAINT `exam_results_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create interviewers table
CREATE TABLE interviewers (
    user_id INT PRIMARY KEY,
    program_id INT,
    qualifications TEXT NOT NULL,
    experience TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (program_id) REFERENCES programs(program_id)
);

-- Create applicants table
CREATE TABLE applicants (
    applicant_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    last_name VARCHAR(50) NOT NULL,
    birth_date DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    mobile_number VARCHAR(20) NOT NULL,
    address_lot VARCHAR(50),
    address_street VARCHAR(100),
    address_town VARCHAR(100),
    address_city VARCHAR(100),
    address_country VARCHAR(100) DEFAULT 'Philippines',
    address_zipcode VARCHAR(10),
    mother_maiden_name VARCHAR(100),
    father_name VARCHAR(100),
    elementary_school VARCHAR(100) NOT NULL,
    elementary_year_graduated YEAR NOT NULL,
    high_school VARCHAR(100) NOT NULL,
    high_school_year_graduated YEAR NOT NULL,
    primary_program_id INT,
    secondary_program_id INT,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (primary_program_id) REFERENCES programs(program_id),
    FOREIGN KEY (secondary_program_id) REFERENCES programs(program_id)
);

-- Create exam registrations table
CREATE TABLE IF NOT EXISTS `exam_registrations` (
  `registration_id` int(11) NOT NULL AUTO_INCREMENT,
  `applicant_id` int(11) NOT NULL,
  `exam_schedule_id` int(11) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('registered','cancelled','completed') NOT NULL DEFAULT 'registered',
  PRIMARY KEY (`registration_id`),
  UNIQUE KEY `unique_applicant_schedule` (`applicant_id`,`exam_schedule_id`),
  KEY `exam_schedule_id` (`exam_schedule_id`),
  CONSTRAINT `exam_registrations_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE,
  CONSTRAINT `exam_registrations_ibfk_2` FOREIGN KEY (`exam_schedule_id`) REFERENCES `exam_schedules` (`exam_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create interview schedules table
CREATE TABLE interview_schedules (
    schedule_id INT PRIMARY KEY AUTO_INCREMENT,
    program_id INT,
    interview_date DATE NOT NULL,
    time_window ENUM('AM', 'PM') NOT NULL,
    max_applicants INT NOT NULL,
    current_applicants INT DEFAULT 0,
    status ENUM('open', 'closed', 'completed') DEFAULT 'open',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (program_id) REFERENCES programs(program_id),
    FOREIGN KEY (created_by) REFERENCES users(user_id)
);

-- Create interview results table
CREATE TABLE interview_results (
    result_id INT PRIMARY KEY AUTO_INCREMENT,
    applicant_id INT,
    interview_schedule_id INT,
    interviewer_id INT,
    rating DECIMAL(5,2) NOT NULL,
    remarks TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (applicant_id) REFERENCES applicants(applicant_id),
    FOREIGN KEY (interview_schedule_id) REFERENCES interview_schedules(schedule_id),
    FOREIGN KEY (interviewer_id) REFERENCES users(user_id)
);

-- Create notifications table
CREATE TABLE notifications (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    title VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('exam', 'interview', 'result', 'general') NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Create activity log table
CREATE TABLE activity_log (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Create program cutoffs table
CREATE TABLE program_cutoffs (
    cutoff_id INT PRIMARY KEY AUTO_INCREMENT,
    program_id INT NOT NULL,
    cutoff_rank INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (program_id) REFERENCES programs(program_id) ON DELETE CASCADE
);

-- Create program rankings table
CREATE TABLE program_rankings (
    ranking_id INT PRIMARY KEY AUTO_INCREMENT,
    applicant_id INT NOT NULL,
    program_id INT NOT NULL,
    exam_score DECIMAL(5,2) NOT NULL,
    rank_position INT NOT NULL,
    is_eligible BOOLEAN DEFAULT FALSE,
    assigned_program_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (applicant_id) REFERENCES applicants(applicant_id) ON DELETE CASCADE,
    FOREIGN KEY (program_id) REFERENCES programs(program_id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_program_id) REFERENCES programs(program_id) ON DELETE SET NULL
);

-- Insert demo data

-- Insert demo colleges
INSERT INTO colleges (college_name, college_code, description) VALUES
('College of Engineering', 'COE', 'College of Engineering offers various engineering programs'),
('College of Automation and Control', 'CAC', 'College of Automation and Control focuses on automation and control systems'),
('College of Engineering Technology', 'CET', 'College of Engineering Technology provides technical education');

-- Insert demo programs
INSERT INTO programs (college_id, program_name, program_code, description) VALUES
-- College of Engineering
(1, 'Bachelor of Science in Electronics Engineering', 'BS ECE', 'Electronics Engineering program'),
(1, 'Bachelor of Science in Mechanical Engineering', 'BS ME', 'Mechanical Engineering program'),
(1, 'Bachelor of Science in Computer Engineering', 'BS CPE', 'Computer Engineering program'),
(1, 'Bachelor of Science in Electrical Engineering', 'BS EE', 'Electrical Engineering program'),

-- College of Automation and Control
(2, 'Bachelor of Science in Instrumentation and Control Engineering Technology', 'BS ICET', 'Instrumentation and Control Engineering Technology program'),
(2, 'Bachelor of Science in Mechatronics Engineering', 'BS ME', 'Mechatronics Engineering program'),
(2, 'Bachelor of Science in Mechatronics Engineering Technology', 'BS MET', 'Mechatronics Engineering Technology program'),

-- College of Engineering Technology
(3, 'Bachelor of Science in Chemistry', 'BS Chem', 'Chemistry program'),
(3, 'Bachelor of Engineering major in Chemical Engineering Technology', 'BE ChET', 'Chemical Engineering Technology program'),
(3, 'Bachelor of Science in Automotive Engineering', 'BS AutoE', 'Automotive Engineering program'),
(3, 'Bachelor of Science in Computer Engineering Technology', 'BS CpET', 'Computer Engineering Technology program'),
(3, 'Bachelor of Science in Electrical Engineering Technology', 'BS EET', 'Electrical Engineering Technology program'),
(3, 'Bachelor of Science in Electromechanical Engineering Technology', 'BS EMET', 'Electromechanical Engineering Technology program'),
(3, 'Bachelor of Science in Heating, Ventilation and Air Conditioning-Refrigeration Engineering Technology', 'BS HVAC-RET', 'HVAC-RET program'),
(3, 'Bachelor of Science in Manufacturing Engineering Technology', 'BS MFET', 'Manufacturing Engineering Technology program');

-- Insert demo admin user
INSERT INTO users (username, password, email, user_type, status) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@tup.edu.ph', 'admin', 'active');

-- Insert demo applicant user
INSERT INTO users (username, password, email, user_type, status) VALUES
('applicant1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'applicant1@example.com', 'applicant', 'active');

-- Insert demo applicant details
INSERT INTO applicants (
    user_id, first_name, last_name, birth_date, gender, mobile_number,
    address_street, address_town, address_city, address_zipcode,
    mother_maiden_name, father_name,
    elementary_school, elementary_year_graduated,
    high_school, high_school_year_graduated,
    primary_program_id, secondary_program_id
) VALUES (
    2, 'Juan', 'Dela Cruz', '2000-01-01', 'male', '09123456789',
    '123 Main St', 'Barangay 1', 'Iloilo City', '5000',
    'Maria Santos', 'Pedro Dela Cruz',
    'Iloilo Central Elementary School', 2012,
    'Iloilo National High School', 2016,
    1, 2
);

-- -- Insert demo exam schedule
-- INSERT INTO exam_schedules (program_id, exam_date, exam_time, venue, max_participants, status) VALUES
-- (1, '2024-04-01', '09:00:00', 'Room 101, Engineering Building', 50, 'scheduled'),
-- (2, '2024-04-01', '14:00:00', 'Room 102, Engineering Building', 50, 'scheduled');

-- Insert demo interview schedule
INSERT INTO interview_schedules (program_id, interview_date, time_window, max_applicants, status, created_by) VALUES
(1, '2024-04-15', 'AM', 20, 'open', 1),
(2, '2024-04-15', 'PM', 20, 'open', 1);

-- Insert demo program heads
INSERT INTO users (username, password, first_name, last_name, email, user_type, status) VALUES
('ph_ece', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Smith', 'ph_ece@tup.edu.ph', 'interviewer', 'active'),
('ph_me', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane', 'Doe', 'ph_me@tup.edu.ph', 'interviewer', 'active'),
('ph_cpe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Robert', 'Johnson', 'ph_cpe@tup.edu.ph', 'interviewer', 'active'),
('ph_ee', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah', 'Williams', 'ph_ee@tup.edu.ph', 'interviewer', 'active'),
('ph_icet', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Michael', 'Brown', 'ph_icet@tup.edu.ph', 'interviewer', 'active'),
('ph_met', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Emily', 'Davis', 'ph_met@tup.edu.ph', 'interviewer', 'active'),
('ph_chem', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'David', 'Wilson', 'ph_chem@tup.edu.ph', 'interviewer', 'active'),
('ph_chet', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Patricia', 'Taylor', 'ph_chet@tup.edu.ph', 'interviewer', 'active'),
('ph_auto', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'James', 'Anderson', 'ph_auto@tup.edu.ph', 'interviewer', 'active'),
('ph_cpet', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jennifer', 'Thomas', 'ph_cpet@tup.edu.ph', 'interviewer', 'active'),
('ph_eet', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Richard', 'Jackson', 'ph_eet@tup.edu.ph', 'interviewer', 'active'),
('ph_emet', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Susan', 'White', 'ph_emet@tup.edu.ph', 'interviewer', 'active'),
('ph_hvac', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Charles', 'Harris', 'ph_hvac@tup.edu.ph', 'interviewer', 'active'),
('ph_mfet', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Margaret', 'Martin', 'ph_mfet@tup.edu.ph', 'interviewer', 'active');

-- Insert program heads
INSERT INTO program_heads (user_id, program_id) VALUES
(3, 1), -- ECE
(4, 2), -- ME
(5, 3), -- CPE
(6, 4), -- EE
(7, 5), -- ICET
(8, 6), -- MET
(9, 7), -- Chem
(10, 8), -- ChET
(11, 9), -- AutoE
(12, 10), -- CpET
(13, 11), -- EET
(14, 12), -- EMET
(15, 13), -- HVAC-RET
(16, 14); -- MFET

-- Update programs with program head IDs
UPDATE programs p
JOIN program_heads ph ON p.program_id = ph.program_id
SET p.program_head_id = ph.program_head_id; 



-- First, drop the foreign key constraint
ALTER TABLE `exam_schedules` 
DROP FOREIGN KEY `exam_schedules_ibfk_1`;

-- Then, drop the program_id column
ALTER TABLE `exam_schedules` 
DROP COLUMN `program_id`;


ALTER TABLE interviews 
ADD COLUMN score DECIMAL(5,2) DEFAULT NULL 
AFTER result;