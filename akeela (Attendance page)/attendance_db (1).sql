-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 02, 2026 at 09:07 AM
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
-- Database: `attendance_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance_records`
--

CREATE TABLE `attendance_records` (
  `id` int(11) NOT NULL,
  `emp_id` varchar(50) NOT NULL,
  `attendance_date` date NOT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `status` enum('Present','Late','Absent') DEFAULT 'Present',
  `remarks` varchar(255) DEFAULT 'On time'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_records`
--

INSERT INTO `attendance_records` (`id`, `emp_id`, `attendance_date`, `check_in`, `check_out`, `status`, `remarks`) VALUES
(7, 'em10', '2026-06-12', '08:18:35', '00:00:00', 'Present', 'Auto Checked In via Dashboard');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `emp_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`emp_id`, `name`, `age`, `email`, `phone`, `created_at`) VALUES
('EM039', 'EM039', 12, 'hafeel9@gmail.com', '15828585269', '2026-06-10 05:23:54'),
('EM1', 'AKEELA', 21, 'akeefathi07@gmail.com', '0248756156459', '2026-06-05 08:36:56'),
('em10', 'Akeela', 20, 'akeefathi07@gmail.com', '3456789', '2026-06-12 06:17:27'),
('EM15', 'AKEELAk', 20, 'hafeel@gmail.com', '0767483453', '2026-06-10 05:31:33'),
('EM2', 'hafeel', 22, 'hafeel@gmail.com', '49179409', '2026-06-05 08:38:30'),
('EM20', 'AFRATH', 25, 'hafeel9@gmail.com', '041840849', '2026-06-10 05:24:53'),
('EM25', 'AKEELA', 55, 'hafeel@gmail.com', '9708342', '2026-06-10 05:33:07'),
('EM3', 'hafeel', 22, 'hafeel@gmail.com', '0767483453', '2026-06-06 15:33:21'),
('EM4', 'AAQIL', 20, 'akeefathi07@gmail.com', '0767483453', '2026-06-07 07:18:58'),
('em5', 'Akeela', 20, 'akeefathi07@gmail.com', '23456', '2026-06-12 06:10:03'),
('em7', 'Akeela', 20, 'akeefathi07@gmail.com', '1234567', '2026-06-12 06:07:16'),
('em8', 'Akeela', 20, 'akeefathi07@gmail.com', '0771234555', '2026-06-11 15:58:28');

-- --------------------------------------------------------

--
-- Table structure for table `performance_reviews`
--

CREATE TABLE `performance_reviews` (
  `id` int(11) NOT NULL,
  `employee_name` varchar(100) NOT NULL,
  `review_period` varchar(20) NOT NULL,
  `kpi_score` decimal(5,2) NOT NULL,
  `reviewed_by` varchar(50) NOT NULL,
  `feedback` text DEFAULT NULL,
  `review_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `performance_reviews`
--

INSERT INTO `performance_reviews` (`id`, `employee_name`, `review_period`, `kpi_score`, `reviewed_by`, `feedback`, `review_date`) VALUES
(1, 'hafeel', 'Q4 2025', 25.10, 'HR Manager', 'mn', '2026-07-01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance_records`
--
ALTER TABLE `attendance_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_daily_attendance` (`emp_id`,`attendance_date`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`emp_id`);

--
-- Indexes for table `performance_reviews`
--
ALTER TABLE `performance_reviews`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance_records`
--
ALTER TABLE `attendance_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `performance_reviews`
--
ALTER TABLE `performance_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance_records`
--
ALTER TABLE `attendance_records`
  ADD CONSTRAINT `attendance_records_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
