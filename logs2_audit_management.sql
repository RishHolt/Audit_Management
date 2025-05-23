-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 19, 2025 at 01:32 AM
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
-- Database: `logs2_audit_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit`
--

CREATE TABLE `audit` (
  `AuditID` int(11) NOT NULL,
  `PlanID` int(11) DEFAULT NULL,
  `Title` varchar(255) NOT NULL,
  `ConductingBy` varchar(100) DEFAULT NULL,
  `ConductedAt` datetime DEFAULT NULL,
  `Status` enum('Pending','Under Review','Completed','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDBDEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auditlogs`
--

CREATE TABLE `auditlogs` (
  `LogID` int(11) NOT NULL,
  `AuditID` int(11) DEFAULT NULL,
  `Action` varchar(255) DEFAULT NULL,
  `ConductedBy` varchar(100) DEFAULT NULL,
  `ConductedAt` datetime DEFAULT current_timestamp(),
  `Details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `auditlogs`
--

INSERT INTO `auditlogs` (`LogID`, `AuditID`, `Action`, `ConductedBy`, `ConductedAt`, `Details`) VALUES
(6, NULL, 'Create Plan', 'System', '2025-05-15 00:23:30', 'Plan created: asdasd (asdasd) scheduled for 2025-05-12'),
(10, NULL, 'Delete Plan', 'System', '2025-05-15 00:29:22', 'PlanID 15 deleted'),
(11, NULL, 'Delete Plan', 'System', '2025-05-15 00:30:29', 'PlanID 17 deleted'),
(12, NULL, 'Create Plan', 'System', '2025-05-15 00:32:55', 'PlanID 18 created: asdas (adsdasd) scheduled for 2025-05-08'),
(13, NULL, 'Delete Plan', 'System', '2025-05-15 00:33:39', 'PlanID 18 deleted'),
(14, NULL, 'Create Plan', 'System', '2025-05-15 00:33:50', 'PlanID 19 created: dsfsdfsdf (sdfsdf) scheduled for 2025-05-15'),
(25, NULL, 'Delete Audit', 'System', '2025-05-15 00:39:03', 'AuditID 19 deleted (PlanID 19)'),
(33, NULL, 'Assign Action', 'System', '2025-05-15 00:40:52', 'Action assigned to asdasdasd for FindingID: 10'),
(34, NULL, 'Edit Action', 'System', '2025-05-15 00:41:00', 'ActionID 5 updated: AssignedTo=asdasdasd, Task=asdasdasd, DueDate=2025-05-13, Status=Under Review'),
(35, NULL, 'Edit Action', 'System', '2025-05-15 00:41:15', 'ActionID 5 updated: AssignedTo=asdasdasd, Task=asdasdasd, DueDate=2025-05-13, Status=Failed'),
(36, NULL, 'Delete Action', 'System', '2025-05-15 00:41:19', 'ActionID 5 deleted'),
(38, NULL, 'Delete Audit', 'System', '2025-05-15 00:41:30', 'AuditID 20 deleted (PlanID 19)'),
(39, NULL, 'Delete Plan', 'System', '2025-05-15 00:41:36', 'PlanID 19 deleted');

-- --------------------------------------------------------

--
-- Table structure for table `auditplan`
--

CREATE TABLE `auditplan` (
  `PlanID` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `Department` varchar(100) DEFAULT NULL,
  `ScheduledDate` date DEFAULT NULL,
  `Status` enum('Scheduled','Open','Assigned','Under Review','Completed','Cancelled') DEFAULT 'Scheduled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `correctiveactions`
--

CREATE TABLE `correctiveactions` (
  `ActionID` int(11) NOT NULL,
  `FindingID` int(11) DEFAULT NULL,
  `AssignedTo` varchar(100) DEFAULT NULL,
  `Task` text DEFAULT NULL,
  `DueDate` date DEFAULT NULL,
  `Status` enum('Pending','Under Review','Completed','Failed') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `findings`
--

CREATE TABLE `findings` (
  `FindingID` int(11) NOT NULL,
  `AuditID` int(11) DEFAULT NULL,
  `Category` enum('Compliant','Non-Compliant','Observation') NOT NULL,
  `Description` text DEFAULT NULL,
  `LoggedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit`
--
ALTER TABLE `audit`
  ADD PRIMARY KEY (`AuditID`),
  ADD KEY `PlanID` (`PlanID`);

--
-- Indexes for table `auditlogs`
--
ALTER TABLE `auditlogs`
  ADD PRIMARY KEY (`LogID`),
  ADD KEY `auditlogs_ibfk_1` (`AuditID`);

--
-- Indexes for table `auditplan`
--
ALTER TABLE `auditplan`
  ADD PRIMARY KEY (`PlanID`);

--
-- Indexes for table `correctiveactions`
--
ALTER TABLE `correctiveactions`
  ADD PRIMARY KEY (`ActionID`),
  ADD KEY `FindingID` (`FindingID`);

--
-- Indexes for table `findings`
--
ALTER TABLE `findings`
  ADD PRIMARY KEY (`FindingID`),
  ADD KEY `AuditID` (`AuditID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit`
--
ALTER TABLE `audit`
  MODIFY `AuditID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `auditlogs`
--
ALTER TABLE `auditlogs`
  MODIFY `LogID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `auditplan`
--
ALTER TABLE `auditplan`
  MODIFY `PlanID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `correctiveactions`
--
ALTER TABLE `correctiveactions`
  MODIFY `ActionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `findings`
--
ALTER TABLE `findings`
  MODIFY `FindingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit`
--
ALTER TABLE `audit`
  ADD CONSTRAINT `audit_ibfk_1` FOREIGN KEY (`PlanID`) REFERENCES `auditplan` (`PlanID`) ON DELETE CASCADE;

--
-- Constraints for table `auditlogs`
--
ALTER TABLE `auditlogs`
  ADD CONSTRAINT `auditlogs_ibfk_1` FOREIGN KEY (`AuditID`) REFERENCES `audit` (`AuditID`),
  ADD CONSTRAINT `fk_auditlogs_auditid` FOREIGN KEY (`AuditID`) REFERENCES `audit` (`AuditID`);

--
-- Constraints for table `correctiveactions`
--
ALTER TABLE `correctiveactions`
  ADD CONSTRAINT `correctiveactions_ibfk_1` FOREIGN KEY (`FindingID`) REFERENCES `findings` (`FindingID`) ON DELETE CASCADE;

--
-- Constraints for table `findings`
--
ALTER TABLE `findings`
  ADD CONSTRAINT `findings_ibfk_1` FOREIGN KEY (`AuditID`) REFERENCES `audit` (`AuditID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
