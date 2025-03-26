-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 26, 2025 at 02:41 PM
-- Server version: 8.0.41-0ubuntu0.20.04.1
-- PHP Version: 8.3.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cs2team2_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `role` enum('Super Admin','Manager','Other') COLLATE utf8mb4_unicode_520_ci DEFAULT 'Manager',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`, `role`, `created_at`) VALUES
(2, 'admin_user', '$2y$10$4IsmIBKfsstvijA/FdgB/OmFhWAfg6Ga9GTHYXkKiJzyDa9fPqpRi', 'Super Admin', '2025-03-18 23:02:13'),
(3, 'new_admin', '$2y$10$4IsmIBKfsstvijA/FdgB/OmFhWAfg6Ga9GTHYXkKiJzyDa9fPqpRi', 'Super Admin', '2025-03-23 15:30:24');

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `cart_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`cart_id`, `user_id`, `created_at`) VALUES
(2, 11, '2025-03-05 11:36:22'),
(3, 12, '2025-03-05 11:36:35'),
(4, 22, '2025-03-12 00:56:18'),
(5, 23, '2025-03-12 06:14:34'),
(6, 24, '2025-03-12 11:43:40'),
(7, 25, '2025-03-12 11:46:02'),
(9, 28, '2025-03-14 12:35:51'),
(10, 29, '2025-03-16 18:14:47'),
(11, 30, '2025-03-16 18:19:08'),
(12, 31, '2025-03-18 17:45:45'),
(14, 33, '2025-03-18 21:03:07'),
(15, 34, '2025-03-18 21:07:24'),
(16, 35, '2025-03-18 22:29:47'),
(18, 32, '2025-03-19 12:24:21'),
(19, 37, '2025-03-19 12:40:38'),
(20, 39, '2025-03-20 23:14:20'),
(38, 41, '2025-03-21 16:22:44'),
(39, 13, '2025-03-22 12:21:57'),
(40, 42, '2025-03-22 15:09:30'),
(41, 44, '2025-03-22 16:32:31'),
(47, 46, '2025-03-23 16:19:43'),
(55, 49, '2025-03-24 15:03:41'),
(57, 50, '2025-03-25 21:56:14'),
(58, 52, '2025-03-26 00:14:48');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` int NOT NULL,
  `cart_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `size` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT 'Required size selection'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`cart_item_id`, `cart_id`, `product_id`, `quantity`, `created_at`, `size`) VALUES
