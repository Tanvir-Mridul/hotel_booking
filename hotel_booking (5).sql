-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 15, 2026 at 07:56 PM
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
(12, 18, 11, 9, 4000.00, 400.00, 3600.00, '2026-01-13 21:48:22'),
(13, 19, 1, 0, 2000.00, 200.00, 1800.00, '2026-01-15 17:13:46'),
(14, 20, 1, 0, 2000.00, 200.00, 1800.00, '2026-01-15 17:17:26'),
(15, 21, 1, 0, 1000.00, 100.00, 900.00, '2026-01-15 17:19:50'),
(16, 22, 1, 0, 1000.00, 100.00, 900.00, '2026-01-15 17:20:51');

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
  `rooms_count` int(11) DEFAULT 1,
  `guests` int(11) DEFAULT 1,
  `booking_date` date DEFAULT NULL,
  `check_in_date` date DEFAULT NULL,
  `check_out_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Confirmed',
  `hotel_id` int(11) DEFAULT NULL,
  `room_title` varchar(200) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `rooms_booked` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `owner_id`, `hotel_name`, `location`, `price`, `rooms_count`, `guests`, `booking_date`, `check_in_date`, `check_out_date`, `status`, `hotel_id`, `room_title`, `room_id`, `rooms_booked`) VALUES
(86, 1, 0, 'Adil Hotel', 'Cox\\\'s Bazar', 2000, 1, 2, NULL, '2026-01-16', '2026-01-17', 'confirmed', 20, 'Dulex Room', 1, 1),
(87, 1, 0, 'Adil Hotel', 'Cox\\\'s Bazar', 2000, 1, 2, NULL, '2026-01-15', '2026-01-16', 'confirmed', 20, 'Dulex Room', 1, 1),
(88, 1, 0, 'Adil Hotel', 'Cox\\\'s Bazar', 1000, 1, 1, NULL, '2026-01-23', '2026-01-24', 'confirmed', 20, 'gvsgs', 3, 1),
(89, 1, 0, 'Adil Hotel', 'Cox\\\'s Bazar', 1000, 1, 1, NULL, '2026-01-17', '2026-01-18', 'cancelled', 20, 'singlke', 2, 1);

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
  `title` varchar(200) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','off','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `owner_id` int(11) DEFAULT NULL,
  `rooms` int(11) DEFAULT 1,
  `capacity` int(11) DEFAULT 2,
  `booked_dates` text DEFAULT '[]',
  `capacity_per_room` int(11) DEFAULT 2,
  `total_rooms` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`id`, `hotel_name`, `title`, `location`, `price`, `description`, `image`, `status`, `created_at`, `owner_id`, `rooms`, `capacity`, `booked_dates`, `capacity_per_room`, `total_rooms`) VALUES
(20, 'Adil Hotel', NULL, 'Cox\\\'s Bazar', NULL, '', '1768494204_hotel_14.jpg', 'approved', '2026-01-15 16:21:16', 14, 1, 2, '[\"2026-01-20\"]', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `hotels_backup_2024`
--

CREATE TABLE `hotels_backup_2024` (
  `id` int(11) NOT NULL DEFAULT 0,
  `hotel_name` varchar(200) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','off','rejected') DEFAULT 'pending',
  `owner_id` int(11) DEFAULT NULL,
  `rooms` int(11) DEFAULT 1,
  `capacity` int(11) DEFAULT 2,
  `booked_dates` text DEFAULT '[]',
  `capacity_per_room` int(11) DEFAULT 2,
  `total_rooms` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(116, 5, 'admin', '💳 New subscription request from Robiul - 1 Month (৳1000.00)', '/hotel_booking/admin/manage_subscriptions.php', 'unread', '2026-01-13 21:46:48'),
(117, 9, 'owner', '✅ Subscription payment successful! Waiting for admin approval.', '/hotel_booking/owner/subscription.php', 'unread', '2026-01-13 21:46:48'),
(118, 9, 'owner', '✅ Your subscription has been approved! Premium features activated.', '/hotel_booking/owner/dashboard.php', 'unread', '2026-01-13 21:47:03'),
(119, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳4000.00\nCommission: ৳400\nOwner gets: ৳3600', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-13 21:48:22'),
(120, 9, 'owner', '✅ Payment Received!\nHotel: Hotel Le meridian\nAmount: ৳4000.00 (You get: ৳3600)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-13 21:48:22'),
(121, 11, 'user', '✅ Payment Successful!\nReceipt ID: RECEIPT_20260113_1541\nAmount: ৳4000.00', '/hotel_booking/user/my_booking.php', 'read', '2026-01-13 21:48:22'),
(122, 5, 'admin', '💳 New subscription request from Tanvir - 3 Months (৳2500.00)', '/hotel_booking/admin/manage_subscriptions.php', 'unread', '2026-01-13 21:53:55'),
(123, 2, 'owner', '✅ Subscription payment successful! Waiting for admin approval.', '/hotel_booking/owner/subscription.php', 'unread', '2026-01-13 21:53:55'),
(124, 2, 'owner', '✅ Your subscription (৳2500) has been approved! Premium features activated.', '/hotel_booking/owner/dashboard.php', 'unread', '2026-01-13 21:58:04'),
(125, 2, 'owner', '⚠️ Your subscription (৳2500) has been deactivated. Your hotels are now offline.', '/hotel_booking/owner/subscription.php', 'unread', '2026-01-13 21:58:39'),
(126, 2, 'owner', '✅ Your subscription (৳2500) has been reactivated. Hotels are now online.', '/hotel_booking/owner/dashboard.php', 'unread', '2026-01-13 21:58:43'),
(127, 5, 'admin', '🏨 New flat uploaded by Tanvir - \"Hotel Inani Long Bay\" at Cox\'\'s bazar (৳4000)', '/hotel_booking/admin/hotels.php', 'unread', '2026-01-15 13:52:19'),
(128, 2, 'owner', '📤 Your flat \"Hotel Inani Long Bay\" has been submitted for admin approval', '/hotel_booking/owner/dashboard.php', 'unread', '2026-01-15 13:52:19'),
(129, 2, 'owner', '✅ Your hotel \"Hotel Inani Long Bay\" has been approved and is now live!', '/hotel_booking/owner/dashboard.php', 'unread', '2026-01-15 13:52:34'),
(130, 5, 'admin', '🏨 New flat uploaded by Tanvir - \"Tanvir\" at Cox\'\'s bazar (৳1000)', '/hotel_booking/admin/hotels.php', 'unread', '2026-01-15 13:54:29'),
(131, 2, 'owner', '📤 Your flat \"Tanvir\" has been submitted for admin approval', '/hotel_booking/owner/dashboard.php', 'unread', '2026-01-15 13:54:29'),
(132, 2, 'owner', '✅ Your hotel \"Tanvir\" has been approved and is now live!', '/hotel_booking/owner/dashboard.php', 'unread', '2026-01-15 13:54:42'),
(133, 5, 'admin', '🏨 New flat uploaded by Tanvir - \"RIfa Ho\" at Cox\'\'s bazar (৳1000)', '/hotel_booking/admin/hotels.php', 'unread', '2026-01-15 14:02:16'),
(134, 2, 'owner', '📤 Your flat \"RIfa Ho\" has been submitted for admin approval', '/hotel_booking/owner/dashboard.php', 'unread', '2026-01-15 14:02:16'),
(135, 14, 'owner', '✅ Your hotel \"Adil Hotel\" has been approved and is now live!', '/hotel_booking/owner/dashboard.php', 'unread', '2026-01-15 16:22:44'),
(136, 5, 'admin', '🏨 New room uploaded by Adil - \"Dulex Room\" (৳2000/night)', '/hotel_booking/admin/hotels.php', 'unread', '2026-01-15 16:24:50'),
(137, 5, 'admin', '🏨 New room uploaded by Adil - \"singlke\" (৳1000/night)', '/hotel_booking/admin/hotels.php', 'unread', '2026-01-15 16:26:37'),
(138, 5, 'admin', '🏨 New room uploaded by Adil - \"gvsgs\" (৳1000/night)', '/hotel_booking/admin/hotels.php', 'unread', '2026-01-15 16:27:09'),
(139, 14, 'owner', '📅 New booking request for \"Dulex Room\" from rahat', '/hotel_booking/owner/manage_bookings.php', 'unread', '2026-01-15 17:01:42'),
(140, 1, 'user', '✅ Booking request sent for \"Dulex Room\". Please complete payment.', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-15 17:01:42'),
(141, 14, 'owner', '📅 New booking request for \"Dulex Room\" from rahat', '/hotel_booking/owner/manage_bookings.php', 'read', '2026-01-15 17:13:35'),
(142, 1, 'user', '✅ Booking request sent for \"Dulex Room\". Please complete payment.', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-15 17:13:35'),
(143, 5, 'admin', '💰 Payment + Commission!\nRoom: Dulex Room\nAmount: ৳2000.00\nCommission: ৳200\nOwner gets: ৳1800', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-15 17:13:46'),
(144, 0, 'owner', '✅ Payment Received!\nRoom: Dulex Room\nAmount: ৳2000.00 (You get: ৳1800)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-15 17:13:46'),
(145, 1, 'user', '✅ Payment Successful!\nReceipt ID: RECEIPT_20260115_7946\nRoom: Dulex Room\nAmount: ৳2000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-15 17:13:46'),
(146, 1, 'user', '✅ Payment Successful!\nReceipt: RECEIPT_20260115181725612\nAmount: ৳2000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-15 17:17:26'),
(147, 14, 'owner', '📅 New booking request for \"gvsgs\" from rahat', '/hotel_booking/owner/manage_bookings.php', 'unread', '2026-01-15 17:19:41'),
(148, 1, 'user', '✅ Booking request sent for \"gvsgs\". Please complete payment.', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-15 17:19:41'),
(149, 1, 'user', '✅ Payment Successful!\nReceipt: RECEIPT_20260115181950584\nAmount: ৳1000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-15 17:19:50'),
(150, 14, 'owner', '📅 New booking request for \"singlke\" from rahat', '/hotel_booking/owner/manage_bookings.php', 'unread', '2026-01-15 17:20:42'),
(151, 1, 'user', '✅ Booking request sent for \"singlke\". Please complete payment.', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-15 17:20:42'),
(152, 1, 'user', '✅ Payment Successful!\nReceipt: REC20260115182051998\nAmount: ৳1000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-15 17:20:51'),
(153, 1, 'user', 'Booking status updated for \'singlke\' to: Cancelled', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-15 18:27:42');

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
  `hotel_id` int(11) DEFAULT NULL,
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

INSERT INTO `owner_subscriptions` (`id`, `owner_id`, `hotel_id`, `package_id`, `tran_id`, `amount`, `start_date`, `end_date`, `status`, `payment_status`) VALUES
(23, 9, NULL, 1, 'SUB_6966bd3ed70d4', 1000.00, '2026-01-14 00:00:00', '2026-02-13', 'approved', 'pending'),
(24, 2, NULL, 2, 'SUB_6966beebf10be', 2500.00, '2026-01-14 00:00:00', '2026-04-14', 'approved', 'pending');

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
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `room_title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `capacity` int(11) DEFAULT 2,
  `room_count` int(11) DEFAULT 1,
  `price_per_night` decimal(10,2) NOT NULL,
  `status` enum('available','booked','maintenance') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `hotel_id`, `room_title`, `description`, `capacity`, `room_count`, `price_per_night`, `status`, `created_at`) VALUES
