-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 05, 2026 at 08:41 AM
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
(37, 1, 2, 'Hotel Sams Plaza', 'Cox\'s bazar', 2000, '2025-12-29', 'confirmed', 9),
(41, 1, 9, 'Hotel Light House', 'Dhaka', 4000, '2026-01-03', 'cancelled', 13),
(45, 1, 9, 'Ramadan Hotel', 'Cox\'s Bazar', 5000, '2026-01-03', 'cancelled', 12),
(49, 1, 9, 'Ramadan Hotel', 'Cox\'s Bazar', 5000, '2026-01-03', 'confirmed', 12),
(50, 1, 9, 'Ramadan Hotel', 'Cox\'s Bazar', 5000, '2026-01-03', 'cancelled', 12),
(51, 1, 9, 'Hotel Light House', 'Dhaka', 4000, '2026-01-04', 'pending', 13),
(52, 1, 9, 'Ramadan Hotel', 'Cox\'s Bazar', 5000, '2026-01-04', 'pending', 12);

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
(12, 'Ramadan Hotel', 'Cox\'s Bazar', 5000, 'Ramadan Hotel', '1767432249_1766508216_sea_crown.jpg', 'off', 9, 1, 2),
(13, 'Hotel Light House', 'Dhaka', 4000, 'Hotel Light House', '1767446444_1766507722_hotel_Le_meridian.jpg', 'off', 9, 1, 2),
(14, 'Hotel Sampan Dhaka', 'Dhaka', 8000, 'Hotel Sampan Dhaka', '1767551451_istockphoto-173587041-612x612.jpg', 'approved', 9, 1, 2);

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
(39, 5, 'admin', '📢 New subscription from Robiul', 'admin/manage_subscriptions.php', 'unread', '2026-01-04 18:29:09'),
(40, 5, 'admin', '📢 New subscription from Robiul', 'admin/manage_subscriptions.php', 'unread', '2026-01-04 18:29:30');

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
(1, 7, 1, 'SUB_6952be30b6234', 1000.00, '2025-12-30 00:00:00', '2026-01-29', 'expired', 'pending'),
(4, 2, 1, 'SUB_6952c4edce8a0', 1000.00, '2025-12-30 00:00:00', '2026-01-29', 'expired', 'pending'),
(5, 8, 1, 'SUB_6954d227c1d3c', 1000.00, '2025-12-31 00:00:00', '2026-01-30', 'expired', 'pending'),
(6, 8, 3, 'SUB_6954d4c22e285', 8000.00, '2025-12-31 00:00:00', '2026-12-31', 'expired', 'pending'),
(7, 9, 3, 'SUB_6958e0c50948b', 8000.00, '2026-01-03 00:00:00', '2027-01-03', 'expired', 'pending'),
(8, 9, 1, 'SUB_695913eba2579', 1000.00, '2026-01-03 00:00:00', '2026-02-02', 'expired', 'pending'),
(9, 9, 2, 'SUB_69591bf3149ad', 2500.00, '2026-01-03 00:00:00', '2026-04-03', 'expired', 'pending'),
(10, 9, 1, 'SUB_69591fa8bd2ca', 1000.00, '2026-01-03 00:00:00', '2026-02-02', 'expired', 'pending'),
(11, 9, 1, 'SUB_6959202ce3b77', 1000.00, '2026-01-03 00:00:00', '2026-02-02', 'expired', 'pending'),
(12, 9, 1, 'SUB_69592146d74fe', 1000.00, '2026-01-03 00:00:00', '2026-02-02', 'expired', 'pending'),
(13, 9, 2, 'SUB_695921cdd0f3a', 2500.00, '2026-01-03 00:00:00', '2026-04-03', 'expired', 'pending'),
(14, 9, 2, 'SUB_69593b3462cec', 2500.00, '2026-01-03 00:00:00', '2026-04-03', 'expired', 'pending'),
(15, 9, 1, 'SUB_69593f037c8e9', 1000.00, '2026-01-03 00:00:00', '2026-02-02', 'expired', 'pending'),
(16, 9, 1, 'SUB_69593faca5edc', 1000.00, '2026-01-03 00:00:00', '2026-02-02', 'expired', 'pending'),
(17, 9, 2, 'SUB_695967e16f921', 2500.00, '2026-01-04 00:00:00', '2026-04-04', 'expired', 'pending'),
(18, 9, 1, 'SUB_695ab177a8e95', 1000.00, '2026-01-05 00:00:00', '2026-02-04', 'approved', 'pending');

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
(9, 'Robiul', 'robiul@gmail.com', '$2y$10$w2my96a4eULq9uEs9DaqJuV9SeGqs6bET8xMK86wRiNeObQSjaS1e', 'owner', '2026-01-03 09:15:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
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
-- Indexes for table `owner_subscriptions`
--
ALTER TABLE `owner_subscriptions`
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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `owner_subscriptions`
--
ALTER TABLE `owner_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
