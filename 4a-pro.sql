-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2024 at 11:33 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `4a-pro`
--

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `u_id` int(11) NOT NULL,
  `ui_id` int(11) NOT NULL,
  `p_id` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `comment` varchar(1000) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`u_id`, `ui_id`, `p_id`, `id`, `comment`, `created_at`) VALUES
(143, 120, 217, 56, 'Gwapaha Jud Oyy', '2024-12-06 23:31:55'),
(145, 122, 217, 57, 'Thank You moshie ko hehehe', '2024-12-06 23:32:30'),
(145, 122, 199, 58, 'Nice Song', '2024-12-06 23:34:43'),
(143, 120, 223, 59, 'Maayo Noun', '2024-12-06 23:37:08'),
(143, 120, 199, 60, 'Thank You', '2024-12-06 23:51:24'),
(145, 122, 223, 61, 'hahaha', '2024-12-08 10:32:17');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `u_id` int(11) DEFAULT NULL,
  `ui_id` int(11) DEFAULT NULL,
  `post_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`u_id`, `ui_id`, `post_id`, `content`, `file_name`, `file_type`, `created_at`) VALUES
(143, 120, 199, 'Ulan Sa Panalangin Lyrics', '1733285600_y2mate.com - ULAN SA PANALANGIN Bisaya Christian Song with Lyrics.mp3', 'audio/mpeg', '2024-12-04 04:13:20'),
(149, 126, 206, 'Hello World', '', '', '2024-12-04 15:27:01'),
(148, 125, 214, 'Hello Breatheren Brothers and Sisters In Christ', '', '', '2024-12-05 08:30:26'),
(143, 120, 215, 'Phyl Jareth Lamoste Certificate\r\n\r\n', '1733405639_Phyl Jareth A. Lamoste.pdf', 'application/pdf', '2024-12-05 13:33:59'),
(145, 122, 216, 'Sunday Service Para Ugma', '1733406710_11-30-24.pptx', 'application/vnd.openxmlformats-officedocument.pres', '2024-12-05 13:51:50'),
(145, 122, 217, 'Its Me', '1733407099_430878108_1097778114802965_8821700676811664314_n.jpg', 'image/jpeg', '2024-12-05 13:58:19'),
(145, 122, 223, 'Yeey Wala nay errors\r\n', '', '', '2024-12-06 23:36:56');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(800) NOT NULL,
  `role` int(11) NOT NULL,
  `flag` int(11) NOT NULL,
  `reset_code` varchar(12) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `role`, `flag`, `reset_code`, `reset_expires`) VALUES
(143, 'cuyosglorious@gmail.com', '$2y$10$uoYKVVELMzJH.CSTOJsGA.bGiCmyVJfTqzuFEqO9XL2pcOjVRnjF6', 1, 1, NULL, NULL),
(145, 'marjoriesagadsad2019@gmail.com', '$2y$10$pl2MAOXhDGZDgNycOjBHkORIqU.deAAxdSu.C4V9Y57bgNsC89cWO', 1, 1, NULL, NULL),
(148, 'jirahgracecuyos@gmail.com', '$2y$10$H1eYeiNnunSK1ItmuRcb5.DLfAgrIjZn2jRnN4hM48/NPLTyFDNfy', 1, 1, NULL, NULL),
(149, 'rehjicy@gmail.com', '$2y$10$YcrT4Jne5XYn9CHC3vu7cuIgR5uMivbFpza00CyOQKKRbCjRtRnhC', 1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

CREATE TABLE `user_info` (
  `u_id` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `gender` varchar(250) NOT NULL,
  `birthday` date DEFAULT NULL,
  `address` varchar(250) NOT NULL,
  `status` int(11) NOT NULL,
  `created` date NOT NULL,
  `updated` date NOT NULL,
  `image_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`u_id`, `id`, `name`, `gender`, `birthday`, `address`, `status`, `created`, `updated`, `image_name`) VALUES
(143, 120, 'Glorious Hope Guillen Cuyos', 'Male', '2002-07-25', 'Purok Shooting Star Babag II Lapu-Lapu City', 1, '2024-12-04', '2024-12-07', '143_1733285570_378398423_1077007263708298_8678816927246300015_n.jpg'),
(145, 122, 'Marjorie Lamoste Sagadsad', 'Female', '1999-07-16', 'Pool Bankal Lapu Lapu City', 1, '2024-12-04', '2024-12-05', '145_1733308311_430878108_1097778114802965_8821700676811664314_n.jpg'),
(148, 125, 'Jirah Grace Guillen Cuyos', 'Male', '1996-10-01', 'Babag II Lapu-Lapu City', 1, '2024-12-04', '2024-12-05', '148_1733387496_grad_pic.jpg'),
(149, 126, 'Jireh Guillen Cuyos', 'Male', '1989-11-12', 'Babag II Lapu-Lapu City', 1, '2024-12-04', '2024-12-04', '149_1733326006_29790393_1728412197201755_1364142211094449735_n.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=224;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
