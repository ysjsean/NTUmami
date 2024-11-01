-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 01, 2024 at 04:08 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ntumami`
--
CREATE DATABASE IF NOT EXISTS `ntumami` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ntumami`;

-- --------------------------------------------------------

--
-- Table structure for table `canteens`
--

CREATE TABLE `canteens` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `created_by` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_by` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `canteens`
--

INSERT INTO `canteens` (`id`, `name`, `description`, `address`, `image_url`, `created_by`, `updated_by`) VALUES
(9, 'Canteen 16', '', '50 Nanyang Walk, Singapore 637658', '/NTUmami/assets/images/locations/6724d92cd3d0c.jpg', '2024-11-01 01:02:35', '2024-11-01 21:35:40'),
(10, 'Canteen 14', '', '34 Nanyang Cres, Singapore 637634', '/NTUmami/assets/images/locations/6723b8ff286bd.jpg', '2024-11-01 01:06:07', NULL),
(11, 'Koufu', 'Located at North Spine', '76 Nanyang Drive, N2.1, #02-03, Nanyang Technological University, 637331', '/NTUmami/assets/images/locations/6723e3d3bf97e.jpg', '2024-11-01 04:08:51', NULL),
(12, 'Canteen 11', '', '20 Nanyang Ave, Nanyang Technological University, Singapore 639809', '/NTUmami/assets/images/locations/6724d53f7be58.jpg', '2024-11-01 04:13:05', '2024-11-01 21:18:55'),
(13, 'Fine Food', 'Located at South Spine', '50 Nanyang Avenue B Food Court, Canteen, South Spine, 639798', '/NTUmami/assets/images/locations/672462e71ff61.jpg', '2024-11-01 13:11:03', NULL),
(14, 'test', '', 'test', '/NTUmami/assets/images/locations/67249f9ecd1c8.jpg', '2024-11-01 17:30:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `canteen_hours`
--

CREATE TABLE `canteen_hours` (
  `id` int(11) NOT NULL,
  `canteen_id` int(11) NOT NULL,
  `open_time` time NOT NULL,
  `close_time` time NOT NULL,
  `days` varchar(20) NOT NULL COMMENT 'Stores selected days in a comma-separated format'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `canteen_hours`
--

INSERT INTO `canteen_hours` (`id`, `canteen_id`, `open_time`, `close_time`, `days`) VALUES
(8, 10, '08:15:00', '21:00:00', 'Mon'),
(9, 10, '08:15:00', '21:00:00', 'Tue'),
(10, 10, '08:15:00', '21:00:00', 'Wed'),
(11, 10, '08:15:00', '21:00:00', 'Thu'),
(12, 10, '08:15:00', '21:00:00', 'Fri'),
(13, 10, '07:00:00', '21:00:00', 'Sun'),
(14, 10, '07:00:00', '21:00:00', 'Sat'),
(37, 11, '07:00:00', '20:00:00', 'Mon'),
(38, 11, '07:00:00', '20:00:00', 'Tue'),
(39, 11, '07:00:00', '20:00:00', 'Wed'),
(40, 11, '07:00:00', '20:00:00', 'Thu'),
(41, 11, '07:00:00', '20:00:00', 'Fri'),
(42, 11, '07:00:00', '15:00:00', 'Sat'),
(48, 13, '07:00:00', '20:00:00', 'Mon'),
(49, 13, '07:00:00', '20:00:00', 'Tue'),
(50, 13, '07:00:00', '20:00:00', 'Wed'),
(51, 13, '07:00:00', '20:00:00', 'Thu'),
(52, 13, '07:00:00', '20:00:00', 'Fri'),
(53, 13, '07:00:00', '15:00:00', 'Sat'),
(54, 14, '08:30:00', '20:00:00', 'Mon'),
(55, 14, '08:30:00', '20:00:00', 'Tue'),
(56, 14, '08:30:00', '20:00:00', 'Wed'),
(57, 12, '07:30:00', '20:00:00', 'Mon'),
(58, 12, '07:30:00', '20:00:00', 'Tue'),
(59, 12, '07:30:00', '20:00:00', 'Wed'),
(60, 12, '07:30:00', '20:00:00', 'Thu'),
(61, 12, '07:30:00', '20:00:00', 'Fri'),
(62, 12, '07:30:00', '20:00:00', 'Sat'),
(63, 12, '07:30:00', '20:00:00', 'Sun'),
(71, 9, '10:30:00', '21:00:00', 'Mon'),
(72, 9, '10:30:00', '21:00:00', 'Tue'),
(73, 9, '10:30:00', '21:00:00', 'Wed'),
(74, 9, '10:30:00', '21:00:00', 'Thu'),
(75, 9, '10:30:00', '21:00:00', 'Fri'),
(76, 9, '10:30:00', '21:00:00', 'Sat'),
(77, 9, '10:30:00', '21:00:00', 'Sun');

-- --------------------------------------------------------

--
-- Table structure for table `foods`
--

CREATE TABLE `foods` (
  `id` int(11) NOT NULL,
  `stall_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(5,2) NOT NULL,
  `description` text NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `is_halal` tinyint(1) NOT NULL DEFAULT 0,
  `is_vegetarian` tinyint(1) NOT NULL DEFAULT 0,
  `is_in_stock` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_by` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stalls`
