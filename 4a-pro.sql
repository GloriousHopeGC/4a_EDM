-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 02, 2024 at 11:38 AM
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
(134, 111, 79, 'My Capela Gameplay, Enjoy', '1732095746_AQNWrChPQiIZJWuVIlbjCX_PyACa8P2rTDAV-pl8CIh-5JVdy7ASg0pw5YvSBjeX4enW2ffDIcfGB-Dn4KroMhsm.mp4', 'video/mp4', '2024-11-20 09:42:26'),
(136, 113, 80, 'I got Myself In Dunk City Dynasty!! &lt;3', '1732096309_Screenrecording_20240923_001520.mp4', 'video/mp4', '2024-11-20 09:51:49'),
(136, 113, 83, 'Slam Dunk Opening Anime', 'slamdunk-theme-song-lyrics.mp3', 'audio/mpeg', '2024-11-20 10:10:37'),
(137, 114, 102, 'Slam Dunk Opening Enjoy Watching', '1732107678_utomp3.com - Slam Dunk  Opening 1 HD.mp4', 'video/mp4', '2024-11-20 13:01:18'),
(135, 112, 114, 'Slam Dunk Cover', '1732259666_AQMpNk4TXsohG2ykTgAVGqOE7RPX5Gn2MZ_DTNdV0JPPSJuNpPX6cnhUgsJ_dzeKijQ1qXU1kQe-9XzbgsQUL9vs.mp4', 'video/mp4', '2024-11-22 07:14:26'),
(32, 62, 120, 'Slam Dunk Cover', '1732522055_AQMpNk4TXsohG2ykTgAVGqOE7RPX5Gn2MZ_DTNdV0JPPSJuNpPX6cnhUgsJ_dzeKijQ1qXU1kQe-9XzbgsQUL9vs.mp4', 'video/mp4', '2024-11-25 08:07:35');

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
(32, 'marjoriesagadsad2019@gmail.com', '$2y$10$MRqMEuYt/yhWB/cjCEc/MusrIy3bVnkYQ4YCjI6gZIIHf.sslYly6', 1, 1, '5bc935a8dbd6', '2024-12-02 18:24:47'),
(127, 'chordcuyos@gmail.com', '$2y$10$Sh6FHcu8JjGm4z7Yry3UCeXBZja50VdTByXE1/5Dq46w8LP8cL.du', 1, 1, NULL, NULL),
(128, 'cuyoshope@gmail.com', '$2y$10$AjMbrfsv.Em3QseVkptQaeRSmEsxgDT4Yy62hv45bmX.861hdkqHe', 2, 1, '5b910040087e', '2024-12-02 18:36:18'),
(134, 'ezzmoneysniper35@gmail.com', '$2y$10$z8WBJZzDRp18tKWBYuGTx.EThXkVc/9Ukx7wPj2FurWlyHckO8MYW', 1, 0, NULL, NULL),
(135, 'cuyosglorious@gmail.com', '$2y$10$x1XH1NCXc4nLpsa.is94se2IQ4z3pjBoxa7G0nTCJp1szTqqPW9zm', 1, 0, '40775a992563', '2024-12-02 18:32:02'),
(136, 'jimmybutler@gmail.com', '$2y$10$Z8vrHMqP0EyiH8KBwqr7buAnD1BibFWNqHR5KkUl80wX1a7kbAmse', 1, 1, NULL, NULL),
(137, 'jirahgracecuyos@gmail.com', '$2y$10$9u7hoT/uRGINBPzjEbfuJuMaPPmO7Ir8jmbdwDdb7kKX58b6eFpaK', 1, 1, NULL, NULL),
(138, 'rehjicy@gmail.com', '$2y$10$eAf/kpfAgwz7qY2.beWyfeVkJrm3kweoX4m3.kN/lf0xb3i1Qjo3e', 1, 1, NULL, NULL),
(139, 'LBJ@gmail.com', '$2y$10$YK2FCh4GEwHfHK4nh7T14eryLfNu43Qd9q3943hwBLkDzhAMR.GdW', 1, 1, 'e83c50e231bc', '2024-12-02 17:23:11');

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
(32, 62, 'Marjorie Lamoste Sagadsad', 'Female', '1998-07-16', 'Pool Bankal Lapu Lapu City', 1, '2024-10-30', '2024-12-02', '32_1733108268_430878108_1097778114802965_8821700676811664314_n.jpg'),
(127, 104, 'Azariah Chord Cuyos', 'Male', '2018-07-17', 'Babag II Lapu-Lapu City', 1, '2024-11-15', '2024-11-16', '127_1731669687_chordova.jpg'),
(128, 105, 'Glorious Hope G. Cuyos', 'Male', '2002-07-25', 'Babag II Lapu-Lapu City', 1, '2024-11-15', '2024-12-02', '128_1733104211_378398423_1077007263708298_8678816927246300015_n.jpg'),
(134, 111, 'Kevin Wayne Durant', 'Male', '1988-09-29', 'Washington Dc', 1, '2024-11-15', '2024-11-20', '134_1732095681_i (1).png'),
(135, 112, 'Glorious Hope Guillen Cuyos', 'Male', '2002-07-25', 'Babag II Lapu-Lapu City', 1, '2024-11-16', '2024-11-20', '135_1732104906_378398423_1077007263708298_8678816927246300015_n.jpg'),
(136, 113, 'Jimmy Butler III', 'Male', '1989-09-14', 'Houston, Texas, United States', 1, '2024-11-20', '2024-11-22', '136_1732096060_jimmy-butler-net-worth-3-scaled.jpeg'),
(137, 114, 'Jirah Grace Guillen Cuyos', 'Female', '1996-10-01', 'Babag II Lapu-Lapu City', 1, '2024-11-20', '2024-11-20', '137_1732106676_grad_pic.jpg'),
(138, 115, 'Jireh Guillen Cuyos', 'Male', '1988-11-12', 'Babag II Lapu-Lapu City', 1, '2024-11-20', '2024-11-20', 'male.jpg'),
(139, 116, 'LeBron James degamo', 'Male', '2024-12-01', 'California Usa', 1, '2024-12-02', '2024-12-02', 'male.jpg');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
