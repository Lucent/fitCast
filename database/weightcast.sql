-- MySQL dump 10.13  Distrib 5.5.22, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: weightcast
-- ------------------------------------------------------
-- Server version	5.5.22-0ubuntu1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `calories`
--

DROP TABLE IF EXISTS `calories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calories` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `food` smallint(6) unsigned DEFAULT NULL,
  `exercise` smallint(6) unsigned DEFAULT NULL,
  `measured` float unsigned DEFAULT NULL,
  UNIQUE KEY `id` (`id`,`date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calories`
--

LOCK TABLES `calories` WRITE;
/*!40000 ALTER TABLE `calories` DISABLE KEYS */;
INSERT INTO `calories` VALUES (1,'2012-05-20',NULL,NULL,NULL);
INSERT INTO `calories` VALUES (1,'2012-05-19',NULL,NULL,NULL);
INSERT INTO `calories` VALUES (1,'2012-05-25',NULL,NULL,NULL);
INSERT INTO `calories` VALUES (1,'2012-05-24',NULL,NULL,NULL);
INSERT INTO `calories` VALUES (1,'2012-05-18',NULL,NULL,NULL);
INSERT INTO `calories` VALUES (1,'2012-06-07',21,NULL,NULL);
INSERT INTO `calories` VALUES (1,'2012-06-06',564,NULL,NULL);
INSERT INTO `calories` VALUES (1,'2012-06-05',1600,NULL,NULL);
INSERT INTO `calories` VALUES (1,'2012-05-22',NULL,NULL,NULL);
INSERT INTO `calories` VALUES (1,'2012-05-23',NULL,NULL,NULL);
INSERT INTO `calories` VALUES (1,'2012-06-04',456,NULL,NULL);
INSERT INTO `calories` VALUES (1,'2012-06-03',465,NULL,NULL);
INSERT INTO `calories` VALUES (8,'2012-05-22',1695,NULL,165);
INSERT INTO `calories` VALUES (8,'2012-05-23',1584,NULL,NULL);
INSERT INTO `calories` VALUES (8,'2012-05-24',1524,229,NULL);
INSERT INTO `calories` VALUES (8,'2012-05-25',2124,286,NULL);
INSERT INTO `calories` VALUES (8,'2012-05-26',2596,NULL,NULL);
INSERT INTO `calories` VALUES (8,'2012-05-27',2060,NULL,NULL);
INSERT INTO `calories` VALUES (8,'2012-05-28',2737,NULL,NULL);
INSERT INTO `calories` VALUES (8,'2012-05-29',2087,NULL,NULL);
INSERT INTO `calories` VALUES (8,'2012-05-30',2356,NULL,NULL);
INSERT INTO `calories` VALUES (8,'2012-05-31',2516,NULL,NULL);
INSERT INTO `calories` VALUES (8,'2012-06-01',1989,NULL,163.8);
INSERT INTO `calories` VALUES (1,'2012-06-02',300,1,NULL);
INSERT INTO `calories` VALUES (1,'2012-06-01',900,31,118);
INSERT INTO `calories` VALUES (1,'2012-05-31',NULL,1200,NULL);
INSERT INTO `calories` VALUES (1,'2012-05-30',1600,654,NULL);
INSERT INTO `calories` VALUES (1,'2012-05-29',1500,654,120);
INSERT INTO `calories` VALUES (1,'2012-05-28',1200,654,120);
INSERT INTO `calories` VALUES (1,'2012-05-21',NULL,NULL,NULL);
INSERT INTO `calories` VALUES (1,'2012-05-26',100,NULL,NULL);
INSERT INTO `calories` VALUES (1,'2012-05-27',NULL,NULL,NULL);
INSERT INTO `calories` VALUES (8,'2012-06-02',2386,NULL,NULL);
INSERT INTO `calories` VALUES (8,'2012-06-03',2068,NULL,NULL);
INSERT INTO `calories` VALUES (8,'2012-06-04',2790,NULL,NULL);
INSERT INTO `calories` VALUES (8,'2012-06-05',1958,NULL,NULL);
INSERT INTO `calories` VALUES (8,'2012-06-06',2554,NULL,NULL);
INSERT INTO `calories` VALUES (8,'2012-06-07',NULL,NULL,NULL);
INSERT INTO `calories` VALUES (1,'2012-06-08',NULL,NULL,NULL);
INSERT INTO `calories` VALUES (1,'2012-06-09',NULL,NULL,NULL);
INSERT INTO `calories` VALUES (1,'2012-06-10',NULL,NULL,NULL);
/*!40000 ALTER TABLE `calories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `metabolism`
--

DROP TABLE IF EXISTS `metabolism`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metabolism` (
  `id` int(10) unsigned NOT NULL,
  `sex` enum('male','female') NOT NULL,
  `age` tinyint(3) unsigned NOT NULL,
  `height` tinyint(3) unsigned NOT NULL,
  `lifestyle` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metabolism`
--

LOCK TABLES `metabolism` WRITE;
/*!40000 ALTER TABLE `metabolism` DISABLE KEYS */;
INSERT INTO `metabolism` VALUES (1,'male',29,72,1.2);
INSERT INTO `metabolism` VALUES (8,'female',23,69,1.2);
/*!40000 ALTER TABLE `metabolism` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `email` varchar(60) DEFAULT NULL,
  `password` varchar(64) NOT NULL,
  `salt` varchar(3) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Lucent',NULL,'2828f136f10194907a78e8a20a64554303b4b6bbca281612d562a5b1978749d7','59f');
INSERT INTO `users` VALUES (8,'jeannette',NULL,'1d80cb95e53b2a0c348d4db081caa1a5739bef95ab6ce3f7e6b740a72b93b44b','5d1');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-06-10 12:58:16
