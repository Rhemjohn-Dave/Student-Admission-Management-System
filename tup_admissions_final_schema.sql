-- TUP Admissions Final Schema (with all foreign keys)
-- Generated for a clean, consistent database structure

CREATE DATABASE IF NOT EXISTS tup_admissions_new;
USE tup_admissions_new;

-- Users
CREATE TABLE users (
  user_id INT(11) NOT NULL AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  first_name VARCHAR(50) NOT NULL,
  last_name VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL,
  phone VARCHAR(20),
  user_type ENUM('admin','interviewer','applicant') NOT NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'inactive',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id),
  UNIQUE KEY username (username),
  UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Colleges
CREATE TABLE colleges (
  college_id INT(11) NOT NULL AUTO_INCREMENT,
  college_name VARCHAR(100) NOT NULL,
  college_code VARCHAR(20) NOT NULL,
  description TEXT,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (college_id),
  UNIQUE KEY college_code (college_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Programs
CREATE TABLE programs (
  program_id INT(11) NOT NULL AUTO_INCREMENT,
  college_id INT(11) NOT NULL,
  program_head_id INT(11),
  program_name VARCHAR(100) NOT NULL,
  program_code VARCHAR(20) NOT NULL,
  description TEXT,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (program_id),
  KEY college_id (college_id),
  KEY program_head_id (program_head_id),
  CONSTRAINT fk_programs_college FOREIGN KEY (college_id) REFERENCES colleges(college_id) ON DELETE CASCADE,
  CONSTRAINT fk_programs_head FOREIGN KEY (program_head_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Program Heads
CREATE TABLE program_heads (
  program_head_id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  program_id INT(11) NOT NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (program_head_id),
  UNIQUE KEY user_id (user_id),
  UNIQUE KEY program_id (program_id),
  CONSTRAINT fk_ph_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  CONSTRAINT fk_ph_program FOREIGN KEY (program_id) REFERENCES programs(program_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Applicants
CREATE TABLE applicants (
  applicant_id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11),
  first_name VARCHAR(50) NOT NULL,
  middle_name VARCHAR(50),
  last_name VARCHAR(50) NOT NULL,
  birth_date DATE NOT NULL,
  gender ENUM('male','female','other') NOT NULL,
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
  primary_program_id INT(11),
  secondary_program_id INT(11),
  PRIMARY KEY (applicant_id),
  KEY user_id (user_id),
  KEY primary_program_id (primary_program_id),
  KEY secondary_program_id (secondary_program_id),
  CONSTRAINT fk_applicants_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
  CONSTRAINT fk_applicants_primary FOREIGN KEY (primary_program_id) REFERENCES programs(program_id) ON DELETE SET NULL,
  CONSTRAINT fk_applicants_secondary FOREIGN KEY (secondary_program_id) REFERENCES programs(program_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Applications
CREATE TABLE applications (
  application_id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  program_id INT(11) NOT NULL,
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (application_id),
  KEY user_id (user_id),
  KEY program_id (program_id),
  CONSTRAINT fk_applications_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  CONSTRAINT fk_applications_program FOREIGN KEY (program_id) REFERENCES programs(program_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Activity Log
CREATE TABLE activity_log (
  log_id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11),
  action VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (log_id),
  KEY user_id (user_id),
  CONSTRAINT fk_activitylog_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Exam Schedules
CREATE TABLE exam_schedules (
  exam_id INT(11) NOT NULL AUTO_INCREMENT,
  exam_date DATE NOT NULL,
  exam_time TIME NOT NULL,
  venue VARCHAR(255) NOT NULL,
  max_participants INT(11) NOT NULL,
  status ENUM('scheduled','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (exam_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Exam Registrations
CREATE TABLE exam_registrations (
  registration_id INT(11) NOT NULL AUTO_INCREMENT,
  applicant_id INT(11) NOT NULL,
  exam_schedule_id INT(11) NOT NULL,
  registration_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status ENUM('registered','cancelled','completed') NOT NULL DEFAULT 'registered',
  PRIMARY KEY (registration_id),
  UNIQUE KEY unique_applicant_schedule (applicant_id, exam_schedule_id),
  KEY exam_schedule_id (exam_schedule_id),
  CONSTRAINT fk_examreg_applicant FOREIGN KEY (applicant_id) REFERENCES applicants(applicant_id) ON DELETE CASCADE,
  CONSTRAINT fk_examreg_schedule FOREIGN KEY (exam_schedule_id) REFERENCES exam_schedules(exam_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Exam Results
CREATE TABLE exam_results (
  result_id INT(11) NOT NULL AUTO_INCREMENT,
  exam_id INT(11) NOT NULL,
  user_id INT(11) NOT NULL,
  score DECIMAL(5,2) NOT NULL,
  status ENUM('passed','failed') NOT NULL,
  notes TEXT,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (result_id),
  KEY exam_id (exam_id),
  KEY user_id (user_id),
  CONSTRAINT fk_examresults_exam FOREIGN KEY (exam_id) REFERENCES exam_schedules(exam_id) ON DELETE CASCADE,
  CONSTRAINT fk_examresults_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Exam Scores
CREATE TABLE exam_scores (
  score_id INT(11) NOT NULL AUTO_INCREMENT,
  registration_id INT(11) NOT NULL,
  score DECIMAL(5,2) NOT NULL,
  rank INT(11),
  assigned_program_id INT(11),
  interview_schedule_id INT(11),
  status ENUM('pending','qualified','not_qualified') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (score_id),
  KEY registration_id (registration_id),
  KEY assigned_program_id (assigned_program_id),
  KEY interview_schedule_id (interview_schedule_id),
  CONSTRAINT fk_examscores_registration FOREIGN KEY (registration_id) REFERENCES exam_registrations(registration_id) ON DELETE CASCADE,
  CONSTRAINT fk_examscores_assigned_program FOREIGN KEY (assigned_program_id) REFERENCES programs(program_id) ON DELETE SET NULL,
  CONSTRAINT fk_examscores_interview_schedule FOREIGN KEY (interview_schedule_id) REFERENCES interview_schedules(schedule_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Interviewers
CREATE TABLE interviewers (
  user_id INT(11) NOT NULL,
  program_id INT(11),
  qualifications TEXT NOT NULL,
  experience TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id),
  KEY program_id (program_id),
  CONSTRAINT fk_interviewers_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  CONSTRAINT fk_interviewers_program FOREIGN KEY (program_id) REFERENCES programs(program_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Interviews
CREATE TABLE interviews (
  interview_id INT(11) NOT NULL AUTO_INCREMENT,
  application_id INT(11) NOT NULL,
  interviewer_id INT(11) NOT NULL,
  program_head_id INT(11) NOT NULL,
  scheduled_date DATE NOT NULL,
  scheduled_time TIME NOT NULL,
  status ENUM('scheduled','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  result ENUM('passed','failed') DEFAULT NULL,
  score DECIMAL(5,2) DEFAULT NULL,
  notes TEXT DEFAULT NULL,
  completed_date DATETIME DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (interview_id),
  KEY application_id (application_id),
  KEY interviewer_id (interviewer_id),
  KEY program_head_id (program_head_id),
  CONSTRAINT fk_interviews_application FOREIGN KEY (application_id) REFERENCES applications(application_id) ON DELETE CASCADE,
  CONSTRAINT fk_interviews_interviewer FOREIGN KEY (interviewer_id) REFERENCES users(user_id) ON DELETE CASCADE,
  CONSTRAINT fk_interviews_program_head FOREIGN KEY (program_head_id) REFERENCES program_heads(program_head_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Interview Results
CREATE TABLE interview_results (
  result_id INT(11) NOT NULL AUTO_INCREMENT,
  applicant_id INT(11),
  interview_schedule_id INT(11),
  interviewer_id INT(11),
  rating DECIMAL(5,2) NOT NULL,
  remarks TEXT,
  submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (result_id),
  KEY applicant_id (applicant_id),
  KEY interview_schedule_id (interview_schedule_id),
  KEY interviewer_id (interviewer_id),
  CONSTRAINT fk_interviewresults_applicant FOREIGN KEY (applicant_id) REFERENCES applicants(applicant_id) ON DELETE SET NULL,
  CONSTRAINT fk_interviewresults_schedule FOREIGN KEY (interview_schedule_id) REFERENCES interview_schedules(schedule_id) ON DELETE SET NULL,
  CONSTRAINT fk_interviewresults_interviewer FOREIGN KEY (interviewer_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Interview Schedules
CREATE TABLE interview_schedules (
  schedule_id INT(11) NOT NULL AUTO_INCREMENT,
  program_id INT(11),
  interview_date DATE NOT NULL,
  time_window ENUM('AM','PM') NOT NULL,
  max_applicants INT(11) NOT NULL,
  current_applicants INT(11) DEFAULT 0,
  status ENUM('open','closed','completed') DEFAULT 'open',
  created_by INT(11),
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (schedule_id),
  KEY program_id (program_id),
  KEY created_by (created_by),
  CONSTRAINT fk_interviewschedules_program FOREIGN KEY (program_id) REFERENCES programs(program_id) ON DELETE SET NULL,
  CONSTRAINT fk_interviewschedules_created_by FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Notifications
CREATE TABLE notifications (
  notification_id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11),
  title VARCHAR(100) NOT NULL,
  message TEXT NOT NULL,
  type ENUM('exam','interview','result','general') NOT NULL,
  is_read BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (notification_id),
  KEY user_id (user_id),
  CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Program Cutoffs
CREATE TABLE program_cutoffs (
  cutoff_id INT(11) NOT NULL AUTO_INCREMENT,
  program_id INT(11) NOT NULL,
  start_rank INT(11) NOT NULL,
  end_rank INT(11) NOT NULL,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (cutoff_id),
  KEY program_id (program_id),
  CONSTRAINT fk_cutoffs_program FOREIGN KEY (program_id) REFERENCES programs(program_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Program Rankings
CREATE TABLE program_rankings (
  ranking_id INT(11) NOT NULL AUTO_INCREMENT,
  applicant_id INT(11) NOT NULL,
  program_id INT(11) NOT NULL,
  exam_score DECIMAL(5,2) NOT NULL,
  rank_position INT(11) NOT NULL,
  is_eligible TINYINT(1) DEFAULT 0,
  assigned_program_id INT(11),
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (ranking_id),
  KEY applicant_id (applicant_id),
  KEY program_id (program_id),
  KEY assigned_program_id (assigned_program_id),
  CONSTRAINT fk_rankings_applicant FOREIGN KEY (applicant_id) REFERENCES applicants(applicant_id) ON DELETE CASCADE,
  CONSTRAINT fk_rankings_program FOREIGN KEY (program_id) REFERENCES programs(program_id) ON DELETE CASCADE,
  CONSTRAINT fk_rankings_assigned_program FOREIGN KEY (assigned_program_id) REFERENCES programs(program_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 


INSERT INTO `programs` (`program_id`, `college_id`, `program_head_id`, `program_name`, `program_code`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Bachelor of Science in Electronics Engineering', 'BS ECE', 'Electronics Engineering program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(2, 1, 2, 'Bachelor of Science in Mechanical Engineering', 'BS ME', 'Mechanical Engineering program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(3, 1, 3, 'Bachelor of Science in Computer Engineering', 'BS CPE', 'Computer Engineering program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(4, 1, 4, 'Bachelor of Science in Electrical Engineering', 'BS EE', 'Electrical Engineering program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(5, 2, 5, 'Bachelor of Science in Instrumentation and Control Engineering Technology', 'BS ICET', 'Instrumentation and Control Engineering Technology program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(6, 2, 6, 'Bachelor of Science in Mechatronics Engineering', 'BS MxE', 'Mechatronics Engineering program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(7, 2, 7, 'Bachelor of Science in Mechatronics Engineering Technology', 'BS MxT', 'Mechatronics Engineering Technology program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(8, 3, 8, 'Bachelor of Science in Chemistry', 'BS Chem', 'Chemistry program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(9, 3, 9, 'Bachelor of Engineering major in Chemical Engineering Technology', 'BE ChET', 'Chemical Engineering Technology program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(10, 3, 10, 'Bachelor of Science in Automotive Engineering', 'BS AutoE', 'Automotive Engineering program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(11, 3, 11, 'Bachelor of Science in Computer Engineering Technology', 'BS CpET', 'Computer Engineering Technology program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(12, 3, 12, 'Bachelor of Science in Electrical Engineering Technology', 'BS EET', 'Electrical Engineering Technology program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(13, 3, 13, 'Bachelor of Science in Electromechanical Engineering Technology', 'BS EMET', 'Electromechanical Engineering Technology program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(14, 3, 14, 'Bachelor of Science in Heating, Ventilation and Air Conditioning-Refrigeration Engineering Technolog', 'BS HVAC-RET', 'HVAC-RET program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(15, 3, 15, 'Bachelor of Science in Manufacturing Engineering Technology', 'BS MFET', 'Manufacturing Engineering Technology program', '2025-05-30 13:28:23', '2025-05-30 13:28:23');


INSERT INTO `users` (`user_id`, `username`, `password`, `first_name`, `last_name`, `email`, `phone`, `user_type`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System', 'Administrator', 'admin@tup.edu.ph', NULL, 'admin', 'active', '2025-05-30 13:28:23', '2025-06-24 02:24:58'),
(2, 'ph_ece', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Renato', 'Deldo', 'ph_ece@tup.edu.ph', '', 'interviewer', 'active', '2025-05-30 13:28:23', '2025-06-02 05:55:08'),
(3, 'ph_me', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane', 'Doe', 'ph_me@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(4, 'ph_cpe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Robert', 'Johnson', 'ph_cpe@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(5, 'ph_ee', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah', 'Williams', 'ph_ee@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(6, 'ph_icet', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Michael', 'Brown', 'ph_icet@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(7, 'ph_mxe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Emily', 'Davis', 'ph_mxe@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(8, 'ph_mxt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Emily', 'Davis', 'ph_mxt@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(9, 'ph_chem', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'David', 'Wilson', 'ph_chem@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(10, 'ph_chet', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Patricia', 'Taylor', 'ph_chet@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(11, 'ph_auto', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'James', 'Anderson', 'ph_auto@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(12, 'ph_cpet', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jennifer', 'Thomas', 'ph_cpet@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(13, 'ph_eet', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Richard', 'Jackson', 'ph_eet@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(14, 'ph_emet', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Susan', 'White', 'ph_emet@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(15, 'ph_hvac', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Charles', 'Harris', 'ph_hvac@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(16, 'ph_mfet', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Margaret', 'Martin', 'ph_mfet@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23');

INSERT INTO `program_heads` (`program_head_id`, `user_id`, `program_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(2, 3, 2, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(3, 4, 3, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(4, 5, 4, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(5, 6, 5, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(6, 7, 6, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(7, 8, 7, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(8, 9, 8, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(9, 10, 9, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(10, 11, 10, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(11, 12, 11, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(12, 13, 12, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(13, 14, 13, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(14, 15, 14, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(15, 16, 15, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23');