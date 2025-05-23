-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 23, 2025 at 09:51 AM
-- Server version: 9.1.0
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fin_general_ledger`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `AccountID` int NOT NULL,
  `AccountName` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `AccountType` enum('Asset','Liability') COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`AccountID`, `AccountName`, `AccountType`) VALUES
(1, 'Utilities', 'Asset'),
(14, 'asd', 'Liability');

-- --------------------------------------------------------

--
-- Table structure for table `journalentries`
--

CREATE TABLE `journalentries` (
  `EntryID` int NOT NULL,
  `AccountID` int NOT NULL,
  `TransactionID` int NOT NULL,
  `EntryType` enum('Debit','Credit') COLLATE utf8mb4_general_ci NOT NULL,
  `Amount` int NOT NULL,
  `EntryDate` datetime(6) NOT NULL,
  `Description` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `journalentries`
--

INSERT INTO `journalentries` (`EntryID`, `AccountID`, `TransactionID`, `EntryType`, `Amount`, `EntryDate`, `Description`) VALUES
(33, 0, 0, 'Debit', 0, '0000-00-00 00:00:00.000000', '');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `TransactionID` int NOT NULL,
  `EntryID` int NOT NULL,
  `PaymentID` int NOT NULL,
  `AllocationID` int NOT NULL,
  `AdjustmentID` int NOT NULL,
  `PayablePaymentID` int NOT NULL,
  `TransactionFrom` enum('Guest','Vendor','Budget','Employee') COLLATE utf8mb4_general_ci NOT NULL,
  `TransactionDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `BudgetAllocated` varchar(400) COLLATE utf8mb4_general_ci NOT NULL,
  `BudgetName` varchar(400) COLLATE utf8mb4_general_ci NOT NULL,
  `Allocated_Department` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `AdjustmentAmount` varchar(400) COLLATE utf8mb4_general_ci NOT NULL,
  `PaymentMethod` varchar(400) COLLATE utf8mb4_general_ci NOT NULL,
  `GuestName` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `TotalAmount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`TransactionID`, `EntryID`, `PaymentID`, `AllocationID`, `AdjustmentID`, `PayablePaymentID`, `TransactionFrom`, `TransactionDate`, `BudgetAllocated`, `BudgetName`, `Allocated_Department`, `AdjustmentAmount`, `PaymentMethod`, `GuestName`, `TotalAmount`) VALUES
(89, 0, 0, 0, 0, 32, 'Vendor', '2025-04-14 16:00:00', '1000', 'SAHOD', 'PAYROLL', '', 'Cash', NULL, NULL),
(90, 0, 0, 0, 39, 0, 'Budget', '2025-04-25 16:00:00', '4000', 'SAHOD', 'PAYROLL', '3000', '', NULL, NULL),
(91, 0, 0, 43, 0, 0, 'Budget', '2025-05-18 16:00:00', '123', 'asd', 'asd', '', '', NULL, NULL),
(92, 0, 0, 0, 0, 33, 'Vendor', '2025-05-18 16:00:00', '100', 'SAHOD', 'PAYROLL', '', 'Cash', NULL, NULL),
(93, 0, 0, 0, 0, 34, 'Vendor', '2025-05-18 16:00:00', '177', 'SAHOD', 'PAYROLL', '', 'Bank Transfer', NULL, NULL),
(94, 0, 0, 0, 0, 35, 'Vendor', '2025-05-18 16:00:00', '250000', 'SAMPLE', 'CORE 2', '', 'Bank Transfer', NULL, NULL),
(95, 0, 0, 0, 0, 36, 'Vendor', '2025-05-18 16:00:00', '0.01', 'SAMPLE', 'CORE 2', '', 'Bank Transfer', NULL, NULL),
(96, 0, 0, 0, 0, 37, 'Vendor', '2025-05-18 16:00:00', '123', 'SAHOD', 'PAYROLL', '', 'Bank Transfer', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`TransactionID`),
  ADD KEY `EntryID` (`EntryID`),
  ADD KEY `PaymentID` (`PaymentID`),
  ADD KEY `AllocationID` (`AllocationID`),
  ADD KEY `AdjustmentID` (`AdjustmentID`),
  ADD KEY `PayableID` (`PayablePaymentID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `TransactionID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
