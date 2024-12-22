-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 08, 2024 at 05:28 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quiz`
--

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attendance`
--

CREATE TABLE `quiz_attendance` (
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `register` varchar(255) NOT NULL,
  `section` varchar(50) NOT NULL,
  `stream` varchar(50) NOT NULL,
  `code` varchar(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  `marks` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_attendance`
--

INSERT INTO `quiz_attendance` (`email`, `name`, `register`, `section`, `stream`, `code`, `title`, `marks`) VALUES
('9921004610@klu.ac.in', 'RATNA SELVAN S', '9921004610', 'S25', 'CSF', '0AFA2A', 'cyber', 0),
('9921004610@klu.ac.in', 'RATNA SELVAN S', '9921004610', 'S25', 'CSF', '0AFA2A', 'cyber', 0),
('9921004773@klu.ac.in', 'VIJAYKUMAR J', '9921004773', 'S11', 'AIML', '0AFA2A', 'cyber', 0);

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--

CREATE TABLE `registration` (
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `reset_token_hash` varchar(255) DEFAULT NULL,
  `reset_token_expires_at` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `registration`
--

INSERT INTO `registration` (`email`, `password`, `reset_token_hash`, `reset_token_expires_at`) VALUES
('9921004002@klu.ac.in', '12345678', NULL, NULL),
('9921004610@klu.ac.in', '$2y$10$mjr2c.UJV81D1qgueiwsF.X26XQCitYbuk3kaRA5Y/jtrgjJCqSli', NULL, NULL),
('9921004613@klu.ac.in', '12345678', NULL, NULL),
('9921004693@klu.ac.in', '12345678', NULL, NULL),
('9921004773@klu.ac.in', '12345678', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `scheduled_quizzes`
--

CREATE TABLE `scheduled_quizzes` (
  `user_email` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `quiz_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `number_of_questions` int(11) DEFAULT NULL,
  `code` varchar(6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `scheduled_quizzes`
--

INSERT INTO `scheduled_quizzes` (`user_email`, `title`, `quiz_date`, `start_time`, `end_time`, `number_of_questions`, `code`, `created_at`, `file_path`) VALUES
('9921004610@klu.ac.in', 'cyber', '2024-02-08', '03:47:00', '07:45:00', 3, '0AFA2A', '2024-02-07 22:15:20', 'uploads/9921004610@klu.ac.in_1707344091.csv');

-- --------------------------------------------------------

--
-- Table structure for table `student_db`
--

CREATE TABLE `student_db` (
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `reset_token_hash` varchar(255) DEFAULT NULL,
  `reset_token_expires_at` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `student_db`
--

INSERT INTO `student_db` (`email`, `password`, `reset_token_hash`, `reset_token_expires_at`) VALUES
('9921004002@klu.ac.in', '12345678', '', NULL),
('9921004263@klu.ac.in', '12345678', NULL, NULL),
('9921004610@klu.ac.in', '12345678', NULL, NULL),
('9921004773@klu.ac.in', '12345678', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_info`
--

CREATE TABLE `student_info` (
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `register` varchar(255) NOT NULL,
  `section` varchar(50) DEFAULT NULL,
  `stream` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_info`
--

INSERT INTO `student_info` (`email`, `name`, `register`, `section`, `stream`) VALUES
('9921004002@klu.ac.in', 'ABHILASH M', '9921004002', 'S27', 'DS'),
('9921004263@klu.ac.in', 'HARISH V', '9921004263', 'S28', 'DS'),
('9921004610@klu.ac.in', 'RATNA SELVAN S', '9921004610', 'S25', 'CSF'),
('9921004773@klu.ac.in', 'VIJAYKUMAR J', '9921004773', 'S11', 'AIML');

-- --------------------------------------------------------

--
-- Table structure for table `uploaded_files`
--

CREATE TABLE `uploaded_files` (
  `user_email` varchar(255) NOT NULL,
  `original_file_name` varchar(255) DEFAULT NULL,
  `unique_key` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `upload_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `uploaded_files`
--

INSERT INTO `uploaded_files` (`user_email`, `original_file_name`, `unique_key`, `file_path`, `upload_timestamp`) VALUES
('9921004610@klu.ac.in', 'quiz.csv', '9921004610@klu.ac.in_1707344091', 'uploads/9921004610@klu.ac.in_1707344091.csv', '2024-02-07 22:14:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `quiz_attendance`
--
ALTER TABLE `quiz_attendance`
  ADD KEY `email` (`email`);

--
-- Indexes for table `registration`
--
ALTER TABLE `registration`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `student_db`
--
ALTER TABLE `student_db`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `student_info`
--
ALTER TABLE `student_info`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `uploaded_files`
--
ALTER TABLE `uploaded_files`
  ADD PRIMARY KEY (`user_email`,`file_path`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `quiz_attendance`
--
ALTER TABLE `quiz_attendance`
  ADD CONSTRAINT `quiz_attendance_ibfk_1` FOREIGN KEY (`email`) REFERENCES `student_info` (`email`);

--
-- Constraints for table `uploaded_files`
--
ALTER TABLE `uploaded_files`
  ADD CONSTRAINT `uploaded_files_ibfk_1` FOREIGN KEY (`user_email`) REFERENCES `registration` (`email`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
