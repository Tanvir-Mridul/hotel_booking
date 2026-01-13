-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 13, 2026 at 09:17 PM
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
-- Database: `hotel_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_commissions`
--

CREATE TABLE `admin_commissions` (
  `id` int(11) NOT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `commission` decimal(10,2) DEFAULT NULL,
  `owner_get` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_commissions`
--

INSERT INTO `admin_commissions` (`id`, `payment_id`, `user_id`, `owner_id`, `amount`, `commission`, `owner_get`, `created_at`) VALUES
(9, 15, 1, 9, 4000.00, 400.00, 3600.00, '2026-01-13 19:56:38'),
(10, 16, 1, 9, 5000.00, 500.00, 4500.00, '2026-01-13 19:59:48');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `owner_id` int(11) NOT NULL,
  `hotel_name` varchar(200) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Confirmed',
  `hotel_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `owner_id`, `hotel_name`, `location`, `price`, `booking_date`, `status`, `hotel_id`) VALUES
(77, 1, 9, 'Hotel Le meridian', 'Cox\'s Bazar', 4000, '2026-01-14', 'confirmed', 15),
(78, 1, 9, 'Ramadan Hotel', 'Cox\'s Bazar', 5000, '2026-01-14', 'confirmed', 12);

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `sender_role` enum('user','owner','admin') NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `receiver_role` enum('user','owner','admin') NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `sender_id`, `sender_role`, `receiver_id`, `receiver_role`, `message`, `is_read`, `created_at`) VALUES
(1, 9, 'owner', 1, 'user', 'hi', 1, '2026-01-11 17:30:11'),
(2, 9, 'owner', 1, 'user', 'hi', 1, '2026-01-11 17:33:55'),
(3, 10, 'user', 9, 'owner', 'hi', 1, '2026-01-11 17:40:34'),
(4, 9, 'owner', 10, 'user', 'hi', 1, '2026-01-11 17:40:50'),
(5, 10, 'user', 9, 'owner', 'ami apanr hotel nichi', 1, '2026-01-11 17:41:19'),
(6, 10, 'user', 9, 'owner', 'ki', 1, '2026-01-11 17:48:00'),
(7, 9, 'owner', 10, 'user', 'hi', 1, '2026-01-11 17:50:32'),
(8, 10, 'user', 9, 'owner', 'hi', 1, '2026-01-11 17:55:43'),
(9, 9, 'owner', 10, 'user', 'hi', 1, '2026-01-11 18:32:33'),
(10, 10, 'user', 9, 'owner', 'hello', 1, '2026-01-11 18:32:57'),
(11, 10, 'user', 9, 'owner', 'hello', 1, '2026-01-11 18:33:08'),
(12, 10, 'user', 9, 'owner', 'hi', 1, '2026-01-11 18:34:08'),
(13, 9, 'owner', 10, 'user', 'hello', 1, '2026-01-11 18:38:31'),
(14, 10, 'user', 9, 'owner', 'hi', 1, '2026-01-11 18:38:51');

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `id` int(11) NOT NULL,
  `hotel_name` varchar(200) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','off','rejected') DEFAULT 'pending',
  `owner_id` int(11) DEFAULT NULL,
  `rooms` int(11) DEFAULT 1,
  `capacity` int(11) DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`id`, `hotel_name`, `location`, `price`, `description`, `image`, `status`, `owner_id`, `rooms`, `capacity`) VALUES
