-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 05, 2025 at 01:57 PM
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
-- Database: `barangay_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_accounts`
--

CREATE TABLE `admin_accounts` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','secretary','treasurer') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_accounts`
--

INSERT INTO `admin_accounts` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2y$10$GvqLbOm9IfLS5kBrPdcL/OWVfxTs9qrM13MrOjCiUFMmUSG605TPu', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `genre` enum('Music','Sports','Education','Health','Technology','Environment','Community','Culture','Art','Emergency') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `active_until` date NOT NULL,
  `occuring_at` date NOT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `allow_registrations` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','archived') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `genre`, `created_at`, `active_until`, `occuring_at`, `thumbnail`, `author`, `allow_registrations`, `status`) VALUES
(1, 'Emergency WOwoowowow!', 'Amongus content wowie zib zib, Amongus content wowie zib zib, ert ert ert', 'Emergency', '2025-06-05 18:55:48', '2025-06-06', '2025-06-05', 'uploads/user man.jpg', 'admin', 1, 'active'),
(2, 'Testing after announcement revamp', 'testing with no thumbnail upload.', 'Music', '2025-06-05 19:21:31', '2025-06-06', '2025-06-06', 'images/image2_BRB.jpg', 'admin', 0, 'active'),
(3, 'Test announcment 4', 'e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 ', 'Technology', '2025-06-05 19:25:56', '2025-06-13', '2025-06-13', 'images/image2_BRB.jpg', 'admin', 1, 'archived'),
(4, 'Test announcmenet 3', 'e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 e35 ', 'Technology', '2025-06-05 19:25:56', '2025-06-13', '2025-06-13', 'images/image2_BRB.jpg', 'admin', 0, 'active'),
(5, 'qwrqr', '515525', 'Health', '2025-06-05 19:55:28', '2025-06-04', '2025-06-07', 'images/image2_BRB.jpg', 'admin', 1, 'archived');

-- --------------------------------------------------------

--
-- Table structure for table `archived_reservations`
--

CREATE TABLE `archived_reservations` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `price_estimate` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `reason` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `archived_reservations`
--

INSERT INTO `archived_reservations` (`id`, `reservation_id`, `user_id`, `type`, `details`, `start_date`, `end_date`, `price_estimate`, `status`, `reason`, `created_at`, `archived_at`) VALUES
(1, 8, 6, 'facility', 'ConfeRoom', '2025-05-27', '2025-05-27', 1000.00, 'approved', 'Meeting', '2025-05-26 21:42:44', '2025-05-26 14:57:56'),
(2, 7, 5, 'item', 'Table', '2025-05-16', '2025-05-17', 6000.00, 'denied', 'Birthday', '2025-05-13 16:53:10', '2025-05-26 14:58:19'),
(3, 6, 5, 'facility', 'Basketball Court', '2025-05-14', '2025-05-15', 1000.00, 'denied', 'Meeting', '2025-05-13 16:08:19', '2025-05-26 14:58:22'),
(4, 5, 1, 'facility', 'Basketball Court', '2025-04-30', '2025-05-09', 1000.00, 'denied', 'LEBROOONNNNNNNNNNNNN JAMES', '2025-04-29 23:42:23', '2025-05-26 14:58:24'),
(5, 4, 1, 'facility', 'Basketball Court', '2025-04-30', '2025-04-30', 1000.00, 'denied', 'wrqwrwq', '2025-04-29 23:39:17', '2025-05-26 14:58:26'),
(6, 9, 6, 'facility', 'Playground', '2025-05-26', '2025-05-26', 1000.00, 'approved', 'Birthday', '2025-05-26 23:11:55', '2025-05-26 15:12:13'),
(7, 10, 6, 'facility', 'Small Meeting Room', '2025-05-27', '2025-05-27', 1000.00, 'denied', 'Meeting', '2025-05-26 23:25:15', '2025-05-26 15:26:27'),
(8, 11, 6, 'facility', 'Basketball Court', '2025-05-27', '2025-05-26', 1000.00, 'denied', 'Not available', '2025-05-26 23:31:11', '2025-05-26 15:34:21');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `phone`, `message`, `created_at`) VALUES
(1, 'olala', 'olala@test.com', '091234567', 'Hello im here', '2025-05-26 07:38:47'),
(2, 'olala', 'olala@test.com', '091234567', 'Hello im here', '2025-05-26 07:49:19'),
(3, 'olala', 'olala@test.com', '0987654321', 'dore mi res kore soso wo', '2025-05-26 07:50:01'),
(4, 'olala', 'olala@test.com', '0987654321', 'dssdsd', '2025-05-26 07:52:58');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `announcement_title` varchar(255) NOT NULL,
  `registered_at` datetime NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `user_id`, `announcement_title`, `registered_at`, `full_name`, `age`, `reason`, `note`) VALUES
