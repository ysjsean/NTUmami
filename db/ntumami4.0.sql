-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 12, 2024 at 07:01 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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
(11, 'Koufu @ North Spine', '', '76 Nanyang Drive, N2.1, #02-03, Nanyang Technological University, 637331', '/NTUmami/assets/images/locations/6723e3d3bf97e.jpg', '2024-11-01 04:08:51', '2024-11-03 04:29:49'),
(12, 'Canteen 11', '', '20 Nanyang Ave, Nanyang Technological University, Singapore 639809', '/NTUmami/assets/images/locations/6724d53f7be58.jpg', '2024-11-01 04:13:05', '2024-11-01 21:18:55'),
(13, 'Fine Food @ South Spine', '', '50 Nanyang Avenue B Food Court, Canteen, South Spine, 639798', '/NTUmami/assets/images/locations/672462e71ff61.jpg', '2024-11-01 13:11:03', '2024-11-03 04:30:06'),
(17, 'NIE Canteen', '', '1 Nanyang Walk, Singapore 637616', '/NTUmami/assets/images/locations/672681447d7c9.jpg', '2024-11-03 03:45:08', '2024-11-05 03:57:27'),
(18, 'Canteen 2', '', '35 Students Walk, Singapore 639548', '/NTUmami/assets/images/locations/67268189dd27b.jpg', '2024-11-03 03:46:17', NULL),
(22, 'Pioneer Hall Canteen', '', '156 Nanyang Cres, Singapore 637125', '/NTUmami/assets/images/locations/67268cfca0246.jpg', '2024-11-03 04:35:08', NULL);

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
(71, 9, '10:30:00', '21:00:00', 'Mon'),
(72, 9, '10:30:00', '21:00:00', 'Tue'),
(73, 9, '10:30:00', '21:00:00', 'Wed'),
(74, 9, '10:30:00', '21:00:00', 'Thu'),
(75, 9, '10:30:00', '21:00:00', 'Fri'),
(76, 9, '10:30:00', '21:00:00', 'Sat'),
(77, 9, '10:30:00', '21:00:00', 'Sun'),
(95, 18, '07:00:00', '22:00:00', 'Sun'),
(96, 18, '07:00:00', '22:00:00', 'Mon'),
(97, 18, '07:00:00', '22:00:00', 'Tue'),
(98, 18, '07:00:00', '22:00:00', 'Wed'),
(99, 18, '07:00:00', '22:00:00', 'Thu'),
(100, 18, '07:00:00', '22:00:00', 'Fri'),
(101, 18, '07:00:00', '22:00:00', 'Sat'),
(123, 22, '08:00:00', '20:00:00', 'Sun'),
(124, 22, '08:00:00', '20:00:00', 'Mon'),
(125, 22, '08:00:00', '20:00:00', 'Tue'),
(126, 22, '08:00:00', '20:00:00', 'Wed'),
(127, 22, '08:00:00', '20:00:00', 'Thu'),
(128, 22, '08:00:00', '20:00:00', 'Fri'),
(129, 22, '08:00:00', '20:00:00', 'Sat'),
(131, 17, '08:00:00', '15:00:00', 'Mon'),
(132, 17, '08:00:00', '15:00:00', 'Tue'),
(133, 17, '08:00:00', '15:00:00', 'Wed'),
(134, 17, '08:00:00', '15:00:00', 'Thu'),
(135, 17, '08:00:00', '15:00:00', 'Fri'),
(136, 12, '07:30:00', '20:00:00', 'Mon'),
(137, 12, '07:30:00', '20:00:00', 'Fri'),
(138, 12, '07:30:00', '20:00:00', 'Sat'),
(139, 12, '07:30:00', '20:00:00', 'Sun');

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_by` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `created_by`) VALUES
(21, 89, '0000-00-00 00:00:00'),
(24, 87, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `special_request` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `food_id`, `qty`, `special_request`) VALUES
(45, 21, 23, 1, ''),
(51, 24, 22, 1, NULL),
(52, 24, 23, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `first_name`, `last_name`, `email`, `message`, `created_at`) VALUES
(1, 'Bryan', 'Koh', 'bryank00h@gmail.com', 'test', '2024-11-11 19:11:28'),
(2, 'Bryan', 'Koh', 'bryank00h@gmail.com', 'hi', '2024-11-11 19:12:17'),
(3, 'Bryan', 'Koh', 'bryank00h@gmail.com', 'test', '2024-11-11 19:14:05'),
(4, 'Bryan', 'Koh', 'bryank00h@gmail.com', 'test2', '2024-11-11 19:14:11'),
(5, 'Bryan', 'Koh', 'bryank00h@gmail.com', 'test2', '2024-11-11 19:16:23'),
(6, 'Bryan', 'Koh', 'bryank00h@gmail.com', 'test3', '2024-11-11 19:16:28'),
(7, 'Bryan', 'Koh', 'bryank00h@gmail.com', 'test 4', '2024-11-11 19:20:50'),
(8, 'Bryan', 'Koh', 'bryank00h@gmail.com', 'test5', '2024-11-11 19:21:10'),
(9, 'Bryan', 'Koh', 'bryank00h@gmail.com', 'test', '2024-11-11 19:21:26'),
(10, 'Bryan', 'Koh', 'bryank00h@gmail.com', 'test7', '2024-11-11 19:21:36');

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

--
-- Dumping data for table `foods`
--

INSERT INTO `foods` (`id`, `stall_id`, `name`, `price`, `description`, `image_url`, `is_halal`, `is_vegetarian`, `is_in_stock`, `created_by`, `updated_by`) VALUES
(1, 5, 'Seafood Hor Fun', 5.50, 'Flat rice noodles with fresh seafood in a savory sauce', '/NTUmami/assets/images/food_images/Seafood Hor Fun.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:08:12'),
(2, 5, 'Beef Noodle Soup', 6.00, 'Tender beef slices with rice noodles in a flavorful broth', '/NTUmami/assets/images/food_images/Beef Noodle Soup.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:08:08'),
(3, 5, 'Tom Yum Noodles', 5.00, 'Spicy and tangy Thai-style noodles with seafood', '/NTUmami/assets/images/food_images/Tom Yum Noodles.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:08:04'),
(4, 6, 'Ramen', 8.00, 'Japanese noodle soup with pork and flavorful broth', '/NTUmami/assets/images/food_images/Ramen.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:07:58'),
(6, 6, 'Chicken Katsu', 7.50, 'Breaded fried chicken served with rice', '/NTUmami/assets/images/food_images/Chicken Katsu.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:07:55'),
(8, 7, 'Mapo Tofu', 5.00, 'Spicy Sichuan dish with tofu and minced meat', '/NTUmami/assets/images/food_images/Mapo Tofu.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:07:51'),
(9, 7, 'Kung Pao Chicken', 6.00, 'Stir-fried chicken with peanuts in a spicy sauce', '/NTUmami/assets/images/food_images/Kung Pao Chicken.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:07:48'),
(12, 10, 'Grilled Chicken', 7.00, 'Juicy grilled chicken served with mashed potatoes', '/NTUmami/assets/images/food_images/Grilled Chicken.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:07:45'),
(13, 10, 'Fish and Chips', 8.50, 'Classic battered fish served with fries', '/NTUmami/assets/images/food_images/Fish and Chips.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:07:38'),
(16, 11, 'Braised Pork Rice', 5.00, 'Rice topped with braised pork in soy sauce', '/NTUmami/assets/images/food_images/Braised Pork Rice.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:07:41'),
(17, 11, 'Taiwanese Popcorn Chicken', 4.50, 'Crispy fried chicken bites with spices', '/NTUmami/assets/images/food_images/Taiwanese Popcorn Chicken.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:07:30'),
(19, 12, 'Duck Rice', 5.50, 'Braised duck served with fragrant rice', '/NTUmami/assets/images/food_images/Duck Rice.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:07:35'),
(20, 12, 'Duck Noodles', 6.00, 'Noodles with braised duck in rich broth', '/NTUmami/assets/images/food_images/Duck Noodles.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:07:26'),
(21, 12, 'Duck Porridge', 4.50, 'Rice porridge with tender braised duck', '/NTUmami/assets/images/food_images/Duck Porridge.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:06:04'),
(22, 13, 'Fried Bee Hoon', 3.00, 'Stir-fried rice vermicelli with vegetables', '/NTUmami/assets/images/food_images/Fried Bee Hoon.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 21:06:11'),
(23, 13, 'Fried Kway Teow', 3.50, 'Stir-fried flat noodles with egg and veggies', '/NTUmami/assets/images/food_images/Fried Kway Teow.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 21:06:17'),
(24, 13, 'Nasi Lemak', 4.00, 'Rice with coconut milk, anchovies, and sambal', '/NTUmami/assets/images/food_images/Nasi Lemak.jpg', 1, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:06:20'),
(26, 14, 'Paneer Tikka', 6.00, 'Grilled paneer cubes marinated in spices', '/NTUmami/assets/images/food_images/Paneer Tikka.jpg', 1, 1, 0, '2024-11-06 01:59:07', '2024-11-12 21:12:57'),
(28, 15, 'Roasted Chicken Rice', 5.00, 'Roasted chicken served with fragrant rice', '/NTUmami/assets/images/food_images/Roasted Chicken Rice.jpg', 1, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:06:27'),
(29, 15, 'Hainanese Chicken Rice', 5.00, 'Poached chicken with rice cooked in chicken broth', '/NTUmami/assets/images/food_images/Hainanese Chicken Rice.jpg', 1, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:06:30'),
(31, 16, 'Lemon Tea', 1.50, 'Refreshing iced lemon tea', '/NTUmami/assets/images/food_images/Lemon Tea.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 21:06:34'),
(32, 16, 'Bandung', 1.80, 'Rose syrup with milk', '/NTUmami/assets/images/food_images/Bandung.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 21:06:37'),
(33, 16, 'Iced Milo', 2.00, 'Chocolate malt drink served cold', '/NTUmami/assets/images/food_images/Iced Milo.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 21:06:41'),
(40, 19, 'Shoyu Ramen', 8.50, 'Soy sauce flavored ramen with pork', '/NTUmami/assets/images/food_images/Shoyu Ramen.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:06:44'),
(41, 19, 'Tonkotsu Ramen', 9.00, 'Rich pork broth ramen with sliced pork', '/NTUmami/assets/images/food_images/Tonkotsu Ramen.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:06:47'),
(46, 21, 'Soy Milk', 1.50, 'Chilled soy milk drink', '/NTUmami/assets/images/food_images/Soy Milk.jpg', 1, 1, 0, '2024-11-06 01:59:07', '2024-11-12 17:18:05'),
(47, 21, 'Grass Jelly Drink', 1.80, 'Refreshing drink with grass jelly', '/NTUmami/assets/images/food_images/Grass Jelly Drink.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 21:06:58'),
(48, 21, 'Coconut Water', 2.00, 'Fresh coconut water served chilled', '/NTUmami/assets/images/food_images/Coconut Water.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 21:07:02'),
(49, 22, 'Spicy Hot Dry Noodles', 5.00, 'Popular Wuhan noodles with a spicy sesame sauce', '/NTUmami/assets/images/food_images/Spicy Hot Dry Noodles.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:07:06'),
(52, 23, 'Biang Biang Noodles', 6.00, 'Thick, hand-pulled noodles with spicy sauce', '/NTUmami/assets/images/food_images/Biang Biang Noodles.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:07:09'),
(54, 23, 'Roujiamo', 4.50, 'Chinese-style sandwich with spiced meat filling', '/NTUmami/assets/images/food_images/Roujiamo.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:07:12'),
(57, 24, 'Fried Rice with Chicken', 5.00, 'Fried rice with chunks of chicken and vegetables', '/NTUmami/assets/images/food_images/Fried Rice with Chicken.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:07:15'),
(61, 26, 'Chicken Chop', 6.50, 'Grilled chicken with mushroom sauce and sides', '/NTUmami/assets/images/food_images/Chicken Chop.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:07:19'),
(62, 26, 'Beef Steak', 8.50, 'Juicy beef steak served with mashed potatoes', '/NTUmami/assets/images/food_images/Beef Steak.jpg', 0, 0, 0, '2024-11-06 01:59:07', '2024-11-12 21:13:05'),
(63, 26, 'Spaghetti Bolognese', 6.00, 'Spaghetti with a rich meat sauce', '/NTUmami/assets/images/food_images/Spaghetti Bolognese.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:05:54'),
(64, 27, 'Japchae', 5.50, 'Sweet potato noodles stir-fried with vegetables', '/NTUmami/assets/images/food_images/Japchae.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 21:05:46'),
(67, 28, 'Char Siew Rice', 5.00, 'Sweet barbecued pork served with rice', '/NTUmami/assets/images/food_images/Char Siew Rice.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:05:42'),
(68, 28, 'Roast Duck Rice', 5.50, 'Tender roast duck served with rice', '/NTUmami/assets/images/food_images/Roast Duck Rice.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:05:38'),
(70, 29, 'Gua Bao', 4.50, 'Steamed bun with braised pork and pickled veggies', '/NTUmami/assets/images/food_images/Gua Bao.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:05:35'),
(73, 30, 'Vegetable Fried Rice', 4.50, 'Fried rice with mixed vegetables', '/NTUmami/assets/images/food_images/Vegetable Fried Rice.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 21:05:25'),
(74, 30, 'Stir-fried Broccoli', 4.00, 'Fresh broccoli stir-fried with garlic', '/NTUmami/assets/images/food_images/Stir-fried Broccoli.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 21:05:20'),
(75, 30, 'Tofu Stir Fry', 4.50, 'Tofu stir-fried with bell peppers and mushrooms', '/NTUmami/assets/images/food_images/Tofu Stir Fry.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 21:05:15'),
(76, 31, 'Iced Lemon Tea', 1.50, 'Refreshing iced tea with a hint of lemon', '/NTUmami/assets/images/food_images/Iced Lemon Tea.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 21:05:05'),
(78, 31, 'Kopi', 1.20, 'Traditional Singaporean coffee', '/NTUmami/assets/images/food_images/Kopi.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 21:05:01'),
(79, 32, 'Fresh Orange Juice', 2.50, 'Freshly squeezed orange juice', '/NTUmami/assets/images/food_images/Fresh Orange Juice.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 21:04:56'),
(80, 32, 'Watermelon Juice', 2.00, 'Fresh watermelon juice served chilled', '/NTUmami/assets/images/food_images/Watermelon Juice.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 21:04:51'),
(83, 33, 'Mee Rebus', 4.00, 'Yellow noodles in a spicy, thick gravy', '/NTUmami/assets/images/food_images/Mee Rebus.jpg', 1, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:04:25'),
(104, 41, 'Bulgogi Rice', 7.00, 'Grilled marinated beef served over rice', '/NTUmami/assets/images/food_images/Bulgogi Rice.jpg', 1, 0, 1, '2024-11-06 01:59:07', '2024-11-06 21:04:18'),
(105, 41, 'Kimchi Fried Rice', 6.00, 'Fried rice with kimchi and egg', '/NTUmami/assets/images/food_images/Kimchi Fried Rice.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 02:58:18'),
(106, 42, 'Vegetable Briyani', 5.50, 'Basmati rice with a mix of vegetables and spices', '/NTUmami/assets/images/food_images/Vegetable Briyani.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 02:58:13'),
(107, 42, 'Paneer Butter Masala', 6.50, 'Cottage cheese in a creamy tomato-based sauce', '/NTUmami/assets/images/food_images/Paneer Butter Masala.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 02:58:08'),
(109, 44, 'Butter Chicken', 7.00, 'Tender chicken in a creamy tomato-based sauce', '/NTUmami/assets/images/food_images/Butter Chicken.jpg', 1, 0, 1, '2024-11-06 01:59:07', '2024-11-06 02:58:00'),
(110, 44, 'Lamb Curry', 8.00, 'Slow-cooked lamb in a rich, spicy curry', '/NTUmami/assets/images/food_images/Lamb Curry.jpg', 1, 0, 1, '2024-11-06 01:59:07', '2024-11-06 02:57:55'),
(125, 49, 'Fried Fish Soup', 5.50, 'Clear soup with crispy fried fish', '/NTUmami/assets/images/food_images/Fried Fish Soup.jpg', 1, 0, 1, '2024-11-06 01:59:07', '2024-11-06 02:57:45'),
(126, 49, 'Ban Mian', 5.50, 'Handmade noodles in fish broth with vegetables', '/NTUmami/assets/images/food_images/Ban Mian.jpg', 1, 0, 1, '2024-11-06 01:59:07', '2024-11-06 02:57:49'),
(130, 51, 'Nasi Ayam', 6.00, 'Fragrant rice served with fried or roasted chicken', '/NTUmami/assets/images/food_images/nasiayam.png', 1, 0, 1, '2024-11-06 01:59:07', '2024-11-06 02:51:15'),
(133, 52, 'Chicken Katsu Don', 7.00, 'Rice bowl topped with breaded chicken cutlet', '/NTUmami/assets/images/food_images/Chicken Katsu Don.jpg', 1, 0, 0, '2024-11-06 01:59:07', '2024-11-12 21:13:32'),
(135, 52, 'Tempura Udon', 7.50, 'Udon noodles served with crispy tempura', '/NTUmami/assets/images/food_images/Tempura Udon.jpg', 1, 0, 1, '2024-11-06 01:59:07', '2024-11-06 02:51:30'),
(136, 53, 'Caesar Salad', 5.00, 'Classic Caesar salad with romaine and Parmesan', '/NTUmami/assets/images/food_images/Caesar Salad.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 02:53:13'),
(137, 53, 'Chicken Avocado Sandwich', 6.50, 'Grilled chicken with avocado in a fresh sandwich', '/NTUmami/assets/images/food_images/Chicken Avocado Sandwich.jpg', 1, 0, 1, '2024-11-06 01:59:07', '2024-11-06 02:53:06'),
(139, 54, 'Seafood Paofan', 8.00, 'Rice in a rich seafood broth with shrimp and fish', '/NTUmami/assets/images/food_images/Seafood Paofan.jpg', 1, 0, 1, '2024-11-06 01:59:07', '2024-11-06 02:53:00'),
(140, 54, 'Chicken Paofan', 7.00, 'Rice soaked in chicken broth with chicken slices', '/NTUmami/assets/images/food_images/Chicken Paofan.jpg', 1, 0, 1, '2024-11-06 01:59:07', '2024-11-06 02:52:57'),
(141, 54, 'Vegetable Paofan', 6.00, 'Rice in a light vegetable broth with greens', '/NTUmami/assets/images/food_images/Vegetable Paofan.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 02:52:52'),
(149, 57, 'Bibimbap', 7.50, 'Rice bowl with assorted vegetables, egg, and meat', '/NTUmami/assets/images/food_images/Bibimbap.jpg', 1, 1, 0, '2024-11-06 01:59:07', '2024-11-12 21:13:14'),
(151, 57, 'Kimchi Stew', 6.00, 'Spicy stew with kimchi and tofu', '/NTUmami/assets/images/food_images/Kimchi Stew.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 02:52:44'),
(152, 58, 'Pad Thai', 6.00, 'Stir-fried rice noodles with shrimp, tofu, and peanuts', '/NTUmami/assets/images/food_images/Pad Thai.jpg', 1, 0, 1, '2024-11-06 01:59:07', '2024-11-06 02:52:41'),
(153, 58, 'Green Curry Chicken', 6.50, 'Thai green curry with chicken and vegetables', '/NTUmami/assets/images/food_images/Green Curry Chicken.jpg', 1, 0, 1, '2024-11-06 01:59:07', '2024-11-06 02:52:31'),
(157, 59, 'Chicken Xiao Long Bao', 5.50, 'Steamed dumplings filled with chicken and soup', '/NTUmami/assets/images/food_images/Chicken Xiao Long Bao.jpg', 1, 0, 1, '2024-11-06 01:59:07', '2024-11-06 02:52:26'),
(158, 59, 'Vegetable Dumplings', 5.00, 'Steamed dumplings filled with vegetables', '/NTUmami/assets/images/food_images/Vegetable Dumplings.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 02:52:20'),
(203, 74, 'Fried Chicken Nasi Lemak', 5.50, 'Nasi lemak with crispy fried chicken', '/NTUmami/assets/images/food_images/Fried Chicken Nasi Lemak.jpg', 1, 0, 0, '2024-11-06 01:59:07', '2024-11-12 21:13:22'),
(204, 74, 'Egg Nasi Lemak', 4.00, 'Nasi lemak served with fried egg and sambal', '/NTUmami/assets/images/food_images/Egg Nasi Lemak.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 02:55:44'),
(205, 75, 'Pad Thai', 6.00, 'Stir-fried rice noodles with shrimp, tofu, and peanuts', '/NTUmami/assets/images/food_images/Pad Thai.jpg', 1, 0, 1, '2024-11-06 01:59:07', '2024-11-06 02:55:39'),
(207, 75, 'Mango Sticky Rice', 5.00, 'Sweet sticky rice with mango slices', '/NTUmami/assets/images/food_images/Mango Sticky Rice.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 02:55:33'),
(211, 78, 'Century Egg Porridge', 4.50, 'Smooth porridge with century egg and green onions', '/NTUmami/assets/images/food_images/Century Egg Porridge.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 02:55:28'),
(212, 78, 'Chicken Porridge', 4.00, 'Warm porridge with shredded chicken', '/NTUmami/assets/images/food_images/Chicken Porridge.jpg', 1, 0, 1, '2024-11-06 01:59:07', '2024-11-06 02:55:22'),
(213, 78, 'Fish Porridge', 4.50, 'Light porridge with fresh fish slices', '/NTUmami/assets/images/food_images/Fish Porridge.jpg', 1, 0, 1, '2024-11-06 01:59:07', '2024-11-06 02:55:14'),
(214, 79, 'Masala Dosa', 4.50, 'Crispy rice crepe filled with spiced potato', '/NTUmami/assets/images/food_images/Masala Dosa.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 02:55:08'),
(215, 79, 'Chicken Biryani', 7.00, 'Aromatic rice cooked with spices and chicken', '/NTUmami/assets/images/food_images/Chicken Biryani.jpg', 1, 0, 1, '2024-11-06 01:59:07', '2024-11-06 02:55:03'),
(218, 62, 'Stir-fried Mixed Vegetables', 4.00, 'Assorted fresh vegetables stir-fried in a savory sauce', '/NTUmami/assets/images/food_images/Stir-fried Mixed Vegetables.jpg', 1, 1, 1, '2024-11-06 01:59:07', '2024-11-06 02:54:15'),
(219, 62, 'Sweet and Sour Pork', 5.50, 'Crispy pork in a sweet and sour sauce', '/NTUmami/assets/images/food_images/Sweet and Sour Pork.jpg', 0, 0, 1, '2024-11-06 01:59:07', '2024-11-06 02:54:23'),
(222, 63, 'Chicken Curry Rice', 5.50, 'Steamed rice topped with flavorful chicken curry', '/NTUmami/assets/images/food_images/chickencurryrice.jpg', 1, 0, 1, '2024-11-06 01:59:07', '2024-11-06 02:54:54'),
(223, 63, 'Fish Curry Rice', 6.00, 'Rice served with spicy fish curry', '/NTUmami/assets/images/food_images/fishcurryrice.jpg', 1, 0, 1, '2024-11-06 01:59:07', '2024-11-06 02:54:43');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('Pending','Preparing','Ready for Pickup','Completed') NOT NULL DEFAULT 'Pending',
  `eat_in_take_out` enum('Eat-In','Take-Out') NOT NULL,
  `total_price` decimal(5,2) NOT NULL,
  `created_by` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `status`, `eat_in_take_out`, `total_price`, `created_by`, `updated_by`) VALUES
(3, 86, 'Pending', 'Eat-In', 26.50, '2024-11-07 18:16:54', NULL),
(4, 86, 'Pending', 'Eat-In', 11.50, '2024-11-09 09:03:41', NULL),
(7, 86, 'Pending', 'Eat-In', 3.50, '2024-11-10 14:34:43', NULL),
(10, 86, 'Pending', 'Eat-In', 6.00, '2024-11-10 15:19:35', NULL),
(12, 86, 'Pending', 'Eat-In', 5.00, '2024-11-10 15:44:25', NULL),
(13, 86, 'Pending', 'Eat-In', 5.00, '2024-11-10 15:45:52', NULL),
(14, 86, 'Pending', 'Eat-In', 4.00, '2024-11-10 15:46:42', NULL),
(15, 86, 'Pending', 'Eat-In', 3.50, '2024-11-10 16:02:38', NULL),
(16, 86, 'Pending', 'Eat-In', 9.00, '2024-11-11 10:34:33', NULL),
(17, 86, 'Pending', 'Eat-In', 8.50, '2024-11-11 14:43:13', NULL),
(18, 86, 'Completed', 'Eat-In', 7.80, '2024-11-11 15:18:01', '2024-11-11 15:40:28'),
(19, 87, 'Completed', 'Eat-In', 17.50, '2024-11-11 16:55:20', '2024-11-11 17:04:22'),
(20, 87, 'Completed', 'Eat-In', 16.00, '2024-11-11 19:48:31', '2024-11-11 19:50:32'),
(21, 87, 'Completed', 'Eat-In', 12.30, '2024-11-11 19:56:19', '2024-11-12 13:09:35'),
(22, 87, 'Pending', 'Eat-In', 23.50, '2024-11-12 13:19:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(5,2) NOT NULL,
  `status` enum('Pending','Preparing','Ready for Pickup','Completed') NOT NULL DEFAULT 'Pending',
  `special_request` text DEFAULT NULL,
  `created_by` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `food_id`, `qty`, `price`, `status`, `special_request`, `created_by`, `updated_by`) VALUES
(5, 3, 1, 3, 5.50, 'Pending', NULL, NULL, '2024-11-07 18:16:54'),
(6, 3, 3, 2, 5.00, 'Preparing', NULL, '2024-11-10 12:18:35', '2024-11-07 18:16:54'),
(7, 4, 1, 1, 5.50, 'Pending', 'Not so wet', NULL, '2024-11-09 09:03:41'),
(8, 4, 2, 1, 6.00, 'Pending', '', NULL, '2024-11-09 09:03:41'),
(11, 7, 23, 1, 3.50, 'Pending', '', NULL, '2024-11-10 14:34:43'),
(14, 10, 26, 1, 6.00, 'Pending', '', NULL, '2024-11-10 15:19:35'),
(16, 12, 28, 1, 5.00, 'Pending', '', NULL, '2024-11-10 15:44:25'),
(17, 13, 49, 1, 5.00, 'Pending', '', NULL, '2024-11-10 15:45:52'),
(18, 14, 83, 1, 4.00, 'Pending', '', NULL, '2024-11-10 15:46:42'),
(19, 15, 23, 1, 3.50, 'Pending', '', NULL, '2024-11-10 16:02:38'),
(20, 16, 41, 1, 9.00, 'Pending', '', NULL, '2024-11-11 10:34:33'),
(21, 17, 29, 1, 5.00, 'Pending', '', NULL, '2024-11-11 14:43:13'),
(22, 17, 23, 1, 3.50, 'Pending', '', NULL, '2024-11-11 14:43:13'),
(23, 18, 47, 1, 1.80, 'Completed', '', '2024-11-11 15:40:28', '2024-11-11 15:18:01'),
(24, 18, 26, 1, 6.00, 'Completed', '', '2024-11-11 15:40:28', '2024-11-11 15:18:01'),
(25, 19, 22, 1, 3.00, 'Completed', '', '2024-11-11 17:04:17', '2024-11-11 16:55:20'),
(26, 19, 40, 1, 8.50, 'Completed', '', '2024-11-11 17:04:19', '2024-11-11 16:55:20'),
(27, 19, 52, 1, 6.00, 'Completed', '', '2024-11-11 19:51:11', '2024-11-11 16:55:20'),
(28, 20, 23, 1, 3.50, 'Completed', '', '2024-11-11 19:50:30', '2024-11-11 19:48:31'),
(29, 20, 24, 1, 4.00, 'Completed', '', '2024-11-11 19:49:58', '2024-11-11 19:48:31'),
(30, 20, 40, 1, 8.50, 'Completed', '', '2024-11-11 19:50:32', '2024-11-11 19:48:31'),
(31, 21, 22, 1, 3.00, 'Completed', '', '2024-11-12 13:09:33', '2024-11-11 19:56:19'),
(32, 21, 47, 1, 1.80, 'Completed', '', '2024-11-12 13:09:34', '2024-11-11 19:56:19'),
(33, 21, 149, 1, 7.50, 'Completed', '', '2024-11-12 13:09:35', '2024-11-11 19:56:19'),
(34, 22, 104, 1, 7.00, 'Pending', '', NULL, '2024-11-12 13:19:01'),
(35, 22, 105, 1, 6.00, 'Preparing', '', NULL, '2024-11-12 13:19:01'),
(36, 22, 125, 1, 5.50, 'Ready for Pickup', '', NULL, '2024-11-12 13:19:01'),
(37, 22, 136, 1, 5.00, 'Completed', '', NULL, '2024-11-12 13:19:01');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `card_last_four` varchar(255) NOT NULL,
  `status` enum('Paid') NOT NULL DEFAULT 'Paid',
  `created_by` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `user_id`, `card_last_four`, `status`, `created_by`) VALUES
(3, 3, 86, '5556', 'Paid', '2024-11-07 18:16:54'),
(4, 4, 86, '6789', 'Paid', '2024-11-09 09:03:41'),
(7, 7, 86, '6789', 'Paid', '2024-11-10 14:34:43'),
(10, 10, 86, '6789', 'Paid', '2024-11-10 15:19:35'),
(12, 12, 86, '4568', 'Paid', '2024-11-10 15:44:25'),
(13, 13, 86, '4568', 'Paid', '2024-11-10 15:45:52'),
(14, 14, 86, '6789', 'Paid', '2024-11-10 15:46:42'),
(15, 15, 86, '4568', 'Paid', '2024-11-10 16:02:38'),
(16, 16, 86, '4242', 'Paid', '2024-11-11 10:34:33'),
(17, 17, 86, '6789', 'Paid', '2024-11-11 14:43:13'),
(18, 18, 86, '6789', 'Paid', '2024-11-11 15:18:01'),
(19, 19, 87, '4242', 'Paid', '2024-11-11 16:55:20'),
(20, 20, 87, '4242', 'Paid', '2024-11-11 19:48:31'),
(21, 21, 87, '4242', 'Paid', '2024-11-11 19:56:19'),
(22, 22, 87, '4242', 'Paid', '2024-11-12 13:19:01');

-- --------------------------------------------------------

--
-- Table structure for table `saved_payment_methods`
--

CREATE TABLE `saved_payment_methods` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cardholder_name` varchar(255) NOT NULL,
  `card_last_four` char(4) NOT NULL,
  `card_expiry` char(5) NOT NULL,
  `card_type` varchar(50) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `saved_payment_methods`
