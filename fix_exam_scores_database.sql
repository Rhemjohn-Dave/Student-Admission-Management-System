-- Fix exam_scores table structure
-- This script will properly set up the exam_scores table

-- First, let's check the current table structure
DESCRIBE exam_scores;

-- Drop the existing table if it has issues
DROP TABLE IF EXISTS exam_scores;

-- Recreate the exam_scores table with proper structure
CREATE TABLE `exam_scores` (
  `score_id` int(11) NOT NULL AUTO_INCREMENT,
  `registration_id` int(11) NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `rank` int(11) DEFAULT NULL,
  `assigned_program_id` int(11) DEFAULT NULL,
  `interview_schedule_id` int(11) DEFAULT NULL,
  `status` enum('pending','qualified','not_qualified') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`score_id`),
  KEY `registration_id` (`registration_id`),
  KEY `assigned_program_id` (`assigned_program_id`),
  KEY `interview_schedule_id` (`interview_schedule_id`),
  CONSTRAINT `exam_scores_ibfk_1` FOREIGN KEY (`registration_id`) REFERENCES `exam_registrations` (`registration_id`) ON DELETE CASCADE,
  CONSTRAINT `exam_scores_ibfk_2` FOREIGN KEY (`assigned_program_id`) REFERENCES `programs` (`program_id`) ON DELETE SET NULL,
  CONSTRAINT `exam_scores_ibfk_3` FOREIGN KEY (`interview_schedule_id`) REFERENCES `interview_schedules` (`schedule_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Verify the table structure
DESCRIBE exam_scores;

-- Show the auto_increment value
SHOW TABLE STATUS LIKE 'exam_scores'; 