-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 25, 2024 at 07:11 PM
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`BookID`, `ResourceID`, `Author`, `ISBN`, `Publisher`, `Edition`, `PublicationDate`, `Title`, `Genre`, `AccessionNumber`, `Quantity`, `AvailableQuantity`) VALUES
(33, 6, 'Aljun Inato', '221', 'Dead', NULL, NULL, 'DUGAGO', 'Poet', '67449BB1608CD', 1, 0),
(35, 5, 'Aljun Inato', '2211', 'Dead', NULL, NULL, 'KINGINA', 'Poet', '67449C8883957', 3, 3),
(37, 7, 'Aljun Inato', '2217', 'Dead', NULL, NULL, 'KINGINA', 'Poet', '6744A0B0F2E5D', 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `borrowedbooks`
--

CREATE TABLE `borrowedbooks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `borrow_date` datetime DEFAULT current_timestamp(),
  `return_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `Status` enum('Borrowed','Returned') DEFAULT 'Borrowed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`TransactionID`, `UserID`, `ResourceID`, `BorrowDate`, `DueDate`, `ReturnDate`, `Fine`, `Status`) VALUES
(7, 8, 6, '2024-11-25 00:00:00', '2024-12-09 00:00:00', NULL, 0.00, 'Borrowed'),
(8, 8, 7, '2024-11-25 00:00:00', '2024-12-09 00:00:00', NULL, 0.00, 'Borrowed'),
(9, 18, 5, '2024-11-25 00:00:00', '2024-12-09 00:00:00', NULL, 0.00, 'Borrowed'),
(10, 18, 5, '2024-11-25 00:00:00', '2024-12-09 00:00:00', NULL, 0.00, 'Borrowed'),
(11, 18, 7, '2024-11-25 00:00:00', '2024-12-09 00:00:00', NULL, 0.00, 'Borrowed');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `Name`, `UserType`, `MembershipID`, `ContactDetails`, `Password`, `BorrowingLimit`) VALUES
(7, 'Royce Abas Fernandez', 'admin', '111', '09866566454', '$2y$10$GQXTaBHHlQBgyp9cXDNyze1HJRDo595gugWH60.qguQYKK//4CuLK', 3),
(8, 'Mark jun', 'student', '2', '09855584848', '$2y$10$b6FHDGWslN9b4/OdcB4KaeEhJc3oLspHDah0MtkB07p1KFTXbeGzq', 3),
(9, 'Aljun', 'staff', '1', '09877477574', '$2y$10$4jMfxYLusHK7t73iwxiIiObQeVIPnr1tCH/BTogzwfq4qwUC/K4oW', 5),
(18, 'Aljun', 'faculty', '12', '09656654544', '$2y$10$rD8YZJd0wR78mOGhmW7mquOaYl9GWoEOHvb6TlYxpOWKjfmggJhm6', 5);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

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
  MODIFY `BookID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `borrowedbooks`
--
ALTER TABLE `borrowedbooks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `libraryresources`
--
ALTER TABLE `libraryresources`
  MODIFY `BookID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `TransactionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrowedbooks`
--
ALTER TABLE `borrowedbooks`
  ADD CONSTRAINT `borrowedbooks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`UserID`),
  ADD CONSTRAINT `borrowedbooks_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`BookID`);

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
