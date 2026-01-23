-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 22, 2026 at 06:31 PM
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
(34, 41, 1, 16, 2000.00, 200.00, 1800.00, '2026-01-21 22:15:50'),
(35, 42, 1, 16, 15000.00, 1500.00, 13500.00, '2026-01-21 22:16:50'),
(36, 43, 11, 16, 3500.00, 350.00, 3150.00, '2026-01-22 15:48:49'),
(37, 44, 11, 16, 7000.00, 700.00, 6300.00, '2026-01-22 16:03:33');

-- --------------------------------------------------------

--
-- Table structure for table `amenities`
--

CREATE TABLE `amenities` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `amenities`
--

INSERT INTO `amenities` (`id`, `name`, `icon`) VALUES
(1, 'Free WiFi', 'fa-wifi'),
(2, 'Air Conditioning', 'fa-snowflake'),
(3, 'TV', 'fa-tv'),
(4, 'Parking', 'fa-car'),
(5, 'Hot Water', 'fa-hot-tub'),
(6, 'Room Service', 'fa-concierge-bell'),
(7, 'Security', 'fa-shield-alt'),
(8, 'Attached Bathroom', 'fa-bath');

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
(124, 1, 0, 'Sampan Beach Resort', 'Cox\'s Bazar', 2000, 1, 1, NULL, '2026-01-22', '2026-01-24', 'confirmed', 22, 'Family Suite Room', 12, 1, 'success', '2026-01-22 04:15:39'),
(125, 1, 0, 'Sampan Beach Resort', 'Cox\'s Bazar', 15000, 1, 1, NULL, '2026-02-11', '2026-02-13', 'confirmed', 22, 'Couple Deluxe Room ', 10, 1, 'success', '2026-01-22 04:16:39'),
(126, 11, 0, 'Sampan Beach Resort', 'Cox\'s Bazar', 3500, 1, 1, NULL, '2026-01-23', '2026-01-24', 'confirmed', 22, 'Couple Deluxe Room ', 10, 1, 'success', '2026-01-22 21:48:38'),
(127, 11, 0, 'Sampan Beach Resort', 'Cox\'s Bazar', 7000, 1, 1, NULL, '2026-01-22', '2026-01-23', 'confirmed', 22, 'Luxury Family Deluxe Room', 11, 1, 'success', '2026-01-22 22:03:24');

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
(21, 'West Park Inn', NULL, 'Dhaka', NULL, 'The West Park Inn, a new 4 star standard hotel in Dhaka situated at the central business hub and diplomatic zone of Banani. Very near to the International Airport. West Park Inn is committed to deliver utmost hospitality services and facilities to meet the needs of corporate and business travelers', '1768571295_hotel_15.png', 'approved', '2026-01-16 13:46:59', 15, 1, 2, '[]', 2, 1),
(22, 'Sampan Beach Resort', NULL, 'Cox\'s Bazar', NULL, 'Sampan Beach Resort is located in one of the most attractive locations in Cox\'s Bazar. It\'s situated just right beside the Marine Drive road near Himchari just from Cox\'s Bazar Kolatoli Bus Stand.\r\n\r\nThe resort is specially designed for guests who want to avoid the city gatherings and enjoy the natural harmony (hill and Seaview together) in the longest natural sea beach in the world.', '1769024367_hotel_16.jpg', 'approved', '2026-01-16 21:21:19', 16, 1, 2, '[]', 2, 1);

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
(247, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳6500.00\nCommission: ৳650\nOwner gets: ৳5850', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-16 20:25:10'),
(248, 16, 'owner', '✅ Your hotel \"Hotel Sampan\" has been approved and is now live!', '/hotel_booking/owner/dashboard.php', 'unread', '2026-01-21 18:23:23'),
(249, 5, 'admin', '🏨 New room uploaded by Robiul - \"Couple Deluxe Room \" (৳7500/night)', '/hotel_booking/admin/hotels.php', 'unread', '2026-01-21 18:47:19'),
(250, 5, 'admin', '💳 New subscription request from Robiul - 1 Month (৳1000.00)', '/hotel_booking/admin/manage_subscriptions.php', 'unread', '2026-01-21 19:05:55'),
(251, 16, 'owner', '✅ Subscription payment successful! Waiting for admin approval.', '/hotel_booking/owner/subscription.php', 'unread', '2026-01-21 19:05:55'),
(252, 16, 'owner', '✅ Your subscription (৳1000) has been approved! Premium features activated.', '/hotel_booking/owner/dashboard.php', 'unread', '2026-01-21 19:06:11'),
(253, 5, 'admin', '🏨 New room uploaded by Robiul - \"Luxury Family Deluxe Room\" (৳8000/night)', '/hotel_booking/admin/hotels.php', 'unread', '2026-01-21 19:42:29'),
(254, 5, 'admin', '🏨 New room uploaded by Robiul - \"Family Suite Room\" (৳8500/night)', '/hotel_booking/admin/hotels.php', 'unread', '2026-01-21 20:01:19'),
(255, 1, 'user', '✅ Payment Successful!\nReceipt ID: REC20260121221536547\nAmount: ৳8500.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-21 21:15:36'),
(256, 16, 'owner', '✅ Payment Received!\nHotel: Sampan Beach Resort\nAmount: ৳8500.00 (You get: ৳7650)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-21 21:15:37'),
(257, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳8500.00\nCommission: ৳850\nOwner gets: ৳7650', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-21 21:15:37'),
(258, 1, 'user', '✅ Payment Successful!\nReceipt ID: REC20260121225837579\nAmount: ৳1000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-21 21:58:37'),
(259, 16, 'owner', '✅ Payment Received!\nHotel: Sampan Beach Resort\nAmount: ৳1000.00 (You get: ৳900)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-21 21:58:37'),
(260, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳1000.00\nCommission: ৳100\nOwner gets: ৳900', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-21 21:58:37'),
(261, 1, 'user', '✅ Payment Successful!\nReceipt ID: REC20260121230929469\nAmount: ৳16000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-21 22:09:29'),
(262, 16, 'owner', '✅ Payment Received!\nHotel: Sampan Beach Resort\nAmount: ৳16000.00 (You get: ৳14400)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-21 22:09:29'),
(263, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳16000.00\nCommission: ৳1600\nOwner gets: ৳14400', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-21 22:09:30'),
(264, 1, 'user', '✅ Payment Successful!\nReceipt ID: REC20260121231056318\nAmount: ৳7500.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-21 22:10:56'),
(265, 16, 'owner', '✅ Payment Received!\nHotel: Sampan Beach Resort\nAmount: ৳7500.00 (You get: ৳6750)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-21 22:10:56'),
(266, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳7500.00\nCommission: ৳750\nOwner gets: ৳6750', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-21 22:10:56'),
(267, 1, 'user', '✅ Payment Successful!\nReceipt ID: REC20260121231207659\nAmount: ৳15000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-21 22:12:07'),
(268, 16, 'owner', '✅ Payment Received!\nHotel: Sampan Beach Resort\nAmount: ৳15000.00 (You get: ৳13500)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-21 22:12:07'),
(269, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳15000.00\nCommission: ৳1500\nOwner gets: ৳13500', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-21 22:12:07'),
(270, 1, 'user', '✅ Payment Successful!\nReceipt ID: REC20260121231550515\nAmount: ৳2000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-21 22:15:50'),
(271, 16, 'owner', '✅ Payment Received!\nHotel: Sampan Beach Resort\nAmount: ৳2000.00 (You get: ৳1800)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-21 22:15:51'),
(272, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳2000.00\nCommission: ৳200\nOwner gets: ৳1800', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-21 22:15:51'),
(273, 1, 'user', '✅ Payment Successful!\nReceipt ID: REC20260121231650592\nAmount: ৳15000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-21 22:16:50'),
(274, 16, 'owner', '✅ Payment Received!\nHotel: Sampan Beach Resort\nAmount: ৳15000.00 (You get: ৳13500)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-21 22:16:50'),
(275, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳15000.00\nCommission: ৳1500\nOwner gets: ৳13500', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-21 22:16:50'),
(276, 5, 'admin', '🏨 New room uploaded by Robiul - \"Comfortable Executive Sharing Room\" (৳8000/night)', '/hotel_booking/admin/hotels.php', 'unread', '2026-01-22 12:48:12'),
(277, 11, 'user', '✅ Payment Successful!\nReceipt ID: REC20260122164849411\nAmount: ৳3500.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-22 15:48:49'),
(278, 16, 'owner', '✅ Payment Received!\nHotel: Sampan Beach Resort\nAmount: ৳3500.00 (You get: ৳3150)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-22 15:48:49'),
(279, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳3500.00\nCommission: ৳350\nOwner gets: ৳3150', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-22 15:48:49'),
(280, 11, 'user', '✅ Payment Successful!\nReceipt ID: REC20260122170332138\nAmount: ৳7000.00', '/hotel_booking/user/my_booking.php', 'unread', '2026-01-22 16:03:33'),
(281, 16, 'owner', '✅ Payment Received!\nHotel: Sampan Beach Resort\nAmount: ৳7000.00 (You get: ৳6300)', '/hotel_booking/owner/finance.php', 'unread', '2026-01-22 16:03:33'),
(282, 5, 'admin', '💰 Payment + Commission!\nAmount: ৳7000.00\nCommission: ৳700\nOwner gets: ৳6300', '/hotel_booking/admin/manage_payments.php', 'unread', '2026-01-22 16:03:33');

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

