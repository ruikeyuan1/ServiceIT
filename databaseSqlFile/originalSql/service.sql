-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 12, 2023 at 03:14 PM
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
-- Database: `serviceP`
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
(3, 'Ameli', 'Lia', '$2y$10$kILA4d2c2CIsNm/Oz8G.2OMfsWp0g6QLOFyeODT6o5WgZYJeK6ofS');

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
(1, 'PresentationUnitestWorkshop.pdf', 1),
(2, 'projeOnintemplate.pdf', 2),
(3, 'Foscolo.pdf', 3),
(6, '', 4);

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
(1, 1, 0, 'Done', '[value-5]', 'phone_repair'),
(2, 2, 2, 'Done', '[value-5]', 'phone_repair'),
(3, 2, 2, 'Done', '[value-5]', 'laptop_repair'),
(4, 2, 2, 'InProgress', '[value-5]', 'phone_repair'),
(5, 1, 2, 'Done', '[value-5]', 'laptop_repair'),
(6, 1, 1, 'Done', '[value-5]', 'laptop_repair'),
(7, 1, 2, 'Done', '[value-5]', 'laptop_repair'),
(8, 3, 0, 'InProgress', 'check', 'phone repair'),
(9, 3, 0, 'InProgress', 'ooops', 'software_service'),
(10, 3, 0, 'InProgress', 'oke', 'hosting_service'),
(11, 3, 0, 'InProgress', 'final check', 'software_service'),
(12, 4, 0, 'InProgress', 'test data\r\n', 'phone_repair');

-- --------------------------------------------------------

--
-- Table structure for table `service_ticket`
--

CREATE TABLE `service_ticket` (
  `id` int(5) NOT NULL,
  `user_id` int(5) DEFAULT NULL,
  `admin_id` int(5) DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `service_type` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `service_ticket`
--

INSERT INTO `service_ticket` (`id`, `user_id`, `admin_id`, `status`, `description`, `service_type`) VALUES
(1, 1, 1, 'InProgress', '[value-5]', 'phone_repair'),
(2, 1, 1, 'InProgress', '[value-5]', 'laptop_repair'),
(3, 1, 0, 'InProgress', '[value-5]', 'laptop_repair'),
(5, 2, 2, 'InProgress', '[value-5]', 'laptop_repair'),
(30, 3, 0, 'InProgress', 'lia', 'phone'),
(31, 3, 0, 'Done', 'lia', 'phone'),
(32, 3, 0, 'Done', 'lia', 'software_service'),
(33, 3, 0, 'InProgress', 'hmmm', 'software_service'),
(34, 4, 0, 'InProgress', 'test ticket', 'phone_repair');

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
(3, 'Liaafernando', 'Lia', '$2y$10$d0d8jMFZb0rO1yHwnnPVLuWdtCNautnnhdVKDtHgrtLyV5xApmWTS', 'liafernando@gmail.com'),
(4, 'Abu', 'Abu', '$2y$10$baL.SHpjLqyy5IhARHbny.Y6QBllwQDPQ2T3.M/.rhGWt/Xdnb.fK', 'Abu@abu');

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
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `contract`
--
ALTER TABLE `contract`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `service_request`
--
ALTER TABLE `service_request`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `service_ticket`
--
ALTER TABLE `service_ticket`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
