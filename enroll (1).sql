-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 04, 2025 at 12:03 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `enroll`
--

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'BSIT 1A', 'Bachelor of Science in Information Technology - 1st Year, Section A', '2025-09-03 15:42:26'),
(2, 'asdasd', 'asdasd', '2025-09-04 09:17:08');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `mname` varchar(100) DEFAULT NULL,
  `lname` varchar(100) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `email` varchar(150) NOT NULL,
  `class_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `fname`, `mname`, `lname`, `gender`, `email`, `class_id`, `created_at`, `updated_at`) VALUES
(5, '123a', 'asd', 'asda', 'sasd', 'Female', 'ahdahasdh@gmail.com', 1, '2025-09-03 15:59:42', '2025-09-03 15:59:42'),
(6, '123123213', 'asddsa', 'asddsa', 'asddsa', 'Male', 'hahaasdh@gmail.com', 1, '2025-09-04 08:40:34', '2025-09-04 08:40:34'),
(8, '13213123', 'haha', 'qwe', 'eqweqwe', 'Male', 'hahaaasddsdh@gmail.com', 1, '2025-09-04 08:55:28', '2025-09-04 08:55:28'),
(9, 'd31231', 'asdasd', 'asdasd', 'asdasd', 'Male', 'ahdahasddh@gmail.com', 1, '2025-09-04 09:14:01', '2025-09-04 09:14:01'),
(10, '02-2526-141427', 'dsadasd', 'asdasd', 'asdadasd', 'Male', 'ahdahasdasdsdh@gmail.com', 1, '2025-09-04 09:36:07', '2025-09-04 09:36:07'),
(11, '02-2526-224599', 'asdasddasdasdsa', 'asdasddasd', 'asdasdasdasd', 'Male', 'ahdahsasdasddh@gmail.com', 1, '2025-09-04 09:58:15', '2025-09-04 09:58:15'),
(12, '02-2526-326558', 'dasdasdasdasdasd', 'asdadadsda', 'asddsa', 'Male', 'ahdaasdhsdh@gmail.com', 1, '2025-09-04 10:00:34', '2025-09-04 10:00:34');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `teacher_id` varchar(50) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `mname` varchar(100) DEFAULT NULL,
  `lname` varchar(100) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `email` varchar(150) NOT NULL,
  `department` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `teacher_id`, `fname`, `mname`, `lname`, `gender`, `email`, `department`, `created_at`, `updated_at`) VALUES
(1, '02-2526-053047', 'sandy jean', 'fabria', 'Panong', 'Female', 'sandy@gmail.com', 'asdasdasd', '2025-09-04 09:57:17', '2025-09-04 09:57:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_students_class` (`class_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teacher_id` (`teacher_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_students_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