(9, 4, 5, 1, '2025-03-12 00:56:18', 'M'),
(10, 5, 2, 2, '2025-03-12 06:14:34', 'M'),
(11, 5, 3, 9, '2025-03-12 06:14:35', 'M'),
(12, 5, 4, 3, '2025-03-12 06:14:36', 'M'),
(13, 5, 5, 1, '2025-03-12 06:14:36', 'M'),
(14, 5, 6, 1, '2025-03-12 06:14:39', 'M'),
(15, 6, 3, 1, '2025-03-12 11:43:40', 'M'),
(16, 6, 4, 90007, '2025-03-12 11:43:45', 'M'),
(88, 11, 9, 1, '2025-03-18 18:07:17', 'M'),
(89, 11, 8, 11, '2025-03-18 18:07:19', 'M'),
(90, 11, 18, 4, '2025-03-18 18:07:22', 'M'),
(94, 11, 3, 2, '2025-03-18 18:13:11', 'M'),
(95, 11, 17, 4, '2025-03-18 18:30:12', 'M'),
(101, 16, 4, 1, '2025-03-18 22:29:47', 'M'),
(102, 16, 5, 1, '2025-03-18 22:29:54', 'M'),
(103, 6, 6, 4, '2025-03-19 10:41:52', 'M'),
(105, 12, 3, 7, '2025-03-19 11:41:06', 'M'),
(106, 12, 4, 5, '2025-03-19 11:41:10', 'M'),
(118, 11, 2, 2, '2025-03-19 12:46:25', 'M'),
(119, 11, 4, 2, '2025-03-19 17:01:19', 'M'),
(121, 12, 18, 2, '2025-03-21 01:17:44', 'M'),
(122, 12, 13, 4, '2025-03-21 01:19:21', 'M'),
(124, 38, 2, 1, '2025-03-21 18:24:41', 'M'),
(126, 12, 7, 1, '2025-03-21 22:50:31', 'M'),
(127, 38, 3, 1, '2025-03-22 00:29:34', 'M'),
(154, 47, 2, 1, '2025-03-24 05:46:42', 'L'),
(155, 47, 8, 7, '2025-03-24 05:46:55', 'One Size'),
(156, 47, 11, 1, '2025-03-24 05:47:00', 'One Size'),
(157, 47, 12, 4, '2025-03-24 05:47:14', 'One Size'),
(158, 47, 14, 6, '2025-03-24 05:47:26', 'One Size'),
(165, 58, 18, 3, '2025-03-26 00:14:48', 'M'),
(166, 58, 18, 1, '2025-03-26 00:29:25', 'S'),
(167, 58, 24, 1, '2025-03-26 00:38:00', 'L'),
(171, 14, 18, 1, '2025-03-26 10:12:15', 'L'),
(172, 14, 19, 2, '2025-03-26 10:12:31', 'L'),
(174, 6, 4, 1, '2025-03-26 10:43:16', 'L');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_520_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `description`, `created_at`) VALUES
(1, 'Men', 'Stylish and comfortable clothing for men.', '2025-02-18 16:25:59'),
(2, 'Women', 'Elegant and trendy fashion for women.', '2025-02-18 16:25:59'),
(3, 'Kids', 'Fun and playful outfits for kids of all ages.', '2025-02-18 16:25:59'),
(4, 'Babies', 'Soft and cozy clothing for newborns and infants.', '2025-02-18 16:25:59'),
(5, 'Accessories', 'Watches, bags, and other fashion accessories.', '2025-02-18 16:25:59');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `inquiry_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `submitted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int NOT NULL,
  `user_id` int NOT NULL,
  `order_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `total_amount`, `status`) VALUES
(1, 28, '2025-03-14 17:34:23', 674.88, 'Pending'),
(2, 28, '2025-03-14 17:41:47', 94.98, 'Pending'),
(3, 28, '2025-03-16 17:26:03', 289.93, 'Cancelled'),
(4, 28, '2025-03-16 17:39:33', 1379.87, 'Delivered'),
(5, 29, '2025-03-16 18:16:16', 469.93, 'Pending'),
(6, 30, '2025-03-16 18:20:15', 129.98, 'Cancelled'),
(7, 30, '2025-03-17 11:45:36', 299.95, 'Delivered'),
(8, 31, '2025-03-18 17:51:33', 264.96, 'Pending'),
(9, 25, '2025-03-18 17:56:30', 89.99, 'Delivered'),
(10, 30, '2025-03-18 17:56:42', 479.93, 'Delivered'),
(11, 34, '2025-03-18 21:07:56', 314.91, 'Pending'),
(12, 11, '2025-03-19 12:23:02', 49.99, 'Delivered'),
(13, 32, '2025-03-19 12:24:52', 179.98, 'Pending'),
(14, 37, '2025-03-19 12:42:20', 249.96, 'Delivered'),
(15, 41, '2025-03-21 16:25:15', 40.00, 'Pending'),
(16, 13, '2025-03-22 12:22:32', 89.98, 'Pending'),
(18, 46, '2025-03-23 16:23:52', 479.96, 'Pending'),
(22, 46, '2025-03-23 18:21:49', 1819.91, 'Delivered'),
(23, 39, '2025-03-23 22:31:06', 49.99, 'Pending'),
(24, 39, '2025-03-24 12:22:30', 49.99, 'Pending'),
(25, 49, '2025-03-24 15:04:21', 59.99, 'Pending'),
(26, 50, '2025-03-25 21:57:10', 99.98, 'Delivered'),
(27, 44, '2025-03-25 23:42:58', 59.99, 'Pending'),
(28, 33, '2025-03-26 00:59:11', 599.95, 'Pending'),
(29, 42, '2025-03-26 10:13:43', 269.97, 'Cancelled'),
(30, 42, '2025-03-26 11:16:55', 179.98, 'Delivered'),
(31, 39, '2025-03-26 13:14:00', 1199.97, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `size` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT 'Required size selection'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`, `size`) VALUES
(1, 1, 4, 4, 49.99, 'M'),
(2, 1, 5, 4, 39.99, 'M'),
(3, 1, 6, 1, 44.99, 'M'),
(4, 1, 3, 3, 89.99, 'M'),
(5, 2, 6, 1, 44.99, 'M'),
(6, 2, 4, 1, 49.99, 'M'),
(7, 3, 4, 3, 49.99, 'M'),
(8, 3, 2, 4, 34.99, 'M'),
(9, 4, 18, 2, 129.99, 'M'),
(10, 4, 19, 3, 89.99, 'M'),
(11, 4, 23, 3, 49.99, 'M'),
(12, 4, 2, 1, 34.99, 'M'),
(14, 4, 8, 1, 199.99, 'M'),
(15, 4, 9, 1, 399.99, 'M'),
(16, 4, 12, 1, 14.99, 'M'),
(17, 5, 2, 1, 34.99, 'M'),
(18, 5, 17, 1, 59.99, 'M'),
(19, 5, 23, 1, 49.99, 'M'),
(20, 5, 7, 1, 249.99, 'M'),
(21, 5, 13, 3, 24.99, 'M'),
(22, 6, 3, 1, 89.99, 'M'),
(23, 6, 5, 1, 39.99, 'M'),
(24, 7, 8, 1, 199.99, 'M'),
(25, 7, 13, 4, 24.99, 'M'),
(26, 8, 24, 1, 34.99, 'M'),
(27, 8, 3, 2, 89.99, 'M'),
(28, 8, 4, 1, 49.99, 'M'),
(29, 9, 3, 1, 89.99, 'M'),
(30, 10, 3, 2, 89.99, 'M'),
(31, 10, 4, 5, 49.99, 'M'),
(33, 11, 2, 9, 34.99, 'M'),
(34, 12, 4, 1, 49.99, 'M'),
(35, 13, 3, 2, 89.99, 'M'),
(36, 14, 2, 2, 34.99, 'M'),
(37, 14, 3, 2, 89.99, 'M'),
(38, 15, 2, 2, 20.00, 'M'),
(39, 16, 6, 2, 44.99, 'M'),
(40, 18, 18, 3, 129.99, 'L'),
(41, 18, 19, 1, 89.99, 'M'),
(43, 22, 5, 5, 39.99, 'S'),
(44, 22, 9, 4, 399.99, 'One Size'),
(45, 22, 2, 1, 20.00, 'L'),
(46, 23, 4, 1, 49.99, 'L'),
(47, 24, 4, 1, 49.99, 'L'),
(48, 25, 17, 1, 59.99, 'M'),
(49, 26, 5, 1, 39.99, 'M'),
(50, 26, 17, 1, 59.99, 'L'),
(51, 27, 17, 1, 59.99, 'L'),
(52, 28, 2, 6, 20.00, 'M'),
(53, 28, 5, 1, 39.99, 'M'),
(54, 28, 18, 1, 129.99, 'M'),
(55, 28, 3, 1, 89.99, 'M'),
(56, 28, 3, 1, 89.99, 'L'),
(57, 28, 18, 1, 129.99, 'L'),
(58, 29, 3, 3, 89.99, 'M'),
(59, 30, 3, 2, 89.99, 'M'),
(60, 31, 9, 3, 399.99, 'One Size');

-- --------------------------------------------------------

--
-- Table structure for table `order_returns`
--

CREATE TABLE `order_returns` (
  `return_id` int NOT NULL,
  `order_id` int NOT NULL,
  `order_item_id` int NOT NULL,
  `user_id` int NOT NULL,
  `quantity` int NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `status` enum('Requested','Approved','Rejected','Completed') COLLATE utf8mb4_unicode_520_ci DEFAULT 'Requested',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `order_returns`
--

INSERT INTO `order_returns` (`return_id`, `order_id`, `order_item_id`, `user_id`, `quantity`, `reason`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, 9, 28, 2, 'Not as described', 'Requested', '2025-03-16 18:09:33', '2025-03-16 18:09:33'),
(2, 4, 15, 28, 1, 'Damaged', 'Requested', '2025-03-16 18:10:07', '2025-03-16 18:10:07'),
(3, 6, 23, 30, 1, 'Other', 'Requested', '2025-03-16 18:21:44', '2025-03-16 18:21:44'),
(4, 6, 22, 30, 1, 'Damaged', 'Requested', '2025-03-17 11:49:56', '2025-03-17 11:49:56'),
(5, 9, 29, 25, 1, 'Wrong size', 'Requested', '2025-03-18 17:57:53', '2025-03-18 17:57:53'),
(6, 10, 30, 30, 2, 'Damaged', 'Requested', '2025-03-18 18:40:04', '2025-03-18 18:40:04'),
(7, 12, 34, 11, 1, 'Damaged', 'Requested', '2025-03-19 12:24:10', '2025-03-19 12:24:10'),
(8, 10, 31, 30, 5, 'Damaged', 'Requested', '2025-03-19 12:44:37', '2025-03-19 12:44:37'),
(10, 30, 59, 42, 2, 'Wrong size', 'Requested', '2025-03-26 11:19:25', '2025-03-26 11:19:25');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_520_ci,
  `price` decimal(10,2) NOT NULL,
  `stock` int NOT NULL,
  `category_id` int DEFAULT NULL,
  `size` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `color` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `low_stock_threshold` int DEFAULT '5',
  `is_featured` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `price`, `stock`, `category_id`, `size`, `color`, `image`, `created_at`, `low_stock_threshold`, `is_featured`) VALUES
