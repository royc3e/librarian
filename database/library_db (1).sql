-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 26, 2024 at 08:26 AM
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
-- Database: `library_db`
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
  `PublicationDate` date DEFAULT NULL,
  `Title` varchar(255) NOT NULL,
  `Genre` varchar(255) DEFAULT NULL,
  `AccessionNumber` varchar(50) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `AvailableQuantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`BookID`, `ResourceID`, `Author`, `ISBN`, `Publisher`, `Edition`, `PublicationDate`, `Title`, `Genre`, `AccessionNumber`, `Quantity`, `AvailableQuantity`) VALUES
(33, 6, 'Aljun Inato', '221', 'Dead', NULL, NULL, 'DUGAGO', 'Fantasy', '67449BB1608CD', 1, 1),
(35, 5, 'Aljun Inato', '2211', 'Dead', NULL, NULL, 'KINGINA', 'Poet', '67449C8883957', 3, 2),
(37, 7, 'Aljun Inato', '2217', 'Dead', NULL, NULL, 'KINGINA MO KA', 'Science Fiction', '6744A0B0F2E5D', 3, 3),
(38, 12, 'Mark Lou Malinao', '2215', 'Dead', NULL, NULL, 'ANG PROBINSYANO', 'Walang Katotohanan', '674577111624A', 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `borrowedbooks`
--

CREATE TABLE `borrowedbooks` (
  `BorrowID` int(11) NOT NULL,
  `BookID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `BorrowDate` date NOT NULL,
  `ReturnDate` date NOT NULL,
  `Status` enum('Pending','Issued','Returned') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `borrowedbooks`
--

INSERT INTO `borrowedbooks` (`BorrowID`, `BookID`, `UserID`, `BorrowDate`, `ReturnDate`, `Status`) VALUES
(1, 33, 8, '2024-11-26', '2024-11-26', 'Returned');

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
  `BookID` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Author` varchar(255) NOT NULL,
  `ISBN` varchar(13) DEFAULT NULL,
  `Genre` varchar(100) DEFAULT NULL,
  `Publisher` varchar(255) DEFAULT NULL,
  `PublicationDate` date DEFAULT NULL,
  `AccessionNumber` varchar(255) NOT NULL,
  `Quantity` int(11) DEFAULT 0,
  `AvailableQuantity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `TransactionID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `ResourceID` int(11) NOT NULL,
  `BorrowDate` datetime NOT NULL,
  `DueDate` datetime NOT NULL,
  `ReturnDate` datetime DEFAULT NULL,
  `Fine` decimal(10,2) DEFAULT 0.00,
  `Status` enum('Borrowed','Returned','Pending') DEFAULT NULL,
  `ApprovalDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`TransactionID`, `UserID`, `ResourceID`, `BorrowDate`, `DueDate`, `ReturnDate`, `Fine`, `Status`, `ApprovalDate`) VALUES
