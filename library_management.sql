-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 22, 2024 at 08:53 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `BookID` int(11) NOT NULL,
  `ResourceID` int(11) DEFAULT NULL,
  `Author` varchar(255) DEFAULT NULL,
  `ISBN` varchar(50) DEFAULT NULL,
  `Publisher` varchar(100) DEFAULT NULL,
  `Edition` varchar(50) DEFAULT NULL,
  `PublicationDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `fines`
--

CREATE TABLE `fines` (
  `FineID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `TransactionID` int(11) DEFAULT NULL,
  `Amount` decimal(10,2) DEFAULT NULL,
  `PaymentStatus` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `libraryresources`
--

CREATE TABLE `libraryresources` (
  `ResourceID` int(11) NOT NULL,
  `Title` varchar(255) DEFAULT NULL,
  `Category` varchar(50) DEFAULT NULL,
  `AccessionNumber` varchar(50) NOT NULL,
  `Availability` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `TransactionID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `ResourceID` int(11) DEFAULT NULL,
  `BorrowDate` date DEFAULT NULL,
  `DueDate` date DEFAULT NULL,
  `ReturnDate` date DEFAULT NULL,
  `Fine` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `UserType` enum('student','faculty','admin','staff') NOT NULL,
  `MembershipID` varchar(50) NOT NULL,
  `ContactDetails` text DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `BorrowingLimit` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `Name`, `UserType`, `MembershipID`, `ContactDetails`, `Password`, `BorrowingLimit`) VALUES
(1, 'Royce', 'student', '2211', '09565654455', '$2y$10$j..eT13Vfh4xdsusp2SSG.Goo/DKsXkELwRvmHvjr.eOS9FehsbYK', 3),
(2, 'Royce', 'faculty', '22111', '09565654455', '$2y$10$gr39cz4Knc3w7VcWRzwFoejA1GoAeT5m/wf.atKyHa/0t1C4a.WHa', 5),
(4, 'Royce', 'admin', '123', '09565654455', '$2y$10$xu6DS7XDtXjTPl0rk9NwYOoptGlvMf6qv6BEphEZGSHY5/8zK7WhK', 3),
(5, 'Royce', 'staff', '1234', '09565654445', '$2y$10$s.sYxD7/UtjD5549qi0CLu72iZZhoPPRB.v6xJZ7JqbrQ9xtrBCO.', 5);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`BookID`),
  ADD KEY `ResourceID` (`ResourceID`);

--
-- Indexes for table `fines`
--
ALTER TABLE `fines`
  ADD PRIMARY KEY (`FineID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `TransactionID` (`TransactionID`);

--
-- Indexes for table `libraryresources`
--
ALTER TABLE `libraryresources`
  ADD PRIMARY KEY (`ResourceID`),
  ADD UNIQUE KEY `AccessionNumber` (`AccessionNumber`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`TransactionID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `ResourceID` (`ResourceID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `MembershipID` (`MembershipID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `BookID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fines`
--
ALTER TABLE `fines`
  MODIFY `FineID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `libraryresources`
--
ALTER TABLE `libraryresources`
  MODIFY `ResourceID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `TransactionID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`ResourceID`) REFERENCES `libraryresources` (`ResourceID`) ON DELETE CASCADE;

--
-- Constraints for table `fines`
--
ALTER TABLE `fines`
  ADD CONSTRAINT `fines_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fines_ibfk_2` FOREIGN KEY (`TransactionID`) REFERENCES `transactions` (`TransactionID`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`ResourceID`) REFERENCES `libraryresources` (`ResourceID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
