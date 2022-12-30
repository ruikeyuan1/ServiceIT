-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 30, 2022 at 12:37 AM
-- Server version: 5.7.34
-- PHP Version: 8.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `service`
--

-- --------------------------------------------------------

--
-- Table structure for table `administrator`
--

CREATE TABLE `administrator` (
  `id` int(5) NOT NULL,
  `username` varchar(25) CHARACTER SET utf8mb4 DEFAULT NULL,
  `name` varchar(25) CHARACTER SET utf8mb4 DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `administrator`
--

INSERT INTO `administrator` (`id`, `username`, `name`, `password`) VALUES
(0, 'data', 'Dada', '[value-4]'),
(1, 'abu', 'Abu', '[value-4]'),
(2, 'titi', 'titi', 'titi1234'),
(3, 'keketty2', 'tjsahdjas', '$2y$10$AxrFhWQp7swTdxqvuV0OqeNZe4n3h36UMoDwtFO2gJt32/jocMrdG'),
(4, 'polina', 'polina', '$2y$10$WACC03U8VoQAk8G7GtKIueuE0qfb9AKNG6yw.dgNQsY.9Hs/GPjHm'),
(5, 'kekeYuan', 'dad', '$2y$10$UIjEB4qlbOEkx/r3xV8BpeIBUPnXuaMcFO8GeU0wLrpCb/ZAEsLvi');

-- --------------------------------------------------------

--
-- Table structure for table `contract`
--

CREATE TABLE `contract` (
  `id` int(5) NOT NULL,
  `file_path` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `user_id` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `contract`
--

INSERT INTO `contract` (`id`, `file_path`, `user_id`) VALUES
(1, 'cover_letter_draft_Ruike_Yuan.pdf', 1),
(2, 'CV_-_Ruike_Yuan.pdf', 2),
(3, 'CoursesToBeF.pdf', 3);

-- --------------------------------------------------------

--
-- Table structure for table `service_request`
--

CREATE TABLE `service_request` (
  `id` int(5) NOT NULL,
  `user_id` int(5) NOT NULL,
  `admin_id` int(5) NOT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `service_type` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `service_request`
--

INSERT INTO `service_request` (`id`, `user_id`, `admin_id`, `status`, `description`, `service_type`) VALUES
(1, 1, 2, 'Done', '[value-5]', 'phone_repair'),
(2, 2, 2, 'Done', '[value-5]', 'phone_repair'),
(3, 2, 2, 'Done', '[value-5]', 'laptop_repair'),
(4, 2, 2, 'Done', '[value-5]', 'phone_repair'),
(5, 1, 0, 'InProgress', '[value-5]', 'laptop_repair'),
(6, 1, 2, 'Done', '[value-5]', 'laptop_repair'),
(7, 1, 2, 'InProgress', '[value-5]', 'laptop_repair');

-- --------------------------------------------------------

--
-- Table structure for table `service_ticket`
--

CREATE TABLE `service_ticket` (
  `id` int(5) NOT NULL,
  `user_id` int(5) NOT NULL,
  `admin_id` int(5) NOT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `service_type` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `service_ticket`
--

INSERT INTO `service_ticket` (`id`, `user_id`, `admin_id`, `status`, `description`, `service_type`) VALUES
(1, 1, 3, 'Done', '[value-5]', 'phone_repair'),
(2, 1, 0, 'Done', '[value-5]', 'laptop_repair'),
(3, 3, 2, 'InProgress', '[value-5]', 'laptop_repair'),
(5, 3, 3, 'Done', '[value-5]', 'laptop_repair');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(5) NOT NULL,
  `name` varchar(25) CHARACTER SET utf8mb4 DEFAULT NULL,
  `username` varchar(25) CHARACTER SET utf8mb4 DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `username`, `password`, `email`) VALUES
(1, 'Ruike Yuan', 'myYuan', 'ruike1234', 'keketty@163.com'),
(2, 'Kaiser Aftab', 'myAdtab', 'kaiser1234', 'kaiser@163.com'),
(3, 'RuikeYuan', 'keketty', '$2y$10$u53CLOuodxenT/9D2YUv9eAMRgaretpSGRr13D2mYsTsWIwgs63Qu', 'yuanruike2002@outlook.com'),
(4, 'Ruike', 'keketty1', '$2y$10$G7ojwo4XnD3050zTDxB8ROmjKkCdmZLu/WGKDtFBpA264/fhXbHom', 'Di'),
(5, 'polina', 'polina', '$2y$10$pkmRnBo8wtLZLkgmcc/NQ.sQtrRb6nqNmH8IXP1Bn2mcGClQNJi6u', 'yuanruike2002@outlook.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administrator`
--
ALTER TABLE `administrator`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contract`
--
ALTER TABLE `contract`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userContract` (`user_id`);

--
-- Indexes for table `service_request`
--
ALTER TABLE `service_request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requestUser` (`user_id`),
  ADD KEY `requestAdmin` (`admin_id`);

--
-- Indexes for table `service_ticket`
--
ALTER TABLE `service_ticket`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticketUser` (`user_id`),
  ADD KEY `ticketAdmin` (`admin_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `administrator`
--
ALTER TABLE `administrator`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contract`
--
ALTER TABLE `contract`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `service_request`
--
ALTER TABLE `service_request`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `service_ticket`
--
ALTER TABLE `service_ticket`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contract`
--
ALTER TABLE `contract`
  ADD CONSTRAINT `userContract` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `service_request`
--
ALTER TABLE `service_request`
  ADD CONSTRAINT `requestAdmin` FOREIGN KEY (`admin_id`) REFERENCES `administrator` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `requestUser` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `service_ticket`
--
ALTER TABLE `service_ticket`
  ADD CONSTRAINT `ticketAdmin` FOREIGN KEY (`admin_id`) REFERENCES `administrator` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ticketUser` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
