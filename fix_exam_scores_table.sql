-- Fix exam_scores table structure
-- Add AUTO_INCREMENT to the primary key

ALTER TABLE `exam_scores` MODIFY `score_id` int(11) NOT NULL AUTO_INCREMENT;

-- Verify the table structure
DESCRIBE exam_scores; 