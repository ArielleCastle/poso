-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 08, 2024 at 11:24 PM
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
-- Database: `poso`
--

-- --------------------------------------------------------

--
-- Table structure for table `2_violation`
--

CREATE TABLE `2_violation` (
  `ID` int(10) NOT NULL,
  `ticket_number` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `second_violation` varchar(1000) NOT NULL,
  `second_total` int(10) NOT NULL,
  `others_violation` varchar(1000) NOT NULL,
  `others_total` int(10) NOT NULL,
  `notes` varchar(1000) NOT NULL,
  `STATUS` varchar(255) NOT NULL DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `3_violation`
--

CREATE TABLE `3_violation` (
  `ID` int(10) NOT NULL,
  `ticket_number` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `third_violation` varchar(1000) NOT NULL,
  `third_total` int(10) NOT NULL,
  `others_violation` varchar(1000) NOT NULL,
  `others_total` int(10) NOT NULL,
  `notes` varchar(1000) NOT NULL,
  `STATUS` varchar(255) NOT NULL DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hh_login`
--

CREATE TABLE `hh_login` (
  `ID` int(10) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `confirmpassword` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hh_login`
--

INSERT INTO `hh_login` (`ID`, `username`, `email`, `password`, `confirmpassword`) VALUES
(1, 'Brian', 'brian@gmail.com', 'Brian', 'Brian');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `ID` int(255) NOT NULL,
  `image` longblob NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `confirmpassword` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`ID`, `image`, `username`, `email`, `password`, `confirmpassword`) VALUES
(2, '', 'Admin123', 'Admin123@gmail.com', 'Admin123', 'Admin123');

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `ID` int(11) NOT NULL,
  `ticket_number` varchar(8) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `license` varchar(50) DEFAULT NULL,
  `confiscated` enum('yes','no') NOT NULL,
  `violation_date` date DEFAULT NULL,
  `violation_time` time DEFAULT NULL,
  `street` varchar(100) DEFAULT NULL,
  `plate_number` varchar(20) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `registration` varchar(100) DEFAULT NULL,
  `vehicle_type` varchar(50) DEFAULT NULL,
  `vehicle_owner` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `report`
--

INSERT INTO `report` (`ID`, `ticket_number`, `first_name`, `middle_name`, `last_name`, `dob`, `address`, `license`, `confiscated`, `violation_date`, `violation_time`, `street`, `plate_number`, `city`, `registration`, `vehicle_type`, `vehicle_owner`, `created_at`) VALUES
(57, '77553349', 'A', 'A', 'A', '2024-11-06', 'A', 'A', 'no', '2024-11-06', '04:35:00', 'A', 'A', 'A', 'A', 'Passenger Car', 'A', '2024-11-05 20:35:28'),
(58, '18546598', 'Test', 'Test', 'Test', '2000-11-09', 'Test', 'Test', 'no', '2024-11-09', '03:47:00', 'Test', 'Test', 'Test', 'Test', 'Passenger Car', 'Test', '2024-11-08 19:48:22'),
(59, '43547672', 'Test', 'Test', 'Test', '2000-11-09', 'Test', 'Test', 'no', '2024-11-09', '03:47:00', 'Test', 'Test', 'Test', 'Test', 'Passenger Car', 'Test', '2024-11-08 19:50:14');

-- --------------------------------------------------------

--
-- Table structure for table `violation`
--

CREATE TABLE `violation` (
  `ID` int(10) NOT NULL,
  `ticket_number` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `first_violation` varchar(1000) NOT NULL,
  `first_total` int(10) NOT NULL,
  `others_violation` varchar(1000) NOT NULL,
  `others_total` int(10) NOT NULL,
  `notes` varchar(1000) NOT NULL,
  `STATUS` varchar(255) NOT NULL DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `violation`
--

INSERT INTO `violation` (`ID`, `ticket_number`, `first_name`, `last_name`, `first_violation`, `first_total`, `others_violation`, `others_total`, `notes`, `STATUS`) VALUES
(46, '77553349', 'A', 'A', 'FAILURE TO WEAR HELMET - 200', 200, '', 0, '', 'PENDING'),
(47, '43547672', 'Test', 'Test', 'FAILURE TO WEAR HELMET - 200, ONEWAY - 200, NO OR/CR WHILE DRIVING - 500, OBSTRUCTION - 500', 1400, '', 0, '', 'PENDING');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `2_violation`
--
ALTER TABLE `2_violation`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `3_violation`
--
ALTER TABLE `3_violation`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `hh_login`
--
ALTER TABLE `hh_login`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `violation`
--
ALTER TABLE `violation`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `2_violation`
--
ALTER TABLE `2_violation`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `3_violation`
--
ALTER TABLE `3_violation`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `hh_login`
--
ALTER TABLE `hh_login`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `violation`
--
ALTER TABLE `violation`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
