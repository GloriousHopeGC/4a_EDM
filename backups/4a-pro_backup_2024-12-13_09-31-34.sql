-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: 4a-pro
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `comment`
--

DROP TABLE IF EXISTS `comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment` (
  `u_id` int(11) NOT NULL,
  `ui_id` int(11) NOT NULL,
  `p_id` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment` varchar(1000) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment`
--

LOCK TABLES `comment` WRITE;
/*!40000 ALTER TABLE `comment` DISABLE KEYS */;
INSERT INTO `comment` VALUES (143,120,217,56,'Gwapaha Jud Oyy','2024-12-06 23:31:55'),(145,122,217,57,'Thank You moshie ko hehehe','2024-12-06 23:32:30'),(145,122,199,58,'Nice Song','2024-12-06 23:34:43'),(143,120,223,59,'Maayo Noun','2024-12-06 23:37:08'),(143,120,199,60,'Thank You','2024-12-06 23:51:24'),(145,122,223,61,'hahaha','2024-12-08 10:32:17'),(145,122,223,62,'Wow','2024-12-13 02:47:03');
/*!40000 ALTER TABLE `comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `u_id` int(11) DEFAULT NULL,
  `ui_id` int(11) DEFAULT NULL,
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`post_id`)
) ENGINE=InnoDB AUTO_INCREMENT=224 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (143,120,199,'Ulan Sa Panalangin Lyrics','1733285600_y2mate.com - ULAN SA PANALANGIN Bisaya Christian Song with Lyrics.mp3','audio/mpeg','2024-12-04 04:13:20'),(149,126,206,'Hello World','','','2024-12-04 15:27:01'),(148,125,214,'Hello Breatheren Brothers and Sisters In Christ','','','2024-12-05 08:30:26'),(143,120,215,'Phyl Jareth Lamoste Certificate\r\n\r\n','1733405639_Phyl Jareth A. Lamoste.pdf','application/pdf','2024-12-05 13:33:59'),(145,122,216,'Sunday Service Para Ugma','1733406710_11-30-24.pptx','application/vnd.openxmlformats-officedocument.pres','2024-12-05 13:51:50'),(145,122,217,'Its Me','1733407099_430878108_1097778114802965_8821700676811664314_n.jpg','image/jpeg','2024-12-05 13:58:19'),(145,122,223,'Yeey Wala nay errors\r\n','','','2024-12-06 23:36:56');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(200) NOT NULL,
  `password` varchar(800) NOT NULL,
  `role` int(11) NOT NULL,
  `flag` int(11) NOT NULL,
  `reset_code` varchar(12) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=152 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (143,'cuyosglorious@gmail.com','$2y$10$uoYKVVELMzJH.CSTOJsGA.bGiCmyVJfTqzuFEqO9XL2pcOjVRnjF6',1,1,NULL,NULL),(145,'marjoriesagadsad2019@gmail.com','$2y$10$pl2MAOXhDGZDgNycOjBHkORIqU.deAAxdSu.C4V9Y57bgNsC89cWO',1,1,NULL,NULL),(148,'jirahgracecuyos@gmail.com','$2y$10$H1eYeiNnunSK1ItmuRcb5.DLfAgrIjZn2jRnN4hM48/NPLTyFDNfy',1,1,NULL,NULL),(149,'rehjicy@gmail.com','$2y$10$YcrT4Jne5XYn9CHC3vu7cuIgR5uMivbFpza00CyOQKKRbCjRtRnhC',1,1,NULL,NULL),(150,'useragcowc@gmail.com','$2y$10$otxv8o6UuuOukQhQaoKnM.JO1/hUf3KTI1L4Sm78dehGjXz5nbYbm',2,1,NULL,NULL),(151,'nenita@gmail.com','$2y$10$7Umvv9g.21a4JhqVbODZeeqNkaMF1mEmO1d2aUKr8.Meskz8OKoIi',1,1,NULL,NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_info`
--

DROP TABLE IF EXISTS `user_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_info` (
  `u_id` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `gender` varchar(250) NOT NULL,
  `birthday` date DEFAULT NULL,
  `address` varchar(250) NOT NULL,
  `status` int(11) NOT NULL,
  `created` date NOT NULL,
  `updated` date NOT NULL,
  `image_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_info`
--

LOCK TABLES `user_info` WRITE;
/*!40000 ALTER TABLE `user_info` DISABLE KEYS */;
INSERT INTO `user_info` VALUES (143,120,'Glorious Hope Guillen Cuyos','Male','2002-07-25','Purok Shooting Star Babag II Lapu-Lapu City',1,'2024-12-04','2024-12-07','143_1733285570_378398423_1077007263708298_8678816927246300015_n.jpg'),(145,122,'Marjorie Lamoste Sagadsad','Female','1999-07-16','Pool Bankal Lapu Lapu City',1,'2024-12-04','2024-12-05','145_1733308311_430878108_1097778114802965_8821700676811664314_n.jpg'),(148,125,'Jirah Grace Guillen Cuyos','Male','1996-10-01','Babag II Lapu-Lapu City',1,'2024-12-04','2024-12-05','148_1733387496_grad_pic.jpg'),(149,126,'Jireh Guillen Cuyos','Male','1989-11-12','Babag II Lapu-Lapu City',1,'2024-12-04','2024-12-04','149_1733326006_29790393_1728412197201755_1364142211094449735_n.jpg'),(150,127,'Administrator','Unknown','2024-07-25','Babag II Lapu-Lapu City',1,'2024-12-13','2024-12-13','unknown.png'),(151,128,'Nenita Guillen Cuyos','Female','1967-11-04','Babag II Lapu-Lapu City',1,'2024-12-13','2024-12-13','151_1734061052_174838667_596265338441350_1046203362860273174_n.jpg');
/*!40000 ALTER TABLE `user_info` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-12-13 16:31:35