(5, 20, 'fghgfh', 'fhfh', 2, 1, 1000.00, 'available', '2026-01-15 18:23:36');

-- --------------------------------------------------------

--
-- Table structure for table `room_images`
--

CREATE TABLE `room_images` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
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
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','owner','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES
(1, 'rahat', 'rahat@gmail.com', NULL, '$2y$10$/b7ZF55mWU.wnAHUBjZSiObScptkteT/VkCcKybKIZz9PSNDyjZqS', 'user', '2025-12-28 12:31:00'),
(2, 'Tanvir', 'owner@gmail.com', NULL, '$2y$10$fuu4L82juLKCmxjhj5td0OfOdhq4vp8cz.szT4UKRTbvNytqVoZ7.', 'owner', '2025-12-28 12:31:00'),
(5, 'admin', 'admin@gmail.com', NULL, 'password', 'admin', '2025-12-28 12:31:00'),
(6, '', 'owner1@gmail.com', NULL, '$2y$10$o5owVGOgCYQ3xz8NkSThxuMyDHw/ly789Fvcg47N0Hh1yTGP68B7K', 'owner', '2025-12-28 12:31:00'),
(7, '', 'owner2@gmail.com', NULL, '$2y$10$nvg1310hmcB.NkoI.soFI.tcQC9t6sYTTMg9mv3ai8N.qL6veXWii', 'owner', '2025-12-28 19:08:41'),
(8, 'Arman', 'arman@gmail.com', NULL, '$2y$10$ZVJlC9wFCFHXmwGxmq.o.OwKs32ohQHW1BsRXrmKCw56ANNZLwTi.', 'owner', '2025-12-31 07:30:20'),
(9, 'Robiul', 'robiul@gmail.com', NULL, '$2y$10$w2my96a4eULq9uEs9DaqJuV9SeGqs6bET8xMK86wRiNeObQSjaS1e', 'owner', '2026-01-03 09:15:23'),
(10, 'Jidni', 'jidni@gmail.com', NULL, '$2y$10$VUciNquBe8TW30TqN9cX9eleoKa1XHvHEc0UhA8cAkbvl5cBWEI3O', 'user', '2026-01-11 17:35:24'),
(11, 'User', 'user@gmail.com', NULL, '$2y$10$GYBDForb8fgm1qMfFKZyBeQM.WgUd5Lqvh7kzDS8NEtydac7POnTa', 'user', '2026-01-13 21:12:24'),
(14, 'Adil', 'Adil@gmail.com', NULL, '$2y$10$AdB2O/iGkf1m4uB04jg4aeowKqC1dp7VROAUxlrd2OV.G9obZ1lx.', 'owner', '2026-01-15 16:21:16');

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
  `room_title` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `tran_id` varchar(100) DEFAULT NULL,
  `payment_status` enum('pending','success','failed','cancelled') DEFAULT 'pending',
  `admin_status` enum('pending','paid_to_owner') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `owner_paid_status` enum('pending','paid') DEFAULT 'pending',
  `owner_paid_date` timestamp NULL DEFAULT NULL,
  `commission` decimal(10,2) DEFAULT 0.00,
  `owner_amount` decimal(10,2) DEFAULT 0.00,
  `receipt_id` varchar(50) DEFAULT NULL,
  `booking_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_payments`