(7, 8, 6, '2024-11-25 00:00:00', '2024-12-09 00:00:00', '2024-11-26 00:00:00', '0.00', 'Returned', NULL),
(8, 8, 7, '2024-11-25 00:00:00', '2024-12-09 00:00:00', '2024-11-26 00:00:00', '0.00', 'Returned', NULL),
(9, 18, 5, '2024-11-25 00:00:00', '2024-12-09 00:00:00', NULL, '0.00', 'Borrowed', NULL),
(10, 18, 5, '2024-11-25 00:00:00', '2024-12-09 00:00:00', NULL, '0.00', 'Borrowed', NULL),
(11, 18, 7, '2024-11-25 00:00:00', '2024-12-09 00:00:00', '2024-11-26 00:00:00', '0.00', 'Returned', NULL),
(12, 8, 5, '2024-11-26 00:00:00', '2024-12-10 00:00:00', '2024-11-26 00:00:00', '0.00', 'Returned', NULL),
(13, 8, 5, '2024-11-26 00:00:00', '2024-12-10 00:00:00', '2024-11-26 00:00:00', '0.00', 'Returned', NULL),
(14, 8, 5, '2024-11-26 00:00:00', '2024-12-10 00:00:00', '2024-11-26 00:00:00', '0.00', 'Returned', NULL),
(15, 8, 7, '2024-11-26 00:00:00', '2024-12-10 00:00:00', '2024-11-26 00:00:00', '0.00', 'Returned', NULL),
(16, 8, 6, '2024-11-26 00:00:00', '2024-12-10 00:00:00', '2024-11-26 00:00:00', '0.00', 'Returned', NULL),
(17, 8, 7, '2024-11-26 00:00:00', '2024-12-10 00:00:00', '2024-11-26 00:00:00', '0.00', 'Returned', NULL),
(18, 8, 5, '2024-11-26 00:00:00', '2024-12-10 00:00:00', '2024-11-26 00:00:00', '0.00', 'Returned', NULL),
(19, 8, 7, '2024-11-26 00:00:00', '2024-12-10 00:00:00', '2024-11-26 00:00:00', '0.00', 'Returned', NULL),
(20, 8, 6, '2024-11-26 00:00:00', '2024-12-10 00:00:00', '2024-11-26 00:00:00', '0.00', 'Returned', NULL),
(21, 18, 7, '2024-11-26 00:00:00', '2024-12-10 00:00:00', '2024-11-26 00:00:00', '0.00', 'Returned', NULL),
(22, 8, 5, '2024-11-26 00:00:00', '2024-12-10 00:00:00', '2024-11-26 00:00:00', '0.00', 'Returned', NULL),
(23, 8, 6, '2024-11-26 00:00:00', '2024-12-10 00:00:00', NULL, '0.00', '', NULL),
(24, 8, 6, '2024-11-26 00:00:00', '2024-12-10 00:00:00', NULL, '0.00', '', NULL),
(25, 8, 5, '2024-11-26 00:00:00', '2024-12-10 00:00:00', NULL, '0.00', '', NULL),
(26, 8, 6, '2024-11-26 00:00:00', '2024-12-10 00:00:00', '2024-11-26 00:00:00', '0.00', 'Returned', '2024-11-26'),
(27, 18, 6, '2024-11-26 00:00:00', '2024-12-10 00:00:00', NULL, '0.00', 'Borrowed', '2024-11-26'),
(28, 18, 7, '2024-11-26 00:00:00', '2024-12-10 00:00:00', NULL, '0.00', 'Borrowed', '2024-11-26'),
(29, 18, 5, '2024-11-26 00:00:00', '2024-12-10 00:00:00', NULL, '0.00', 'Borrowed', '2024-11-26'),
(30, 8, 7, '2024-11-26 00:00:00', '2024-12-10 00:00:00', '2024-11-26 00:00:00', '0.00', 'Returned', '2024-11-26'),
(31, 8, 7, '2024-11-26 00:00:00', '2024-12-10 00:00:00', '2024-11-26 00:00:00', '0.00', 'Returned', '2024-11-26'),
(32, 8, 12, '2024-11-26 00:00:00', '2024-12-10 00:00:00', NULL, '0.00', 'Borrowed', '2024-11-26'),
(33, 8, 12, '2024-11-26 00:00:00', '2024-12-10 00:00:00', NULL, '0.00', 'Borrowed', '2024-11-26'),
(34, 21, 12, '2024-11-26 00:00:00', '2024-12-10 00:00:00', NULL, '0.00', 'Borrowed', '2024-11-26');

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
(7, 'Royce Abas Fernandez', 'admin', '111', '09866566454', '$2y$10$GQXTaBHHlQBgyp9cXDNyze1HJRDo595gugWH60.qguQYKK//4CuLK', 3),
(8, 'Mark jun', 'student', '2', '09855584848', '$2y$10$b6FHDGWslN9b4/OdcB4KaeEhJc3oLspHDah0MtkB07p1KFTXbeGzq', 3),
(9, 'Aljun', 'staff', '1', '09877477574', '$2y$10$4jMfxYLusHK7t73iwxiIiObQeVIPnr1tCH/BTogzwfq4qwUC/K4oW', 5),
(18, 'Aljun', 'faculty', '12', '09656654544', '$2y$10$rD8YZJd0wR78mOGhmW7mquOaYl9GWoEOHvb6TlYxpOWKjfmggJhm6', 5),
(21, 'John Doe', 'student', '2211', '09878755454', '$2y$10$IwpjQAg2C.HMb0nd2otzJub0AEkI5NWQnJqp/z12e/c8hhD1R4h6y', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`BookID`),
  ADD UNIQUE KEY `ResourceID` (`ResourceID`);

--
-- Indexes for table `borrowedbooks`
--
ALTER TABLE `borrowedbooks`
  ADD PRIMARY KEY (`BorrowID`),
  ADD KEY `BookID` (`BookID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `libraryresources`
--
ALTER TABLE `libraryresources`
  ADD PRIMARY KEY (`BookID`);

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
  MODIFY `BookID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `borrowedbooks`
--
ALTER TABLE `borrowedbooks`
  MODIFY `BorrowID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `libraryresources`
--
ALTER TABLE `libraryresources`
  MODIFY `BookID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `TransactionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrowedbooks`
--
ALTER TABLE `borrowedbooks`
  ADD CONSTRAINT `borrowedbooks_ibfk_1` FOREIGN KEY (`BookID`) REFERENCES `books` (`BookID`) ON DELETE CASCADE,
  ADD CONSTRAINT `borrowedbooks_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`ResourceID`) REFERENCES `books` (`ResourceID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