(2, 'Classic Men’s Polo Shirt', 'A timeless classic, this men’s polo shirt features a breathable cotton blend fabric, a modern fit, and a buttoned collar. Perfect for casual or smart wear.', 20.00, 23, 1, 'S,M,L', 'Black,White,Navy', 'menpolo1.avif,menpolo2.avif,menpolo3.avif', '2025-02-18 16:29:05', 5, 0),
(3, 'Men’s Insulated Puffer Jacket', 'Stay warm in style with this insulated puffer jacket, designed for cold weather. Features a water-resistant shell, premium insulation, and a detachable hood.', 89.99, 5, 1, 'S,M,L', 'Black', 'menpuffer1.avif,menpuffer2.webp,menpuffer3.webp', '2025-02-18 16:30:03', 5, 0),
(4, 'Classic Men’s Pullover Hoodie', 'Stay warm and stylish with this premium men’s pullover hoodie. Designed for comfort, it features a soft fleece lining, adjustable drawstring hood, and a relaxed fit. Perfect for casual outings or layering in colder seasons.', 49.99, 17, 1, 'S,M,L', 'Black', 'menhood1.avif,menhood2.avif,menhood3.avif', '2025-02-18 16:30:50', 5, 0),
(5, 'Men’s Performance Gym Wear', 'Built for performance, this gym wear is designed with breathable, sweat-wicking fabric to keep you dry during workouts. The slim-fit design enhances movement and comfort, making it perfect for training sessions or casual wear.', 39.99, 23, 1, 'S,M,L', 'Dark Grey', 'mengym1.avif,mengym2.webp,mengym3.avif', '2025-02-18 16:31:05', 5, 0),
(6, 'Men’s Athletic Jogger Pants', 'Upgrade your activewear with these stylish and comfortable jogger pants. Designed with a tapered fit, elastic waistband, and moisture-wicking fabric, these pants provide ultimate flexibility and comfort for workouts or casual wear.', 44.99, 42, 1, 'S,M,L', 'Black', 'menpants1.avif,menpants2.avif,menpants3.avif', '2025-02-18 16:31:47', 5, 0),
(7, 'Women\'s Luxury Diamond Watch', 'A stunning luxury watch featuring diamond-studded hour markers, a rose gold & silver bracelet, and a scratch-resistant sapphire crystal face.', 249.99, 15, 5, 'One Size', 'Rose Gold & Silver', 'bwomenwatch1.avif,bwomenwatch2.webp', '2025-02-18 16:55:28', 5, 0),
(8, 'Men\'s Classic Chronograph Watch', 'A premium men\'s watch with a stainless steel body, chronograph functionality, and water resistance up to 100m.', 199.99, 20, 5, 'One Size', 'Black & Silver', 'cmenwatch1.avif,cmenwatch2.avif', '2025-02-18 16:56:00', 5, 0),
(9, 'Blue Luxury Tote Bag', 'A stylish and spacious tote bag made from premium materials, featuring a signature monogram pattern and comfortable leather handles.', 399.99, 3, 5, 'One Size', 'Blue', 'goyardblue1.webp,goyardblue2.webp', '2025-02-18 16:56:18', 5, 0),
(10, 'Green Monogram Travel Duffle Bag', 'A high-end travel duffle bag crafted with durable materials, featuring leather handles, a spacious interior, and monogram detailing.', 599.99, 8, 5, 'One Size', 'Green', 'goyardgreen1.webp,goyardgreen2.webp', '2025-02-18 16:56:36', 5, 0),
(11, 'Navy Blue Minimalist Backpack', 'A modern and functional backpack, designed with a durable canvas body, leather accents, and multiple compartments for daily use.', 149.99, 12, 5, 'One Size', 'Navy Blue', 'backpack.avif,backpack2.jpg', '2025-02-18 16:57:10', 5, 0),
(12, 'Cozy Cotton Baby Bodysuit', 'A soft and breathable baby bodysuit made from 100% organic cotton. Features a snap-button closure for easy changing and a gentle stretch for all-day comfort.', 14.99, 24, 4, 'One Size', 'White', 'newbabie1.avif,newbabie2.avif,newbabie3.avif\r\n', '2025-02-18 19:05:24', 5, 0),
(13, 'Snuggly Knitted Baby Sweater', 'Keep your baby warm and stylish with this ultra-soft knitted sweater. Designed with delicate patterns and easy pull-on styling for a cozy winter look.', 24.99, 15, 4, 'One Size', 'Beige', 'babies2.avif', '2025-02-18 19:05:24', 5, 0),
(14, 'Comfy Baby Two-Piece Outfit Set', 'A stylish two-piece outfit set featuring a soft cotton long-sleeve top and matching joggers. Perfect for daily wear and playtime.', 19.99, 18, 4, 'One Size', 'Pink', 'babies3.avif', '2025-02-18 19:05:24', 5, 0),
(15, 'Adorable Baby Hooded Romper', 'A soft and fluffy hooded romper designed for ultimate comfort. Features a zip closure for easy dressing and playful bear ears on the hood.', 17.99, 10, 4, 'One Size', 'Blue', 'babies4.avif', '2025-02-18 19:05:24', 5, 0),
(16, 'Fluffy Baby Fleece Pajama Set', 'An ultra-cozy fleece pajama set designed to keep your baby warm during chilly nights. Features soft cuffs and breathable fabric for maximum comfort.', 12.99, 30, 4, 'One Size', 'Brown', 'babies6.avif', '2025-02-18 19:05:25', 5, 0),
(17, 'Chic Wide-Leg Tailored Pants', 'A stylish pair of high-waisted, wide-leg tailored pants with a relaxed yet sophisticated fit. Perfect for office wear or casual outings.', 59.99, 36, 2, 'S,M,L', 'Sky Blue', 'bluepants1.avif,bluepants2.webp,bluepants3.avif', '2025-02-18 19:28:26', 5, 0),
(18, 'Women\'s Luxury Puffer Jacket', 'Stay warm in style with this insulated puffer jacket. Designed for extreme comfort with lightweight padding, a detachable hood, and a fitted silhouette.', 129.99, 10, 2, 'S,M,L', 'Black', 'womenmonclear1.avif,womenmonclear2.avif,womenmonclear3.avif', '2025-02-18 19:28:26', 5, 0),
(19, 'Elegant Women\'s Blazer Dress', 'A sophisticated blazer-style dress featuring a tailored fit, sleek lapels, and buttoned closure. Perfect for formal events or power dressing.', 89.99, 50, 2, 'S,M,L', 'Black', 'Nwomendress1.avif,Nwomendress2.avif,Nwomendress3.avif', '2025-02-18 19:28:26', 5, 0),
(20, 'Elegant Knit Sweater Dress', 'A cozy yet elegant knit sweater dress featuring a relaxed fit, ribbed cuffs, and a flattering silhouette. Perfect for chilly days with boots or sneakers.', 74.99, 33, 2, 'S,M,L', 'Beige', 'womensweater1.avif,womensweater2.avif,womensweater3.avif', '2025-02-18 19:28:26', 5, 0),
(21, 'Sky Blue Sleeveless Dress', 'A stunning sleeveless midi dress with a tailored fit and elegant flowing fabric. Designed with a structured silhouette and soft pastel hue, perfect for both formal events and casual outings.', 119.99, 10, 2, 'S,M,L', 'Sky Blue', 'bluedress1.avif,bluedress2.avif,bluedress3.avif', '2025-02-18 19:28:26', 5, 0),
(22, 'Classic Boys\' Polo Shirt', 'A timeless polo shirt made from breathable cotton fabric, featuring a stylish embroidered logo and buttoned collar. Perfect for both casual and formal occasions.', 35.00, 33, 3, 'S,M,L', 'White', 'boypolo1.avif,boypolo2.jpg,boypolo3.jpg', '2025-02-18 19:49:56', 5, 0),
(23, 'Boys\' Insulated Puffer Vest', 'Keep your child warm in this stylish insulated puffer vest, designed with lightweight padding and a zip-up front for easy layering.', 49.99, 20, 3, 'S,M,L', 'White & Black', 'boypuffer1.jpg,boypuffer2.avif,boypuffer3.avif', '2025-02-18 19:49:56', 5, 0),
(24, 'Cozy Girls\' Knitted Sweater', 'A soft and warm knitted sweater with a relaxed fit, featuring ribbed cuffs and a stylish textured design. Perfect for chilly days.', 34.99, 30, 3, 'S,M,L', 'Cream', 'girlsweater1.avif,girlsweater2.avif', '2025-02-18 19:49:56', 5, 0),
(25, 'Elegant Girls\' Floral Dress', 'A charming floral dress with a soft, breathable fabric. Designed with a flattering A-line silhouette and delicate ruffle details for a stylish, playful look.', 39.99, 22, 3, 'S,M,L', 'Pink & White', 'girldress1.avif,girldress2.avif', '2025-02-18 19:49:56', 5, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `image_id` int NOT NULL,
  `product_id` int NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `is_primary` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`image_id`, `product_id`, `image_path`, `is_primary`, `created_at`) VALUES