(4, 'Taj hotel', 'Dhaka', 3000, '', '1766507370_istockphoto-173587041-612x612.jpg', 'off', 2, 1, 2),
(5, 'Hotel Le Meridien', 'Dhaka', 5000, 'Hotel Le Meridien', '1766507722_hotel_Le_meridian.jpg', 'off', 2, 1, 2),
(6, 'Hotel Sea Crown', 'Cox\'s bazar', 3000, 'Hotel Sea Crown', '1766508216_sea_crown.jpg', 'off', 2, 1, 2),
(7, 'Hotel Sea Moon', 'Cox\'s bazar', 4000, 'Hotel Sea Moon', '1766508248_sea moon.jpg', 'off', 2, 1, 2),
(8, 'Hotel Sweet Plaza', 'Chittagong', 2500, 'Hotel Sweet Plaza', '1766509884_istockphoto-173587041-612x612.jpg', 'off', 2, 1, 2),
(9, 'Hotel Sams Plaza', 'Cox\'s bazar', 2000, 'Hotel Sams Plaza', '1766510479_istockphoto-173587041-612x612.jpg', 'off', 2, 1, 2),
(10, 'Hotel Sea Moon', 'Cox\'s bazar', 70000, 'Hotel Sea Moon', '1766512258_istockphoto-173587041-612x612.jpg', 'off', 2, 1, 2),
(11, 'Hotel Sampan', 'Sylhet', 2000, 'Hotel Sampan', '1767030698_istockphoto-173587041-612x612.jpg', 'off', 7, 1, 2),
(12, 'Ramadan Hotel', 'Cox\'s Bazar', 5000, 'Ramadan Hotel', '1767432249_1766508216_sea_crown.jpg', 'approved', 9, 1, 2),
(13, 'Hotel Light House', 'Dhaka', 4000, 'Hotel Light House', '1767446444_1766507722_hotel_Le_meridian.jpg', 'approved', 9, 1, 2),
(15, 'Hotel Le meridian', 'Cox\'s Bazar', 4000, '', '1768160401_1766508216_sea_crown.jpg', 'approved', 9, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `receiver_role` enum('admin','owner','user') NOT NULL,
  `message` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT '#',
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `receiver_id`, `receiver_role`, `message`, `link`, `status`, `created_at`) VALUES
(1, 9, 'owner', '📅 New booking request for \"Hotel Light House\" from Jidni', '/hotel_booking/owner/manage_bookings.php', 'read', '2026-01-11 19:10:56'),
(2, 5, 'admin', '📅 New booking created for \"Hotel Light House\"', '/hotel_booking/admin/dashboard.php', 'unread', '2026-01-11 19:10:56'),
(4, 5, 'admin', '✅ Booking #56 confirmed by owner for \"Hotel Light House\"', '/hotel_booking/admin/dashboard.php', 'unread', '2026-01-11 19:12:02'),
(6, 7, 'owner', '⚠️ Your subscription has been deactivated. Your hotels are now offline.', '/hotel_booking/owner/subscription.php', 'unread', '2026-01-11 19:33:21'),
(22, 5, 'admin', '💳 New subscription request from Robiul - Package ID: 2', '/hotel_booking/admin/manage_subscriptions.php', 'unread', '2026-01-11 19:35:01'),
(23, 5, 'admin', '💳 New subscription request from Robiul - Package ID: 2', '/hotel_booking/admin/manage_subscriptions.php', 'unread', '2026-01-11 19:37:39'),
(24, 9, 'owner', '✅ Your subscription has been approved! Premium features activated.', '/hotel_booking/owner/dashboard.php', 'unread', '2026-01-11 19:38:06'),
(25, 5, 'admin', '🏨 New flat uploaded by Robiul - \"Hotel Le meridian\"', '/hotel_booking/admin/hotels.php', 'unread', '2026-01-11 19:40:01'),
(26, 9, 'owner', '⚠️ Your subscription has been deactivated. Your hotels are now offline.', '/hotel_booking/owner/subscription.php', 'read', '2026-01-11 19:46:24'),
(27, 5, 'admin', '💳 New subscription request from Robiul - 3 Months (৳2500.00)', '/hotel_booking/admin/manage_subscriptions.php', 'unread', '2026-01-11 19:46:50'),
(28, 9, 'owner', '✅ Subscription payment successful! Waiting for admin approval.', '/hotel_booking/owner/subscription.php', 'unread', '2026-01-11 19:46:50'),
(29, 9, 'owner', '✅ Your subscription has been approved! Premium features activated.', '/hotel_booking/owner/dashboard.php', 'unread', '2026-01-11 19:50:43'),
(30, 9, 'owner', '⚠️ Your subscription has been deactivated. Your hotels are now offline.', '/hotel_booking/owner/subscription.php', 'unread', '2026-01-11 19:50:43'),
(31, 5, 'admin', '💳 New subscription request from Robiul - 1 Month (৳1000.00)', '/hotel_booking/admin/manage_subscriptions.php', 'unread', '2026-01-11 19:51:08'),
(32, 9, 'owner', '✅ Subscription payment successful! Waiting for admin approval.', '/hotel_booking/owner/subscription.php', 'unread', '2026-01-11 19:51:08'),
(33, 9, 'owner', '✅ Your subscription has been approved! Premium features activated.', '/hotel_booking/owner/dashboard.php', 'read', '2026-01-11 20:11:27'),
(34, 9, 'owner', '⚠️ Your subscription has been deactivated. Your hotels are now offline.', '/hotel_booking/owner/subscription.php', 'unread', '2026-01-11 20:14:24'),
(35, 9, 'owner', '✅ Your subscription has been reactivated. Hotels are now online.', '/hotel_booking/owner/dashboard.php', 'unread', '2026-01-11 20:14:25'),
(36, 9, 'owner', '📅 New booking request for \"Hotel Le meridian\" from Jidni', '/hotel_booking/owner/manage_bookings.php', 'read', '2026-01-11 20:14:37'),
(37, 5, 'admin', '📅 New booking created for \"Hotel Le meridian\"', '/hotel_booking/admin/dashboard.php', 'unread', '2026-01-11 20:14:37'),
(38, 10, 'user', '✅ Your booking for \"Hotel Le meridian\" has been confirmed by owner', '/hotel_booking/user/my_booking.php', 'read', '2026-01-11 20:14:56'),
(39, 5, 'admin', '✅ Booking #57 confirmed by owner for \"Hotel Le meridian\"', '/hotel_booking/admin/dashboard.php', 'unread', '2026-01-11 20:14:56'),
(40, 9, 'owner', '❌ Booking cancelled for \"Hotel Le meridian\" by Jidni', '/hotel_booking/owner/manage_bookings.php', 'read', '2026-01-11 20:15:13'),
(41, 9, 'owner', '📅 New booking request for \"Hotel Le meridian\" from Jidni', '/hotel_booking/owner/manage_bookings.php', 'read', '2026-01-11 20:18:37'),
(42, 5, 'admin', '📅 New booking created for \"Hotel Le meridian\"', '/hotel_booking/admin/dashboard.php', 'unread', '2026-01-11 20:18:37'),
(43, 10, 'user', '❌ Your booking for \"Hotel Le meridian\" was cancelled by owner', '/hotel_booking/user/my_booking.php', 'read', '2026-01-11 20:21:07'),
(44, 5, 'admin', '❌ Booking #58 cancelled by owner for \"Hotel Le meridian\"', '/hotel_booking/admin/dashboard.php', 'unread', '2026-01-11 20:21:07'),
(45, 9, 'owner', '📅 New booking request for \"Hotel Le meridian\" from Jidni', '/hotel_booking/owner/manage_bookings.php', 'read', '2026-01-11 20:21:20'),
(46, 5, 'admin', '📅 New booking created for \"Hotel Le meridian\"', '/hotel_booking/admin/dashboard.php', 'unread', '2026-01-11 20:21:20'),
(47, 9, 'owner', '❌ Booking cancelled for \"Hotel Le meridian\" by Jidni', '/hotel_booking/owner/manage_bookings.php', 'read', '2026-01-11 20:25:06'),
(48, 1, 'user', '❌ Your booking for \"Ramadan Hotel\" was cancelled by owner', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-11 20:31:04'),
(49, 5, 'admin', '❌ Booking #52 cancelled by owner for \"Ramadan Hotel\"', '/hotel_booking/admin/dashboard.php', 'unread', '2026-01-11 20:31:04'),
(50, 1, 'user', '❌ Your booking for \"Hotel Light House\" was cancelled by owner', '/hotel_booking/user/my_booking.php', 'read', '2026-01-11 20:31:08'),
(51, 5, 'admin', '❌ Booking #51 cancelled by owner for \"Hotel Light House\"', '/hotel_booking/admin/dashboard.php', 'unread', '2026-01-11 20:31:08'),
(52, 9, 'owner', '📅 New booking request for \"Hotel Le meridian\" from Jidni', '/hotel_booking/owner/manage_bookings.php', 'read', '2026-01-11 20:31:48'),
(53, 5, 'admin', '📅 New booking created for \"Hotel Le meridian\"', '/hotel_booking/admin/dashboard.php', 'unread', '2026-01-11 20:31:48'),
(54, 10, 'user', '✅ Your booking for \"Hotel Le meridian\" has been confirmed by owner', '/hotel_booking/user/my_booking.php', 'read', '2026-01-11 20:32:06'),
(55, 5, 'admin', '✅ Booking #60 confirmed by owner for \"Hotel Le meridian\"', '/hotel_booking/admin/dashboard.php', 'unread', '2026-01-11 20:32:06'),
(56, 5, 'admin', '💰 New Payment Received!\nUser: rahat\nHotel: Hotel Le meridian\nAmount: ৳4000.00\nPlease pay to owner.', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-13 16:58:46'),
(57, 9, 'owner', '✅ Payment Received!\nUser rahat paid for \"Hotel Le meridian\" (৳4000.00).\nPayment will be released after admin verification.', '/hotel_booking/owner/dashboard.php', 'read', '2026-01-13 16:58:46'),
(58, 1, 'user', '✅ Payment Successful!\nYour booking for \"Hotel Le meridian\" is confirmed.\nAmount: ৳4000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-13 16:58:46'),
(59, 9, 'owner', '💰 Payment Released!\nAmount ৳4000.00 for \"Hotel Le meridian\" has been paid to you.', '/hotel_booking/owner/dashboard.php', 'unread', '2026-01-13 17:07:26'),
(60, 5, 'admin', '💰 New Payment Received!\nUser: rahat\nHotel: Hotel Light House\nAmount: ৳4000.00\nPlease pay to owner.', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-13 17:08:12'),
(61, 9, 'owner', '✅ Payment Received!\nUser rahat paid for \"Hotel Light House\" (৳4000.00).\nPayment will be released after admin verification.', '/hotel_booking/owner/dashboard.php', 'unread', '2026-01-13 17:08:12'),
(62, 1, 'user', '✅ Payment Successful!\nYour booking for \"Hotel Light House\" is confirmed.\nAmount: ৳4000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-13 17:08:12'),
(63, 9, 'owner', '💰 Payment Released!\nAmount ৳4000.00 for \"Hotel Light House\" has been paid to you.', '/hotel_booking/owner/dashboard.php', 'unread', '2026-01-13 17:09:42'),
(64, 9, 'owner', '💰 Payment Received!\nAmount ৳4000.00 for \"Hotel Light House\" has been paid to you.\nBalance updated in your account.', '/hotel_booking/owner/finance.php', 'unread', '2026-01-13 17:38:37'),
(65, 9, 'owner', '💰 Payment Received!\nAmount ৳4000.00 for \"Hotel Le meridian\" has been paid to you.\nBalance updated in your account.', '/hotel_booking/owner/finance.php', 'unread', '2026-01-13 17:38:43'),
(66, 5, 'admin', '💰 New Payment Received!\nUser: rahat\nHotel: Ramadan Hotel\nAmount: ৳5000.00\nPlease pay to owner.', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-13 17:40:52'),
(67, 9, 'owner', '✅ Payment Received!\nUser rahat paid for \"Ramadan Hotel\" (৳5000.00).\nPayment will be released after admin verification.', '/hotel_booking/owner/dashboard.php', 'unread', '2026-01-13 17:40:52'),
(68, 1, 'user', '✅ Payment Successful!\nYour booking for \"Ramadan Hotel\" is confirmed.\nAmount: ৳5000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-13 17:40:52'),
(69, 9, 'owner', '💰 Payment Received!\nAmount ৳5000.00 for \"Ramadan Hotel\" has been paid to you.\nBalance updated in your account.', '/hotel_booking/owner/finance.php', 'read', '2026-01-13 17:42:02'),
(70, 5, 'admin', '💰 New Payment Received!\nUser: rahat\nHotel: Hotel Le meridian\nAmount: ৳4000.00\nPlease pay to owner.', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-13 17:46:48'),
(71, 9, 'owner', '✅ Payment Received!\nUser rahat paid for \"Hotel Le meridian\" (৳4000.00).\nPayment will be released after admin verification.', '/hotel_booking/owner/dashboard.php', 'unread', '2026-01-13 17:46:48'),
(72, 1, 'user', '✅ Payment Successful!\nYour booking for \"Hotel Le meridian\" is confirmed.\nAmount: ৳4000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-13 17:46:48'),
(73, 9, 'owner', '💰 Payment Received!\nAmount ৳4000.00 for \"Hotel Le meridian\" has been paid to you.\nBalance updated in your account.', '/hotel_booking/owner/finance.php', 'unread', '2026-01-13 17:49:24'),
(74, 5, 'admin', '💰 New Payment Received!\nUser: rahat\nHotel: Ramadan Hotel\nAmount: ৳5000.00\nPlease pay to owner.', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-13 18:04:17'),
(75, 9, 'owner', '✅ Payment Received!\nUser rahat paid for \"Ramadan Hotel\" (৳5000.00).\nPayment will be released after admin verification.', '/hotel_booking/owner/dashboard.php', 'unread', '2026-01-13 18:04:17'),
(76, 1, 'user', '✅ Payment Successful!\nYour booking for \"Ramadan Hotel\" is confirmed.\nAmount: ৳5000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-13 18:04:17'),
(77, 5, 'admin', '💰 New Payment Received!\nUser: rahat\nHotel: Hotel Le meridian\nAmount: ৳4000.00\nPlease pay to owner.', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-13 18:09:45'),
(78, 9, 'owner', '✅ Payment Received!\nUser rahat paid for \"Hotel Le meridian\" (৳4000.00).\nPayment will be released after admin verification.', '/hotel_booking/owner/dashboard.php', 'unread', '2026-01-13 18:09:46'),
(79, 1, 'user', '✅ Payment Successful!\nYour booking for \"Hotel Le meridian\" is confirmed.\nAmount: ৳4000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-13 18:09:46'),
(80, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳4000.00\nCommission: ৳400\nOwner gets: ৳3600', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-13 18:54:03'),
(81, 9, 'owner', '✅ Payment Received!\nHotel: Hotel Le meridian\nAmount: ৳4000.00 (You get: ৳3600)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-13 18:54:03'),
(82, 1, 'user', '✅ Payment Successful!\nReceipt ID: RECEIPT_20260113_1241\nAmount: ৳4000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-13 18:54:03'),
(83, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳5000.00\nCommission: ৳500\nOwner gets: ৳4500', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-13 18:57:01'),
(84, 9, 'owner', '✅ Payment Received!\nHotel: Ramadan Hotel\nAmount: ৳5000.00 (You get: ৳4500)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-13 18:57:01'),
(85, 1, 'user', '✅ Payment Successful!\nReceipt ID: RECEIPT_20260113_7151\nAmount: ৳5000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-13 18:57:01'),
(86, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳4000.00\nCommission: ৳400\nOwner gets: ৳3600', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-13 19:01:38'),
(87, 9, 'owner', '✅ Payment Received!\nHotel: Hotel Light House\nAmount: ৳4000.00 (You get: ৳3600)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-13 19:01:38'),
(88, 1, 'user', '✅ Payment Successful!\nReceipt ID: RECEIPT_20260113_1459\nAmount: ৳4000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-13 19:01:38'),
(89, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳5000.00\nCommission: ৳500\nOwner gets: ৳4500', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-13 19:13:20'),
(90, 9, 'owner', '✅ Payment Received!\nHotel: Ramadan Hotel\nAmount: ৳5000.00 (You get: ৳4500)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-13 19:13:20'),
(91, 1, 'user', '✅ Payment Successful!\nReceipt ID: RECEIPT_20260113_9379\nAmount: ৳5000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-13 19:13:20'),
(92, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳4000.00\nCommission: ৳400\nOwner gets: ৳3600', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-13 19:16:16'),
(93, 9, 'owner', '✅ Payment Received!\nHotel: Hotel Light House\nAmount: ৳4000.00 (You get: ৳3600)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-13 19:16:16'),
(94, 1, 'user', '✅ Payment Successful!\nReceipt ID: RECEIPT_20260113_7053\nAmount: ৳4000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-13 19:16:16'),
(95, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳4000.00\nCommission: ৳400\nOwner gets: ৳3600', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-13 19:30:57'),
(96, 9, 'owner', '✅ Payment Received!\nHotel: Hotel Light House\nAmount: ৳4000.00 (You get: ৳3600)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-13 19:30:57'),
(97, 1, 'user', '✅ Payment Successful!\nReceipt ID: RECEIPT_20260113_8796\nAmount: ৳4000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-13 19:30:57'),
(98, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳4000.00\nCommission: ৳400\nOwner gets: ৳3600', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-13 19:37:16'),
(99, 9, 'owner', '✅ Payment Received!\nHotel: Hotel Light House\nAmount: ৳4000.00 (You get: ৳3600)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-13 19:37:16'),
(100, 1, 'user', '✅ Payment Successful!\nReceipt ID: RECEIPT_20260113_4751\nAmount: ৳4000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-13 19:37:16'),
(101, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳4000.00\nCommission: ৳400\nOwner gets: ৳3600', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-13 19:44:21'),
(102, 9, 'owner', '✅ Payment Received!\nHotel: Hotel Light House\nAmount: ৳4000.00 (You get: ৳3600)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-13 19:44:21'),
(103, 1, 'user', '✅ Payment Successful!\nReceipt ID: RECEIPT_20260113_5885\nAmount: ৳4000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-13 19:44:21'),
(104, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳4000.00\nCommission: ৳400\nOwner gets: ৳3600', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-13 19:56:38'),
(105, 9, 'owner', '✅ Payment Received!\nHotel: Hotel Le meridian\nAmount: ৳4000.00 (You get: ৳3600)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-13 19:56:38'),
(106, 1, 'user', '✅ Payment Successful!\nReceipt ID: RECEIPT_20260113_7428\nAmount: ৳4000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-13 19:56:38'),
(107, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳5000.00\nCommission: ৳500\nOwner gets: ৳4500', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-13 19:59:48'),
(108, 9, 'owner', '✅ Payment Received!\nHotel: Ramadan Hotel\nAmount: ৳5000.00 (You get: ৳4500)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-13 19:59:48'),
(109, 1, 'user', '✅ Payment Successful!\nReceipt ID: RECEIPT_20260113_9077\nAmount: ৳5000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-13 19:59:48');

-- --------------------------------------------------------

--
-- Table structure for table `owner_finance`
--

CREATE TABLE `owner_finance` (
  `owner_id` int(11) NOT NULL,
  `total_earned` decimal(10,2) DEFAULT 0.00,
  `total_paid` decimal(10,2) DEFAULT 0.00,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `owner_subscriptions`
--

CREATE TABLE `owner_subscriptions` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `tran_id` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('pending','approved','expired') DEFAULT 'pending',
  `payment_status` enum('pending','success','failed','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `owner_subscriptions`
--

INSERT INTO `owner_subscriptions` (`id`, `owner_id`, `package_id`, `tran_id`, `amount`, `start_date`, `end_date`, `status`, `payment_status`) VALUES
(20, 9, 2, 'SUB_6963fbfb5764e', 2500.00, '2026-01-12 00:00:00', '2026-04-12', 'expired', 'pending'),
(21, 9, 2, 'SUB_6963fe2230869', 2500.00, '2026-01-12 00:00:00', '2026-04-12', 'expired', 'pending'),
(22, 9, 1, 'SUB_6963ff2603180', 1000.00, '2026-01-12 00:00:00', '2026-02-11', 'approved', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `owner_withdrawals`
--

CREATE TABLE `owner_withdrawals` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','paid','rejected') DEFAULT 'pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `type` enum('subscription','booking') DEFAULT NULL,
  `payer_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `tran_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','success','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `duration_days` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `name`, `duration_days`, `price`) VALUES
(1, '1 Month', 30, 1000),
(2, '3 Months', 90, 2500),
(3, '1 Year', 365, 8000);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','owner','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'rahat', 'rahat@gmail.com', '$2y$10$/b7ZF55mWU.wnAHUBjZSiObScptkteT/VkCcKybKIZz9PSNDyjZqS', 'user', '2025-12-28 12:31:00'),
(2, 'Tanvir', 'owner@gmail.com', '$2y$10$fuu4L82juLKCmxjhj5td0OfOdhq4vp8cz.szT4UKRTbvNytqVoZ7.', 'owner', '2025-12-28 12:31:00'),
(5, 'admin', 'admin@gmail.com', 'password', 'admin', '2025-12-28 12:31:00'),
(6, '', 'owner1@gmail.com', '$2y$10$o5owVGOgCYQ3xz8NkSThxuMyDHw/ly789Fvcg47N0Hh1yTGP68B7K', 'owner', '2025-12-28 12:31:00'),
(7, '', 'owner2@gmail.com', '$2y$10$nvg1310hmcB.NkoI.soFI.tcQC9t6sYTTMg9mv3ai8N.qL6veXWii', 'owner', '2025-12-28 19:08:41'),
(8, 'Arman', 'arman@gmail.com', '$2y$10$ZVJlC9wFCFHXmwGxmq.o.OwKs32ohQHW1BsRXrmKCw56ANNZLwTi.', 'owner', '2025-12-31 07:30:20'),
(9, 'Robiul', 'robiul@gmail.com', '$2y$10$w2my96a4eULq9uEs9DaqJuV9SeGqs6bET8xMK86wRiNeObQSjaS1e', 'owner', '2026-01-03 09:15:23'),
(10, 'Jidni', 'jidni@gmail.com', '$2y$10$VUciNquBe8TW30TqN9cX9eleoKa1XHvHEc0UhA8cAkbvl5cBWEI3O', 'user', '2026-01-11 17:35:24');

-- --------------------------------------------------------

--
-- Table structure for table `user_payments`
--

CREATE TABLE `user_payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `hotel_name` varchar(200) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `tran_id` varchar(100) DEFAULT NULL,
  `payment_status` enum('pending','success','failed','cancelled') DEFAULT 'pending',
  `admin_status` enum('pending','paid_to_owner') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `owner_paid_status` enum('pending','paid') DEFAULT 'pending',
  `owner_paid_date` timestamp NULL DEFAULT NULL,
  `commission` decimal(10,2) DEFAULT 0.00,
  `owner_amount` decimal(10,2) DEFAULT 0.00,
  `receipt_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_payments`
--

INSERT INTO `user_payments` (`id`, `user_id`, `owner_id`, `booking_id`, `hotel_name`, `amount`, `tran_id`, `payment_status`, `admin_status`, `created_at`, `owner_paid_status`, `owner_paid_date`, `commission`, `owner_amount`, `receipt_id`) VALUES
(15, 1, 9, 77, 'Hotel Le meridian', 4000.00, 'USERPAY_6966a36eb2812', 'success', 'pending', '2026-01-13 19:56:37', 'paid', '2026-01-13 20:01:07', 400.00, 3600.00, 'RECEIPT_20260113_7428'),
(16, 1, 9, 78, 'Ramadan Hotel', 5000.00, 'USERPAY_6966a42bbf0c2', 'success', 'pending', '2026-01-13 19:59:48', 'paid', '2026-01-13 20:00:32', 500.00, 4500.00, 'RECEIPT_20260113_9077');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_commissions`
--
ALTER TABLE `admin_commissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `owner_finance`
--
ALTER TABLE `owner_finance`
  ADD PRIMARY KEY (`owner_id`);

--
-- Indexes for table `owner_subscriptions`
--
ALTER TABLE `owner_subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `owner_withdrawals`
--
ALTER TABLE `owner_withdrawals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tran_id` (`tran_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_payments`
--
ALTER TABLE `user_payments`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_commissions`
--
ALTER TABLE `admin_commissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `owner_subscriptions`
--
ALTER TABLE `owner_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `owner_withdrawals`
--
ALTER TABLE `owner_withdrawals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_payments`
--
ALTER TABLE `user_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