--
-- Dumping data for table `owner_finance`
--

INSERT INTO `owner_finance` (`owner_id`, `total_earned`, `total_paid`, `last_updated`) VALUES
(16, 0.00, 9450.00, '2026-01-22 16:32:58');

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
(25, 14, NULL, 1, 'SUB_6969421073ee7', 1000.00, '2026-01-16 00:00:00', '2026-02-15', 'approved', 'pending'),
(26, 16, NULL, 1, 'SUB_6971238621693', 1000.00, '2026-01-22 00:00:00', '2026-02-21', 'approved', 'pending');

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
  `discount_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `active` tinyint(1) DEFAULT 1,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `hotel_id`, `room_title`, `description`, `capacity`, `room_count`, `price_per_night`, `discount_price`, `created_at`, `is_active`, `active`, `image`) VALUES
(7, 20, 'Panorama Ocean Suite Sea View with Balcony', 'Adult Occupancy: 4\r\nComplementary Child Occupancy: 2\r\nOn Demand Extra Bed: 1\r\nMaximum Number of Guests Allowed: 5', 5, 2, 6500.00, NULL, '2026-01-16 13:07:06', 1, 1, NULL),
(8, 20, 'Super Deluxe Family Room (City or Hill View)', 'Super Deluxe Family Room (City or Hill View) .Amazing Room', 3, 1, 4000.00, NULL, '2026-01-16 13:10:19', 1, 1, NULL),
(9, 21, 'Deluxe Double Room', 'Deluxe Double Room', 2, 1, 4000.00, NULL, '2026-01-16 14:04:14', 1, 1, NULL),
(10, 22, 'Couple Deluxe Room ', 'This room size is 15 by 12 ft long with a comfortable 7 by 5 ft couple bed. The total has a spacious living space with 180 sq. feet and has a wooden floor, a wooden ceiling, and a large balcony that maximizes your experience.', 2, 1, 7500.00, 3500.00, '2026-01-21 18:47:19', 1, 1, '1769024862_room_10.jpg'),
(11, 22, 'Luxury Family Deluxe Room', 'The size of the Family Deluxe Room is 625 square feet. This room has King-sized spacious beds measuring 7 feet by 6 feet. Everything from the floor to the ceiling is made of wood, and there is even a unique sofa and a private balcony.\r\n\r\n', 4, 2, 8000.00, 7000.00, '2026-01-21 19:42:29', 1, 1, '1769024896_room_11.jpg'),
(12, 22, 'Family Suite Room', 'Each room size of the Family Suite is 20 ft by 20 ft and it has a total 3 rooms of 1200 square feet. There are five spacious beds (6 ft X 7 ft) and one single bed another one room two double beds (4 ft x 7 ft) in this room. The living space boasts a wooden floor, a wooden ceiling, and a sizable balcony. Your accommodation has a view of the ocean and the sunset.', 6, 3, 8500.00, 7500.00, '2026-01-21 20:01:19', 1, 1, NULL),
(13, 22, 'Comfortable Executive Sharing Room', 'These 3 rooms each are 400 square feet in sized comfortable beds measuring 5 by 7 feet. The living area features a spacious balcony, wooden floor, and ceiling. If you stay in one of our ocean-view rooms, you may watch the sun go down while gazing at the beauty.', 8, 1, 8000.00, 5000.00, '2026-01-22 12:48:12', 1, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `room_amenities`
--

CREATE TABLE `room_amenities` (
  `room_id` int(11) NOT NULL,
  `amenity_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_amenities`
--

INSERT INTO `room_amenities` (`room_id`, `amenity_id`) VALUES
(9, 1),
(9, 2),
(9, 3),
(9, 4),
(9, 5),
(9, 6),
(9, 7),
(9, 8),
(10, 1),
(10, 2),
(10, 3),
(10, 4),
(10, 5),
(10, 6),
(10, 7),
(10, 8),
(11, 1),
(11, 2),
(11, 3),
(11, 4),
(11, 5),
(11, 6),
(11, 7),
(11, 8),
(12, 1),
(12, 2),
(12, 3),
(12, 4),
(12, 5),
(12, 6),
(12, 7),
(12, 8),
(13, 1),
(13, 2),
(13, 3),
(13, 4),
(13, 5),
(13, 6),
(13, 7),
(13, 8);

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
(10, 9, '1768572254_room_9_1.jpg', 0, '2026-01-16 14:04:14'),
(13, 10, '1769025163_room_10.jpg', 1, '2026-01-21 19:52:43'),
(15, 11, '1769025184_room_11.jpg', 1, '2026-01-21 19:53:04'),
(16, 12, '1769025679_room_12_0.jpg', 1, '2026-01-21 20:01:19'),
(17, 13, '1769086092_room_13_0.jpg', 1, '2026-01-22 12:48:12');

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
(3, '1 Year', 365, 8000),
(4, '6 Month', 180, 4000);

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
(15, 'Tanvir', 'tanvir@gmail.com', NULL, '$2y$10$Ych/1xJlymU2meXvmdE09OsSpshw/azaINhrLijD6KFIqifMA5oHG', 'owner', '2026-01-16 13:46:59'),
(16, 'Robiul', 'Robiul@gmail.com', NULL, '$2y$10$IDm9.WDBwQWAw2AMK.uRN.6UbBvLOgvwLW8UFusk3leOpddaXSjv.', 'owner', '2026-01-16 21:21:19');

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
(41, 1, 16, 124, 'Sampan Beach Resort', 'Family Suite Room', 2000.00, 'USERPAY_6971500fb1395', 'success', 'pending', '2026-01-21 22:15:50', 'pending', NULL, 200.00, 1800.00, 'REC20260121231550515', '2026-01-22'),
(42, 1, 16, 125, 'Sampan Beach Resort', 'Couple Deluxe Room ', 15000.00, 'USERPAY_69715049d8281', 'success', 'pending', '2026-01-21 22:16:50', 'pending', NULL, 1500.00, 13500.00, 'REC20260121231650592', '2026-01-22'),
(43, 11, 16, 126, 'Sampan Beach Resort', 'Couple Deluxe Room ', 3500.00, 'USERPAY_697246d859279', 'success', 'pending', '2026-01-22 15:48:49', 'paid', '2026-01-22 16:32:58', 350.00, 3150.00, 'REC20260122164849411', '2026-01-22'),
(44, 11, 16, 127, 'Sampan Beach Resort', 'Luxury Family Deluxe Room', 7000.00, 'USERPAY_69724a4de7a39', 'success', 'pending', '2026-01-22 16:03:32', 'paid', '2026-01-22 16:03:49', 700.00, 6300.00, 'REC20260122170332138', '2026-01-22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_commissions`
--
ALTER TABLE `admin_commissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `amenities`
--
ALTER TABLE `amenities`
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
-- Indexes for table `room_amenities`
--
ALTER TABLE `room_amenities`
  ADD PRIMARY KEY (`room_id`,`amenity_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `amenities`
--
ALTER TABLE `amenities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `blocked_dates`
--
ALTER TABLE `blocked_dates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=283;

--
-- AUTO_INCREMENT for table `owner_subscriptions`
--
ALTER TABLE `owner_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `room_dates`
--
ALTER TABLE `room_dates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room_images`
--
ALTER TABLE `room_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `user_payments`
--
ALTER TABLE `user_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

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
