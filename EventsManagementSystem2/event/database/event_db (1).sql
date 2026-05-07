-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2025 at 04:46 PM
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
-- Database: `event_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audience`
--

CREATE TABLE `audience` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `contact` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `event_id` int(30) NOT NULL,
  `payment_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0= pending, 1 =Paid',
  `attendance_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1= present',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = for verification,  1 = confirmed,2= declined',
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audience`
--

INSERT INTO `audience` (`id`, `name`, `contact`, `email`, `address`, `event_id`, `payment_status`, `attendance_status`, `status`, `date_created`) VALUES
(2, 'Peter Kalule', '0772551782', 'peterk@gmail.com', 'Kawempe', 1, 1, 0, 1, '0000-00-00 00:00:00'),
(3, 'Moses Gidagui', '0789613828', 'mosesgidagui@gmail.com', 'Bunga', 1, 0, 0, 0, '2025-04-20 16:55:13');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `venue_id` int(30) NOT NULL,
  `event` text NOT NULL,
  `description` text NOT NULL,
  `schedule` datetime NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Public, 2-Private',
  `audience_capacity` int(30) NOT NULL,
  `payment_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Free,payable',
  `amount` double NOT NULL DEFAULT 0,
  `banner` text NOT NULL,
  `date_created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `venue_id`, `event`, `description`, `schedule`, `type`, `audience_capacity`, `payment_type`, `amount`, `banner`, `date_created`) VALUES
(1, 1, 'Cultural Day Celebration', 'A day to celebrate the rich cultural heritage of Uganda through music, dance, and traditional attire.', '2025-06-15 10:00:00', 1, 500, 1, 0, 'admin/assets/uploads/cultural_day.jpg', '2023-04-01 12:00:00'),
(2, 2, 'Annual Career Fair', 'An opportunity for students to meet potential employers and explore career opportunities.', '2025-08-20 09:00:00', 1, 1000, 1, 0, 'uploads/career_fair.jpg', '2023-05-10 14:30:00'),
(3, 3, 'Science and Innovation Symposium', 'A platform for students and researchers to showcase innovative projects in science and technology.', '2025-09-10 13:00:00', 2, 300, 2, 5000, 'uploads/science_symposium.jpg', '2023-06-05 08:45:00'),
(4, 4, 'Annual Music Festival', 'A celebration of music with performances from local and international artists.', '2025-11-05 18:00:00', 1, 800, 2, 10000, 'uploads/music_festival.jpg', '2023-07-20 11:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `email` varchar(200) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `cover_img` text NOT NULL,
  `about_content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `system_settings`
CHANGE `cover_img` `cover_image` VARCHAR(255) NOT NULL;
DESCRIBE system_settings;



--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `name`, `email`, `contact`, `cover_img`, `about_content`) VALUES
(1, 'Event Management System', 'info@ugandaevents.com', '0789123456', 'uploads/cover_image.jpg', 'Welcome to the Uganda University Event Management System. This platform is designed to help universities in Uganda organize and manage events efficiently.');
UPDATE `system_settings`
SET `name` = 'UniEvents Hub',
    `email` = 'info@event.com',
    `contact` = '0752356989',
    `cover_img` = 'event/admin/assets/uploads/login_image.jpg',
    `about_content` = 'UniEvents Hub is a centralized platform designed to simplify the management of university events. It enables organizers to easily create, schedule, and promote events, while giving students a convenient way to discover, register, and RSVP.

Our mission is to enhance campus engagement by making event participation seamless, organized, and accessible for everyone within the university community.'
WHERE `id` = 1;

UPDATE `system_settings`
SET `cover_image` = 'admin/assets/uploads/cover_image'
WHERE `id` = 1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `username` varchar(200) NOT NULL,
  `password` text NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 2 COMMENT '1=Admin,2=Staff'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `type`) VALUES
(1, 'Administrator', 'admin@event.com', 'f09697f61d3f58ed3f272f888086beca', 1),
(2, 'Winnie Student', 'winnie@stud.event.com', 'ad6a280417a0f533d8b670c61667e1a0', 3),
(3, 'Pamela Student', 'pamela@stud.event.com', 'ad6a280417a0f533d8b670c61667e1a0', 3);
-- --------------------------------------------------------

--
-- Table structure for table `venue`
--

CREATE TABLE `venue` (
  `id` int(11) NOT NULL,
  `venue` text NOT NULL,
  `address` text NOT NULL,
  `description` text NOT NULL,
  `rate` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venue`
--

INSERT INTO `venue` (`id`, `venue`, `address`, `description`, `rate`) VALUES
(1, 'Kampala National Theatre', 'Kampala, Uganda', 'A historic venue for cultural performances and events.', 50000),
(2, 'Makerere University Main Hall', 'Makerere University, Kampala', 'A spacious hall suitable for conferences and seminars.', 30000),
(3, 'Gulu Sports Stadium', 'Gulu, Uganda', 'A large outdoor stadium ideal for sports events and concerts.', 80000);

-- --------------------------------------------------------

--
-- Table structure for table `venue_booking`
--

CREATE TABLE `venue_booking` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `address` text NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(100) NOT NULL,
  `venue_id` int(30) NOT NULL,
  `duration` varchar(100) NOT NULL,
  `datetime` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0-for verification,1=confirmed,2=canceled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venue_booking`
--

INSERT INTO `venue_booking` (`id`, `name`, `address`, `email`, `contact`, `venue_id`, `duration`, `datetime`, `status`) VALUES
(1, 'Pamela Nakanwagi', 'Kampala, Uganda', 'pamela@stud.event.com', '0789123456', 1, '3 hours', '2023-12-15 10:00:00', 0),
(2, 'Kalule Peter', 'Entebbe, Uganda', 'kalulepeter@gmail.com', '0777987654', 2, '4 hours', '2023-12-20 14:00:00', 1),
(3, 'Moses Gidagui', 'Gulu, Uganda', 'mosesgidagui@gmail.com', '0712345678', 3, '6 hours', '2024-01-10 18:00:00', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audience`
--
ALTER TABLE `audience`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `venue`
--
ALTER TABLE `venue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `venue_booking`
--
ALTER TABLE `venue_booking`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audience`
--
ALTER TABLE `audience`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `venue`
--
ALTER TABLE `venue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `venue_booking`
--
ALTER TABLE `venue_booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
DESCRIBE venue;
DESCRIBE users;