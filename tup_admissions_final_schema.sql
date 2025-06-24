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