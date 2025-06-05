-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 04, 2025 at 04:31 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tup_admissions`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`log_id`, `user_id`, `action`, `created_at`) VALUES
(1, 1, 'Added new exam schedule ID: 1', '2025-05-30 13:56:53'),
(2, 1, 'Closed exam schedule ID: 1', '2025-05-30 13:57:11'),
(3, 1, 'Added new exam schedule ID: 2', '2025-05-30 13:57:28'),
(4, 1, 'Closed exam schedule ID: 1', '2025-05-30 13:57:29'),
(5, 2, 'exam_registration', '2025-05-30 13:57:36'),
(6, 1, 'Updated exam score for registration ID: 1', '2025-05-30 13:58:00'),
(7, 2, 'login', '2025-05-30 15:03:55'),
(8, 3, 'login', '2025-05-30 15:07:49'),
(9, 1, 'login', '2025-05-30 15:08:14'),
(10, 1, 'Updated exam score for registration ID: 1', '2025-05-30 15:44:00'),
(11, 17, 'exam_registration', '2025-06-02 04:33:08'),
(12, 1, 'Added new exam schedule ID: 3', '2025-06-02 15:23:50'),
(13, 31, 'exam_registration', '2025-06-02 15:24:59'),
(14, 38, 'exam_registration', '2025-06-02 16:09:55'),
(15, 1, 'Closed exam schedule ID: 3', '2025-06-02 16:13:03'),
(16, 1, 'Closed exam schedule ID: 2', '2025-06-02 16:18:58'),
(17, 1, 'Added new exam schedule ID: 4', '2025-06-02 16:24:41'),
(18, 38, 'exam_registration', '2025-06-02 16:25:58'),
(19, 1, 'Marked exam schedule ID: 4 as completed', '2025-06-02 16:29:17');

-- --------------------------------------------------------

--
-- Table structure for table `applicants`
--

CREATE TABLE `applicants` (
  `applicant_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `birth_date` date NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `mobile_number` varchar(20) NOT NULL,
  `address_lot` varchar(50) DEFAULT NULL,
  `address_street` varchar(100) DEFAULT NULL,
  `address_town` varchar(100) DEFAULT NULL,
  `address_city` varchar(100) DEFAULT NULL,
  `address_country` varchar(100) DEFAULT 'Philippines',
  `address_zipcode` varchar(10) DEFAULT NULL,
  `mother_maiden_name` varchar(100) DEFAULT NULL,
  `father_name` varchar(100) DEFAULT NULL,
  `elementary_school` varchar(100) NOT NULL,
  `elementary_year_graduated` year(4) NOT NULL,
  `high_school` varchar(100) NOT NULL,
  `high_school_year_graduated` year(4) NOT NULL,
  `primary_program_id` int(11) DEFAULT NULL,
  `secondary_program_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applicants`
--

