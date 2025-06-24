-- This script fixes the table structures for the TUP Admissions database.
-- It adds AUTO_INCREMENT to primary keys and ensures all tables have a primary key.

--
-- Table structure for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT,
  ADD KEY `user_id` (`user_id`);

--
-- Table structure for table `applicants`
--
ALTER TABLE `applicants`
  MODIFY `applicant_id` int(11) NOT NULL AUTO_INCREMENT,
  ADD KEY `user_id` (`user_id`),
  ADD KEY `primary_program_id` (`primary_program_id`),
  ADD KEY `secondary_program_id` (`secondary_program_id`);

--
-- Table structure for table `applications`
--
ALTER TABLE `applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `program_id` (`program_id`);

--
-- Table structure for table `colleges`
--
ALTER TABLE `colleges`
  MODIFY `college_id` int(11) NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`college_id`),
  ADD UNIQUE KEY `college_code` (`college_code`);

--
-- Table structure for table `exam_registrations`
--
ALTER TABLE `exam_registrations`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`registration_id`),
  ADD UNIQUE KEY `unique_applicant_schedule` (`applicant_id`,`exam_schedule_id`),
  ADD KEY `exam_schedule_id` (`exam_schedule_id`);

--
-- Table structure for table `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`result_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Table structure for table `exam_schedules`
--
ALTER TABLE `exam_schedules`
  MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`exam_id`);

--
-- Table structure for table `exam_scores`
--
ALTER TABLE `exam_scores`
  MODIFY `score_id` int(11) NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`score_id`),
  ADD KEY `registration_id` (`registration_id`),
  ADD KEY `assigned_program_id` (`assigned_program_id`),
  ADD KEY `interview_schedule_id` (`interview_schedule_id`);

--
-- Table structure for table `interviewers`
--
ALTER TABLE `interviewers`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `program_id` (`program_id`);

--
-- Table structure for table `interviews`
--
ALTER TABLE `interviews`
  MODIFY `interview_id` int(11) NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`interview_id`),
  ADD KEY `application_id` (`application_id`),
  ADD KEY `interviewer_id` (`interviewer_id`),
  ADD KEY `program_head_id` (`program_head_id`);

--
-- Table structure for table `interview_results`
--
ALTER TABLE `interview_results`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`result_id`),
  ADD KEY `applicant_id` (`applicant_id`),
  ADD KEY `interview_schedule_id` (`interview_schedule_id`),
  ADD KEY `interviewer_id` (`interviewer_id`);

--
-- Table structure for table `interview_schedules`
--
ALTER TABLE `interview_schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Table structure for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Table structure for table `programs`
--
ALTER TABLE `programs`
  MODIFY `program_id` int(11) NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`program_id`),
  ADD KEY `college_id` (`college_id`),
  ADD KEY `program_head_id` (`program_head_id`);

--
-- Table structure for table `program_cutoffs`
--
ALTER TABLE `program_cutoffs`
  MODIFY `cutoff_id` int(11) NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`cutoff_id`),
  ADD KEY `program_id` (`program_id`);

--
-- Table structure for table `program_heads`
--
ALTER TABLE `program_heads`
  MODIFY `program_head_id` int(11) NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`program_head_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `program_id` (`program_id`);

--
-- Table structure for table `program_rankings`
--
ALTER TABLE `program_rankings`
  MODIFY `ranking_id` int(11) NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`ranking_id`),
  ADD KEY `applicant_id` (`applicant_id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `assigned_program_id` (`assigned_program_id`);

--
-- Table structure for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`); 