(1, 2, 'menpolo3.avif', 0, '2025-03-14 13:15:11'),
(2, 2, 'menpolo2.avif', 0, '2025-03-14 13:15:11'),
(3, 2, 'menpolo1.avif', 1, '2025-03-14 13:15:11'),
(4, 3, 'menpuffer3.webp', 0, '2025-03-14 13:15:11'),
(5, 3, 'menpuffer2.webp', 0, '2025-03-14 13:15:11'),
(6, 3, 'menpuffer1.avif', 1, '2025-03-14 13:15:11'),
(7, 4, 'menhood3.avif', 0, '2025-03-14 13:15:11'),
(8, 4, 'menhood2.avif', 0, '2025-03-14 13:15:11'),
(9, 4, 'menhood1.avif', 1, '2025-03-14 13:15:11'),
(10, 5, 'mengym3.avif', 0, '2025-03-14 13:15:11'),
(11, 5, 'mengym2.webp', 0, '2025-03-14 13:15:11'),
(12, 5, 'mengym1.avif', 1, '2025-03-14 13:15:11'),
(13, 6, 'menpants3.avif', 0, '2025-03-14 13:15:11'),
(14, 6, 'menpants2.avif', 0, '2025-03-14 13:15:11'),
(15, 6, 'menpants1.avif', 1, '2025-03-14 13:15:11'),
(16, 7, 'bwomenwatch2.webp', 0, '2025-03-14 13:15:11'),
(17, 7, 'bwomenwatch1.avif', 1, '2025-03-14 13:15:11'),
(18, 8, 'cmenwatch2.avif', 0, '2025-03-14 13:15:11'),
(19, 8, 'cmenwatch1.avif', 1, '2025-03-14 13:15:11'),
(20, 9, 'goyardblue2.webp', 0, '2025-03-14 13:15:11'),
(21, 9, 'goyardblue1.webp', 1, '2025-03-14 13:15:11'),
(22, 10, 'goyardgreen2.webp', 0, '2025-03-14 13:15:11'),
(23, 10, 'goyardgreen1.webp', 1, '2025-03-14 13:15:11'),
(24, 11, 'backpack2.jpg', 0, '2025-03-14 13:15:11'),
(25, 11, 'backpack.avif', 1, '2025-03-14 13:15:11'),
(26, 12, 'babies1.avif', 1, '2025-03-14 13:15:11'),
(27, 13, 'babies2.avif', 1, '2025-03-14 13:15:11'),
(28, 14, 'babies3.avif', 1, '2025-03-14 13:15:11'),
(29, 15, 'babies4.avif', 1, '2025-03-14 13:15:11'),
(30, 16, 'babies6.avif', 1, '2025-03-14 13:15:11'),
(31, 17, 'bluepants3.avif', 0, '2025-03-14 13:15:11'),
(32, 17, 'bluepants2.webp', 0, '2025-03-14 13:15:11'),
(33, 17, 'bluepants1.avif', 1, '2025-03-14 13:15:11'),
(34, 18, 'womenmonclear3.avif', 0, '2025-03-14 13:15:11'),
(35, 18, 'womenmonclear2.avif', 0, '2025-03-14 13:15:11'),
(36, 18, 'womenmonclear1.avif', 1, '2025-03-14 13:15:11'),
(37, 19, 'Nwomendress3.avif', 0, '2025-03-14 13:15:11'),
(38, 19, 'Nwomendress2.avif', 0, '2025-03-14 13:15:11'),
(39, 19, 'Nwomendress1.avif', 1, '2025-03-14 13:15:11'),
(40, 20, 'womensweater3.avif', 0, '2025-03-14 13:15:11'),
(41, 20, 'womensweater2.avif', 0, '2025-03-14 13:15:11'),
(42, 20, 'womensweater1.avif', 1, '2025-03-14 13:15:11'),
(43, 21, 'bluedress3.avif', 0, '2025-03-14 13:15:11'),
(44, 21, 'bluedress2.avif', 0, '2025-03-14 13:15:11'),
(45, 21, 'bluedress1.avif', 1, '2025-03-14 13:15:11'),
(46, 22, 'boypolo3.jpg', 0, '2025-03-14 13:15:11'),
(47, 22, 'boypolo2.jpg', 0, '2025-03-14 13:15:11'),
(48, 22, 'boypolo1.avif', 1, '2025-03-14 13:15:11'),
(49, 23, 'boypuffer3.avif', 0, '2025-03-14 13:15:11'),
(50, 23, 'boypuffer2.avif', 0, '2025-03-14 13:15:11'),
(51, 23, 'boypuffer1.jpg', 1, '2025-03-14 13:15:11'),
(52, 24, 'girlsweater2.avif', 0, '2025-03-14 13:15:11'),
(53, 24, 'girlsweater1.avif', 1, '2025-03-14 13:15:11'),
(54, 25, 'girldress2.avif', 0, '2025-03-14 13:15:11'),
(55, 25, 'girldress1.avif', 1, '2025-03-14 13:15:11'),
(64, 2, 'menpolo3.avif', 0, '2025-03-14 13:20:50'),
(65, 2, 'menpolo2.avif', 0, '2025-03-14 13:20:50'),
(66, 2, 'menpolo1.avif', 1, '2025-03-14 13:20:50'),
(67, 3, 'menpuffer3.webp', 0, '2025-03-14 13:20:50'),
(68, 3, 'menpuffer2.webp', 0, '2025-03-14 13:20:50'),
(69, 3, 'menpuffer1.avif', 1, '2025-03-14 13:20:50'),
(70, 4, 'menhood3.avif', 0, '2025-03-14 13:20:50'),
(71, 4, 'menhood2.avif', 0, '2025-03-14 13:20:50'),
(72, 4, 'menhood1.avif', 1, '2025-03-14 13:20:50'),
(73, 5, 'mengym3.avif', 0, '2025-03-14 13:20:50'),
(74, 5, 'mengym2.webp', 0, '2025-03-14 13:20:50'),
(75, 5, 'mengym1.avif', 1, '2025-03-14 13:20:50'),
(76, 6, 'menpants3.avif', 0, '2025-03-14 13:20:50'),
(77, 6, 'menpants2.avif', 0, '2025-03-14 13:20:50'),
(78, 6, 'menpants1.avif', 1, '2025-03-14 13:20:50'),
(79, 7, 'bwomenwatch2.webp', 0, '2025-03-14 13:20:50'),
(80, 7, 'bwomenwatch1.avif', 1, '2025-03-14 13:20:50'),
(81, 8, 'cmenwatch2.avif', 0, '2025-03-14 13:20:50'),
(82, 8, 'cmenwatch1.avif', 1, '2025-03-14 13:20:50'),
(83, 9, 'goyardblue2.webp', 0, '2025-03-14 13:20:50'),
(84, 9, 'goyardblue1.webp', 1, '2025-03-14 13:20:50'),
(85, 10, 'goyardgreen2.webp', 0, '2025-03-14 13:20:50'),
(86, 10, 'goyardgreen1.webp', 1, '2025-03-14 13:20:50'),
(87, 11, 'backpack2.jpg', 0, '2025-03-14 13:20:50'),
(88, 11, 'backpack.avif', 1, '2025-03-14 13:20:50'),
(89, 12, 'babies1.avif', 1, '2025-03-14 13:20:50'),
(90, 13, 'babies2.avif', 1, '2025-03-14 13:20:50'),
(91, 14, 'babies3.avif', 1, '2025-03-14 13:20:50'),
(92, 15, 'babies4.avif', 1, '2025-03-14 13:20:50'),
(93, 16, 'babies6.avif', 1, '2025-03-14 13:20:50'),
(94, 17, 'bluepants3.avif', 0, '2025-03-14 13:20:50'),
(95, 17, 'bluepants2.webp', 0, '2025-03-14 13:20:50'),
(96, 17, 'bluepants1.avif', 1, '2025-03-14 13:20:50'),
(97, 18, 'womenmonclear3.avif', 0, '2025-03-14 13:20:50'),
(98, 18, 'womenmonclear2.avif', 0, '2025-03-14 13:20:50'),
(99, 18, 'womenmonclear1.avif', 1, '2025-03-14 13:20:50'),
(100, 19, 'Nwomendress3.avif', 0, '2025-03-14 13:20:50'),
(101, 19, 'Nwomendress2.avif', 0, '2025-03-14 13:20:50'),
(102, 19, 'Nwomendress1.avif', 1, '2025-03-14 13:20:50'),
(103, 20, 'womensweater3.avif', 0, '2025-03-14 13:20:50'),
(104, 20, 'womensweater2.avif', 0, '2025-03-14 13:20:50'),
(105, 20, 'womensweater1.avif', 1, '2025-03-14 13:20:50'),
(106, 21, 'bluedress3.avif', 0, '2025-03-14 13:20:50'),
(107, 21, 'bluedress2.avif', 0, '2025-03-14 13:20:50'),
(108, 21, 'bluedress1.avif', 1, '2025-03-14 13:20:50'),
(109, 22, 'boypolo3.jpg', 0, '2025-03-14 13:20:50'),
(110, 22, 'boypolo2.jpg', 0, '2025-03-14 13:20:50'),
(111, 22, 'boypolo1.avif', 1, '2025-03-14 13:20:50'),
(112, 23, 'boypuffer3.avif', 0, '2025-03-14 13:20:50'),
(113, 23, 'boypuffer2.avif', 0, '2025-03-14 13:20:50'),
(114, 23, 'boypuffer1.jpg', 1, '2025-03-14 13:20:50'),
(115, 24, 'girlsweater2.avif', 0, '2025-03-14 13:20:50'),
(116, 24, 'girlsweater1.avif', 1, '2025-03-14 13:20:50'),
(117, 25, 'girldress2.avif', 0, '2025-03-14 13:20:50'),
(118, 25, 'girldress1.avif', 1, '2025-03-14 13:20:50');

