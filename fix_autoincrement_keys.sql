ALTER TABLE `applications` MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `applicants` MODIFY `applicant_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `exam_schedules` MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT; 
ALTER TABLE `users` MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY;