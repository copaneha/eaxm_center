-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2026 at 07:08 AM
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
-- Database: `exam_center`
--

-- --------------------------------------------------------

--
-- Table structure for table `admit_cards`
--

CREATE TABLE `admit_cards` (
  `admit_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `exam_id` int(11) DEFAULT NULL,
  `seat_id` int(11) DEFAULT NULL,
  `generated_on` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `status` varchar(20) DEFAULT 'Present',
  `punch_time` datetime DEFAULT current_timestamp(),
  `ip_address` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `exam_id`, `status`, `punch_time`, `ip_address`) VALUES
(2, 1, 1, 'Present', '2026-03-23 03:01:46', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) DEFAULT NULL,
  `attendance_date` date DEFAULT NULL,
  `status` enum('Present','Absent') DEFAULT 'Present',
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendances`
--

INSERT INTO `attendances` (`id`, `emp_id`, `attendance_date`, `status`, `time_in`, `time_out`) VALUES
(2, 11, '2026-03-23', 'Present', '11:13:41', NULL),
(3, 11, '2026-04-27', 'Present', '11:40:55', '11:41:32');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `admin_reply` text DEFAULT NULL,
  `status` enum('Pending','Replied') DEFAULT 'Pending',
  `reply_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `admin_reply`, `status`, `reply_message`, `created_at`) VALUES
(2, 'neha maurya', 'nm3456008@gmail.com', 'result related', 'my result no issue why', 'Drrttffs', 'Replied', NULL, '2026-04-27 05:52:15');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `course_description` text DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `fees` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_name`, `course_description`, `duration`, `fees`, `created_at`) VALUES
(3, 'copa', 'computer  and programming assitant', '1', 20000.00, '2026-03-23 08:38:38');

-- --------------------------------------------------------

--
-- Table structure for table `duties`
--

CREATE TABLE `duties` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `exam_name` varchar(255) NOT NULL,
  `exam_date` date DEFAULT NULL,
  `exam_time` varchar(100) NOT NULL,
  `room` varchar(50) DEFAULT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `duties`
--

INSERT INTO `duties` (`id`, `employee_id`, `exam_name`, `exam_date`, `exam_time`, `room`, `assigned_at`) VALUES
(8, 11, ' python', '2026-04-27', '10:45am', '12', '2026-04-27 06:46:57');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `centre` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `name`, `email`, `role`, `centre`, `password`, `photo`, `created_at`) VALUES
(11, 'neha maurya', 'nm3456008@gmail.com', 'empolye', '2', '$2y$10$BFCGdql.PrpIcAQhlhQotea19GLeOA3myHJDDbb9m8l/9AZCNYrCu', '1774255272_neha.jpeg', '2026-03-23 10:13:12');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `exam_id` int(11) NOT NULL,
  `exam_name` varchar(100) DEFAULT NULL,
  `subject_name` varchar(100) DEFAULT NULL,
  `exam_date` date DEFAULT NULL,
  `exam_time` time DEFAULT NULL,
  `exam_end_time` time DEFAULT NULL,
  `centre` varchar(100) DEFAULT NULL,
  `total_students` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `course` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`exam_id`, `exam_name`, `subject_name`, `exam_date`, `exam_time`, `exam_end_time`, `centre`, `total_students`, `status`, `course`) VALUES