INSERT INTO `applicants` (`applicant_id`, `user_id`, `first_name`, `middle_name`, `last_name`, `birth_date`, `gender`, `mobile_number`, `address_lot`, `address_street`, `address_town`, `address_city`, `address_country`, `address_zipcode`, `mother_maiden_name`, `father_name`, `elementary_school`, `elementary_year_graduated`, `high_school`, `high_school_year_graduated`, `primary_program_id`, `secondary_program_id`) VALUES
(1, 2, 'Juan', NULL, 'Dela Cruz', '2000-01-01', 'male', '09123456789', NULL, '123 Main St', 'Barangay 1', 'Iloilo City', 'Philippines', '5000', 'Maria Santos', 'Pedro Dela Cruz', 'Iloilo Central Elementary School', '2012', 'Iloilo National High School', '2016', 1, 2),
(2, 17, 'Rhemjohn Dave', 'Nestor', 'Pitong', '1998-03-25', 'male', '09567030731', 'Handumanan', 'Handumanan', 'Handumanan', 'Bacolod City', 'Philippines', '6100', 'Jazmine Cantero', 'Myre Candelario', 'Erams East', '2011', 'KNHS', '2015', 1, 6),
(57, 16, 'Juan', NULL, 'Dela Cruz', '2000-01-01', 'male', '09123456789', NULL, '123 Main St', 'Barangay 1', 'Iloilo City', 'Philippines', '5000', 'Maria Santos', 'Pedro Dela Cruz', 'Iloilo Central Elementary School', '2012', 'Iloilo National High School', '2016', 1, 2),
(58, 17, 'Maria', NULL, 'Lopez', '2001-05-14', 'female', '09123456780', NULL, '456 Mabini St', 'Barangay 2', 'Iloilo City', 'Philippines', '5001', 'Luz Ramirez', 'Jose Lopez', 'Jaro Elementary School', '2013', 'Jaro High School', '2017', 2, 1),
(59, 18, 'Carlos', NULL, 'Reyes', '2002-03-22', 'male', '09123456781', NULL, '789 Rizal St', 'Barangay 3', 'Iloilo City', 'Philippines', '5002', 'Elena Cruz', 'Ramon Reyes', 'La Paz Elementary School', '2014', 'La Paz High School', '2018', 3, 1),
(60, 19, 'Ana', NULL, 'Santos', '2000-07-19', 'female', '09123456782', NULL, '321 Luna St', 'Barangay 4', 'Iloilo City', 'Philippines', '5003', 'Gloria Diaz', 'Leo Santos', 'Mandurriao Elementary School', '2012', 'Mandurriao High School', '2016', 1, 3),
(61, 20, 'Mark', NULL, 'Gomez', '1999-11-30', 'male', '09123456783', NULL, '654 Bonifacio St', 'Barangay 5', 'Iloilo City', 'Philippines', '5004', 'Carla Reyes', 'Mario Gomez', 'Arevalo Elementary School', '2011', 'Arevalo High School', '2015', 2, 1),
(62, 21, 'Liza', NULL, 'Torres', '2001-02-25', 'female', '09123456784', NULL, '987 Del Pilar St', 'Barangay 6', 'Iloilo City', 'Philippines', '5005', 'Teresa Mendoza', 'Carlos Torres', 'Molo Elementary School', '2013', 'Molo High School', '2017', 3, 2),
(63, 22, 'James', NULL, 'Garcia', '2002-08-10', 'male', '09123456785', NULL, '159 Mabuhay Rd', 'Barangay 7', 'Iloilo City', 'Philippines', '5006', 'Rosa Rivera', 'Andres Garcia', 'Lapaz Elementary School', '2014', 'Lapaz High School', '2018', 2, 3),
(64, 23, 'Grace', NULL, 'Flores', '2000-12-01', 'female', '09123456786', NULL, '753 Kalayaan St', 'Barangay 8', 'Iloilo City', 'Philippines', '5007', 'Lilian Cruz', 'Fernando Flores', 'Villa Elementary School', '2012', 'Villa High School', '2016', 1, 2),
(65, 24, 'Leo', NULL, 'Castro', '2001-06-05', 'male', '09123456787', NULL, '852 Katipunan St', 'Barangay 9', 'Iloilo City', 'Philippines', '5008', 'Celia Luna', 'Victor Castro', 'Tanza Elementary School', '2013', 'Tanza High School', '2017', 2, 1),
(66, 25, 'Nina', NULL, 'Ramos', '2002-09-18', 'female', '09123456788', NULL, '147 Freedom St', 'Barangay 10', 'Iloilo City', 'Philippines', '5009', 'Diana Perez', 'Eduardo Ramos', 'Infante Elementary School', '2014', 'Infante High School', '2018', 3, 2),
(67, 26, 'John', NULL, 'Silva', '2000-03-12', 'male', '09123456789', NULL, '369 Unity Ave', 'Barangay 11', 'Iloilo City', 'Philippines', '5010', 'Sandra Cruz', 'Roberto Silva', 'Timawa Elementary School', '2012', 'Timawa High School', '2016', 1, 3),
(68, 27, 'Paula', NULL, 'Diaz', '2001-10-30', 'female', '09123456790', NULL, '951 Hope St', 'Barangay 12', 'Iloilo City', 'Philippines', '5011', 'Aurora Rivera', 'Nestor Diaz', 'General Elementary School', '2013', 'General High School', '2017', 2, 1),
(69, 28, 'Allan', NULL, 'Padilla', '2002-04-08', 'male', '09123456791', NULL, '258 Sunrise Blvd', 'Barangay 13', 'Iloilo City', 'Philippines', '5012', 'Estella Ramos', 'Arnold Padilla', 'Sta. Barbara Elementary', '2014', 'Sta. Barbara High', '2018', 3, 2),
(70, 29, 'Elaine', NULL, 'Navarro', '1999-07-21', 'female', '09123456792', NULL, '753 Sunshine Rd', 'Barangay 14', 'Iloilo City', 'Philippines', '5013', 'Milagros Garcia', 'Luis Navarro', 'San Miguel Elementary', '2011', 'San Miguel High', '2015', 1, 2),
(71, 30, 'Dennis', NULL, 'Cruz', '2000-12-15', 'male', '09123456793', NULL, '864 Liberty St', 'Barangay 15', 'Iloilo City', 'Philippines', '5014', 'Clarita Santos', 'Ernesto Cruz', 'Central Elementary', '2012', 'Central High', '2016', 2, 3),
(72, 31, 'Cindy', NULL, 'Aguilar', '2001-01-19', 'female', '09123456794', NULL, '975 Heritage St', 'Barangay 16', 'Iloilo City', 'Philippines', '5015', 'Vivian Lopez', 'Antonio Aguilar', 'Assumption Elementary', '2013', 'Assumption High', '2017', 3, 1),
(73, 32, 'Brent', NULL, 'Villanueva', '2002-06-27', 'male', '09123456795', NULL, '186 Prosperity Ave', 'Barangay 17', 'Iloilo City', 'Philippines', '5016', 'Patricia Reyes', 'Danilo Villanueva', 'St. Joseph Elem.', '2014', 'St. Joseph High', '2018', 1, 3),
(74, 33, 'Angelica', NULL, 'Morales', '1999-09-09', 'female', '09123456796', NULL, '297 Harmony St', 'Barangay 18', 'Iloilo City', 'Philippines', '5017', 'Feliza Diaz', 'Renato Morales', 'North Elementary', '2011', 'North High', '2015', 2, 1),
(75, 34, 'Miguel', NULL, 'Salazar', '2000-05-04', 'male', '09123456797', NULL, '408 Unity Blvd', 'Barangay 19', 'Iloilo City', 'Philippines', '5018', 'Teresita Santos', 'Jaime Salazar', 'South Elementary', '2012', 'South High', '2016', 3, 2),
(76, 35, 'Jenny', NULL, 'Fernandez', '2001-11-11', 'female', '09123456798', NULL, '519 Victory Rd', 'Barangay 20', 'Iloilo City', 'Philippines', '5019', 'Remedios Cruz', 'Cesar Fernandez', 'East Elementary', '2013', 'East High', '2017', 1, 2),
(77, 38, 'Joelie', 'Umadhay', 'Alegre', '1997-09-21', 'female', '09567030713', 'Handumanan', 'Handumanan', 'Handumanan', 'Bacolod', 'Philippines', '6100', 'Girlie Alegre', 'Joel Alegre', 'HES', '2009', 'HNHS', '2014', 1, 13);

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `application_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`application_id`, `user_id`, `program_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'pending', '2025-05-30 13:31:58', '2025-05-30 13:31:58'),
(2, 17, 1, 'approved', '2025-06-02 04:33:12', '2025-06-02 16:37:24'),
(3, 31, 1, 'pending', '2025-06-02 15:24:55', '2025-06-02 15:24:55'),
(4, 38, 1, 'approved', '2025-06-02 16:09:59', '2025-06-02 16:37:42');

