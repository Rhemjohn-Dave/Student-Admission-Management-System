-- Fix activity_log table structure
ALTER TABLE `activity_log`
ADD PRIMARY KEY (`log_id`),
ADD KEY `user_id` (`user_id`),
ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`); 