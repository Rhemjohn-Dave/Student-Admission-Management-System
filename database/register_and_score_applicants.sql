-- SQL code to register applicants for an exam and encode their scores

-- Section 1: Register Applicants to an Exam
-- Replace applicant_id and exam_schedule_id with actual values.
-- The status will default to 'registered' and registration_date to the current timestamp.
INSERT INTO `exam_registrations` (`applicant_id`, `exam_schedule_id`, `status`) VALUES
(PUT_APPLICANT_ID_HERE, PUT_EXAM_SCHEDULE_ID_HERE, 'registered');

-- If you need to register multiple applicants, you can add more rows like this:
-- (ANOTHER_APPLICANT_ID, SAME_OR_DIFFERENT_EXAM_SCHEDULE_ID, 'registered'),
-- (YET_ANOTHER_APPLICANT_ID, ANOTHER_EXAM_SCHEDULE_ID, 'registered');

-- Section 2: Encode Exam Scores for Applicants
-- Replace exam_id, user_id, score, and status with actual values.
-- For `exam_id`, use the `exam_id` from the `exam_schedules` table.
-- For `user_id`, use the `user_id` associated with the applicant who took the exam (from the `users` table).
-- For `score`, enter the numerical score.
-- For `status`, enter 'passed' or 'failed' based on the score/cutoff.
INSERT INTO `exam_results` (`exam_id`, `user_id`, `score`, `status`, `notes`) VALUES
(PUT_EXAM_ID_HERE, PUT_USER_ID_HERE, 85.50, 'passed', 'Optional notes about the result.');

-- If you need to encode scores for multiple applicants, add more rows:
-- (SAME_OR_DIFFERENT_EXAM_ID, ANOTHER_USER_ID, 60.00, 'failed', 'Did not meet the cutoff.'),
-- (EXAM_ID_FOR_THIS_RESULT, FINAL_USER_ID, 72.30, 'passed', NULL); 