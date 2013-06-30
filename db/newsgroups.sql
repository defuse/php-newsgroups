-- MySQL dump 10.13  Distrib 5.1.66, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: newsgroups
-- ------------------------------------------------------
-- Server version	5.1.66-0+squeeze1

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
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(1000) NOT NULL,
  `is_admin` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES (1,'root','havoc@defuse.ca','sha256:1000:MI2aqLKszq5sckOnKS6y5nz1X6bXO98g:HLq8KJTbYqFk5puksSyXk+DvijFIV3DE',0);
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `root_post_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (3,'defuse.ring0',9),(4,'defuse.general',94);
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `user` varchar(255) NOT NULL,
  `post_date` bigint(20) NOT NULL,
  `title` varchar(1000) NOT NULL,
  `contents` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group` (`group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=103 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (9,3,'SYSTEM',1372561601,'This is the root-level post for defuse.ring0.',''),(14,3,'root',1372561847,'Test post! :)','This is a TEST POST!!!!!!'),(13,3,'root',1372561834,'Test post! :)','This is a TEST POST!!!!!!'),(15,3,'root',1372561847,'Test post! :)','This is a TEST POST!!!!!!'),(16,3,'root',1372561848,'Test post! :)','This is a TEST POST!!!!!!'),(17,3,'root',1372561848,'Test post! :)','This is a TEST POST!!!!!!'),(18,3,'root',1372561848,'Test post! :)','This is a TEST POST!!!!!!'),(19,3,'root',1372562273,'Test post! :)','This is a TEST POST!!!!!!'),(20,3,'root',1372562289,'Test post! :)','This is a TEST POST!!!!!!'),(21,3,'root',1372563049,'Test post! :)','This is a TEST POST!!!!!!'),(22,3,'FOO',1372564470,'REPLY WUN!!','A REPLYZZ'),(23,3,'FOO',1372564470,'REPLY WUN!!','A REPLYZZ'),(24,3,'FOO',1372564470,'REPLY WUN!!','A REPLYZZ'),(25,3,'FOO',1372564470,'REPLY WUN!!','A REPLYZZ'),(26,3,'FOO',1372564470,'REPLY WUN!!','A REPLYZZ'),(27,3,'FOO',1372564470,'REPLY WUN!!','A REPLYZZ'),(28,3,'FOO',1372564470,'REPLY WUN!!','A REPLYZZ'),(29,3,'FOO',1372564470,'REPLY WUN!!','A REPLYZZ'),(30,3,'FOO',1372564470,'REPLY WUN!!','A REPLYZZ'),(31,3,'FOO',1372564489,'REPLY WUN!!','A REPLYZZ'),(32,3,'FOO',1372564489,'REPLY WUN!!','A REPLYZZ'),(33,3,'FOO',1372564489,'REPLY WUN!!','A REPLYZZ'),(34,3,'FOO',1372564489,'REPLY WUN!!','A REPLYZZ'),(35,3,'FOO',1372564489,'REPLY WUN!!','A REPLYZZ'),(36,3,'FOO',1372564489,'REPLY WUN!!','A REPLYZZ'),(37,3,'FOO',1372564489,'REPLY WUN!!','A REPLYZZ'),(38,3,'FOO',1372564489,'REPLY WUN!!','A REPLYZZ'),(39,3,'FOO',1372564489,'REPLY WUN!!','A REPLYZZ'),(40,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(41,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(42,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(43,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(44,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(45,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(46,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(47,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(48,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(49,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(50,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(51,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(52,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(53,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(54,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(55,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(56,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(57,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(58,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(59,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(60,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(61,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(62,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(63,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(64,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(65,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(66,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(67,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(68,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(69,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(70,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(71,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(72,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(73,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(74,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(75,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(76,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(77,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(78,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(79,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(80,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(81,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(82,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(83,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(84,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(85,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(86,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(87,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(88,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(89,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(90,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(91,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(92,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(93,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(94,4,'SYSTEM',1372568194,'This is the root-level post for defuse.general.',''),(95,3,'Anonymous',1372631374,'Your Subject...','Type here...'),(96,3,'Anonymous5',1372632374,'Re: Your Subject...','This should be a reply'),(97,3,'Anonymous',1372632421,'Re: Your Subject...','Type here...'),(98,3,'Anonymous',1372632428,'Re: Your Subject...','Type here...'),(99,3,'Anonymous',1372632439,'Re: Your Subject...','Type here...'),(100,3,'Anonymous',1372632585,'Re: REPLY TWO!!','this is the second reply HAHAHAHAHAHAHA'),(101,4,'Anonymous',1372632682,'Go to defuse.ring0','There is nothing here to see. The interesting stuff happens in defuse.ring0.'),(102,3,'Anonymous',1372632852,'A post without a reply','asfasdfasdf');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `replies`
--

DROP TABLE IF EXISTS `replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `replies` (
  `parent_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  KEY `parent_id` (`parent_id`,`child_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `replies`
--

LOCK TABLES `replies` WRITE;
/*!40000 ALTER TABLE `replies` DISABLE KEYS */;
INSERT INTO `replies` VALUES (9,13),(9,14),(9,15),(9,16),(9,17),(9,18),(9,19),(9,20),(9,21),(9,95),(9,102),(13,31),(13,41),(13,61),(14,32),(14,43),(14,65),(15,33),(15,45),(15,69),(16,34),(16,47),(16,73),(17,35),(17,49),(17,77),(18,36),(18,51),(18,81),(19,37),(19,53),(19,85),(20,38),(20,55),(20,89),(21,39),(21,57),(21,93),(31,40),(31,59),(31,100),(32,42),(32,63),(33,44),(33,67),(34,46),(34,71),(35,48),(35,75),(36,50),(36,79),(37,52),(37,83),(38,54),(38,87),(39,56),(39,91),(40,58),(41,60),(42,62),(43,64),(44,66),(45,68),(46,70),(47,72),(48,74),(49,76),(50,78),(51,80),(52,82),(53,84),(54,86),(55,88),(56,90),(57,92),(94,101),(95,96),(95,99),(96,97),(96,98);
/*!40000 ALTER TABLE `replies` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-06-30 16:54:47
