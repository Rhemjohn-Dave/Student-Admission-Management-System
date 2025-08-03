-- Fix existing interview times that were incorrectly stored
-- This script converts interviews with '00:00:00' time to proper times based on the interview schedule

-- First, let's see what we have
SELECT 
    i.interview_id,
    i.scheduled_date,
    i.scheduled_time,
    s.time_window,
    CONCAT(a.first_name, ' ', a.last_name) as applicant_name
FROM interviews i
JOIN applications app ON i.application_id = app.application_id
JOIN applicants a ON app.user_id = a.user_id
JOIN interview_schedules s ON s.program_id = app.program_id AND s.interview_date = i.scheduled_date
WHERE i.scheduled_time = '00:00:00' OR i.scheduled_time = '00:00:00.000000';

-- Update interviews with '00:00:00' time to proper times based on the schedule
UPDATE interviews i
JOIN applications app ON i.application_id = app.application_id
JOIN interview_schedules s ON s.program_id = app.program_id AND s.interview_date = i.scheduled_date
SET i.scheduled_time = CASE 
    WHEN s.time_window = 'PM' THEN '14:00:00'
    WHEN s.time_window = 'AM' THEN '09:00:00'
    ELSE '09:00:00'  -- Default to AM if time_window is not set
END
WHERE (i.scheduled_time = '00:00:00' OR i.scheduled_time = '00:00:00.000000')
AND s.time_window IS NOT NULL;

-- Verify the fix
SELECT 
    i.interview_id,
    i.scheduled_date,
    i.scheduled_time,
    s.time_window,
    CONCAT(a.first_name, ' ', a.last_name) as applicant_name
FROM interviews i
JOIN applications app ON i.application_id = app.application_id
JOIN applicants a ON app.user_id = a.user_id
JOIN interview_schedules s ON s.program_id = app.program_id AND s.interview_date = i.scheduled_date
ORDER BY i.scheduled_date DESC, i.scheduled_time DESC; 