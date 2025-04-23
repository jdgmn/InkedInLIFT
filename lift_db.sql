-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2025 at 04:52 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.3.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lift_db`
--

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_member_sessions`
-- (See below for the actual view)
--
CREATE TABLE `active_member_sessions` (
`Customer_ID` int(11)
,`Date` date
,`FName` varchar(50)
,`LName` varchar(50)
,`Check-in` time
,`Check-out` time
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `active_non_member_sessions`
-- (See below for the actual view)
--
CREATE TABLE `active_non_member_sessions` (
`Customer_ID` int(11)
,`Date` date
,`FName` varchar(50)
,`LName` varchar(50)
,`Payment_Status` enum('paid','unpaid')
,`Check-in` time
,`Check-out` time
);

-- --------------------------------------------------------

--
-- Table structure for table `active_sessions`
--

CREATE TABLE `active_sessions` (
  `ID` int(11) NOT NULL,
  `Customer_ID` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Check-in` time NOT NULL,
  `Check-out` time DEFAULT NULL,
  `Customer_Type` enum('member','non-member') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `archived_sessions`
--

CREATE TABLE `archived_sessions` (
  `ID` int(11) NOT NULL,
  `Customer_ID` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Check-in` time NOT NULL,
  `Check-out` time NOT NULL,
  `Customer_Type` enum('member','non-member') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `ID` int(11) NOT NULL,
  `FName` varchar(50) NOT NULL,
  `LName` varchar(50) NOT NULL,
  `ContactNo` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `ID` int(11) NOT NULL,
  `Customer_ID` int(11) NOT NULL,
  `Start_Date` date NOT NULL,
  `Expiry_Date` date GENERATED ALWAYS AS (`Start_Date` + interval `Duration` month) STORED,
  `Duration` int(11) NOT NULL,
  `Status` enum('vaild','expired') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `non_members`
--

CREATE TABLE `non_members` (
  `ID` int(11) NOT NULL,
  `Customer_ID` int(11) NOT NULL,
  `Payment_Status` enum('paid','unpaid') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure for view `active_member_sessions`
--
DROP TABLE IF EXISTS `active_member_sessions`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_member_sessions`  AS SELECT `a`.`Customer_ID` AS `Customer_ID`, `a`.`Date` AS `Date`, `c`.`FName` AS `FName`, `c`.`LName` AS `LName`, `a`.`Check-in` AS `Check-in`, `a`.`Check-out` AS `Check-out` FROM (`active_sessions` `a` join `customers` `c` on(`a`.`Customer_ID` = `c`.`ID`)) WHERE `a`.`Customer_Type` = 'member' ;

-- --------------------------------------------------------

--
-- Structure for view `active_non_member_sessions`
--
DROP TABLE IF EXISTS `active_non_member_sessions`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `active_non_member_sessions`  AS SELECT `a`.`Customer_ID` AS `Customer_ID`, `a`.`Date` AS `Date`, `c`.`FName` AS `FName`, `c`.`LName` AS `LName`, `n`.`Payment_Status` AS `Payment_Status`, `a`.`Check-in` AS `Check-in`, `a`.`Check-out` AS `Check-out` FROM ((`active_sessions` `a` join `customers` `c` on(`a`.`Customer_ID` = `c`.`ID`)) join `non_members` `n` on(`a`.`Customer_ID` = `n`.`Customer_ID`)) WHERE `a`.`Customer_Type` = 'non-member' ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `active_sessions`
--
ALTER TABLE `active_sessions`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Customer_ID` (`Customer_ID`);

--
-- Indexes for table `archived_sessions`
--
ALTER TABLE `archived_sessions`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Customer_ID` (`Customer_ID`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Customer_ID` (`Customer_ID`);

--
-- Indexes for table `non_members`
--
ALTER TABLE `non_members`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `non_members_ibfk_1` (`Customer_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `active_sessions`
--
ALTER TABLE `active_sessions`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `archived_sessions`
--
ALTER TABLE `archived_sessions`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `non_members`
--
ALTER TABLE `non_members`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `active_sessions`
--
ALTER TABLE `active_sessions`
  ADD CONSTRAINT `active_sessions_ibfk_1` FOREIGN KEY (`Customer_ID`) REFERENCES `customers` (`ID`);

--
-- Constraints for table `archived_sessions`
--
ALTER TABLE `archived_sessions`
  ADD CONSTRAINT `archived_sessions_ibfk_1` FOREIGN KEY (`Customer_ID`) REFERENCES `customers` (`ID`);

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_ibfk_1` FOREIGN KEY (`Customer_ID`) REFERENCES `customers` (`ID`);

--
-- Constraints for table `non_members`
--
ALTER TABLE `non_members`
  ADD CONSTRAINT `non_members_ibfk_1` FOREIGN KEY (`Customer_ID`) REFERENCES `customers` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