--

INSERT INTO `user_payments` (`id`, `user_id`, `owner_id`, `booking_id`, `hotel_name`, `room_title`, `amount`, `tran_id`, `payment_status`, `admin_status`, `created_at`, `owner_paid_status`, `owner_paid_date`, `commission`, `owner_amount`, `receipt_id`, `booking_date`) VALUES
(18, 11, 9, 85, 'Hotel Le meridian', NULL, 4000.00, 'USERPAY_6966bd9e5499f', 'success', 'pending', '2026-01-13 21:48:22', 'paid', '2026-01-15 18:48:18', 400.00, 3600.00, 'RECEIPT_20260113_1541', '2026-01-16'),
(19, 1, 0, 87, 'Adil Hotel', 'Dulex Room', 2000.00, 'USERPAY_6969204283868', 'success', 'pending', '2026-01-15 17:13:46', 'pending', NULL, 200.00, 1800.00, 'RECEIPT_20260115_7946', '2026-01-15'),
(20, 1, 0, 86, 'Adil Hotel', NULL, 2000.00, 'USERPAY_6969211e6848d', 'success', 'pending', '2026-01-15 17:17:25', 'pending', NULL, 200.00, 1800.00, 'RECEIPT_20260115181725612', '2026-01-15'),
(21, 1, 0, 88, 'Adil Hotel', NULL, 1000.00, 'USERPAY_696921af5b321', 'success', 'pending', '2026-01-15 17:19:50', 'pending', NULL, 100.00, 900.00, 'RECEIPT_20260115181950584', '2026-01-15'),
(22, 1, 0, 89, 'Adil Hotel', NULL, 1000.00, 'USERPAY_696921ebcb99f', 'success', 'pending', '2026-01-15 17:20:51', 'pending', NULL, 100.00, 900.00, 'REC20260115182051998', '2026-01-15');

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_owner` (`owner_id`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_owner_subscriptions_hotel` (`hotel_id`);

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
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `room_images`
--
ALTER TABLE `room_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT for table `owner_subscriptions`
--
ALTER TABLE `owner_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

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
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `room_images`
--
ALTER TABLE `room_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user_payments`
--
ALTER TABLE `user_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `owner_subscriptions`
--
ALTER TABLE `owner_subscriptions`
  ADD CONSTRAINT `fk_owner_subscriptions_hotel` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`);

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_images`
--
ALTER TABLE `room_images`
  ADD CONSTRAINT `room_images_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