(1, 'copa', 'computer fundamental', '2026-03-23', '02:55:00', '03:50:00', 'js iti', 1, 'Active', NULL),
(2, 'copa', 'python', '2026-04-27', '15:25:00', '03:50:00', 'js iti', 2, 'Active', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exam_centres`
--

CREATE TABLE `exam_centres` (
  `id` int(11) NOT NULL,
  `centre_name` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `contact_no` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `centre_code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_centres`
--

INSERT INTO `exam_centres` (`id`, `centre_name`, `address`, `city`, `status`, `contact_no`, `email`, `centre_code`) VALUES
(15, 'js iti', 'js pvt iti college palhi patti varanasi', 'varanasi', 'Active', '8400645246', 'jsiti@gmai.com', '2');

-- --------------------------------------------------------

--
-- Table structure for table `exam_results`
--

CREATE TABLE `exam_results` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `exam_id` int(11) DEFAULT NULL,
  `total_questions` int(11) DEFAULT NULL,
  `correct_answers` int(11) DEFAULT NULL,
  `score_percentage` decimal(5,2) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_submissions`
--

CREATE TABLE `exam_submissions` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `total_questions` int(11) NOT NULL,
  `correct_ans` int(11) NOT NULL,
  `wrong_ans` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `submitted_at` datetime DEFAULT current_timestamp(),
  `is_issued` tinyint(1) DEFAULT 0,
  `issued_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_submissions`
--

INSERT INTO `exam_submissions` (`id`, `student_id`, `exam_id`, `total_questions`, `correct_ans`, `wrong_ans`, `score`, `status`, `submitted_at`, `is_issued`, `issued_at`) VALUES
(3, 1, 1, 2, 0, 0, 0, 'Completed', '2026-04-26 23:45:46', 1, '2026-04-27 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `exam_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `q1` text DEFAULT NULL,
  `q2` text DEFAULT NULL,
  `q3` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `student_id`, `exam_id`, `rating`, `q1`, `q2`, `q3`, `created_at`) VALUES
(1, 1, 0, 5, 'yes', 'no', ' thod our best facility', '2026-03-23 10:02:23'),
(2, 1, 0, 5, 'yes', 'no', ' thod our best facility', '2026-03-23 10:03:32');

-- --------------------------------------------------------

--
-- Table structure for table `labs`
--

CREATE TABLE `labs` (
  `id` int(11) NOT NULL,
  `centre_id` int(11) DEFAULT NULL,
  `lab_name` varchar(50) DEFAULT NULL,
  `lab_code` varchar(50) DEFAULT NULL,
  `floor_no` varchar(50) DEFAULT NULL,
  `total_computers` int(11) DEFAULT NULL,
  `status` enum('Available','Maintenance') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `labs`
--

INSERT INTO `labs` (`id`, `centre_id`, `lab_name`, `lab_code`, `floor_no`, `total_computers`, `status`) VALUES
(21, 15, 'copa', '20', '1', 25, '');

-- --------------------------------------------------------

--
-- Table structure for table `question_bank`
--

CREATE TABLE `question_bank` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `exam_name` varchar(100) DEFAULT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `question` text DEFAULT NULL,
  `option_a` varchar(255) DEFAULT NULL,
  `option_b` varchar(255) DEFAULT NULL,
  `option_c` varchar(255) DEFAULT NULL,
  `option_d` varchar(255) DEFAULT NULL,
  `correct_option` varchar(1) DEFAULT NULL,
  `marks` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question_bank`
--

INSERT INTO `question_bank` (`id`, `exam_id`, `exam_name`, `subject`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `marks`, `created_at`) VALUES
(1, 1, 'copa', 'computer fundamental', 'What is the brain of the computer?', 'RAM', 'CPU', 'Hard Disk', 'Monitor', 'B', 1, '2026-03-23 09:38:38'),
(2, 1, 'copa', 'computer fundamental', 'What does \'RAM\' stand for?', 'Read Access Memory', 'Random Access Memory', 'Remote Authorization Mechanism', 'Real Absolute Memory', 'A', 1, '2026-03-23 09:39:38'),
(3, 2, 'copa', 'python', '. Which keyword is used to define a function in Python?', 'func', 'define', 'def', 'function', 'C', 1, '2026-04-27 06:05:52'),
(4, 2, 'copa', 'python', 'Which of the following data types is immutable?', ' List', 'Dictionary', 'Tuple', 'Set', 'C', 1, '2026-04-27 06:35:03'),
(5, 2, 'copa', 'python', 'Which symbol is used for comments in Python?', '//', '#', '/* */', '--', 'B', 1, '2026-04-27 06:37:01'),
(6, 2, 'copa', 'python', 'Which function is used to get user input?', 'scan()', 'get()', 'input()', 'read()', 'C', 1, '2026-04-27 06:38:04'),
(7, 2, 'copa', 'python', 'Which of the following is used to create a dictionary?', '[]', '{}', '()', '<>', 'B', 1, '2026-04-27 06:42:09');

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `exam_name` varchar(100) DEFAULT NULL,
  `total_marks` int(11) DEFAULT NULL,
  `obtained_marks` int(11) DEFAULT NULL,
  `exam_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seat_allocation`
--

CREATE TABLE `seat_allocation` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `centre_id` int(11) NOT NULL,
  `lab_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `seat_no` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seat_allocation`
--

INSERT INTO `seat_allocation` (`id`, `exam_id`, `centre_id`, `lab_id`, `student_id`, `seat_no`, `created_at`) VALUES
(15, 1, 15, 21, 1, 'PC-1', '2026-03-23 09:35:19');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `roll_no` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `course` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `name`, `father_name`, `email`, `phone`, `gender`, `dob`, `roll_no`, `password`, `status`, `course`, `address`, `photo`, `created_at`, `rejection_reason`) VALUES
(1, 'neha maurya', 'savaru maurya', 'nm3456008@gmail.com', '8400645242', 'Female', '2002-10-10', '1001', '$2y$10$NmQkhFwewOtkJymDha5jiOzQJO9.czsDGmMXzlprr5xDZouM49Uoi', 'approved', 'copa', 'vikrampur kaithan palahi patti varanasi', '1774255272_neha.jpeg', '2026-03-23 08:41:12', NULL),
(2, 'Pratima maurya ', 'Savaru maurya ', 'pratimamurya0@gmail.com', '7652035830', 'Female', '1999-08-25', NULL, NULL, 'rejected', 'copa', 'Vikarmpur', '1774331373_IMG_20260214_211928.jpg', '2026-03-24 05:49:33', 'Dghfd'),
(3, 'Sonam', 'Santosh', 'aditiyadav14563@gmail.com', '7317845610', 'Female', '2025-09-17', '1003', 'TPWAXR2G', 'approved', 'copa', 'Palahi patti varanasi', '1777283104_alert.gif', '2026-04-27 09:45:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','employee') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`) VALUES
(1, 'nm3456008@gmail.com', 'e00cf25ad42683b3df678c61f42c6bda', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admit_cards`
--
ALTER TABLE `admit_cards`
  ADD PRIMARY KEY (`admit_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emp_id` (`emp_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `duties`
--
ALTER TABLE `duties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`exam_id`);

--
-- Indexes for table `exam_centres`
--
ALTER TABLE `exam_centres`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exam_submissions`
--
ALTER TABLE `exam_submissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `labs`
--
ALTER TABLE `labs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `centre_id` (`centre_id`);

--
-- Indexes for table `question_bank`
--
ALTER TABLE `question_bank`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seat_allocation`
--
ALTER TABLE `seat_allocation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admit_cards`
--
ALTER TABLE `admit_cards`
  MODIFY `admit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `duties`
--
ALTER TABLE `duties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `exam_centres`
--
ALTER TABLE `exam_centres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_submissions`
--
ALTER TABLE `exam_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `labs`
--
ALTER TABLE `labs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `question_bank`
--
ALTER TABLE `question_bank`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seat_allocation`
--
ALTER TABLE `seat_allocation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `duties`
--
ALTER TABLE `duties`
  ADD CONSTRAINT `duties_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `labs`
--
ALTER TABLE `labs`
  ADD CONSTRAINT `labs_ibfk_1` FOREIGN KEY (`centre_id`) REFERENCES `exam_centres` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
