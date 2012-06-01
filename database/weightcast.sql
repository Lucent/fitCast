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
  `net` smallint(6) DEFAULT NULL,
  `measured` float unsigned DEFAULT NULL,
  UNIQUE KEY `id` (`id`,`date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calories`
--

LOCK TABLES `calories` WRITE;
/*!40000 ALTER TABLE `calories` DISABLE KEYS */;
INSERT INTO `calories` VALUES (1,'2012-05-25',1400,500,NULL,295);
INSERT INTO `calories` VALUES (1,'2012-05-26',1500,600,NULL,NULL);
INSERT INTO `calories` VALUES (1,'2012-05-28',1700,800,NULL,NULL);
INSERT INTO `calories` VALUES (1,'2012-05-29',1800,900,NULL,NULL);
INSERT INTO `calories` VALUES (1,'2012-05-21',1000,100,NULL,220);
INSERT INTO `calories` VALUES (1,'2012-05-22',1100,200,NULL,NULL);
INSERT INTO `calories` VALUES (1,'2012-05-23',1200,300,NULL,NULL);
INSERT INTO `calories` VALUES (1,'2012-05-24',1300,400,NULL,NULL);
INSERT INTO `calories` VALUES (1,'2012-05-27',1600,700,NULL,NULL);
INSERT INTO `calories` VALUES (1,'2012-05-30',1900,1000,NULL,NULL);
INSERT INTO `calories` VALUES (1,'2012-05-31',2000,1100,NULL,NULL);
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
  `startweight` smallint(5) unsigned NOT NULL,
  `lifestyle` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metabolism`
--

LOCK TABLES `metabolism` WRITE;
/*!40000 ALTER TABLE `metabolism` DISABLE KEYS */;
INSERT INTO `metabolism` VALUES (1,'male',29,72,295,1.2);
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
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Lucent',NULL,'2828f136f10194907a78e8a20a64554303b4b6bbca281612d562a5b1978749d7','59f');
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

-- Dump completed on 2012-05-31 21:50:13
