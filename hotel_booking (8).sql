-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 16, 2026 at 09:35 PM
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
(25, 32, 1, 15, 5500.00, 550.00, 4950.00, '2026-01-16 14:22:57'),
(26, 33, 1, 15, 5500.00, 550.00, 4950.00, '2026-01-16 20:04:10'),
(27, 34, 11, 14, 4000.00, 400.00, 3600.00, '2026-01-16 20:14:18'),
(28, 35, 11, 14, 6500.00, 650.00, 5850.00, '2026-01-16 20:25:10');

-- --------------------------------------------------------

--
-- Table structure for table `blocked_dates`
--

CREATE TABLE `blocked_dates` (
  `id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT 0,
  `blocked_date` date NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `rooms_booked` int(11) DEFAULT 1,
  `payment_status` enum('pending','success','failed') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `owner_id`, `hotel_name`, `location`, `price`, `rooms_count`, `guests`, `booking_date`, `check_in_date`, `check_out_date`, `status`, `hotel_id`, `room_title`, `room_id`, `rooms_booked`, `payment_status`, `created_at`) VALUES
(103, 1, 0, 'West Park Inn', 'Dhaka', 5500, 1, 2, NULL, '2026-01-16', '2026-01-17', 'confirmed', 21, 'Deluxe Double Room', 9, 1, 'pending', '2026-01-17 01:57:52'),
(104, 1, 0, 'Sayeman Beach Resort', 'Cox\'s Bazar', 6500, 1, 1, NULL, '2026-01-23', '2026-01-24', 'confirmed', 20, 'Panorama Ocean Suite Sea View with Balcony', 7, 1, 'pending', '2026-01-17 01:57:52'),
(105, 1, 0, 'West Park Inn', 'Dhaka', 5500, 1, 1, NULL, '2026-01-29', '2026-01-30', 'initiated', 21, 'Deluxe Double Room', 9, 1, 'pending', '2026-01-17 01:57:57'),
(106, 1, 0, 'West Park Inn', 'Dhaka', 5500, 1, 1, NULL, '2026-01-29', '2026-01-30', 'initiated', 21, 'Deluxe Double Room', 9, 1, 'pending', '2026-01-17 01:58:03'),
(107, 1, 0, 'West Park Inn', 'Dhaka', 5500, 1, 1, NULL, '2026-01-29', '2026-01-30', 'initiated', 21, 'Deluxe Double Room', 9, 1, 'pending', '2026-01-17 02:00:13'),
(108, 1, 0, 'West Park Inn', 'Dhaka', 5500, 1, 1, NULL, '2026-01-17', '2026-01-18', 'cancelled', 21, 'Deluxe Double Room', 9, 1, 'success', '2026-01-17 02:00:27'),
(109, 1, 0, 'Sayeman Beach Resort', 'Cox\'s Bazar', 4000, 1, 1, NULL, '2026-01-19', '2026-01-20', 'initiated', 20, 'Super Deluxe Family Room (City or Hill View)', 8, 1, 'pending', '2026-01-17 02:04:41'),
(110, 11, 0, 'Sayeman Beach Resort', 'Cox\'s Bazar', 6500, 1, 1, NULL, '2026-01-30', '2026-01-31', 'confirmed', 20, 'Panorama Ocean Suite Sea View with Balcony', 7, 1, 'success', '2026-01-17 02:08:53'),
(111, 11, 0, 'Sayeman Beach Resort', 'Cox\'s Bazar', 4000, 1, 1, NULL, '2026-01-19', '2026-01-20', 'cancelled', 20, 'Super Deluxe Family Room (City or Hill View)', 8, 1, 'success', '2026-01-17 02:10:40'),
(112, 11, 0, 'Sayeman Beach Resort', 'Cox\'s Bazar', 4000, 1, 1, NULL, '2026-01-30', '2026-01-31', 'initiated', 20, 'Super Deluxe Family Room (City or Hill View)', 8, 1, 'pending', '2026-01-17 02:26:05');

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
(20, 'Sayeman Beach Resort', NULL, 'Cox\'s Bazar', NULL, 'After fifty years of glorious past, Sayeman Beach Resort revives its famed legacy of comfort, elegance and impeccable service. An eminent landmark constructed in 1964, this legendary first private hotel of Cox’s Bazar is reborn, infusing modern sophistication into this vintage-chic, iconic hotel at a new beachfront location of Marine Drive, Kolatoli, Cox’s Bazar.With its richly historic past, the Hotel Sayeman now fully becomes a part of the exciting and rapidly changing present with the addition of a modern, elegant luxury ocean front hotel. The beauty of Cox’s Bazar – the climate, the panoramic ocean views, the sandy beaches, plus the rich culture and history along with the warmth of the sun – is what attracts people here. And the Sayeman Beach Resort provides you exactly just that with extraordinary comfort, luxury and services.', '1768567688_hotel_14.png', 'approved', '2026-01-15 16:21:16', 14, 1, 2, '[\"2026-01-20\"]', 2, 1),
(21, 'West Park Inn', NULL, 'Dhaka', NULL, 'The West Park Inn, a new 4 star standard hotel in Dhaka situated at the central business hub and diplomatic zone of Banani. Very near to the International Airport. West Park Inn is committed to deliver utmost hospitality services and facilities to meet the needs of corporate and business travelers', '1768571295_hotel_15.png', 'approved', '2026-01-16 13:46:59', 15, 1, 2, '[]', 2, 1);

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
(230, 15, 'owner', '📅 New booking request for \"Deluxe Double Room\" from rahat', '/hotel_booking/owner/manage_bookings.php', 'unread', '2026-01-16 14:22:47'),
(231, 1, 'user', '✅ Booking request sent for \"Deluxe Double Room\". Please complete payment.', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-16 14:22:47'),
(232, 1, 'user', '✅ Payment Successful!\nReceipt ID: REC20260116152257956\nAmount: ৳5500.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-16 14:22:57'),
(233, 15, 'owner', '✅ Payment Received!\nHotel: West Park Inn\nAmount: ৳5500.00 (You get: ৳4950)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-16 14:22:57'),
(234, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳5500.00\nCommission: ৳550\nOwner gets: ৳4950', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-16 14:22:57'),
(235, 14, 'owner', '📅 New booking request for \"Panorama Ocean Suite Sea View with Balcony\" from rahat', '/hotel_booking/owner/manage_bookings.php', 'unread', '2026-01-16 18:36:18'),
(236, 1, 'user', '✅ Booking request sent for \"Panorama Ocean Suite Sea View with Balcony\". Please complete payment.', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-16 18:36:18'),
(237, 1, 'user', 'Booking status updated for \'Panorama Ocean Suite Sea View with Balcony\' to: Confirmed', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-16 19:02:00'),
(238, 1, 'user', '✅ Payment Successful!\nReceipt ID: REC20260116210410429\nAmount: ৳5500.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-16 20:04:10'),
(239, 15, 'owner', '✅ Payment Received!\nHotel: West Park Inn\nAmount: ৳5500.00 (You get: ৳4950)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-16 20:04:10'),
(240, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳5500.00\nCommission: ৳550\nOwner gets: ৳4950', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-16 20:04:10'),
(241, 11, 'user', '✅ Payment Successful!\nReceipt ID: REC20260116211418113\nAmount: ৳4000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-16 20:14:18'),
(242, 14, 'owner', '✅ Payment Received!\nHotel: Sayeman Beach Resort\nAmount: ৳4000.00 (You get: ৳3600)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-16 20:14:18'),
(243, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳4000.00\nCommission: ৳400\nOwner gets: ৳3600', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-16 20:14:18'),
(244, 11, 'user', 'Booking status updated for \'Super Deluxe Family Room (City or Hill View)\' to: Cancelled', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-16 20:15:33'),
(245, 11, 'user', '✅ Payment Successful!\nReceipt ID: REC20260116212509131\nAmount: ৳6500.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-16 20:25:10'),
(246, 14, 'owner', '✅ Payment Received!\nHotel: Sayeman Beach Resort\nAmount: ৳6500.00 (You get: ৳5850)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-16 20:25:10'),
(247, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳6500.00\nCommission: ৳650\nOwner gets: ৳5850', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-16 20:25:10');

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
(25, 14, NULL, 1, 'SUB_6969421073ee7', 1000.00, '2026-01-16 00:00:00', '2026-02-15', 'approved', 'pending');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `hotel_id`, `room_title`, `description`, `capacity`, `room_count`, `price_per_night`, `created_at`, `is_active`, `active`) VALUES
(7, 20, 'Panorama Ocean Suite Sea View with Balcony', 'Adult Occupancy: 4\r\nComplementary Child Occupancy: 2\r\nOn Demand Extra Bed: 1\r\nMaximum Number of Guests Allowed: 5', 5, 2, 6500.00, '2026-01-16 13:07:06', 1, 1),
(8, 20, 'Super Deluxe Family Room (City or Hill View)', 'Super Deluxe Family Room (City or Hill View) .Amazing Room', 3, 1, 4000.00, '2026-01-16 13:10:19', 1, 1),
(9, 21, 'Deluxe Double Room', 'Deluxe Double Room', 3, 1, 5500.00, '2026-01-16 14:04:14', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `room_dates`
--

CREATE TABLE `room_dates` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('available','booked','blocked') DEFAULT 'available',
  `booking_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

--
-- Dumping data for table `room_images`
--

INSERT INTO `room_images` (`id`, `room_id`, `image_url`, `is_primary`, `created_at`) VALUES
(7, 7, '1768568826_room_7_0.jpg', 1, '2026-01-16 13:07:06'),
(8, 8, '1768569019_room_8_0.png', 1, '2026-01-16 13:10:19'),
(9, 9, '1768572254_room_9_0.jpg', 1, '2026-01-16 14:04:14'),
(10, 9, '1768572254_room_9_1.jpg', 0, '2026-01-16 14:04:14');

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
(5, 'admin', 'admin@gmail.com', NULL, 'password', 'admin', '2025-12-28 12:31:00'),
(11, 'User', 'user@gmail.com', NULL, '$2y$10$GYBDForb8fgm1qMfFKZyBeQM.WgUd5Lqvh7kzDS8NEtydac7POnTa', 'user', '2026-01-13 21:12:24'),
(14, 'Adil', 'Adil@gmail.com', NULL, '$2y$10$AdB2O/iGkf1m4uB04jg4aeowKqC1dp7VROAUxlrd2OV.G9obZ1lx.', 'owner', '2026-01-15 16:21:16'),
(15, 'Tanvir', 'tanvir@gmail.com', NULL, '$2y$10$Ych/1xJlymU2meXvmdE09OsSpshw/azaINhrLijD6KFIqifMA5oHG', 'owner', '2026-01-16 13:46:59');

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
(32, 1, 15, 103, 'West Park Inn', 'Deluxe Double Room', 5500.00, 'USERPAY_696a49b9a574e', 'success', 'pending', '2026-01-16 14:22:57', 'pending', NULL, 550.00, 4950.00, 'REC20260116152257956', '2026-01-16'),
(33, 1, 15, 108, 'West Park Inn', 'Deluxe Double Room', 5500.00, 'USERPAY_696a99b0211b3', 'success', 'pending', '2026-01-16 20:04:10', 'pending', NULL, 550.00, 4950.00, 'REC20260116210410429', '2026-01-17'),
(34, 11, 14, 111, 'Sayeman Beach Resort', 'Super Deluxe Family Room (City or Hill View)', 4000.00, 'USERPAY_696a9c122f3ad', 'success', 'pending', '2026-01-16 20:14:18', 'pending', NULL, 400.00, 3600.00, 'REC20260116211418113', '2026-01-17'),
(35, 11, 14, 110, 'Sayeman Beach Resort', 'Panorama Ocean Suite Sea View with Balcony', 6500.00, 'USERPAY_696a9e9cc13a4', 'success', 'pending', '2026-01-16 20:25:09', 'pending', NULL, 650.00, 5850.00, 'REC20260116212509131', '2026-01-17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_commissions`
--
ALTER TABLE `admin_commissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blocked_dates`
--
ALTER TABLE `blocked_dates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_blocked_dates` (`hotel_id`,`room_id`,`blocked_date`);

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
-- Indexes for table `room_dates`
--
ALTER TABLE `room_dates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_room_date` (`room_id`,`date`),
  ADD KEY `booking_id` (`booking_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `blocked_dates`
--
ALTER TABLE `blocked_dates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=248;

--
-- AUTO_INCREMENT for table `owner_subscriptions`
--
ALTER TABLE `owner_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `room_dates`
--
ALTER TABLE `room_dates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room_images`
--
ALTER TABLE `room_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user_payments`
--
ALTER TABLE `user_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blocked_dates`
--
ALTER TABLE `blocked_dates`
  ADD CONSTRAINT `blocked_dates_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `room_dates`
--
ALTER TABLE `room_dates`
  ADD CONSTRAINT `room_dates_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_dates_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `room_images`
--
ALTER TABLE `room_images`
  ADD CONSTRAINT `room_images_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