(7, 5, 'yosh', '2025-05-18 17:29:55', 'Mikaela Pablo', 21, 'yes', ''),
(15, 5, 'oyaya', '2025-05-19 16:15:37', 'Mikaela Pablo', 21, 'Meet new people', 'yes'),
(18, 6, 'Lorem Ipsum', '2025-05-26 22:10:32', 'miyo pablo', 22, 'Volunteer', 'haha');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `sender_type` enum('admin','user') NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sent_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `sender_type`, `subject`, `message`, `is_read`, `created_at`, `sent_at`) VALUES
(1, 1, 1, 'admin', 'Payment method', 'qweqweqwe', 0, '2025-04-29 17:18:53', '2025-05-19 21:57:31'),
(2, 1, 1, 'admin', 'Invoice', 'testing', 0, '2025-04-29 17:32:19', '2025-05-19 21:57:31'),
(3, 4, 1, 'user', 'Invoicewqrqrww', 'rdsfdsffsf', 0, '2025-04-29 17:36:10', '2025-05-19 21:57:31'),
(10, 5, 1, 'admin', 'hello', 'gg', 0, '2025-05-19 15:17:18', '2025-05-19 23:17:18'),
(11, 1, 5, 'admin', 'hello', 'gg', 0, '2025-05-19 15:22:16', '2025-05-19 23:22:16'),
(12, 1, 5, 'admin', 'hello', 'hi', 0, '2025-05-19 15:29:10', '2025-05-19 23:29:10'),
(13, 5, 1, 'admin', 'hello', 'hello', 0, '2025-05-19 15:31:14', '2025-05-19 23:31:14'),
(14, 1, 5, 'admin', 'hello', 'oop', 0, '2025-05-19 15:55:16', '2025-05-19 23:55:16'),
(15, 1, 5, 'admin', 'hello', 'oop', 0, '2025-05-19 15:56:23', '2025-05-19 23:56:23'),
(16, 5, 1, 'admin', 'hello', 'ee', 0, '2025-05-19 15:56:42', '2025-05-19 23:56:42'),
(17, 1, 5, 'admin', 'hello', 'oop', 0, '2025-05-19 15:59:01', '2025-05-19 23:59:01'),
(18, 1, 5, 'admin', 'hello', 'oop', 0, '2025-05-19 15:59:13', '2025-05-19 23:59:13'),
(19, 1, 5, 'admin', 'Re: hello', 'yes', 0, '2025-05-19 15:59:37', '2025-05-19 23:59:37'),
(20, 1, 5, 'admin', 'hello', 'hello', 0, '2025-05-19 16:09:30', '2025-05-20 00:09:30'),
(21, 5, 1, 'admin', 'hello', 'hello', 0, '2025-05-19 16:12:09', '2025-05-20 00:12:09'),
(22, 5, 1, 'admin', 'hi', 'hi', 0, '2025-05-19 16:13:15', '2025-05-20 00:13:15'),
(23, 1, 5, 'admin', 'hello', 'jjj', 0, '2025-05-19 16:18:40', '2025-05-20 00:18:40'),
(24, 1, 5, 'admin', 'hello', 'hello', 0, '2025-05-19 16:18:53', '2025-05-20 00:18:53'),
(25, 1, 5, 'admin', 'hello', 'hello', 0, '2025-05-19 16:23:48', '2025-05-20 00:23:48'),
(26, 1, 5, 'admin', 'Re: hi', 'How are you my friend', 0, '2025-05-19 16:25:36', '2025-05-20 00:25:36'),
(27, 5, 1, 'admin', 'hello', 'Im good my friend', 0, '2025-05-19 16:26:00', '2025-05-20 00:26:00'),
(28, 1, 5, 'admin', 'hello', 'hello', 0, '2025-05-20 02:23:02', '2025-05-20 10:23:02'),
(29, 5, 1, 'admin', 'hello', 'hi', 0, '2025-05-20 02:23:32', '2025-05-20 10:23:32'),
(30, 1, 5, 'admin', 'hello', 'hi', 0, '2025-05-20 02:33:00', '2025-05-20 10:33:00'),
(31, 5, 1, 'admin', 'hello', 'hello im mika', 0, '2025-05-25 08:55:26', '2025-05-25 16:55:26'),
(32, 1, 5, 'admin', 'hello', 'hello im admin', 0, '2025-05-25 08:55:44', '2025-05-25 16:55:44'),
(33, 1, 5, 'admin', 'hello', 'hello im admin', 0, '2025-05-25 08:57:51', '2025-05-25 16:57:51'),
(34, 1, 5, 'admin', 'hello', 'hello im admin', 0, '2025-05-25 08:59:31', '2025-05-25 16:59:31'),
(35, 1, 5, 'admin', 'hello', 'kk', 0, '2025-05-25 09:01:11', '2025-05-25 17:01:11'),
(36, 1, 5, 'admin', 'hello', 'jjjj', 0, '2025-05-25 09:03:07', '2025-05-25 17:03:07'),
(37, 1, 5, 'admin', 'hello', 'p', 0, '2025-05-25 09:06:59', '2025-05-25 17:06:59'),
(38, 1, 5, 'admin', 'hello', 'iiii', 0, '2025-05-25 09:16:20', '2025-05-25 17:16:20'),
(39, 5, 1, 'admin', 'hello', '99', 0, '2025-05-25 09:16:45', '2025-05-25 17:16:45'),
(40, 6, 1, 'admin', 'hello', 'hello', 0, '2025-05-25 15:48:08', '2025-05-25 23:48:08'),
(41, 1, 6, 'admin', 'hello', 'hi', 0, '2025-05-25 15:48:34', '2025-05-25 23:48:34'),
(42, 1, 6, 'admin', 'Re: hello', 'hi', 0, '2025-05-26 14:00:19', '2025-05-26 22:00:19');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('facility','item') NOT NULL,
  `details` text NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price_estimate` decimal(10,2) NOT NULL,
  `reason` text NOT NULL,
  `additional_note` text DEFAULT NULL,
  `status` enum('pending','approved','denied') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `type`, `details`, `start_date`, `end_date`, `quantity`, `price_estimate`, `reason`, `additional_note`, `status`, `created_at`) VALUES
(1, 1, 'facility', 'Bulwagan', '2025-04-19 01:23:00', '2025-05-01 23:23:00', NULL, 1000.00, 'Testing reason', 'fjwefoiwefjeiwjffeewfwef', 'pending', '2025-04-29 15:24:04'),
(2, 1, 'item', 'Chair', '2025-04-25 14:24:00', '2025-05-15 02:27:00', 52, 10400.00, 'birthday hehe', 'ioqwrjowe', 'denied', '2025-04-29 15:25:03'),
(3, 1, 'item', 'Table', '2025-04-30 23:38:00', '2025-05-23 23:38:00', 26, 7800.00, 'lamesa', 'wow!', 'pending', '2025-04-29 15:38:50');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `blood_type` varchar(3) DEFAULT NULL,
  `sss_number` varchar(20) DEFAULT NULL,
  `pagibig_number` varchar(20) DEFAULT NULL,
  `tin_number` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `middle_name`, `last_name`, `email`, `contact_number`, `password`, `address`, `city`, `zip_code`, `birthday`, `blood_type`, `sss_number`, `pagibig_number`, `tin_number`, `status`) VALUES
(1, 'Test', 'Users', 'LastName', 'user@email.com', NULL, '$2y$10$18P2qMQ8K0OgAfMRwV1M8eVIcpi.NH.fITHXcQfPAWSZRmk4JZR/i', 'Road Testing Address', 'Quezon', '2424', '2025-04-09', 'A+', '', '', '', 'active'),
(3, 'Testsss', 'Casers4', 'Nameee', 'user3@email.com', '42242424242', '$2y$10$N.1p0qYQXyrcUlzljdwyQ.biIFSBV7eJVsgh65UFoRnIC/w3INPsK', 'sadjasiod address', 'Queaizon', '2424', '2025-04-19', 'A+', '2424', '4334346', '123456789123', 'active'),
(4, 'Test', 'User', 'Casess', 'user4@email.com', '34634646346', '$2y$10$GQ2a1cXGiAEaHldnI3Kv2eYeX3Zsj.Fx6aDZfZ5AQ50btzhhsL2J2', 'ewtwette', 'Awooga', '2535', '2025-04-15', 'A-', '22422424244444', '434434444444', '343442424242', 'active'),
(5, 'Mikaela', 'Calaunan', 'Pablo', 'mikaelapablo@gmail.com', '09123456789', '$2y$10$XQj9LREGsMbWxRP6EQm2xOOwHcRbzMKArnB8B4mqZt2cjMEgym04C', '1771 zenia street', 'caloocan', '1403', '2003-08-26', '', '', '', '', 'active'),
(6, 'Miyo Mikee', 'Sasaki', 'Pablo', 'Miyosasaki@gmail.com', '09123456799', '$2y$10$Xd81FleTGLTviUpoQX6g..IG6Shv18G9whiTBGEbTAHBAfdbK41jy', '1771 zenia street', 'caloocan', '1403', '2021-09-11', 'O+', '', '', '', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_accounts`
--
ALTER TABLE `admin_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `archived_reservations`
--
ALTER TABLE `archived_reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `admin_accounts`
--
ALTER TABLE `admin_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `archived_reservations`
--
ALTER TABLE `archived_reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `archived_reservations`
--
ALTER TABLE `archived_reservations`
  ADD CONSTRAINT `archived_reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
