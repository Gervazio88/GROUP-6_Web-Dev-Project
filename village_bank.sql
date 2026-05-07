-- phpMyAdmin SQL Dump
-- version 5.2.1deb1+deb12u1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 07, 2026 at 01:53 AM
-- Server version: 10.11.14-MariaDB-0+deb12u2
-- PHP Version: 8.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `village_bank`
--

-- --------------------------------------------------------

--
-- Table structure for table `bank_funds`
--

CREATE TABLE `bank_funds` (
  `id` int(11) NOT NULL,
  `total` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bank_funds`
--

INSERT INTO `bank_funds` (`id`, `total`) VALUES
(1, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `interest` decimal(10,2) DEFAULT NULL,
  `paid` decimal(10,2) DEFAULT 0.00,
  `due_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `reminder` text DEFAULT NULL,
  `message` text DEFAULT NULL,
  `fine` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`id`, `username`, `amount`, `interest`, `paid`, `due_date`, `status`, `reminder`, `message`, `fine`) VALUES
(48, 'pamela', 600000.00, 120000.00, 0.00, NULL, 'rejected', NULL, NULL, 0.00),
(49, 'pamela', 60000.00, 12000.00, 80000.00, '2026-05-19', 'closed', NULL, NULL, 8000.00),
(50, 'pamela', 70000.00, 14000.00, 0.00, NULL, 'rejected', NULL, NULL, 0.00),
(51, 'pamela', 40000.00, 8000.00, 0.00, NULL, 'rejected', NULL, NULL, 0.00),
(52, 'pamela', 60000.00, 12000.00, 0.00, NULL, 'rejected', NULL, 'Loan rejected due to existing loan balance', 0.00),
(53, 'pamela', 70000.00, 14000.00, 0.00, NULL, 'rejected', NULL, 'Loan rejected due to existing loan balance', 0.00),
(54, 'pamela', 60000000.00, 12000000.00, 0.00, NULL, 'rejected', NULL, 'Loan rejected due to existing loan balance', 0.00),
(55, 'pamela', 70000.00, 14000.00, 94000.00, '2026-05-19', 'closed', NULL, NULL, 10000.00),
(56, 'pamela', 30000.00, 6000.00, 0.00, NULL, 'rejected', NULL, 'Loan rejected due to existing loan balance', 0.00),
(57, 'pamela', 677777.00, 135555.40, 0.00, NULL, 'rejected', NULL, 'Loan rejected due to existing loan balance', 0.00),
(58, 'pamela', 677777.00, 135555.40, 0.00, NULL, 'rejected', NULL, 'Loan rejected due to insufficient funds', 0.00),
(59, 'pamela', 80000.00, 16000.00, 113000.00, '2026-05-20', 'closed', NULL, NULL, 17000.00),
(60, 'josi', 40000.00, 8000.00, 0.00, NULL, 'rejected', NULL, 'Loan rejected due to insufficient funds', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `loan_reminders`
--

CREATE TABLE `loan_reminders` (
  `id` int(11) NOT NULL,
  `loan_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `member_details`
--

CREATE TABLE `member_details` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `idnumber` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member_details`
--

INSERT INTO `member_details` (`id`, `username`, `fullname`, `gender`, `idnumber`, `phone`, `email`, `dob`, `status`) VALUES
(3, 'vano', 'victor kainja', 'Male', 'bit001923', '09986768678', 'bit001923@must.ac.mw', '2003-09-04', 'approved'),
(6, 'josi', 'Mphatso Josiah', 'Male', 'bit001823', '0993022980', 'bit001823@must.ac.mw', '2009-12-11', 'approved'),
(7, 'chiku', 'chikumbutso Kainja', 'Male', 'bit001823', '0892577488', 'chiku@gmail.com', '2008-01-28', 'approved'),
(8, 'pame', 'pamela kainja', 'Female', 'ikjhfhfh', '0998484895', 'pamela@gmail.com', '2000-04-23', 'approved'),
(9, 'pamela', 'tendai kainja', 'Female', 'ikjhfhfh', '0998484859', 'Tendai@gmail.com', '2000-04-23', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `savings`
--

CREATE TABLE `savings` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `date` date DEFAULT curdate(),
  `amount` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `savings`
--

INSERT INTO `savings` (`id`, `username`, `date`, `amount`, `total`) VALUES
(27, 'pamela', '2026-05-04', 46000.00, 46000.00),
(28, 'pamela', '2026-05-05', 30000.00, 76000.00),
(29, 'rejo@15', '2026-05-05', 60000.00, 60000.00),
(30, 'vano', '2026-05-05', 60000.00, 60000.00),
(31, 'vano', '2026-05-05', 60000.00, 120000.00),
(35, 'pamela', '2026-05-05', 70000.00, 70000.00),
(43, 'pamela', '2026-05-05', 67000.00, 67000.00),
(44, 'pamela', '2026-05-05', 67000.00, 67000.00);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `balance` decimal(10,2) DEFAULT NULL,
  `date` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `username`, `fullname`, `type`, `amount`, `balance`, `date`) VALUES
(48, 'pamela', 'tendai kainja', 'savings', 46000.00, 46000.00, '2026-05-04'),
(49, 'pamela', 'tendai kainja', 'savings', 30000.00, 76000.00, '2026-05-05'),
(50, 'rejo@15', 'rejoice njunga', 'savings', 60000.00, 60000.00, '2026-05-05'),
(51, 'vano', 'victor kainja', 'loan repay', 84000.00, 0.00, '2026-05-05'),
(52, 'vano', 'victor kainja', 'savings', 60000.00, 60000.00, '2026-05-05'),
(53, 'vano', 'victor kainja', 'savings', 60000.00, 120000.00, '2026-05-05'),
(54, 'pamela', 'tendai kainja', 'savings', 70000.00, 70000.00, '2026-05-05'),
(55, 'pamela', 'tendai kainja', 'savings', 67000.00, 67000.00, '2026-05-05'),
(56, 'pamela', 'tendai kainja', 'savings', 67000.00, 67000.00, '2026-05-05'),
(57, 'pamela', 'pamela', 'loan repay', 72000.00, 72000.00, '2026-05-05'),
(58, 'pamela', 'pamela', 'loan repay', 3000.00, 75000.00, '2026-05-05'),
(59, 'pamela', 'pamela', 'loan repay', 80000.00, 80000.00, '2026-05-05'),
(60, 'pamela', 'pamela', 'loan repay', 94000.00, 94000.00, '2026-05-05'),
(61, 'pamela', 'pamela', 'loan repay', 113000.00, 113000.00, '2026-05-06');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `keyword` varchar(60) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'member'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `keyword`, `role`) VALUES
(9, 'vano', '$2y$10$Os27kUj9ohZEXw2ZRzQbl.lENE04XYlZuoNiaGnbbOKzf32HT6W0W', 'grandma', 'treasurer'),
(10, 'racheal', '$2y$10$JkV2qvVjc0USm7H613Ii/Oj89RjDYwzeRn51EPs/Ehmd.m2oKVaKe', 'ray', 'member'),
(11, 'rejo@15', '$2y$10$U9Qj3P095xSv37nT.aX8w.DEXd7NlEKCjSaTllaDun8RF7XmvdWJq', 'rich', 'member'),
(12, 'josi', '$2y$10$bckVEdXveZCJi2Mf2JjvH.gjuxOrZc2eAH9GrgZIgDLet6.y3kBHG', 'rojema', 'member'),
(13, 'chiku', '$2y$10$4SOw/.FpQ0G49U/fPHh6DOdxRXQcPXkOyJyekHHw2PGiPCRea.lC.', 'victor', 'member'),
(14, 'pame', '$2y$10$6FlAR0QsPx0OMEiLsrgigugEhh23E9A201Md9RPS2tEVcBzB3oIuG', 'victor', 'member'),
(16, 'pamela', '$2y$10$sFivKZy9ZRiRqF2IlSdd2.2JF9E0rvFO8jaYKNoa52MVgUWsPgHvG', 'victor', 'chairperson'),
(17, 'Ntuana', '$2y$10$HxUZAygAg7aQYYLmJbBIi.Pqof8DThxyI6zONtYpYN58inz04vml6', 'Patie', 'member'),
(18, 'Sascha', '$2y$10$Be1oUlLSJ3nJg4MqaZRxqujUFPbhUs4v2/wj36m.8uc4uy8nGcJXK', 'Patie', 'member'),
(19, 'Kes', '$2y$10$UNBqbKIDi6iXBTL1.MK8DugTAIDUAieRSLnilMPT1u9dtd.o/XIkq', 'Stina', 'member'),
(20, 'kkkkkk', '$2y$10$emXoS0OyujiM7DbsDVMRO.AadJ3fkcFkk4GPSpHpDinuwjOtPy0jS', '', 'member'),
(21, 'rejo15', '$2y$10$uh/HtxaNLJhn2hCBpLqIRe7xq.GEGKQ5jIXgdIrdWY6N1L3akIRmu', 'richard', 'member');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bank_funds`
--
ALTER TABLE `bank_funds`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `loan_reminders`
--
ALTER TABLE `loan_reminders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member_details`
--
ALTER TABLE `member_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `savings`
--
ALTER TABLE `savings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `loan_reminders`
--
ALTER TABLE `loan_reminders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `member_details`
--
ALTER TABLE `member_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `savings`
--
ALTER TABLE `savings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE;

--
-- Constraints for table `savings`
--
ALTER TABLE `savings`
  ADD CONSTRAINT `savings_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
