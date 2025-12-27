-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 27, 2025 at 02:34 PM
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

INSERT INTO `bookings` (`id`, `user_id`, `hotel_name`, `location`, `price`, `booking_date`, `status`, `hotel_id`) VALUES
(1, 1, 'Hotel Sea Crown', 'Cox\'s Bazar', 4000, '2025-12-17', 'Confirmed', NULL),
(2, 1, 'Hotel Sweet Plaza', 'Chittagong', 2500, '2025-12-25', 'Confirmed', NULL),
(3, 1, 'Hotel Sea Moon', 'Cox\'s bazar', 70000, '2025-12-18', 'Confirmed', NULL),
(4, 1, 'Hotel Sea Crown', 'Cox\'s bazar', 3000, '2025-12-27', 'Confirmed', NULL),
(5, 1, 'Hotel Sea Crown', 'Cox\'s bazar', 3000, '2025-12-26', 'cancelled', NULL);

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
(4, 'Taj hotel', 'Dhaka', 3000, '', '1766507370_istockphoto-173587041-612x612.jpg', 'approved', 2, 1, 2),
(5, 'Hotel Le Meridien', 'Dhaka', 5000, 'Hotel Le Meridien', '1766507722_hotel_Le_meridian.jpg', 'approved', 2, 1, 2),
(6, 'Hotel Sea Crown', 'Cox\'s bazar', 3000, 'Hotel Sea Crown', '1766508216_sea_crown.jpg', 'approved', 2, 1, 2),
(7, 'Hotel Sea Moon', 'Cox\'s bazar', 4000, 'Hotel Sea Moon', '1766508248_sea moon.jpg', 'approved', 2, 1, 2),
(8, 'Hotel Sweet Plaza', 'Chittagong', 2500, 'Hotel Sweet Plaza', '1766509884_istockphoto-173587041-612x612.jpg', 'approved', 2, 1, 2),
(9, 'Hotel Sams Plaza', 'Cox\'s bazar', 2000, 'Hotel Sams Plaza', '1766510479_istockphoto-173587041-612x612.jpg', 'off', 2, 1, 2),
(10, 'Hotel Sea Moon', 'Cox\'s bazar', 70000, 'Hotel Sea Moon', '1766512258_istockphoto-173587041-612x612.jpg', 'approved', 2, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','owner','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'rahat', 'rahat@gmail.com', '$2y$10$/b7ZF55mWU.wnAHUBjZSiObScptkteT/VkCcKybKIZz9PSNDyjZqS', 'user'),
(2, 'Tanvir', 'owner@gmail.com', '$2y$10$fuu4L82juLKCmxjhj5td0OfOdhq4vp8cz.szT4UKRTbvNytqVoZ7.', 'owner'),
(5, 'admin', 'admin@gmail.com', 'password', 'admin'),
(6, '', 'owner1@gmail.com', '$2y$10$o5owVGOgCYQ3xz8NkSThxuMyDHw/ly789Fvcg47N0Hh1yTGP68B7K', 'owner');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