--

CREATE TABLE `stalls` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `canteen_id` int(11) NOT NULL,
  `cuisine_type` enum('Chinese','Western','Indian','Malay','Japanese','Korean','Taiwan','Fusion') NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `is_open` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_by` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stalls`
--

INSERT INTO `stalls` (`id`, `name`, `canteen_id`, `cuisine_type`, `vendor_id`, `is_open`, `created_by`, `updated_by`) VALUES
(5, 'Noodle Stall', 12, 'Chinese', 5, 0, '2024-11-01 15:53:19', '2024-11-01 21:00:52'),
(6, 'Kiso Japanese Cuisine', 12, 'Japanese', 1, 0, '2024-11-01 16:16:16', '2024-11-01 16:29:01'),
(7, 'Si Chuan Mei Shi', 12, 'Chinese', 4, 0, '2024-11-01 16:38:39', NULL),
(9, 'Menya Takashi', 10, 'Japanese', 6, 0, '2024-11-01 22:11:01', NULL),
(10, 'Western', 10, 'Western', 7, 0, '2024-11-01 22:13:49', NULL),
(11, 'Taiwan Cuisine', 13, 'Taiwan', 8, 0, '2024-11-01 22:20:37', '2024-11-01 22:22:57'),
(12, 'Braised Duck Rice', 13, 'Chinese', 9, 0, '2024-11-01 22:27:02', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','vendor','admin') NOT NULL DEFAULT 'user',
  `created_by` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_by` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `name`, `password`, `role`, `created_by`, `updated_by`) VALUES
(1, 'syoung001', 'syoung001@e.ntu.edu.sg', 'Sean', '$2y$10$xtom6jp94LekLxgv2Yb0PuOm3TWzw4dTuMyTJdu6lR0btBBHcv7CK', 'admin', '2024-10-31 00:39:56', '2024-10-31 00:41:05'),
(2, 'fc11_1', 'kiso@gmail.com', 'test', '$2y$10$5NR1t99a5WLvWejhwYy2A.SP6a7g20Z7lwpmsYwVByiGOCtA4z5uC', 'vendor', '2024-10-31 22:25:17', '2024-11-01 16:27:19'),
(4, 'fc11_2', 'tastytreats@gmail.com', 'test2', '$2y$10$dqkdxzXxTuaaK7VHRnDW7equzcVUuyYXz002xPS25EA2IwaNC8Sj.', 'vendor', '2024-11-01 14:48:42', '2024-11-01 16:28:33'),
(5, 'fc11_3', 'sichuan_fg@gmail.com', 'test3', '$2y$10$1tqvArfG/vJDbj0s2IjgauPWClWdFXn7ODjS9Iio0l.8O82E0dxLi', 'vendor', '2024-11-01 16:38:11', NULL),
(6, 'fc11_4', 'fc11_4@gmail.com', 'test4', '$2y$10$.7he7XbNublNDfVpmJN.RevpIV/3jjv.rgBFAyyDzJS1/uEKyUK6m', 'vendor', '2024-11-01 20:36:07', NULL),
(7, 'fc14_1', 'fc_1@gmail.com', 'test5', '$2y$10$P.w3d5nPJsdxdDyQiQ.VIO5ttkHqU7tY0umN4ll/zvkiE7305zyuS', 'vendor', '2024-11-01 22:09:54', NULL),
(8, 'fc14_2', 'fc_12@gmail.com', 'test6', '$2y$10$csgAcp4utN5y1rKBAyDG2eNwYfv.v9KrkFcvlamZQnsy60wkFTv7W', 'vendor', '2024-11-01 22:13:19', NULL),
(9, 'ff_1', 'ff_1@gmail.com', 'test7', '$2y$10$IqvzvNdFVda1VE18.NRNIOfhac1kMPG1RBWPQgMpZRp5DATeZUFwO', 'vendor', '2024-11-01 22:16:43', NULL),
(10, 'ff_2', 'ff_2@gmai.com', 'test8', '$2y$10$C1gO17uNhniCyQqiLmxebeaAkp1LTXHazyiUGHC10s/qw9W/jNPIm', 'vendor', '2024-11-01 22:26:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `created_by` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_by` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `user_id`, `business_name`, `contact_number`, `created_by`, `updated_by`) VALUES
(1, 2, 'Kiso Japanese Cuisine', '98745663', '2024-10-31 22:25:17', '2024-11-01 16:27:19'),
(3, 4, 'Tasty Treats', '98563215', '2024-11-01 14:48:42', NULL),
(4, 5, 'Si Chuan Mei Shi', '87456351', '2024-11-01 16:38:11', NULL),
(5, 6, 'Noodle 11', '87452365', '2024-11-01 20:36:07', NULL),
(6, 7, 'Japanese 14', '98754128', '2024-11-01 22:09:54', '2024-11-01 22:10:14'),
(7, 8, 'Western 14', '87456982', '2024-11-01 22:13:19', NULL),
(8, 9, 'Taiwan Cuisine ff', '87452365', '2024-11-01 22:16:43', '2024-11-01 22:22:49'),
(9, 10, 'Duck Rice ff', '84575692', '2024-11-01 22:26:34', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `canteens`
--
ALTER TABLE `canteens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `canteen_hours`
--
ALTER TABLE `canteen_hours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_hour_canteen` (`canteen_id`);

--
-- Indexes for table `foods`
--
ALTER TABLE `foods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_food_stall` (`stall_id`);

--
-- Indexes for table `stalls`
--
ALTER TABLE `stalls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_stall_vendor` (`vendor_id`),
  ADD KEY `fk_stall_canteen` (`canteen_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_vendor_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `canteens`
--
ALTER TABLE `canteens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `canteen_hours`
--
ALTER TABLE `canteen_hours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `foods`
--
ALTER TABLE `foods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stalls`
--
ALTER TABLE `stalls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `canteen_hours`
--
ALTER TABLE `canteen_hours`
  ADD CONSTRAINT `fk_hour_canteen` FOREIGN KEY (`canteen_id`) REFERENCES `canteens` (`id`);

--
-- Constraints for table `foods`
--
ALTER TABLE `foods`
  ADD CONSTRAINT `fk_food_stall` FOREIGN KEY (`stall_id`) REFERENCES `stalls` (`id`);

--
-- Constraints for table `stalls`
--
ALTER TABLE `stalls`
  ADD CONSTRAINT `fk_stall_canteen` FOREIGN KEY (`canteen_id`) REFERENCES `canteens` (`id`),
  ADD CONSTRAINT `fk_stall_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`);

--
-- Constraints for table `vendors`
--
ALTER TABLE `vendors`
  ADD CONSTRAINT `fk_vendor_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