--

INSERT INTO `saved_payment_methods` (`id`, `user_id`, `cardholder_name`, `card_last_four`, `card_expiry`, `card_type`, `is_default`, `created_by`, `updated_by`) VALUES
(1, 86, 'Sean', '6789', '12/26', 'Visa', 1, '2024-11-06 18:33:38', '2024-11-06 18:57:54'),
(2, 86, 'Sean', '4568', '09/28', 'Master', 0, '2024-11-10 15:31:00', NULL),
(11, 87, 'fewfef', '1212', '03/27', 'Mastercard', 1, '2024-11-12 11:20:58', '2024-11-12 11:36:30'),
(17, 87, 'test', '4242', '03/28', 'Visa', 0, '2024-11-12 11:26:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `stalls`
--

CREATE TABLE `stalls` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `canteen_id` int(11) NOT NULL,
  `cuisine_type` enum('Chinese','Western','Indian','Malay','Japanese','Korean','Taiwan','Thai','Fusion','Drinks') NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `is_open` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_by` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stalls`
--

INSERT INTO `stalls` (`id`, `name`, `canteen_id`, `cuisine_type`, `vendor_id`, `is_open`, `created_by`, `updated_by`) VALUES
(5, 'Noodle Stall', 12, 'Chinese', 5, 1, '2024-11-01 15:53:19', '2024-11-09 16:00:17'),
(6, 'Kiso Japanese Cuisine', 12, 'Japanese', 1, 0, '2024-11-01 16:16:16', '2024-11-01 16:29:01'),
(7, 'Si Chuan Mei Shi', 12, 'Chinese', 4, 0, '2024-11-01 16:38:39', NULL),
(10, 'Western', 10, 'Western', 7, 0, '2024-11-01 22:13:49', NULL),
(11, 'Taiwan Cuisine', 13, 'Taiwan', 8, 0, '2024-11-01 22:20:37', '2024-11-01 22:22:57'),
(12, 'Braised Duck Rice', 13, 'Chinese', 9, 0, '2024-11-01 22:27:02', NULL),
(13, 'Bai Li Xiang Economic Bee Hoon', 9, 'Chinese', 13, 1, '2024-11-05 03:18:05', NULL),
(14, 'Ananda\'s Restaurant', 9, 'Indian', 12, 1, '2024-11-05 03:18:27', NULL),
(15, 'One Chicken', 9, 'Chinese', 11, 1, '2024-11-05 03:18:53', NULL),
(16, 'Fortune 16 Drinks', 9, 'Drinks', 10, 1, '2024-11-05 03:19:17', '2024-11-06 02:02:20'),
(19, 'Menya Takashi', 10, 'Japanese', 16, 1, '2024-11-05 03:31:35', NULL),
(21, 'Asia Farm Drinks', 10, 'Drinks', 17, 1, '2024-11-05 03:34:21', '2024-11-06 02:02:28'),
(22, 'Wuhan Delicacies', 10, 'Chinese', 30, 1, '2024-11-05 03:34:59', '2024-11-05 16:27:08'),
(23, 'Xi An Cuisine', 11, 'Chinese', 31, 1, '2024-11-05 03:37:33', '2024-11-05 16:27:19'),
(24, 'Mini Wok', 11, 'Chinese', 32, 1, '2024-11-05 03:37:57', '2024-11-05 16:27:31'),
(26, 'Western Cuisine', 11, 'Western', 34, 1, '2024-11-05 03:38:30', '2024-11-05 16:28:28'),
(27, 'Korean Delights', 11, 'Korean', 35, 1, '2024-11-05 03:39:00', '2024-11-05 16:28:44'),
(28, 'Roasted Delights', 11, 'Chinese', 36, 1, '2024-11-05 03:39:14', '2024-11-05 16:41:44'),
(29, 'Taiwan Food', 11, 'Taiwan', 37, 1, '2024-11-05 03:40:11', '2024-11-05 16:29:10'),
(30, 'Vegetarian', 11, 'Fusion', 38, 1, '2024-11-05 03:40:31', '2024-11-05 16:29:18'),
(31, 'Koufu Drinks', 11, 'Drinks', 39, 1, '2024-11-05 03:40:51', '2024-11-06 02:02:41'),
(32, 'Fruit & Juices', 12, 'Drinks', 40, 1, '2024-11-05 03:42:41', '2024-11-06 02:02:53'),
(33, 'Malay Food', 12, 'Malay', 41, 1, '2024-11-05 03:43:00', '2024-11-05 17:14:38'),
(41, 'Japanese & Korean', 13, 'Fusion', 48, 1, '2024-11-05 03:48:32', '2024-11-05 16:43:09'),
(42, 'Vegetarian', 13, 'Fusion', 49, 1, '2024-11-05 03:49:44', '2024-11-05 16:42:53'),
(44, 'Indian Food', 13, 'Indian', 50, 1, '2024-11-05 03:50:09', '2024-11-05 16:42:47'),
(49, 'Fish Soup Ban Mian', 17, 'Chinese', 53, 1, '2024-11-05 03:57:52', '2024-11-05 17:03:20'),
(51, 'Nasi Ayam', 17, 'Malay', 55, 1, '2024-11-05 03:58:29', '2024-11-05 17:03:40'),
(52, 'Japanese Cuisine', 17, 'Japanese', 56, 1, '2024-11-05 03:58:57', '2024-11-05 17:03:07'),
(53, 'Sandwiches & Salad Bar', 17, 'Fusion', 57, 1, '2024-11-05 03:59:19', '2024-11-05 17:04:07'),
(54, 'Paofan', 17, 'Chinese', 58, 1, '2024-11-05 04:00:13', '2024-11-05 17:03:29'),
(57, 'Korean', 18, 'Korean', 61, 1, '2024-11-05 04:04:55', '2024-11-05 17:04:21'),
(58, 'Thai Cuisine', 18, 'Thai', 62, 1, '2024-11-05 04:05:15', '2024-11-06 02:05:45'),
(59, 'Xiao Long Bao', 18, 'Chinese', 63, 1, '2024-11-05 04:05:26', '2024-11-05 17:04:51'),
(62, 'Mini Wok', 18, 'Chinese', 66, 1, '2024-11-05 04:06:14', '2024-11-05 17:05:13'),
(63, 'Curry Rice', 18, 'Chinese', 67, 1, '2024-11-05 04:06:52', '2024-11-05 17:05:27'),
(74, 'Nasi Lemak', 22, 'Malay', 78, 1, '2024-11-05 04:15:26', '2024-11-05 17:07:26'),
(75, 'Thai Cuisine', 22, 'Thai', 79, 1, '2024-11-05 04:15:46', '2024-11-06 02:05:52'),
(78, 'Porridge', 22, 'Chinese', 81, 1, '2024-11-05 04:16:55', '2024-11-05 17:06:46'),
(79, 'Indian', 22, 'Indian', 82, 1, '2024-11-05 04:17:12', '2024-11-05 17:06:58');

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
(10, 'ff_2', 'ff_2@gmai.com', 'test8', '$2y$10$C1gO17uNhniCyQqiLmxebeaAkp1LTXHazyiUGHC10s/qw9W/jNPIm', 'vendor', '2024-11-01 22:26:34', NULL),
(11, 'bkoh031', 'bkoh031@gmail.com', 'Bryan', '$2y$10$7NFucluGPmqsd0ti1EzxROHMF8pUs/VWKZM4vZrVYVjzuJl.BarYm', 'admin', '2024-11-02 01:07:17', '2024-11-02 01:07:54'),
(12, 'can16_drinks', 'can16_drinks@gmail.com', 'John Tan', '$2y$10$N8QW7NwOOeNE3qia7MC57eY780mpFq63MEpnTqtKS52SodGtcXMAS', 'vendor', '2024-11-05 03:13:35', NULL),
(13, 'can16_chickenrice', 'can16_chickenrice@gmail.com', 'Derrick Lim', '$2y$10$MMMf2iDCfO1FAVhDSRx/HuvDsbCYq0hDSZw00275FXGvCleJU3xo2', 'vendor', '2024-11-05 03:14:55', NULL),
(14, 'can16_indianfood', 'can16_indianfood@gmail.com', 'Sree', '$2y$10$vicZIC0x0doiphZfYa999ulzcsbxUHUK.Zchhv6TCc5.1NbScApRW', 'vendor', '2024-11-05 03:15:55', NULL),
(15, 'can16_chinesefood', 'can16_chinesefood@gmail.com', 'Lim Mei Ling', '$2y$10$cYCCphrT1ppsepBGBCK3mOy02Cu0iAwRY5qKWrGp9R02GPqXMRykO', 'vendor', '2024-11-05 03:16:58', NULL),
(16, 'can14_western', 'can14_western@gmail.com', 'Leon Koh', '$2y$10$aIBrMPULlh4WsgeqfBy6X.JB9RzsEsbGkg8SdzL.F3/eJVH7zGAzy', 'vendor', '2024-11-05 03:28:13', NULL),
(17, 'can14_chinese', 'can14_chinese@gmail.com', 'Soh Kim Leong', '$2y$10$biml7PoQif2AxbJ/pr9WPO3LslkF0aezESgF.I032VPoAd3SlOOei', 'vendor', '2024-11-05 03:28:49', NULL),
(18, 'can14_japanese', 'can14_japanese@gmail.com', 'Kentaro', '$2y$10$yLdR9lj57/fy6eS69g1l3e2h23Hu9YH1eHLzA6.ehVgdM3KVsgjV6', 'vendor', '2024-11-05 03:30:33', NULL),
(20, 'can14_drinks', 'can14_drinks@gmail.com', 'Rachel Tan', '$2y$10$1mTMdCqp0osv.zhgDVuw4uxUgkwlH9oaBUHPtAHP.w4KRrrslXQ0.', 'vendor', '2024-11-05 03:31:13', NULL),
(32, 'korean_bibim', 'korean_bibim@gmail.com', 'Sarah Lim', '$2y$10$0zhtp4bqZyvs8FNsrUFFJ..T0gTN7VH8yi9EECgtNdGQtW5feCTvK', 'vendor', '2024-11-05 16:19:17', NULL),
(33, 'wuhan_delicacies', 'wuhan_delicacies@gmail.com', 'Jasmine Ng', '$2y$10$Do3iKZwJSWwPqrDOV8cneemxYRzEdPu5zUAT82iIn47Ewc7gOL2M.', 'vendor', '2024-11-05 16:21:46', NULL),
(34, 'xi_an_cuisine', 'xi_an_cuisine@gmail.com', 'Emily Chia', '$2y$10$FRp2hvioBDSvKbmRCBfTPumuLKpADJ32rPzuf.db6sR/Rwu0R01zi', 'vendor', '2024-11-05 16:22:22', NULL),
(35, 'koufu_mini_wok', 'koufu_mini_wok@gmail.com', 'Daniel Ho', '$2y$10$mARqiiu1T76t72ejdN9yYeOBi.8Wzv3DDXXEi9jWMREAdd86WFDUi', 'vendor', '2024-11-05 16:23:05', NULL),
(37, 'koufu_western_cuisine', 'koufu_western_cuisine@gmail.com', 'Brian Goh', '$2y$10$6FtlxhSHZE/xxHRVKfsIY.qsPMaXvC4p1G36jJU2IB9vawzcw2Fu.', 'vendor', '2024-11-05 16:24:08', NULL),
(38, 'koufu_korean_delights', 'koufu_korean_delights@gmail.com', 'Linda Koh', '$2y$10$ozwGqfqmhCWDrMVG.C4x5.Gz9pBF/8GdO7rnB.oLAIZrSYITcVZyW', 'vendor', '2024-11-05 16:24:40', NULL),
(39, 'koufu_roasted_delights', 'koufu_roasted_delights@gmail.com', 'Victor Tan', '$2y$10$LCXDhMKpOCi5tYJ5e8LJ.uFyeKL8Ms5kKxawS4k3KqPGJiUWD7x6.', 'vendor', '2024-11-05 16:25:13', NULL),
(40, 'koufu_taiwan_food', 'koufu_taiwan_food@gmail.com', 'Fiona Chan', '$2y$10$nxpJmcBfnx8avwwXG2k9lOrlCQzR5XhKSsgxup.L/X6ZB/PNS1bi.', 'vendor', '2024-11-05 16:25:42', NULL),
(41, 'koufu_vegetarian', 'koufu_vegetarian@gmail.com', 'David Lee', '$2y$10$t4caI05pbjMIT1kAq7bIXO3edadRz4ArTFULRQ7pCHOHYNTttp/G6', 'vendor', '2024-11-05 16:26:12', NULL),
(42, 'koufu_drinks', 'koufu_drinks@gmail.com', 'Grace Tan', '$2y$10$XUJGsTr1cOqqJm/LRYoq0.BJHuT52udhnisOaOy3uDSWU6QXztQLy', 'vendor', '2024-11-05 16:26:42', NULL),
(43, 'can11_fruit_and_juices', 'can11_fruit_and_juices@gmail.com', 'Henry Wong', '$2y$10$Ip/zpU0hVOGyMc9xxJmLEOeqpXIvhbxE/mHOl0LoSiZneqXD/D2pG', 'vendor', '2024-11-05 16:33:11', NULL),
(44, 'can11_malay_food', 'can11_malay_food@gmail.com', 'Irene Ng', '$2y$10$Byq8UReCm1g.WeBpE/62T.hO6HA7j83j1YYsuSPWcIUiM3fQ3Ij7y', 'vendor', '2024-11-05 16:33:42', NULL),
(48, 'ff_nasi_padang', 'ff_nasi_padang@gmail.com', 'Rachel Lee', '$2y$10$2Gz.xZbnnsiX5OiOrrcwHuqj4zlG0kW09b.hcAvXBmI6rosE73XVi', 'vendor', '2024-11-05 16:36:11', NULL),
(50, 'ff_chicken_rice', 'ff_chicken_rice@gmail.com', 'Sarah Ong', '$2y$10$pFdvSwv6gZy1Q3aIXpcI7O2oBvSmgBNObGFNOxwzi86hLKIJ39diC', 'vendor', '2024-11-05 16:37:10', NULL),
(51, 'ff_japanese_and_korean', 'ff_japanese_and_korean@gmail.com', 'Nicholas Yeo', '$2y$10$t7tedBgw5p1iyurQq9ZHl.yWD.5p.ZJP3/0v1Ujp9.s3lctxoDkoC', 'vendor', '2024-11-05 16:37:42', NULL),
(52, 'ff_vegetarian', 'ff_vegetarian@gmail.com', 'Sylvia Lim', '$2y$10$SrbndeDHHH9S6ENSjJtUKuhPLgOzFIbhWYb3sEp9pOWnSHFrh0/Xm', 'vendor', '2024-11-05 16:39:03', NULL),
(53, 'ff_indian_food', 'ff_indian_food@gmail.com', 'Daryl Tay', '$2y$10$F7Rfr0gf7sQG7Y5opqVXF./GXgG9XMCq70pKGYfFrq27PxLvtDwuu', 'vendor', '2024-11-05 16:39:43', NULL),
(54, 'ff_drinks_stall', 'ff_drinks_stall@gmail.com', 'Alex Ng', '$2y$10$4qZIxJgMkyy6JDBRnFdr5eKRDV9tk1h1RwFmtQm88LVytobLFDjSi', 'vendor', '2024-11-05 16:40:14', NULL),
(55, 'nie_western_food', 'nie_western_food@gmail.com', 'Fiona Wong', '$2y$10$L5HPvUK5lDq5dZJ05pP56u2NFMBxumPMqAOmvQ3UKizYm1iRoqFiO', 'vendor', '2024-11-05 16:45:22', NULL),
(56, 'nie_fish_soup_ban_mian', 'nie_fish_soup_ban_mian@gmail.com', 'John Tan', '$2y$10$yGxNTuKEZ88aiCEHsIm8dePCl6h/Cvt7UKwLNGuBcXwmWhOLomZVi', 'vendor', '2024-11-05 16:46:00', NULL),
(57, 'nie_nasi_padang', 'nie_nasi_padang@gmail.com', 'Victor Ng', '$2y$10$CgKRlqOvo64anqlru7KItev2soo8Wjb8jiXsumT3glHocpSUPEyfS', 'vendor', '2024-11-05 16:46:28', NULL),
(58, 'nie_nasi_ayam', 'nie_nasi_ayam@gmail.com', 'Wendy Teo', '$2y$10$wKXIaClJbvBRgqh0Ye2ic.aU.31s/BiwFdX0i7LHadHCsDw4Ui/6y', 'vendor', '2024-11-05 16:46:54', NULL),
(59, 'nie_japanese_cuisine', 'nie_japanese_cuisine@gmail.com', 'Chris Ong', '$2y$10$k34eVfi15ZXjxcGwGTX8Zedgwb8VxPz191EtG9ftXKPraVOdurlTu', 'vendor', '2024-11-05 16:47:24', NULL),
(60, 'nie_sandwiches_and_salad_bar', 'nie_sandwiches_and_salad_bar@gmail.com', 'Sylvia Chia', '$2y$10$wYoahKQYJy6aRlWTf2XdVe3i/wZe26j/x73g6uyPHfy5zlIjWKBSy', 'vendor', '2024-11-05 16:47:52', NULL),
(61, 'nie_paofan', 'nie_paofan@gmail.com', 'Kevin Goh', '$2y$10$V3JiqsZe5GMZlt1nE//d8.y/ABqQEjYfmKNdkda31..JA48/Nf.cu', 'vendor', '2024-11-05 16:48:20', NULL),
(62, 'nie_ba_chor_mee', 'nie_ba_chor_mee@gmail.com', 'Rachel Lim', '$2y$10$a.LgR5m7LoUe5idZ51zgI.yGahZanB6VMJNZE7iWxgFZTXck/NEf.', 'vendor', '2024-11-05 16:48:45', NULL),
(63, 'nie_fruit_juices', 'nie_fruit_juices@gmail.com', 'Daniel Tan', '$2y$10$sOmxGx8SnGcAUGEWgplmeOle/3KKQEbtPkHy/g7fKHXf9L7ZKQq3S', 'vendor', '2024-11-05 16:49:09', NULL),
(64, 'can2_korean', 'can2_korean@gmail.com', 'Jasmine Koh', '$2y$10$B/i6ySfdhX2pwrJBcq2NJu15Uqd5fVvuq0AtWQdj5n7eC5/wXFwhS', 'vendor', '2024-11-05 16:50:05', NULL),
(65, 'can2_thai_cuisine', 'can2_thai_cuisine@gmail.com', 'Henry Ong', '$2y$10$93HlSym6IN5GEdCVLPU/TOaPusNuDutTmByR.LX7JPGEmSloaDzTW', 'vendor', '2024-11-05 16:50:29', NULL),
(66, 'can2_xiao_long_bao', 'can2_xiao_long_bao@gmail.com', 'Emily Lee', '$2y$10$A9zJF7mV/0vZdhHoAlpYAOJ3e1GdIPnijHLdfrQxA34SLMxOZ51Bu', 'vendor', '2024-11-05 16:50:59', NULL),
(67, 'can2_western_cuisine', 'can2_western_cuisine@gmail.com', 'Alex Ho', '$2y$10$TCb8EH5flao7pcden.KZ1OMxpyoH2AYEXPdok5ylTAbqiRK4sBS.y', 'vendor', '2024-11-05 16:51:37', NULL),
(69, 'can2_mini_wok', 'can2_mini_wok@gmail.com', 'Sarah Chia', '$2y$10$uIzEQW.n6EaKKfW1MGuFc.wnrEN.erACMJUFUjzVgLCbFc1FrVDl2', 'vendor', '2024-11-05 16:52:29', NULL),
(70, 'can2_curry_rice', 'can2_curry_rice@gmail.com', 'Brian Tan', '$2y$10$mGolII64e.merewfAxtPi.IEZVjS4t4ipZhpzyfV1fg1vTCERWZGu', 'vendor', '2024-11-05 16:52:56', NULL),
(71, 'can2_taiwan_cuisine', 'can2_taiwan_cuisine@gmail.com', 'Victor Lee', '$2y$10$jN0yf89YgOfhTsPR/oK9nupmQZ8DInld.hMIqH8Z9lhngihBprCTS', 'vendor', '2024-11-05 16:53:21', NULL),
(81, 'pioneer_nasi_lemak', 'pioneer_nasi_lemak@gmail.com', 'Sarah Yeo', '$2y$10$U.7lDQUYsUlb/RGzOQhkUO2EB1T46uPADaUUV8vtT9IyPebCjmVhC', 'vendor', '2024-11-05 16:59:12', NULL),
(82, 'pioneer_thai_cuisine', 'pioneer_thai_cuisine@gmail.com', 'Daniel Lee', '$2y$10$q.uOQI7pYU3TS2FSluBibeD1gUmxqzuTXFPOBxvKfR3HA37YhR8DG', 'vendor', '2024-11-05 16:59:37', NULL),
(84, 'pioneer_porridge', 'pioneer_porridge@gmail.com', 'Victor Ong', '$2y$10$vIW6KU5/FicU.nCR0uj/NuB5LxmRbe8nF77kJ/iLfCSRHsyIz34Me', 'vendor', '2024-11-05 17:00:59', NULL),
(85, 'pioneer_indian', 'pioneer_indian@gmail.com', 'Grace Tan', '$2y$10$wXz5ZhqwARgUzHOhojuzW.kVVUTYtrS8FgvZkcdSOpdcrRqzDKvXu', 'vendor', '2024-11-05 17:01:31', NULL),
(86, 'seany', 'seanyoungsongjie@gmail.com', 'Sean', '$2y$10$ViIHxdIPFE4QrGs.Kdwp3ekSch5Ptz.XzxucGN5RNx1VM5SK1GXyy', 'user', '2024-11-06 22:33:25', NULL),
(87, 'bkoh', 'bkoh@gmail.com', 'Bryan Koh', '$2y$10$eqFTqZnIo.oOfWEUB5Z8GujeGEbyZDz/9Am0TwX2GUuz0CyegNaAu', 'user', '2024-11-12 00:13:21', '2024-11-12 04:20:55'),
(89, 'harry', 'harry@gmail.com', 'Bryan Koh', '$2y$10$m2LYTfXFy6aTCW0Klq3ZQ.BfrQANtkDkvSeK4bGmCpTMbaRNmaNNK', 'user', '2024-11-12 19:46:58', '2024-11-12 21:04:57');

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `user_id` int(11) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `street2` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'Singapore'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`user_id`, `phone`, `birthdate`, `street`, `street2`, `city`, `postal_code`, `country`) VALUES
(89, '97979797', '2012-11-07', '688 Jurong West Central 1, #11-249', '#11-920', 'Singapore', '64068844', 'Singapore');

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
(1, 2, 'Can 11 Kiso Japanese Cuisine', '98745663', '2024-10-31 22:25:17', '2024-11-05 17:10:14'),
(3, 4, 'Can 11 Tasty Treats', '98563215', '2024-11-01 14:48:42', '2024-11-05 17:10:26'),
(4, 5, 'Can 11 Si Chuan Mei Shi', '87456351', '2024-11-01 16:38:11', '2024-11-05 17:10:34'),
(5, 6, 'Can 11 Noodle', '87452365', '2024-11-01 20:36:07', '2024-11-05 17:10:45'),
(6, 7, 'Can 14 Japanese', '98754128', '2024-11-01 22:09:54', '2024-11-05 17:10:57'),
(7, 8, 'Can 14 Western', '87456982', '2024-11-01 22:13:19', '2024-11-05 17:12:17'),
(8, 9, 'Taiwan Cuisine ff', '87452365', '2024-11-01 22:16:43', '2024-11-01 22:22:49'),
(9, 10, 'Duck Rice ff', '84575692', '2024-11-01 22:26:34', NULL),
(10, 12, 'Fortune 16 Drinks', '91234567', '2024-11-05 03:13:35', NULL),
(11, 13, 'Can 16 One Chicken', '92345678', '2024-11-05 03:14:55', '2024-11-05 17:12:04'),
(12, 14, 'Can 16 Ananda\'s Restaurant', '93456789', '2024-11-05 03:15:55', '2024-11-05 17:11:56'),
(13, 15, 'Can 16 Bai Li Xiang', '99994561', '2024-11-05 03:16:58', '2024-11-05 17:11:48'),
(15, 17, 'Can 14 Fish Soup Ban Mian', '96584123', '2024-11-05 03:28:49', '2024-11-05 17:11:28'),
(16, 18, 'Can 14 Menya Takashi', '98512345', '2024-11-05 03:30:33', '2024-11-05 17:11:20'),
(17, 20, 'Can 14 Drinks Stall', '98512468', '2024-11-05 03:31:14', '2024-11-05 03:34:32'),
(29, 32, 'Can 16 Korean Bibim', '92345678', '2024-11-05 16:19:17', '2024-11-05 17:13:38'),
(30, 33, 'Can 16 Wuhan Delicacies', '86789012', '2024-11-05 16:21:46', '2024-11-05 17:13:51'),
(31, 34, 'Koufu Xi An Cuisine', '97890123', '2024-11-05 16:22:22', '2024-11-05 16:31:33'),
(32, 35, 'Koufu Mini Wok', '88901234', '2024-11-05 16:23:05', '2024-11-05 16:31:23'),
(34, 37, 'Koufu Western Cuisine', '90123456', '2024-11-05 16:24:08', '2024-11-05 16:31:12'),
(35, 38, 'Koufu Korean Delights', '81234567', '2024-11-05 16:24:40', '2024-11-05 16:31:05'),
(36, 39, 'Koufu Roasted Delights', '92345678', '2024-11-05 16:25:13', '2024-11-05 16:31:00'),
(37, 40, 'Koufu Taiwan Food', '83456789', '2024-11-05 16:25:42', '2024-11-05 16:30:54'),
(38, 41, 'Koufu Vegetarian', '94567890', '2024-11-05 16:26:12', '2024-11-05 16:30:47'),
(39, 42, 'Koufu Drinks', '85678901', '2024-11-05 16:26:42', NULL),
(40, 43, 'Can 11 Fruit & Juices', '86789012', '2024-11-05 16:33:11', NULL),
(41, 44, 'Can 11 Malay Food', '97890123', '2024-11-05 16:33:42', NULL),
(45, 48, 'FF Nasi Padang', '81234567', '2024-11-05 16:36:11', NULL),
(47, 50, 'FF Chicken Rice', '83456789', '2024-11-05 16:37:10', NULL),
(48, 51, 'FF Japanese & Korean', '94567890', '2024-11-05 16:37:42', NULL),
(49, 52, 'FF Vegetarian', '85678901', '2024-11-05 16:39:03', NULL),
(50, 53, 'FF Indian Food', '86789012', '2024-11-05 16:39:43', NULL),
(51, 54, 'FF Drinks Stall', '97890123', '2024-11-05 16:40:14', NULL),
(52, 55, 'NIE Western Food', '90123456', '2024-11-05 16:45:22', NULL),
(53, 56, 'NIE Fish Soup Ban Mian', '81234567', '2024-11-05 16:46:00', NULL),
(54, 57, 'NIE Nasi Padang', '92345678', '2024-11-05 16:46:28', NULL),
(55, 58, 'NIE Nasi Ayam', '83456789', '2024-11-05 16:46:54', NULL),
(56, 59, 'NIE Japanese Cuisine', '94567890', '2024-11-05 16:47:24', NULL),
(57, 60, 'NIE Sandwiches & Salad Bar', '85678901', '2024-11-05 16:47:52', NULL),
(58, 61, 'NIE Paofan', '86789012', '2024-11-05 16:48:20', NULL),
(59, 62, 'NIE Ba Chor Mee', '97890123', '2024-11-05 16:48:45', NULL),
(60, 63, 'NIE Fruit Juices', '88901234', '2024-11-05 16:49:09', NULL),
(61, 64, 'Can 2 Korean', '89012345', '2024-11-05 16:50:05', NULL),
(62, 65, 'Can 2 Thai Cuisine', '90123456', '2024-11-05 16:50:29', NULL),
(63, 66, 'Can 2 Xiao Long Bao', '81234567', '2024-11-05 16:50:59', NULL),
(64, 67, 'Can 2 Western Cuisine', '92345678', '2024-11-05 16:51:37', NULL),
(66, 69, 'Can 2 Mini Wok', '94567890', '2024-11-05 16:52:29', NULL),
(67, 70, 'Can 2 Curry Rice', '85678901', '2024-11-05 16:52:56', NULL),
(68, 71, 'Can 2 Taiwan Cuisine', '86789012', '2024-11-05 16:53:21', NULL),
(78, 81, 'Pioneer Nasi Lemak', '86789012', '2024-11-05 16:59:12', NULL),
(79, 82, 'Pioneer Thai Cuisine', '97890123', '2024-11-05 16:59:37', NULL),
(81, 84, 'Pioneer Porridge', '90123456', '2024-11-05 17:00:59', NULL),
(82, 85, 'Pioneer Indian', '81234567', '2024-11-05 17:01:31', NULL);

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
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cart_user` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cartItem_cart` (`cart_id`),
  ADD KEY `fk_cartItem_food` (`food_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `foods`
--
ALTER TABLE `foods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_food_stall` (`stall_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_user` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orderItem_order` (`order_id`),
  ADD KEY `fk_orderItem_food` (`food_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payment_user` (`user_id`);

--
-- Indexes for table `saved_payment_methods`
--
ALTER TABLE `saved_payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_spm_user` (`user_id`);

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
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`user_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `canteen_hours`
--
ALTER TABLE `canteen_hours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `foods`
--
ALTER TABLE `foods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=224;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `saved_payment_methods`
--
ALTER TABLE `saved_payment_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `stalls`
--
ALTER TABLE `stalls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `canteen_hours`
--
ALTER TABLE `canteen_hours`
  ADD CONSTRAINT `fk_hour_canteen` FOREIGN KEY (`canteen_id`) REFERENCES `canteens` (`id`);

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_cartItem_cart` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`),
  ADD CONSTRAINT `fk_cartItem_food` FOREIGN KEY (`food_id`) REFERENCES `foods` (`id`);

--
-- Constraints for table `foods`
--
ALTER TABLE `foods`
  ADD CONSTRAINT `fk_food_stall` FOREIGN KEY (`stall_id`) REFERENCES `stalls` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_orderItem_food` FOREIGN KEY (`food_id`) REFERENCES `foods` (`id`),
  ADD CONSTRAINT `fk_orderItem_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `saved_payment_methods`
--
ALTER TABLE `saved_payment_methods`
  ADD CONSTRAINT `fk_spm_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `stalls`
--
ALTER TABLE `stalls`
  ADD CONSTRAINT `fk_stall_canteen` FOREIGN KEY (`canteen_id`) REFERENCES `canteens` (`id`),
  ADD CONSTRAINT `fk_stall_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`);

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