-- --------------------------------------------------------

--
-- Table structure for table `product_sizes`
--

CREATE TABLE `product_sizes` (
  `product_size_id` int NOT NULL,
  `product_id` int NOT NULL,
  `size` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `product_sizes`
--

INSERT INTO `product_sizes` (`product_size_id`, `product_id`, `size`, `stock`, `created_at`) VALUES
(1, 2, 'L', 9, '2025-03-14 13:20:37'),
(2, 2, 'M', 4, '2025-03-14 13:20:37'),
(3, 2, 'S', 10, '2025-03-14 13:20:37'),
(4, 3, 'L', 5, '2025-03-14 13:15:11'),
(5, 3, 'M', 0, '2025-03-14 13:15:11'),
(6, 4, 'L', 4, '2025-03-14 13:15:11'),
(7, 4, 'M', 6, '2025-03-14 13:15:11'),
(8, 5, 'L', 10, '2025-03-14 13:20:37'),
(9, 5, 'M', 8, '2025-03-14 13:20:37'),
(10, 5, 'S', 5, '2025-03-14 13:20:37'),
(11, 6, 'L', 8, '2025-03-14 13:20:37'),
(12, 6, 'M', 8, '2025-03-14 13:20:37'),
(13, 6, 'S', 8, '2025-03-14 13:20:37'),
(14, 7, 'One Size', 15, '2025-03-14 13:15:11'),
(15, 8, 'One Size', 20, '2025-03-14 13:15:11'),
(16, 9, 'One Size', 3, '2025-03-14 13:15:11'),
(17, 10, 'One Size', 8, '2025-03-14 13:15:11'),
(18, 11, 'One Size', 12, '2025-03-14 13:15:11'),
(19, 12, 'One Size', 8, '2025-03-14 13:15:11'),
(20, 13, 'One Size', 5, '2025-03-14 13:15:11'),
(21, 14, 'One Size', 6, '2025-03-14 13:15:11'),
(22, 15, 'One Size', 10, '2025-03-14 13:15:11'),
(23, 16, 'One Size', 10, '2025-03-14 13:15:11'),
(24, 17, 'L', 6, '2025-03-14 13:20:37'),
(25, 17, 'M', 7, '2025-03-14 13:20:37'),
(26, 17, 'S', 8, '2025-03-14 13:20:37'),
(27, 18, 'L', 1, '2025-03-14 13:20:37'),
(28, 18, 'M', 4, '2025-03-14 13:20:37'),
(29, 18, 'S', 5, '2025-03-14 13:20:37'),
(30, 19, 'L', 6, '2025-03-14 13:20:37'),
(31, 19, 'M', 5, '2025-03-14 13:20:37'),
(32, 19, 'S', 15, '2025-03-14 13:20:37'),
(33, 20, 'L', 6, '2025-03-14 13:20:37'),
(34, 20, 'M', 6, '2025-03-14 13:20:37'),
(35, 20, 'S', 6, '2025-03-14 13:20:37'),
(36, 21, 'L', 0, '2025-03-14 13:20:37'),
(37, 21, 'M', 0, '2025-03-14 13:20:37'),
(38, 21, 'S', 10, '2025-03-14 13:20:37'),
(39, 22, 'L', 6, '2025-03-14 13:20:37'),
(40, 22, 'M', 6, '2025-03-14 13:20:37'),
(41, 22, 'S', 6, '2025-03-14 13:20:37'),
(42, 23, 'L', 5, '2025-03-14 13:20:37'),
(43, 23, 'M', 5, '2025-03-14 13:20:37'),
(44, 23, 'S', 10, '2025-03-14 13:20:37'),
(45, 24, 'L', 6, '2025-03-14 13:20:37'),
(46, 24, 'M', 6, '2025-03-14 13:20:37'),
(47, 24, 'S', 6, '2025-03-14 13:20:37'),
(48, 25, 'L', 6, '2025-03-14 13:20:37'),
(49, 25, 'M', 6, '2025-03-14 13:20:37'),
(50, 25, 'S', 2, '2025-03-14 13:20:37'),
(51, 3, 'S', 0, '2025-03-22 15:58:19'),
(52, 4, 'S', 7, '2025-03-22 15:58:19');

-- --------------------------------------------------------

--
-- Stand-in structure for view `product_stock_by_size`
-- (See below for the actual view)
--
CREATE TABLE `product_stock_by_size` (
`category` varchar(100)
,`description` text
,`low_stock_threshold` int
,`name` varchar(100)
,`price` decimal(10,2)
,`product_id` int
,`size` varchar(20)
,`size_stock` int
,`total_stock` int
);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int NOT NULL,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `rating` int NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_520_ci,
  `review_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `user_id`, `product_id`, `rating`, `comment`, `review_date`) VALUES
(8, 52, 3, 1, 'trash', '2025-03-26 00:12:38'),
(9, 52, 4, 3, 'good', '2025-03-26 00:13:35'),
(10, 52, 5, 5, 'bad quality', '2025-03-26 00:14:00'),
(11, 33, 6, 5, 'good', '2025-03-26 00:59:52');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `password`, `address`, `created_at`) VALUES
(8, 'master', 'Admin', 'masterAdmin28@gmail.com', '$2y$10$I50abIBSYDjz5wa/vJCaVOHL13fLd6ffcPM0W.ezvlCnJPF5d3vMC', NULL, '2025-03-04 20:36:47'),
(10, 'Callum', 'Thomas', 'callum28@gmail.com', '$2y$10$vyPB88LLHG/pE8irxlnAOudsEPGovdIsxuTPDsrpPP2dz.f6AArMe', NULL, '2025-03-05 11:34:56'),
(11, 'Horatio', 'Al-Saadi', 'horatio.als@gmail.com', 'VJNGzpwV9pVxZwb', '', '2025-03-05 11:36:15'),
(12, 'ibrahim', 'khan', '', 'VJNGzpwV9pVxZwb', '', '2025-03-05 11:36:22'),
(13, 'Simarinder', 'Singh', 'simarindersinghwork@gmail.com', '$2y$10$J4UUJnSTJrjIyni/jFIPCukS0Vr/UlvJTQ5KzSbOsNxa4KKicxjCu', NULL, '2025-03-05 12:25:15'),
(14, 'bob', 'ross', 'bobross@gmail.com', '$2y$10$pZLPKbvsJYjXNNSOqR0YguuNZa/nsob/Yh2Lo6oL8TAFeHoUwf.FK', NULL, '2025-03-06 22:17:29'),
(16, 'master', 'admin', 'masteradmin8624@outlook.com', '$2y$10$LrsV2bTUQ2qOwS3FOVHLeuNem0kcdX1H.NKOHAOD4OUifduBoGeBW', NULL, '2025-03-08 22:05:39'),
(17, 'Callum', 'Thomas', 'callumthomas123456@gmail.com', '1234', '20 moss side', '2025-03-08 22:17:27'),
(18, 'm', 'i', 'mi2914@gmail.com', '$2y$10$7MOzpZYDu/QOHgnyW8DL8eV84ePEq1B99dzJn.4s5lXlq89D/AXU.', NULL, '2025-03-11 21:41:45'),
(19, 'i', 'm', 'im2914@gmail.com', '$2y$10$N6C0eTyRI.2mG4pG.Z00ruVkMpvNLCUYZ3OB6Hds06FxM3O7u/pxq', NULL, '2025-03-11 21:46:34'),
(20, 'm', 'i', 'ima2914@gmail.com', '$2y$10$anvCPBCviRhcieK6OsauFOJ7w.1Kls/bKtA0Cnb9RUlF/CGECxlyi', NULL, '2025-03-11 22:27:00'),
(22, 'callum', 'thomas', 'callum@gmail.com', '$2y$10$TiH.trscUMeHri1o0gyfwO8k/S/viIwPU9qCwJuZO.hpZMswSwacC', NULL, '2025-03-12 00:49:38'),
(23, 'kabir', 'sarham', 'Hanif00muhammad@icloud.com', '$2y$10$TbgYnl0ZIAqwYrs192d9eOPYeh8izWYx3VVU5CrMxpQooznfAkXFq', NULL, '2025-03-12 06:13:42'),
(24, 'Hussein', 'Ahmed', 'hussein200305@gmail.com', '$2y$10$v7blmtBp.P9ujgYciLpU5.IdHGgYuml5I0TFAGIDQg2uZx.tRzSp2', NULL, '2025-03-12 11:43:19'),
(25, 'ibrahim', 'khan ', 'ibrahim000@gmail.com', '87654321', '', '2025-03-12 11:44:02'),
(27, 'callum', 'Thomas', 'callumthomas1234567@gmail.com', '$2y$10$dvBNkU.LKGLOdN8NJqdO5OYMI9oCeWxZHgiDVeNM57Y9xPZQnDUtW', NULL, '2025-03-12 18:17:48'),
(28, 'rene', 'diddy', 'rene@gmail.com', '$2y$10$ZRpAqeAvNmQBCLBRN7jFpOy5BglZ5IC/u7wuPVT09pEm7nLz2/hzC', NULL, '2025-03-14 12:35:03'),
(29, 'sarham', 'jamil', 'sjs@gmail.com', '$2y$10$ngi5FR23.k573vGxCIxcxOqy8Y9vauKkNanqwYWxsAT6Wulz0gOea', NULL, '2025-03-16 18:14:24'),
(30, 'bruce ', 'wayne ', 'batman@gmail.com', '$2y$10$WC8ket6RqHdyzw.n0.fDieUGnPJlw2ttkCV7KIuP3cCa5BA/mTidy', NULL, '2025-03-16 18:18:42'),
(31, 'KAB', 'baby', 'fhgdg@gmail.com', '$2y$10$lsNHNyj8pXFjtTKzMObou.g.rjl4rHq6HLn8WrledNbBp/wJmBH6q', NULL, '2025-03-17 11:59:14'),
(32, 'Min Khant', 'Kyaw', 'min@gmail.com', '$2y$10$.nfEJYqns37gqtUivV8XsO9CLXvpEn/IOqAcHYxHtKM842QGrlNbO', NULL, '2025-03-18 18:14:46'),
(33, 'Mason ', 'Greenwood ', 'greenman@gmail.com', '$2y$10$8W.RTNKyoF3KqurG23oBUe2Ohp4E7JpiPAEnI82wZfKCbxdIoDMpi', NULL, '2025-03-18 21:02:32'),
(34, 'Tammy', 'Wong', 'tamwong@yahoo.com', '$2y$10$72cc2yTbJFNWeRW8Y8S3tuTwmdAfeypdXNKhN7MvpPjuA9rXVZ0dm', NULL, '2025-03-18 21:06:53'),
(35, 'callum', 'Thomas', 'callumthomas12345678@gmail.com', '$2y$10$k2krPGimCBMP2lEu.ycVXe.nJLVlhyB4v/a7RhhDwvHtrONSPbdRG', NULL, '2025-03-18 22:28:08'),
(37, 'Steve', 'Shortall', 'stephen.shortall@blackline.com', '$2y$10$2oJfLOBx6JbmcXi1TGUjDOx1RCN081W5Xg3igxZQbBLOfIt/.8KRe', NULL, '2025-03-19 12:39:20'),
(39, 'ibrahim', 'kh', 'ibrahimk@gmail.com', '$2y$10$XNgb3URre1nkmHbF1K1.EuBTB3Efso7MvSbDgF7UfUXPkf96ofISi', NULL, '2025-03-20 16:36:02'),
(41, 'test ', 'test ', 'test@gmail.com', '$2y$10$ZardiGfz5/KVBQNDe.zKgOIrS3Reeg8wP2nFlTWpXbO1w3vLjWQFi', NULL, '2025-03-21 16:19:43'),
(42, 'Horatio', 'Al-Saadi', 'raysspamhor@gmail.com', '$2y$10$L6vS0fF.DRM2Dq8a5jONeuq5w32jkJzclHHGzFA.5BLt5eXrgCP5W', NULL, '2025-03-22 15:08:51'),
(43, 'Godson', 'Biji', 'godsontest@gmail.com', '$2y$10$IaVGb9Iz7j57d.ub0xNXIukkHfHWK19xceb2bUT2hLgd/rtHTOc1G', NULL, '2025-03-22 16:16:10'),
(44, 'new ', 'user ', 'new@gmail.com', '$2y$10$WNYExR8D3eEXY8xCqAIfDe7qnrhTF7QIGUJe2IKGcH8U6Bl68fsVO', NULL, '2025-03-22 16:31:34'),
(46, 'jamil', 'sarham', 'sarham@gmail.com', '$2y$10$K5WOA3f6Xfq3gEvFTnVAD.PXkVm23jrOxa91brhsNwgI7fDVu7xji', NULL, '2025-03-23 16:18:14'),
(49, 'user', 'user', 'user25@gmail.com', '$2y$10$Cfdy3LThmeUBcSIjr3fpn.selez5OpwbqGyIYzRDIxbhtLNbwp52i', NULL, '2025-03-24 15:03:14'),
(50, 'Kabir ', 'Sarham ', 'kabirsarham8@gmail.com', '$2y$10$jodvlvwAty8PiKSb8M8u2.8Pw2.ZuzVczsR24dJEMziU3tCE8K2M2', NULL, '2025-03-25 21:55:17'),
(51, 'kabir ', 'jam', 'boy@gmail.com', '$2y$10$nSMm5Y56zDPtSCJqVQhkSOMqFyCEpQEEdtyyFYBJxSBmILADREJlO', '', '2025-03-25 23:25:51'),
(52, 'review', 'test', 'reviewtest@gmail.com', '$2y$10$v5895sysiedVash7zqD6AODvaE4qrNYXir07Hkp.MtWMvMPRFu0WK', NULL, '2025-03-26 00:11:41'),
(53, 'Godson', 'A', '1245367@gmail.com', '$2y$10$yqoIEAb0zhpoFGo2cV5Fbe.sp6dTOCbiAw3uw8GwRzjX.7WJMv3Hm', NULL, '2025-03-26 10:48:24'),
(56, 'Test', 'test', 'test_test@gmail.com', '$2y$10$UgApqZ5AxwEF18Lbfiy6r.BzJFU1XkKywm56FuuhNcqbXmOjqh4wW', NULL, '2025-03-26 11:14:16');

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `wishlist_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `wishlists`
--

INSERT INTO `wishlists` (`wishlist_id`, `user_id`, `created_at`) VALUES
(1, 23, '2025-03-12 06:38:12'),
(2, 24, '2025-03-12 11:43:43'),
(3, 25, '2025-03-12 11:46:04'),
(5, 28, '2025-03-14 12:35:59'),
(6, 29, '2025-03-16 18:14:50'),
(7, 30, '2025-03-16 18:19:14'),
(8, 31, '2025-03-18 17:45:46'),
(10, 32, '2025-03-18 18:15:08'),
(11, 33, '2025-03-18 21:03:14'),
(12, 35, '2025-03-18 22:29:39'),
(13, 37, '2025-03-19 12:40:49'),
(14, 11, '2025-03-19 12:46:08'),
(15, 39, '2025-03-20 23:13:10'),
(23, 41, '2025-03-21 16:25:24'),
(24, 43, '2025-03-22 16:16:49'),
(28, 46, '2025-03-23 16:40:40'),
(42, 50, '2025-03-25 21:55:47'),
(47, 44, '2025-03-25 23:34:51'),
(48, 52, '2025-03-26 00:14:20'),
(49, 42, '2025-03-26 09:38:30');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist_items`
--

CREATE TABLE `wishlist_items` (
  `wishlist_item_id` int NOT NULL,
  `wishlist_id` int NOT NULL,
  `product_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `wishlist_items`
--

INSERT INTO `wishlist_items` (`wishlist_item_id`, `wishlist_id`, `product_id`, `created_at`) VALUES
(2, 1, 4, '2025-03-12 06:39:33'),
(4, 1, 5, '2025-03-12 06:43:24'),
(5, 1, 2, '2025-03-12 06:43:26'),
(12, 5, 3, '2025-03-14 12:35:59'),
(15, 5, 18, '2025-03-16 17:34:42'),
(17, 5, 20, '2025-03-16 17:34:45'),
(19, 5, 2, '2025-03-16 17:38:05'),
(21, 5, 12, '2025-03-16 17:38:33'),
(22, 6, 3, '2025-03-16 18:14:50'),
(23, 6, 23, '2025-03-16 18:15:04'),
(24, 6, 7, '2025-03-16 18:15:10'),
(25, 6, 13, '2025-03-16 18:15:19'),
(35, 8, 3, '2025-03-18 17:50:59'),
(36, 8, 4, '2025-03-18 17:51:00'),
(40, 3, 19, '2025-03-18 18:06:53'),
(41, 7, 18, '2025-03-18 18:30:01'),
(42, 7, 19, '2025-03-18 18:30:02'),
(45, 11, 2, '2025-03-18 21:03:14'),
(46, 11, 5, '2025-03-18 21:03:19'),
(48, 8, 18, '2025-03-19 11:56:46'),
(49, 8, 2, '2025-03-19 12:04:58'),
(50, 2, 4, '2025-03-19 12:38:27'),
(51, 13, 2, '2025-03-19 12:40:49'),
(52, 7, 2, '2025-03-19 12:46:20'),
(53, 23, 18, '2025-03-21 16:25:30'),
(54, 23, 19, '2025-03-21 16:25:32'),
(55, 23, 2, '2025-03-21 18:24:37'),
(56, 23, 4, '2025-03-21 22:11:47'),
(58, 8, 7, '2025-03-21 22:50:36'),
(59, 23, 3, '2025-03-22 00:29:38'),
(60, 28, 4, '2025-03-23 16:40:40'),
(62, 28, 3, '2025-03-23 16:43:39'),
(63, 28, 2, '2025-03-23 16:43:40'),
(65, 28, 6, '2025-03-23 16:43:45'),
(66, 28, 19, '2025-03-23 16:43:54'),
(67, 28, 5, '2025-03-23 16:49:56'),
(71, 28, 11, '2025-03-23 18:25:03'),
(72, 28, 15, '2025-03-23 18:25:11'),
(73, 28, 18, '2025-03-23 18:25:16'),
(74, 28, 8, '2025-03-23 18:29:22'),
(82, 15, 2, '2025-03-23 21:46:59'),
(84, 42, 3, '2025-03-25 21:55:49'),
(86, 42, 5, '2025-03-25 21:55:54'),
(87, 42, 6, '2025-03-25 21:55:56'),
(88, 42, 17, '2025-03-25 21:56:19'),
(89, 10, 3, '2025-03-25 22:17:43'),
(90, 10, 5, '2025-03-25 22:17:47'),
(91, 15, 15, '2025-03-25 23:07:01'),
(92, 47, 3, '2025-03-25 23:34:51'),
(94, 47, 5, '2025-03-25 23:35:00'),
(95, 47, 2, '2025-03-25 23:35:02'),
(96, 47, 17, '2025-03-25 23:35:08'),
(97, 47, 18, '2025-03-25 23:35:09'),
(98, 47, 19, '2025-03-25 23:35:10'),
(101, 48, 3, '2025-03-26 00:33:11'),
(103, 48, 24, '2025-03-26 00:36:23'),
(104, 48, 8, '2025-03-26 00:38:27'),
(105, 48, 9, '2025-03-26 00:40:53'),
(106, 48, 13, '2025-03-26 00:40:58'),
(107, 48, 14, '2025-03-26 00:42:09'),
(108, 48, 15, '2025-03-26 00:42:10'),
(109, 48, 12, '2025-03-26 00:42:11'),
(110, 11, 23, '2025-03-26 00:56:53'),
(111, 11, 21, '2025-03-26 00:58:11'),
(112, 11, 3, '2025-03-26 00:58:33'),
(114, 2, 6, '2025-03-26 10:43:01'),
(115, 49, 2, '2025-03-26 10:54:01'),
(117, 49, 18, '2025-03-26 11:05:40'),
(118, 49, 3, '2025-03-26 11:06:37'),
(119, 49, 8, '2025-03-26 11:15:12'),
(120, 49, 7, '2025-03-26 11:15:12');

-- --------------------------------------------------------

--
-- Structure for view `product_stock_by_size`
--
DROP TABLE IF EXISTS `product_stock_by_size`;

CREATE ALGORITHM=UNDEFINED DEFINER=`cs2team2`@`localhost` SQL SECURITY DEFINER VIEW `product_stock_by_size`  AS SELECT `p`.`product_id` AS `product_id`, `p`.`name` AS `name`, `p`.`description` AS `description`, `p`.`price` AS `price`, `c`.`name` AS `category`, `ps`.`size` AS `size`, `ps`.`stock` AS `size_stock`, `p`.`stock` AS `total_stock`, `p`.`low_stock_threshold` AS `low_stock_threshold` FROM ((`products` `p` join `product_sizes` `ps` on((`p`.`product_id` = `ps`.`product_id`))) left join `categories` `c` on((`p`.`category_id` = `c`.`category_id`))) ORDER BY `p`.`product_id` ASC, (case `ps`.`size` when 'S' then 1 when 'M' then 2 when 'L' then 3 when 'One Size' then 4 else 5 end) ASC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`inquiry_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `order_returns`
--
ALTER TABLE `order_returns`
  ADD PRIMARY KEY (`return_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `order_item_id` (`order_item_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD PRIMARY KEY (`product_size_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `wishlist_items`
--
ALTER TABLE `wishlist_items`
  ADD PRIMARY KEY (`wishlist_item_id`),
  ADD KEY `wishlist_id` (`wishlist_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=182;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `inquiry_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `order_returns`
--
ALTER TABLE `order_returns`
  MODIFY `return_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `image_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT for table `product_sizes`
--
ALTER TABLE `product_sizes`
  MODIFY `product_size_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `wishlist_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `wishlist_items`
--
ALTER TABLE `wishlist_items`
  MODIFY `wishlist_item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`cart_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_returns`
--
ALTER TABLE `order_returns`
  ADD CONSTRAINT `order_returns_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_returns_ibfk_2` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`order_item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_returns_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD CONSTRAINT `product_sizes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wishlist_items`
--
ALTER TABLE `wishlist_items`
  ADD CONSTRAINT `wishlist_items_ibfk_1` FOREIGN KEY (`wishlist_id`) REFERENCES `wishlists` (`wishlist_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `wishlist_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
