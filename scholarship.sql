-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 30, 2025 at 10:12 AM
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
-- Database: `scholarship`
--

-- --------------------------------------------------------

--
-- Table structure for table `application`
--

CREATE TABLE `application` (
  `application_id` int(11) NOT NULL,
  `student_id` varchar(20) DEFAULT NULL,
  `scholarship_id` int(11) DEFAULT NULL,
  `sub_date` date DEFAULT NULL,
  `status` enum('Pending','Shortlisted','Approved','Rejected') DEFAULT 'Pending',
  `score` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `application`
--

INSERT INTO `application` (`application_id`, `student_id`, `scholarship_id`, `sub_date`, `status`, `score`) VALUES
(4, '2022/E/063', 9, '2025-06-24', 'Approved', 100),
(5, '2022/E/065', 14, '2025-06-24', 'Approved', 100),
(6, '2022/E/109', 16, '2025-06-30', 'Rejected', 40),
(7, '2022/E/076', 17, '2025-06-30', 'Rejected', 80),
(8, '2022/E/076', 18, '2025-06-30', 'Approved', 80),
(9, '2022/E/109', 17, '2025-06-30', 'Rejected', 70),
(10, '2022/E/063', 16, '2025-06-30', 'Approved', 90),
(11, '2022/E/063', 17, '2025-06-30', 'Approved', 90),
(12, '2022/E/109', 9, '2025-06-30', 'Approved', 100);

-- --------------------------------------------------------

--
-- Table structure for table `coordinator`
--

CREATE TABLE `coordinator` (
  `coordinator_id` varchar(20) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coordinator`
--

INSERT INTO `coordinator` (`coordinator_id`, `name`, `email`, `phone_number`, `password`) VALUES
('C001', 'john', 'John@gmail.com', '0753445113', '3444'),
('C002', 'Amila', 'Amila@gmail.com', '0742343221', '1234'),
('C003', 'Smith', 'Smith@gmail.com', '0714522557', '1111'),
('C004', 'Mayooran', 'Mayooran@gmail.com', '0751234567', '2222');

-- --------------------------------------------------------

--
-- Table structure for table `eligibility_criteria`
--

CREATE TABLE `eligibility_criteria` (
  `criteria_id` int(11) NOT NULL,
  `scholarship_id` int(11) DEFAULT NULL,
  `min_score` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eligibility_criteria`
--

INSERT INTO `eligibility_criteria` (`criteria_id`, `scholarship_id`, `min_score`) VALUES
(1, 9, 80),
(2, 14, 80),
(4, 16, 75),
(5, 17, 82),
(6, 18, 78);

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `notification_id` int(11) NOT NULL,
  `application_id` int(11) DEFAULT NULL,
  `notif_date` date DEFAULT NULL,
  `status_message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`notification_id`, `application_id`, `notif_date`, `status_message`) VALUES
(13, 4, '2025-06-24', 'Your application for \'bursary\' has been Pending.'),
(14, 4, '2025-06-24', 'Your application for \'bursary\' has been Approved.'),
(15, 4, '2025-06-24', 'Scholarship payment for June 2025 has been credited. Ref: 1234.'),
(16, 5, '2025-06-24', 'Your application for \'union\' has been Approved.'),
(17, 7, '2025-06-30', 'Your application for \'StudentShip\' has been Rejected.'),
(18, 6, '2025-06-30', 'Your application for \'Mahapola\' has been Rejected.'),
(19, 8, '2025-06-30', 'Your application for \'HH\' has been Approved.'),
(20, 8, '2025-06-30', 'Scholarship payment for January 2025 has been credited. Ref: 1234.'),
(21, 9, '2025-06-30', 'Your application for \'StudentShip\' has been Rejected.'),
(22, 11, '2025-06-30', 'Your application for \'StudentShip\' has been Approved.'),
(23, 11, '2025-06-30', 'Scholarship payment for January 2025 has been credited. Ref: 2345.'),
(24, 10, '2025-06-30', 'Your application for \'Mahapola\' has been Approved.'),
(25, 12, '2025-06-30', 'Your application for \'bursary\' has been Approved.'),
(26, 5, '2025-06-30', 'Scholarship payment for January 2025 has been credited. Ref: 0001.'),
(27, 12, '2025-06-30', 'Scholarship payment for January 2025 has been credited. Ref: 00001.');

-- --------------------------------------------------------

--
-- Table structure for table `provider`
--

CREATE TABLE `provider` (
  `provider_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact_no` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `provider`
--

INSERT INTO `provider` (`provider_id`, `name`, `email`, `contact_no`) VALUES
(1, 'Nippon', 'Nippon@gmail.com', '0651111111'),
(3, 'YFC', 'YFC@gmail.com', '0652222123'),
(10, 'Goverment', 'Lanka@gmail.com', '0651234321'),
(11, 'Tharu Store', 'Tharu@gmail.com', '0652343211'),
(12, 'Helping Hand', 'Help@gmail.com', '0651234567');

-- --------------------------------------------------------

--
-- Table structure for table `scholarship`
--

CREATE TABLE `scholarship` (
  `scholarship_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `coordinator_id` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarship`
--

INSERT INTO `scholarship` (`scholarship_id`, `name`, `amount`, `deadline`, `provider_id`, `coordinator_id`) VALUES
(9, 'bursary', 10000.00, '2025-06-30', 3, 'C001'),
(14, 'union', 15000.00, '2025-07-31', 1, 'C001'),
(16, 'Mahapola', 5000.00, '2025-12-30', 10, 'C002'),
(17, 'StudentShip', 11000.00, '2025-10-30', 11, 'C004'),
(18, 'HH', 12000.00, '2025-12-30', 12, 'C003');

-- --------------------------------------------------------

--
-- Table structure for table `scholarship_awarded`
--

CREATE TABLE `scholarship_awarded` (
  `award_id` int(11) NOT NULL,
  `application_id` int(11) DEFAULT NULL,
  `award_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarship_awarded`
--

INSERT INTO `scholarship_awarded` (`award_id`, `application_id`, `award_date`) VALUES
(2, 4, '2025-06-24'),
(3, 5, '2025-06-24'),
(4, 8, '2025-06-30'),
(5, 11, '2025-06-30'),
(6, 10, '2025-06-30'),
(7, 12, '2025-06-30');

-- --------------------------------------------------------

--
-- Table structure for table `scholarship_payment`
--

CREATE TABLE `scholarship_payment` (
  `payment_id` int(11) NOT NULL,
  `award_id` int(11) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `transfer_reference` varchar(100) DEFAULT NULL,
  `payment_month` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarship_payment`
--

INSERT INTO `scholarship_payment` (`payment_id`, `award_id`, `payment_date`, `amount`, `transfer_reference`, `payment_month`) VALUES
(4, 2, '2025-06-24', 10000.00, '200222', 'May 2025'),
(5, 2, '2025-06-24', 10000.00, '1234', 'June 2025'),
(6, 4, '2025-06-30', 12000.00, '1234', 'January 2025'),
(7, 5, '2025-06-30', 11000.00, '2345', 'January 2025'),
(8, 3, '2025-06-30', 15000.00, '0001', 'January 2025'),
(9, 7, '2025-06-30', 10000.00, '00001', 'January 2025');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` varchar(20) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `dept` varchar(50) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `name`, `email`, `dept`, `dob`, `password`) VALUES
('2022/E/063', 'Gowtham', 'jegatheeswarangowtham@gmail.com', 'CSE', '2002-08-13', '$2y$10$ryG6RSG1zmiVg13PJ4Fhz.6gqVjx3k7C7Nd1R/cHuPqBHOgNaQsUu'),
('2022/E/065', 'Renujan', 'Renu@gmail.com', 'CSE', '2002-03-03', '$2y$10$E9d2VXgCT3T179QUM0/yIO8q3izpZCoc5cnlPxAJl/Fo/OpUpHH5q'),
('2022/E/076', 'Anojinth', 'Ano@gmail.com', 'EEE', '2025-10-30', '$2y$10$T5ohv7ICfY25GPKBRYx/QOIVS3bH9sradQvh/rtTCq5RJf9VV8Ib2'),
('2022/E/109', 'Kilshan', 'Kilshan@gmail.com', 'CSE', '2002-06-28', '$2y$10$gT9lDhYbUZOnfgrgA8m9WOOJTaoHIaFt5U5h/kXrC.N99GUirSxci');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `application`
--
ALTER TABLE `application`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `scholarship_id` (`scholarship_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `coordinator`
--
ALTER TABLE `coordinator`
  ADD PRIMARY KEY (`coordinator_id`);

--
-- Indexes for table `eligibility_criteria`
--
ALTER TABLE `eligibility_criteria`
  ADD PRIMARY KEY (`criteria_id`),
  ADD KEY `scholarship_id` (`scholarship_id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `application_id` (`application_id`);

--
-- Indexes for table `provider`
--
ALTER TABLE `provider`
  ADD PRIMARY KEY (`provider_id`);

--
-- Indexes for table `scholarship`
--
ALTER TABLE `scholarship`
  ADD PRIMARY KEY (`scholarship_id`),
  ADD KEY `provider_id` (`provider_id`),
  ADD KEY `coordinator_id` (`coordinator_id`);

--
-- Indexes for table `scholarship_awarded`
--
ALTER TABLE `scholarship_awarded`
  ADD PRIMARY KEY (`award_id`),
  ADD KEY `application_id` (`application_id`);

--
-- Indexes for table `scholarship_payment`
--
ALTER TABLE `scholarship_payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `award_id` (`award_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `application`
--
ALTER TABLE `application`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `eligibility_criteria`
--
ALTER TABLE `eligibility_criteria`
  MODIFY `criteria_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `provider`
--
ALTER TABLE `provider`
  MODIFY `provider_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `scholarship`
--
ALTER TABLE `scholarship`
  MODIFY `scholarship_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `scholarship_awarded`
--
ALTER TABLE `scholarship_awarded`
  MODIFY `award_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `scholarship_payment`
--
ALTER TABLE `scholarship_payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `application`
--
ALTER TABLE `application`
  ADD CONSTRAINT `application_ibfk_2` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarship` (`scholarship_id`),
  ADD CONSTRAINT `application_ibfk_3` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`);

--
-- Constraints for table `eligibility_criteria`
--
ALTER TABLE `eligibility_criteria`
  ADD CONSTRAINT `eligibility_criteria_ibfk_1` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarship` (`scholarship_id`);

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `application` (`application_id`);

--
-- Constraints for table `scholarship`
--
ALTER TABLE `scholarship`
  ADD CONSTRAINT `scholarship_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `provider` (`provider_id`),
  ADD CONSTRAINT `scholarship_ibfk_2` FOREIGN KEY (`coordinator_id`) REFERENCES `coordinator` (`coordinator_id`);

--
-- Constraints for table `scholarship_awarded`
--
ALTER TABLE `scholarship_awarded`
  ADD CONSTRAINT `scholarship_awarded_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `application` (`application_id`);

--
-- Constraints for table `scholarship_payment`
--
ALTER TABLE `scholarship_payment`
  ADD CONSTRAINT `scholarship_payment_ibfk_1` FOREIGN KEY (`award_id`) REFERENCES `scholarship_awarded` (`award_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
