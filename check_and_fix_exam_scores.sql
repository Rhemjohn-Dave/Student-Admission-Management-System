-- Check and fix exam_scores table auto-increment issue

-- First, let's see the current table structure
DESCRIBE exam_scores;

-- Check if score_id has AUTO_INCREMENT
SHOW CREATE TABLE exam_scores;

-- Check the current auto_increment value
SHOW TABLE STATUS LIKE 'exam_scores';

-- Fix the auto_increment issue
ALTER TABLE `exam_scores` MODIFY `score_id` int(11) NOT NULL AUTO_INCREMENT;

-- Reset the auto_increment to start from 1
ALTER TABLE `exam_scores` AUTO_INCREMENT = 1;

-- Verify the fix
DESCRIBE exam_scores;
SHOW TABLE STATUS LIKE 'exam_scores';

-- If the above doesn't work, let's try a more comprehensive fix
-- First, let's see if there are any existing records
SELECT COUNT(*) as total_records FROM exam_scores;

-- If there are no important records, we can recreate the table
-- DROP TABLE IF EXISTS exam_scores;
-- CREATE TABLE `exam_scores` (
--   `score_id` int(11) NOT NULL AUTO_INCREMENT,
--   `registration_id` int(11) NOT NULL,
--   `score` decimal(5,2) NOT NULL,
--   `rank` int(11) DEFAULT NULL,
--   `assigned_program_id` int(11) DEFAULT NULL,
--   `interview_schedule_id` int(11) DEFAULT NULL,
--   `status` enum('pending','qualified','not_qualified') NOT NULL DEFAULT 'pending',
--   `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
--   `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
--   PRIMARY KEY (`score_id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; 