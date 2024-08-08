-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 08, 2024 at 08:10 AM
-- Server version: 8.0.30
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wescem`
--

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` int NOT NULL,
  `plan_name` varchar(50) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=default, 0 = not default',
  `point_per_day` decimal(20,8) DEFAULT NULL,
  `version` varchar(30) DEFAULT NULL,
  `earning_rate` decimal(20,8) DEFAULT NULL,
  `image` varchar(100) NOT NULL DEFAULT '1.png',
  `price` float(20,8) NOT NULL,
  `duration` int NOT NULL DEFAULT '90',
  `profit` varchar(191) NOT NULL DEFAULT '0',
  `speed` varchar(191) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `plan_name`, `is_default`, `point_per_day`, `version`, `earning_rate`, `image`, `price`, `duration`, `profit`, `speed`) VALUES
(1, 'Free Plan', 1, '0.02000000', 'V 1.0', '0.00001389', '1.png', 0.00000000, 0, '2', '1'),
(2, 'Plan V1.1', 0, '15.00000000', 'V 1.1', '0.01041667', '2.png', 300.00000000, 90, '5', '10'),
(3, 'Plan V1.2', 0, '53.00000000', 'V 1.2', '0.03680556', '3.png', 1000.00000000, 90, '5.25', '100');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int NOT NULL,
  `currency_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Dogecoin',
  `currency_symbol` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Đ',
  `currency_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DOGE',
  `wallet_min` int NOT NULL DEFAULT '20',
  `wallet_max` int NOT NULL DEFAULT '50'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `currency_name`, `currency_symbol`, `currency_code`, `wallet_min`, `wallet_max`) VALUES
(1, 'Dogecoin', 'Đ', 'DOGE', 20, 50);

-- --------------------------------------------------------

--
-- Table structure for table `transactions_history`
--

CREATE TABLE `transactions_history` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `plan_id` int NOT NULL,
  `amount` varchar(50) NOT NULL,
  `paid_amount` varchar(50) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending' COMMENT 'pending,paid',
  `hash` varchar(191) DEFAULT NULL,
  `txid` varchar(191) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `unique_id` int NOT NULL,
  `username` varchar(191) DEFAULT NULL,
  `balance` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `cashouts` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `plan_id` int DEFAULT NULL,
  `reference_user_id` int NOT NULL,
  `affiliate_earns` float(20,8) NOT NULL DEFAULT '0.00000000',
  `affiliate_paid` float(20,8) NOT NULL DEFAULT '0.00000000',
  `ip_addr` varchar(25) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_plan_history`
--

CREATE TABLE `user_plan_history` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `plan_id` int NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'inactive' COMMENT 'active,inactive',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expire_date` timestamp NULL DEFAULT NULL,
  `last_sum` varchar(191) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_withdrawal`
--

CREATE TABLE `user_withdrawal` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'payment' COMMENT 'payment,affiliate',
  `amount` float(20,8) NOT NULL,
  `status` enum('PENDING','PROCESSING','SUCCESS') NOT NULL DEFAULT 'PENDING',
  `tx` varchar(191) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_paid` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions_history`
--
ALTER TABLE `transactions_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_plan_history`
--
ALTER TABLE `user_plan_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_withdrawal`
--
ALTER TABLE `user_withdrawal`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transactions_history`
--
ALTER TABLE `transactions_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_plan_history`
--
ALTER TABLE `user_plan_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_withdrawal`
--
ALTER TABLE `user_withdrawal`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