-- --------------------------------------------------------

--
-- Table structure for table `colleges`
--

CREATE TABLE `colleges` (
  `college_id` int(11) NOT NULL,
  `college_name` varchar(100) NOT NULL,
  `college_code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `colleges`
--

INSERT INTO `colleges` (`college_id`, `college_name`, `college_code`, `description`, `created_at`, `updated_at`) VALUES
(1, 'College of Engineering', 'COE', 'College of Engineering offers various engineering programs', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(2, 'College of Automation and Control', 'CAC', 'College of Automation and Control focuses on automation and control systems', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(3, 'College of Engineering Technology', 'CET', 'College of Engineering Technology provides technical education', '2025-05-30 13:28:23', '2025-05-30 13:28:23');

-- --------------------------------------------------------

--
-- Table structure for table `exam_registrations`
--

CREATE TABLE `exam_registrations` (
  `registration_id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `exam_schedule_id` int(11) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('registered','cancelled','completed') NOT NULL DEFAULT 'registered'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_registrations`
--

INSERT INTO `exam_registrations` (`registration_id`, `applicant_id`, `exam_schedule_id`, `registration_date`, `status`) VALUES
(7, 77, 4, '2025-06-02 16:25:58', 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `exam_results`
--

CREATE TABLE `exam_results` (
  `result_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `status` enum('passed','failed') NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_results`
--

INSERT INTO `exam_results` (`result_id`, `exam_id`, `user_id`, `score`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 2, 2, 80.00, 'passed', NULL, '2025-05-30 15:44:00', '2025-06-02 16:16:53'),
(38, 4, 38, 12.00, 'failed', NULL, '2025-06-02 16:49:47', '2025-06-02 16:49:55');

-- --------------------------------------------------------

--
-- Table structure for table `exam_schedules`
--

CREATE TABLE `exam_schedules` (
  `exam_id` int(11) NOT NULL,
  `exam_date` date NOT NULL,
  `exam_time` time NOT NULL,
  `venue` varchar(255) NOT NULL,
  `max_participants` int(11) NOT NULL,
  `status` enum('scheduled','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_schedules`
--

INSERT INTO `exam_schedules` (`exam_id`, `exam_date`, `exam_time`, `venue`, `max_participants`, `status`, `created_at`, `updated_at`) VALUES
(1, '2025-05-30', '08:00:00', 'TUP Visayas', 20, 'scheduled', '2025-05-30 13:56:53', '2025-06-02 16:25:35'),
(2, '2025-06-02', '08:00:00', 'TUP Visayas', 20, 'scheduled', '2025-05-30 13:57:28', '2025-06-02 16:25:43'),
(3, '2025-06-09', '08:00:00', 'Erams East', 50, 'cancelled', '2025-06-02 15:23:50', '2025-06-02 16:13:02'),
(4, '2025-06-10', '07:00:00', 'TUP VISAYAS', 50, 'completed', '2025-06-02 16:24:41', '2025-06-02 16:29:17');

-- --------------------------------------------------------

--
-- Table structure for table `exam_scores`
--

CREATE TABLE `exam_scores` (
  `score_id` int(11) NOT NULL,
  `registration_id` int(11) NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `rank` int(11) DEFAULT NULL,
  `assigned_program_id` int(11) DEFAULT NULL,
  `interview_schedule_id` int(11) DEFAULT NULL,
  `status` enum('pending','qualified','not_qualified') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `interviewers`
--

CREATE TABLE `interviewers` (
  `user_id` int(11) NOT NULL,
  `program_id` int(11) DEFAULT NULL,
  `qualifications` text NOT NULL,
  `experience` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `interviews`
--

CREATE TABLE `interviews` (
  `interview_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `interviewer_id` int(11) NOT NULL,
  `program_head_id` int(11) NOT NULL,
  `scheduled_date` date NOT NULL,
  `scheduled_time` time NOT NULL,
  `status` enum('scheduled','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `result` enum('passed','failed') DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `completed_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `interviews`
--

INSERT INTO `interviews` (`interview_id`, `application_id`, `interviewer_id`, `program_head_id`, `scheduled_date`, `scheduled_time`, `status`, `result`, `score`, `notes`, `completed_date`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2024-04-15', '00:00:00', 'completed', 'passed', 25.00, 'Evaluation Scores:\nInterest and Motivation: 5/5\nCommunication Skills: 5/5\nComprehension and Critical Thinking: 5/5\nProgram-Relevant Skills: 5/5\nFinancial Capacity: 5/5\nTotal Score: 25/25\n\nAdditional Notes:\nasdasdas', '2025-05-30 21:32:35', '2025-05-30 13:31:59', '2025-05-30 13:32:35'),
(2, 2, 1, 1, '2024-04-15', '00:00:00', 'completed', 'passed', 19.00, 'Evaluation Scores:\nInterest and Motivation: 5/5\nCommunication Skills: 5/5\nComprehension and Critical Thinking: 5/5\nProgram-Relevant Skills: 3/5\nFinancial Capacity: 1/5\nTotal Score: 19/25\n\nAdditional Notes:\n', '2025-06-02 23:44:49', '2025-06-02 04:33:12', '2025-06-02 15:44:49'),
(3, 3, 1, 1, '2024-04-15', '00:00:00', 'completed', 'passed', 24.00, 'Evaluation Scores:\nInterest and Motivation: 4/5\nCommunication Skills: 5/5\nComprehension and Critical Thinking: 5/5\nProgram-Relevant Skills: 5/5\nFinancial Capacity: 5/5\nTotal Score: 24/25\n\nAdditional Notes:\n', '2025-06-02 23:41:48', '2025-06-02 15:24:55', '2025-06-02 15:41:48'),
(4, 4, 1, 1, '2024-04-15', '00:00:00', 'scheduled', NULL, NULL, NULL, NULL, '2025-06-02 16:09:59', '2025-06-02 16:09:59');

-- --------------------------------------------------------

--
-- Table structure for table `interview_results`
--

CREATE TABLE `interview_results` (
  `result_id` int(11) NOT NULL,
  `applicant_id` int(11) DEFAULT NULL,
  `interview_schedule_id` int(11) DEFAULT NULL,
  `interviewer_id` int(11) DEFAULT NULL,
  `rating` decimal(5,2) NOT NULL,
  `remarks` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `interview_schedules`
--

CREATE TABLE `interview_schedules` (
  `schedule_id` int(11) NOT NULL,
  `program_id` int(11) DEFAULT NULL,
  `interview_date` date NOT NULL,
  `time_window` enum('AM','PM') NOT NULL,
  `max_applicants` int(11) NOT NULL,
  `current_applicants` int(11) DEFAULT 0,
  `status` enum('open','closed','completed') DEFAULT 'open',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `interview_schedules`
--

INSERT INTO `interview_schedules` (`schedule_id`, `program_id`, `interview_date`, `time_window`, `max_applicants`, `current_applicants`, `status`, `created_by`, `created_at`) VALUES
(1, 1, '2024-04-15', 'AM', 20, 4, 'open', 1, '2025-05-30 13:28:23'),
(2, 2, '2024-04-15', 'PM', 20, 0, 'open', 1, '2025-05-30 13:28:23');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `type` enum('exam','interview','result','general') NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
(1, 1, 'New Interview Scheduled', 'New interview scheduled with Rhemjohn Pitong for Bachelor of Science in Electronics Engineering on April 15, 2024 AM', 'interview', 0, '2025-05-30 13:31:59'),
(2, 1, 'New Interview Scheduled', 'New interview scheduled with   for Bachelor of Science in Electronics Engineering on April 15, 2024 AM', 'interview', 0, '2025-06-02 04:33:12'),
(3, 1, 'New Interview Scheduled', 'New interview scheduled with   for Bachelor of Science in Electronics Engineering on April 15, 2024 AM', 'interview', 0, '2025-06-02 15:24:55'),
(4, 1, 'New Interview Scheduled', 'New interview scheduled with   for Bachelor of Science in Electronics Engineering on April 15, 2024 AM', 'interview', 0, '2025-06-02 16:09:59');

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `program_id` int(11) NOT NULL,
  `college_id` int(11) NOT NULL,
  `program_head_id` int(11) DEFAULT NULL,
  `program_name` varchar(100) NOT NULL,
  `program_code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`program_id`, `college_id`, `program_head_id`, `program_name`, `program_code`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Bachelor of Science in Electronics Engineering', 'BS ECE', 'Electronics Engineering program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(2, 1, 2, 'Bachelor of Science in Mechanical Engineering', 'BS ME', 'Mechanical Engineering program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(3, 1, 3, 'Bachelor of Science in Computer Engineering', 'BS CPE', 'Computer Engineering program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(4, 1, 4, 'Bachelor of Science in Electrical Engineering', 'BS EE', 'Electrical Engineering program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(5, 2, 5, 'Bachelor of Science in Instrumentation and Control Engineering Technology', 'BS ICET', 'Instrumentation and Control Engineering Technology program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(6, 2, 6, 'Bachelor of Science in Mechatronics Engineering', 'BS ME', 'Mechatronics Engineering program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(7, 2, 7, 'Bachelor of Science in Mechatronics Engineering Technology', 'BS MET', 'Mechatronics Engineering Technology program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(8, 3, 8, 'Bachelor of Science in Chemistry', 'BS Chem', 'Chemistry program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(9, 3, 9, 'Bachelor of Engineering major in Chemical Engineering Technology', 'BE ChET', 'Chemical Engineering Technology program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(10, 3, 10, 'Bachelor of Science in Automotive Engineering', 'BS AutoE', 'Automotive Engineering program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(11, 3, 11, 'Bachelor of Science in Computer Engineering Technology', 'BS CpET', 'Computer Engineering Technology program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(12, 3, 12, 'Bachelor of Science in Electrical Engineering Technology', 'BS EET', 'Electrical Engineering Technology program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(13, 3, 13, 'Bachelor of Science in Electromechanical Engineering Technology', 'BS EMET', 'Electromechanical Engineering Technology program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(14, 3, 14, 'Bachelor of Science in Heating, Ventilation and Air Conditioning-Refrigeration Engineering Technolog', 'BS HVAC-RET', 'HVAC-RET program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
(15, 3, NULL, 'Bachelor of Science in Manufacturing Engineering Technology', 'BS MFET', 'Manufacturing Engineering Technology program', '2025-05-30 13:28:23', '2025-05-30 13:28:23');

-- --------------------------------------------------------

--
-- Table structure for table `program_cutoffs`
--

CREATE TABLE `program_cutoffs` (
  `cutoff_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `cutoff_rank` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program_cutoffs`
--

INSERT INTO `program_cutoffs` (`cutoff_id`, `program_id`, `cutoff_rank`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 9, 500, 1, '2025-05-30 16:07:50', '2025-05-30 16:20:10'),
(2, 1, 150, 1, '2025-05-30 16:07:58', '2025-05-30 16:07:58'),
(3, 2, 300, 1, '2025-05-30 16:09:36', '2025-05-30 16:09:36');

-- --------------------------------------------------------

--
-- Table structure for table `program_heads`
--

CREATE TABLE `program_heads` (
  `program_head_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program_heads`
--

INSERT INTO `program_heads` (`program_head_id`, `user_id`, `program_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(2, 4, 2, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(3, 5, 3, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(4, 6, 4, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(5, 7, 5, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(6, 8, 6, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(7, 9, 7, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(8, 10, 8, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(9, 11, 9, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(10, 12, 10, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(11, 13, 11, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(12, 14, 12, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(13, 15, 13, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(14, 16, 14, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23');

-- --------------------------------------------------------

--
-- Table structure for table `program_rankings`
--

CREATE TABLE `program_rankings` (
  `ranking_id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `exam_score` decimal(5,2) NOT NULL,
  `rank_position` int(11) NOT NULL,
  `is_eligible` tinyint(1) DEFAULT 0,
  `assigned_program_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program_rankings`
--

INSERT INTO `program_rankings` (`ranking_id`, `applicant_id`, `program_id`, `exam_score`, `rank_position`, `is_eligible`, `assigned_program_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 80.00, 1, 1, 1, '2025-06-02 16:49:56', '2025-06-02 16:49:56'),
(2, 77, 1, 12.00, 2, 1, 13, '2025-06-02 16:49:56', '2025-06-02 16:49:56'),
(4, 1, 2, 80.00, 1, 1, 1, '2025-06-02 16:49:56', '2025-06-02 16:49:56'),
(5, 77, 13, 12.00, 1, 1, 13, '2025-06-02 16:49:56', '2025-06-02 16:49:56');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `user_type` enum('admin','interviewer','applicant') NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `first_name`, `last_name`, `email`, `phone`, `user_type`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '', 'admin@tup.edu.ph', NULL, 'admin', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(2, 'applicant1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rhemjohn', 'Pitong', 'applicant1@example.com', NULL, 'applicant', 'active', '2025-05-30 13:28:23', '2025-05-30 13:30:01'),
(3, 'ph_ece', '$2y$10$bgGTgQRuLq1YBohm8VOF..gwLyuVuVlKGA//y0c0/PA920g5orvjG', 'Renato', 'Deldo', 'ph_ece@tup.edu.ph', '', 'interviewer', 'active', '2025-05-30 13:28:23', '2025-06-02 05:55:08'),
(4, 'ph_me', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane', 'Doe', 'ph_me@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(5, 'ph_cpe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Robert', 'Johnson', 'ph_cpe@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(6, 'ph_ee', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah', 'Williams', 'ph_ee@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(7, 'ph_icet', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Michael', 'Brown', 'ph_icet@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(8, 'ph_met', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Emily', 'Davis', 'ph_met@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(9, 'ph_chem', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'David', 'Wilson', 'ph_chem@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(10, 'ph_chet', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Patricia', 'Taylor', 'ph_chet@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(11, 'ph_auto', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'James', 'Anderson', 'ph_auto@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(12, 'ph_cpet', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jennifer', 'Thomas', 'ph_cpet@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(13, 'ph_eet', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Richard', 'Jackson', 'ph_eet@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(14, 'ph_emet', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Susan', 'White', 'ph_emet@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(15, 'ph_hvac', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Charles', 'Harris', 'ph_hvac@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(16, 'ph_mfet', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Margaret', 'Martin', 'ph_mfet@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
(17, 'rhemjohn', '$2y$10$/t7ajDgS8E9zYqdv7c.ESutX6AMNXDgCrrDodpGWn3AnIyAEL.Fmu', '', '', 'rdpitong@gmail.com', NULL, 'applicant', 'active', '2025-06-02 04:32:38', '2025-06-02 04:32:59'),
(18, 'applicant16', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '', 'applicant16@example.com', NULL, 'applicant', 'active', '2025-06-02 15:22:16', '2025-06-02 15:22:16'),
(19, 'applicant17', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '', 'applicant17@example.com', NULL, 'applicant', 'active', '2025-06-02 15:22:16', '2025-06-02 15:22:16'),
(20, 'applicant18', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '', 'applicant18@example.com', NULL, 'applicant', 'active', '2025-06-02 15:22:16', '2025-06-02 15:22:16'),
(21, 'applicant19', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '', 'applicant19@example.com', NULL, 'applicant', 'active', '2025-06-02 15:22:16', '2025-06-02 15:22:16'),
(22, 'applicant20', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '', 'applicant20@example.com', NULL, 'applicant', 'active', '2025-06-02 15:22:16', '2025-06-02 15:22:16'),
(23, 'applicant21', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '', 'applicant21@example.com', NULL, 'applicant', 'active', '2025-06-02 15:22:16', '2025-06-02 15:22:16'),
(24, 'applicant22', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '', 'applicant22@example.com', NULL, 'applicant', 'active', '2025-06-02 15:22:16', '2025-06-02 15:22:16'),
(25, 'applicant23', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '', 'applicant23@example.com', NULL, 'applicant', 'active', '2025-06-02 15:22:16', '2025-06-02 15:22:16'),
(26, 'applicant24', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '', 'applicant24@example.com', NULL, 'applicant', 'active', '2025-06-02 15:22:16', '2025-06-02 15:22:16'),
(27, 'applicant25', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '', 'applicant25@example.com', NULL, 'applicant', 'active', '2025-06-02 15:22:16', '2025-06-02 15:22:16'),
(28, 'applicant26', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '', 'applicant26@example.com', NULL, 'applicant', 'active', '2025-06-02 15:22:16', '2025-06-02 15:22:16'),
(29, 'applicant27', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '', 'applicant27@example.com', NULL, 'applicant', 'active', '2025-06-02 15:22:16', '2025-06-02 15:22:16'),
(30, 'applicant28', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '', 'applicant28@example.com', NULL, 'applicant', 'active', '2025-06-02 15:22:16', '2025-06-02 15:22:16'),
(31, 'applicant29', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '', 'applicant29@example.com', NULL, 'applicant', 'active', '2025-06-02 15:22:16', '2025-06-02 15:22:16'),
(32, 'applicant30', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '', 'applicant30@example.com', NULL, 'applicant', 'active', '2025-06-02 15:22:16', '2025-06-02 15:22:16'),
(33, 'applicant31', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '', 'applicant31@example.com', NULL, 'applicant', 'active', '2025-06-02 15:22:16', '2025-06-02 15:22:16'),
(34, 'applicant32', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '', 'applicant32@example.com', NULL, 'applicant', 'active', '2025-06-02 15:22:16', '2025-06-02 15:22:16'),
(35, 'applicant33', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '', 'applicant33@example.com', NULL, 'applicant', 'active', '2025-06-02 15:22:16', '2025-06-02 15:22:16'),
(36, 'applicant34', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '', 'applicant34@example.com', NULL, 'applicant', 'active', '2025-06-02 15:22:16', '2025-06-02 15:22:16'),
(37, 'applicant35', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '', 'applicant35@example.com', NULL, 'applicant', 'active', '2025-06-02 15:22:16', '2025-06-02 15:22:16'),
(38, 'joelie', '$2y$10$wqDF0a8VualpEnBShzkYXuM6fslb.UZLiljC44iTQHhB.t4dw4bSG', '', '', 'joelieannalegre@yahoo.com', NULL, 'applicant', 'active', '2025-06-02 16:09:24', '2025-06-02 16:09:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `applicants`
--
ALTER TABLE `applicants`
  ADD PRIMARY KEY (`applicant_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `primary_program_id` (`primary_program_id`),
  ADD KEY `secondary_program_id` (`secondary_program_id`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `colleges`
--
ALTER TABLE `colleges`
  ADD PRIMARY KEY (`college_id`),
  ADD UNIQUE KEY `college_code` (`college_code`);

--
-- Indexes for table `exam_registrations`
--
ALTER TABLE `exam_registrations`
  ADD PRIMARY KEY (`registration_id`),
  ADD UNIQUE KEY `unique_applicant_schedule` (`applicant_id`,`exam_schedule_id`),
  ADD KEY `exam_schedule_id` (`exam_schedule_id`);

--
-- Indexes for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD PRIMARY KEY (`result_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `exam_schedules`
--
ALTER TABLE `exam_schedules`
  ADD PRIMARY KEY (`exam_id`);

--
-- Indexes for table `exam_scores`
--
ALTER TABLE `exam_scores`
  ADD PRIMARY KEY (`score_id`),
  ADD UNIQUE KEY `registration_id` (`registration_id`),
  ADD KEY `assigned_program_id` (`assigned_program_id`),
  ADD KEY `interview_schedule_id` (`interview_schedule_id`);

--
-- Indexes for table `interviewers`
--
ALTER TABLE `interviewers`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `interviews`
--
ALTER TABLE `interviews`
  ADD PRIMARY KEY (`interview_id`),
  ADD KEY `application_id` (`application_id`),
  ADD KEY `interviewer_id` (`interviewer_id`),
  ADD KEY `program_head_id` (`program_head_id`);

--
-- Indexes for table `interview_results`
--
ALTER TABLE `interview_results`
  ADD PRIMARY KEY (`result_id`),
  ADD KEY `applicant_id` (`applicant_id`),
  ADD KEY `interview_schedule_id` (`interview_schedule_id`),
  ADD KEY `interviewer_id` (`interviewer_id`);

--
-- Indexes for table `interview_schedules`
--
ALTER TABLE `interview_schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`program_id`),
  ADD KEY `college_id` (`college_id`),
  ADD KEY `programs_ibfk_2` (`program_head_id`);

--
-- Indexes for table `program_cutoffs`
--
ALTER TABLE `program_cutoffs`
  ADD PRIMARY KEY (`cutoff_id`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `program_heads`
--
ALTER TABLE `program_heads`
  ADD PRIMARY KEY (`program_head_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `program_id` (`program_id`);

--
-- Indexes for table `program_rankings`
--
ALTER TABLE `program_rankings`
  ADD PRIMARY KEY (`ranking_id`),
  ADD KEY `applicant_id` (`applicant_id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `assigned_program_id` (`assigned_program_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `applicants`
--
ALTER TABLE `applicants`
  MODIFY `applicant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `colleges`
--
ALTER TABLE `colleges`
  MODIFY `college_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `exam_registrations`
--
ALTER TABLE `exam_registrations`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `exam_schedules`
--
ALTER TABLE `exam_schedules`
  MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `exam_scores`
--
ALTER TABLE `exam_scores`
  MODIFY `score_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `interviews`
--
ALTER TABLE `interviews`
  MODIFY `interview_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `interview_results`
--
ALTER TABLE `interview_results`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `interview_schedules`
--
ALTER TABLE `interview_schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `program_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `program_cutoffs`
--
ALTER TABLE `program_cutoffs`
  MODIFY `cutoff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `program_heads`
--
ALTER TABLE `program_heads`
  MODIFY `program_head_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `program_rankings`
--
ALTER TABLE `program_rankings`
  MODIFY `ranking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `applicants`
--
ALTER TABLE `applicants`
  ADD CONSTRAINT `applicants_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `applicants_ibfk_2` FOREIGN KEY (`primary_program_id`) REFERENCES `programs` (`program_id`),
  ADD CONSTRAINT `applicants_ibfk_3` FOREIGN KEY (`secondary_program_id`) REFERENCES `programs` (`program_id`);

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_registrations`
--
ALTER TABLE `exam_registrations`
  ADD CONSTRAINT `exam_registrations_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_registrations_ibfk_2` FOREIGN KEY (`exam_schedule_id`) REFERENCES `exam_schedules` (`exam_id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD CONSTRAINT `exam_results_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exam_schedules` (`exam_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_results_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_scores`
--
ALTER TABLE `exam_scores`
  ADD CONSTRAINT `exam_scores_ibfk_1` FOREIGN KEY (`registration_id`) REFERENCES `exam_registrations` (`registration_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_scores_ibfk_2` FOREIGN KEY (`assigned_program_id`) REFERENCES `programs` (`program_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `exam_scores_ibfk_3` FOREIGN KEY (`interview_schedule_id`) REFERENCES `interview_schedules` (`schedule_id`) ON DELETE SET NULL;

--
-- Constraints for table `interviewers`
--
ALTER TABLE `interviewers`
  ADD CONSTRAINT `interviewers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `interviewers_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`);

--
-- Constraints for table `interviews`
--
ALTER TABLE `interviews`
  ADD CONSTRAINT `interviews_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`application_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `interviews_ibfk_2` FOREIGN KEY (`interviewer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `interviews_ibfk_3` FOREIGN KEY (`program_head_id`) REFERENCES `program_heads` (`program_head_id`) ON DELETE CASCADE;

--
-- Constraints for table `interview_results`
--
ALTER TABLE `interview_results`
  ADD CONSTRAINT `interview_results_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`),
  ADD CONSTRAINT `interview_results_ibfk_2` FOREIGN KEY (`interview_schedule_id`) REFERENCES `interview_schedules` (`schedule_id`),
  ADD CONSTRAINT `interview_results_ibfk_3` FOREIGN KEY (`interviewer_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `interview_schedules`
--
ALTER TABLE `interview_schedules`
  ADD CONSTRAINT `interview_schedules_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`),
  ADD CONSTRAINT `interview_schedules_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `programs`
--
ALTER TABLE `programs`
  ADD CONSTRAINT `programs_ibfk_1` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `programs_ibfk_2` FOREIGN KEY (`program_head_id`) REFERENCES `program_heads` (`program_head_id`) ON DELETE SET NULL;

--
-- Constraints for table `program_cutoffs`
--
ALTER TABLE `program_cutoffs`
  ADD CONSTRAINT `program_cutoffs_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE;

--
-- Constraints for table `program_heads`
--
ALTER TABLE `program_heads`
  ADD CONSTRAINT `program_heads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `program_heads_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE;

--
-- Constraints for table `program_rankings`
--
ALTER TABLE `program_rankings`
  ADD CONSTRAINT `program_rankings_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `program_rankings_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `program_rankings_ibfk_3` FOREIGN KEY (`assigned_program_id`) REFERENCES `programs` (`program_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
