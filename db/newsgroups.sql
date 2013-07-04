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
  `password_hash` varchar(1000) NOT NULL,
  `is_admin` tinyint(1) NOT NULL,
  `user_class` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES (1,'root','sha256:1000:MI2aqLKszq5sckOnKS6y5nz1X6bXO98g:HLq8KJTbYqFk5puksSyXk+DvijFIV3DE',0,0),(2,'root2','sha256:10000:+p6b+cUi5SRUyJkQ4lcHIcq4Y/X/Q3j8:dH2SSACBkkEzIdDUmECobBlC6U5wlTxp',1,0),(6,'assssss','sha256:1000:l3t2MA5hyaFgNjXgQk+mmm0nWXIldtQt:dnXFOv3G4EDcfYJWlAzWdevW0vt6vAfu',0,0);
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
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (3,'defuse.ring0',9),(11,'defuse.test',151);
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `class_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `ability` enum('NOACCESS','READONLY','READWRITECAPTCHA','READWRITE') NOT NULL DEFAULT 'NOACCESS',
  KEY `class_id` (`class_id`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,11,'READWRITECAPTCHA'),(0,11,'READWRITE'),(1,3,'NOACCESS'),(0,3,'READWRITE');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
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
) ENGINE=MyISAM AUTO_INCREMENT=153 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (9,3,'SYSTEM',1372561601,'This is the root-level post for defuse.ring0.',''),(14,3,'root',1372561847,'Test post! :)','This is a TEST POST!!!!!!'),(13,3,'root',1372561834,'Test post! :)','This is a TEST POST!!!!!!'),(15,3,'root',1372561847,'Test post! :)','This is a TEST POST!!!!!!'),(16,3,'root',1372561848,'Test post! :)','This is a TEST POST!!!!!!'),(17,3,'root',1372561848,'Test post! :)','This is a TEST POST!!!!!!'),(18,3,'root',1372561848,'Test post! :)','This is a TEST POST!!!!!!'),(19,3,'root',1372562273,'Test post! :)','This is a TEST POST!!!!!!'),(20,3,'root',1372562289,'Test post! :)','This is a TEST POST!!!!!!'),(21,3,'root',1372563049,'Test post! :)','This is a TEST POST!!!!!!'),(22,3,'FOO',1372564470,'REPLY WUN!!','A REPLYZZ'),(23,3,'FOO',1372564470,'REPLY WUN!!','A REPLYZZ'),(24,3,'FOO',1372564470,'REPLY WUN!!','A REPLYZZ'),(25,3,'FOO',1372564470,'REPLY WUN!!','A REPLYZZ'),(26,3,'FOO',1372564470,'REPLY WUN!!','A REPLYZZ'),(27,3,'FOO',1372564470,'REPLY WUN!!','A REPLYZZ'),(28,3,'FOO',1372564470,'REPLY WUN!!','A REPLYZZ'),(29,3,'FOO',1372564470,'REPLY WUN!!','A REPLYZZ'),(30,3,'FOO',1372564470,'REPLY WUN!!','A REPLYZZ'),(31,3,'FOO',1372564489,'REPLY WUN!!','A REPLYZZ'),(32,3,'FOO',1372564489,'REPLY WUN!!','A REPLYZZ'),(33,3,'FOO',1372564489,'REPLY WUN!!','A REPLYZZ'),(34,3,'FOO',1372564489,'REPLY WUN!!','A REPLYZZ'),(35,3,'FOO',1372564489,'REPLY WUN!!','A REPLYZZ'),(36,3,'FOO',1372564489,'REPLY WUN!!','A REPLYZZ'),(37,3,'FOO',1372564489,'REPLY WUN!!','A REPLYZZ'),(38,3,'FOO',1372564489,'REPLY WUN!!','A REPLYZZ'),(39,3,'FOO',1372564489,'REPLY WUN!!','A REPLYZZ'),(40,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(41,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(42,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(43,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(44,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(45,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(46,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(47,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(48,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(49,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(50,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(51,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(52,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(53,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(54,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(55,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(56,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(57,3,'FOO',1372564490,'REPLY WUN!!','A REPLYZZ'),(58,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(59,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(60,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(61,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(62,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(63,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(64,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(65,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(66,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(67,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(68,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(69,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(70,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(71,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(72,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(73,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(74,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(75,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(76,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(77,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(78,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(79,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(80,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(81,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(82,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(83,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(84,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(85,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(86,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(87,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(88,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(89,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(90,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(91,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(92,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(93,3,'FOO',1372564493,'REPLY WUN!!','A REPLYZZ'),(123,3,'root2',1372712823,'test from new thing','the new thing\r\n\r\n\r\n\r\n\r\na\r\na'),(95,3,'',1372631374,'Your Subject...','Type here...'),(96,3,'Anonymous5',1372632374,'Re: Your Subject...','This should be a reply'),(97,3,'',1372632421,'Re: Your Subject...','Type here...'),(98,3,'',1372632428,'Re: Your Subject...','Type here...'),(99,3,'',1372632439,'Re: Your Subject...','Type here...'),(100,3,'',1372632585,'Re: REPLY TWO!!','this is the second reply HAHAHAHAHAHAHA'),(124,3,'',1372712840,'test from anon','test from anon'),(125,3,'root2',1372713532,'Re: test from anon','Anonymous said:\r\n> test from anon'),(102,3,'',1372632852,'A post without a reply','asfasdfasdf'),(103,3,'',1372633279,'Your Subject...','Type here...'),(104,3,'',1372633289,'Re: Test post! :)','Type here...'),(105,3,'',1372644888,'Re: A post without a reply','> Type here...\r\n\r\nNo!\r\n\r\n> 1\r\n> > 2\r\n> >> 3\r\n> >> 3\r\n> > 2\r\n> > > > > 5\r\n> blah!'),(106,3,'',1372645283,'Re: A post without a reply','> Type here...\r\n\r\nasdfasdfasdfasdfasdf\r\n\r\n> 1'),(107,3,'',1372645406,'Re: A post without a reply','level 0 bug?'),(108,3,'',1372645718,'Re: A post without a reply','>I wonder whether the monkeys will eventually follow the same economic path\r\n>as our own financial masters and bring about a complete collapse of the\r\n>fruit economy.\r\n'),(110,3,'',1372646202,'Re: A post without a reply','Anonymous said:\r\n> >I wonder whether the monkeys will eventually follow the same economic path\r\n> >as our own financial masters and bring about a complete collapse of the\r\n> >fruit economy.\r\n'),(111,3,'',1372646357,'Re: A post without a reply','Anonymous said:\r\n> >I wonder whether the monkeys will eventually follow the same economic path\r\n> >as our own financial masters and bring about a complete collapse of the\r\n> >fruit economy.\r\n'),(112,3,'',1372646365,'Re: A post without a reply','Anonymous said:\r\n> Anonymous said:\r\n> > >I wonder whether the monkeys will eventually follow the same economic path\r\n> > >as our own financial masters and bring about a complete collapse of the\r\n> > >fruit economy.\r\n'),(113,3,'',1372651006,'lipsum','\r\n\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus porta nisi sit amet mattis aliquam. Curabitur sed laoreet sem, eget pharetra metus. Fusce placerat justo eget lobortis porttitor. Quisque mollis nisl sed arcu suscipit, at aliquam libero lobortis. Donec nec mauris ligula. Quisque pulvinar eleifend sapien, et imperdiet eros. Pellentesque tristique magna non vestibulum faucibus. Nam feugiat mauris quis porttitor egestas. Aliquam erat volutpat. Integer id mauris eu nulla vulputate hendrerit sed nec ipsum. Donec a tortor quis nibh mollis scelerisque. Mauris tortor elit, malesuada vitae placerat vitae, pharetra ut ligula. Nam facilisis eu eros id feugiat. Sed in tellus at erat posuere interdum et non lorem. Nunc blandit, nibh vitae condimentum volutpat, elit quam vehicula quam, id vulputate felis metus nec lorem.\r\n\r\nSed ac risus purus. Curabitur ultrices laoreet tellus, id mollis enim porttitor vel. Integer tristique metus molestie, dapibus tortor vel, mattis erat. Integer scelerisque sapien sit amet accumsan pellentesque. Phasellus tincidunt dui id mattis eleifend. Nam tortor erat, elementum nec imperdiet ut, sollicitudin ut nisl. Vivamus ultrices quis elit ac convallis. Vivamus purus nulla, consectetur sed ornare at, malesuada nec mauris. In vulputate pulvinar elit ut eleifend. Ut facilisis mauris sit amet mauris hendrerit vulputate. Vivamus aliquam blandit sapien sagittis ultricies. Sed adipiscing augue eget eros tristique, fermentum auctor turpis cursus. Sed dapibus dictum erat ut congue. Morbi ac diam vel nisl interdum cursus.\r\n\r\nCras vel nulla vel ligula pretium adipiscing a ac mauris. Donec placerat, arcu nec porta molestie, massa magna rutrum elit, eu accumsan justo est nec ipsum. Sed at urna eros. Donec vulputate diam ut urna viverra ullamcorper. Nunc tempus lacus vel quam facilisis ornare. Integer interdum in ante in rhoncus. Aenean pretium, tortor in sollicitudin luctus, massa enim posuere mi, eget imperdiet enim lacus quis nulla. Donec iaculis facilisis adipiscing. Suspendisse condimentum ante sit amet arcu scelerisque accumsan. Nullam nec dolor molestie, hendrerit ligula a, euismod sapien. Phasellus elementum orci massa, vel sodales metus vulputate feugiat. Proin at velit sem. Sed velit libero, sagittis a elementum quis, imperdiet vitae sapien. Nullam ultricies tellus a interdum rutrum.\r\n\r\nAenean urna sem, semper non aliquet at, elementum eu turpis. Vestibulum ac dui congue, iaculis felis non, porta dolor. Etiam tempus, lacus eu tristique rhoncus, quam lorem convallis lorem, sed blandit odio dui at velit. Suspendisse a ipsum accumsan, luctus diam sit amet, ullamcorper lacus. Sed dictum ligula id commodo consectetur. Maecenas laoreet interdum ultrices. Sed congue et dui in euismod. Suspendisse potenti. Integer eu nisi et tellus porta venenatis a sit amet tellus.\r\n\r\nAliquam vitae felis tincidunt, mattis ipsum id, adipiscing risus. Donec accumsan odio in ultricies dapibus. Fusce venenatis consequat neque, sed varius lectus hendrerit vel. Integer fringilla dui et libero sollicitudin mattis. Aliquam eget gravida arcu. Sed ac quam vel risus commodo tristique vel et est. Fusce at mi consectetur, consequat purus quis, feugiat eros. '),(114,3,'',1372651027,'Re: lipsum','Anonymous said:\r\n> \r\n> \r\n> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus porta nisi sit amet mattis aliquam. Curabitur sed laoreet sem, eget pharetra metus. Fusce placerat justo eget lobortis porttitor. Quisque mollis nisl sed arcu suscipit, at aliquam libero lobortis. Donec nec mauris ligula. Quisque pulvinar eleifend sapien, et imperdiet eros. Pellentesque tristique magna non vestibulum faucibus. Nam feugiat mauris quis porttitor egestas. Aliquam erat volutpat. Integer id mauris eu nulla vulputate hendrerit sed nec ipsum. Donec a tortor quis nibh mollis scelerisque. Mauris tortor elit, malesuada vitae placerat vitae, pharetra ut ligula. Nam facilisis eu eros id feugiat. Sed in tellus at erat posuere interdum et non lorem. Nunc blandit, nibh vitae condimentum volutpat, elit quam vehicula quam, id vulputate felis metus nec lorem.\r\n\r\nyes this is brilliant\r\n\r\n> Sed ac risus purus. Curabitur ultrices laoreet tellus, id mollis enim porttitor vel. Integer tristique metus molestie, dapibus tortor vel, mattis erat. Integer scelerisque sapien sit amet accumsan pellentesque. Phasellus tincidunt dui id mattis eleifend. Nam tortor erat, elementum nec imperdiet ut, sollicitudin ut nisl. Vivamus ultrices quis elit ac convallis. Vivamus purus nulla, consectetur sed ornare at, malesuada nec mauris. In vulputate pulvinar elit ut eleifend. Ut facilisis mauris sit amet mauris hendrerit vulputate. Vivamus aliquam blandit sapien sagittis ultricies. Sed adipiscing augue eget eros tristique, fermentum auctor turpis cursus. Sed dapibus dictum erat ut congue. Morbi ac diam vel nisl interdum cursus.\r\n\r\nomg wonderful\r\n\r\n> \r\n> Cras vel nulla vel ligula pretium adipiscing a ac mauris. Donec placerat, arcu nec porta molestie, massa magna rutrum elit, eu accumsan justo est nec ipsum. Sed at urna eros. Donec vulputate diam ut urna viverra ullamcorper. Nunc tempus lacus vel quam facilisis ornare. Integer interdum in ante in rhoncus. Aenean pretium, tortor in sollicitudin luctus, massa enim posuere mi, eget imperdiet enim lacus quis nulla. Donec iaculis facilisis adipiscing. Suspendisse condimentum ante sit amet arcu scelerisque accumsan. Nullam nec dolor molestie, hendrerit ligula a, euismod sapien. Phasellus elementum orci massa, vel sodales metus vulputate feugiat. Proin at velit sem. Sed velit libero, sagittis a elementum quis, imperdiet vitae sapien. Nullam ultricies tellus a interdum rutrum.\r\n> \r\n> Aenean urna sem, semper non aliquet at, elementum eu turpis. Vestibulum ac dui congue, iaculis felis non, porta dolor. Etiam tempus, lacus eu tristique rhoncus, quam lorem convallis lorem, sed blandit odio dui at velit. Suspendisse a ipsum accumsan, luctus diam sit amet, ullamcorper lacus. Sed dictum ligula id commodo consectetur. Maecenas laoreet interdum ultrices. Sed congue et dui in euismod. Suspendisse potenti. Integer eu nisi et tellus porta venenatis a sit amet tellus.\r\n> \r\n> Aliquam vitae felis tincidunt, mattis ipsum id, adipiscing risus. Donec accumsan odio in ultricies dapibus. Fusce venenatis consequat neque, sed varius lectus hendrerit vel. Integer fringilla dui et libero sollicitudin mattis. Aliquam eget gravida arcu. Sed ac quam vel risus commodo tristique vel et est. Fusce at mi consectetur, consequat purus quis, feugiat eros. '),(115,3,'',1372704675,'Your Subject...','Type here...'),(118,6,'',1372709648,'THIS SHOULD BE DELETED','asdfasdfasdfasdf'),(126,3,'root2',1372713535,'Re: test from anon','Anonymous said:\r\n> test from anon'),(119,6,'',1372709667,'SO SHOULD THIS','Anonymous said:\r\n> asdfasdfasdfasdf'),(127,3,'root2',1372713589,'Re: test from anon','root2 said:\r\n> Anonymous said:\r\n> > test from anon\r\n\r\nnice test!'),(129,129,'SYSTEM',1372742038,'This is the root-level post for .',''),(151,11,'SYSTEM',1372965521,'This is the root-level post for defuse.test.',''),(131,3,'root2',1372862793,'Re: Test post! :) AAAAAAAAAAA','root said:\r\n> This is a TEST POST!!!!!!\r\nAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'),(152,11,'root2',1372966018,'Hallo!','This is a newsgroup!'),(138,3,'root2',1372950983,'Re: test from anon','On July 1, 2013, 21:18 UTC, root2 said:\r\n> Anonymous said:\r\n> > test from anon\r\n\r\nha!'),(137,3,'root2',1372910325,'Re: Test post! :) AAAAAAAAAAA','root2 said:\r\n> root said:\r\n> > This is a TEST POST!!!!!!\r\n> AAAAAAAAAAAAAAAAAAAAAAAAAAAAA\r\n\r\n// This program is free software: you can redistribute it and/or modify\r\n// it under the terms of the GNU General Public License as published by\r\n// the Free Software Foundation, either version 3 of the License, or\r\n// (at your option) any later version.\r\n//\r\n// This program is distributed in the hope that it will be useful,\r\n// but WITHOUT ANY WARRANTY; without even the implied warranty of\r\n// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the\r\n// GNU General Public License for more details.\r\n//\r\n// You should have received a copy of the GNU General Public License\r\n// along with this program.  If not, see <http://www.gnu.org/licenses/>.\r\n\r\n#include <stdio.h>\r\n#include <inttypes.h>\r\n\r\n\r\n#define INDEX_HASH_WIDTH 8\r\n#define INDEX_POSITION_WIDTH 6\r\n#define INDEX_ENTRY_WIDTH (INDEX_HASH_WIDTH + INDEX_POSITION_WIDTH)\r\n\r\nstruct IndexEntry {\r\n    unsigned char hash[INDEX_HASH_WIDTH]; // First 64 bits of the hash\r\n    unsigned char position[INDEX_POSITION_WIDTH]; // Position of word in dictionary (48-bit little endian integer)\r\n};\r\n\r\n\r\nvoid freadIndexEntryAt(FILE* file, int64_t index, struct IndexEntry* out)\r\n{\r\n    fseek(file, index * INDEX_ENTRY_WIDTH, SEEK_SET);\r\n    fread(out->hash, sizeof(unsigned char), INDEX_HASH_WIDTH, file);\r\n    fread(out->position, sizeof(unsigned char), INDEX_POSITION_WIDTH, file);\r\n}\r\n\r\n/*\r\n * Compares two INDEX_HASH_WIDTH-char arrays.\r\n * Returns 1 if the first argument is greater than the second.\r\n * Returns -1 if the first argument is less than the second.\r\n * Returns 0 if both are equal.\r\n */\r\nint hashcmp(const unsigned char hashA[INDEX_HASH_WIDTH], const unsigned char hashB[INDEX_HASH_WIDTH])\r\n{\r\n    int i = 0;\r\n    for(i = 0; i < INDEX_HASH_WIDTH; i++)\r\n    {\r\n        if(hashA[i] > hashB[i])\r\n            return 1;\r\n        else if(hashA[i] < hashB[i])\r\n            return -1;\r\n    }\r\n\r\n    return 0;\r\n}\r\n\r\n\r\nint main(int argc, char **argv)\r\n{\r\n    struct IndexEntry current, max;\r\n    FILE* file = fopen(argv[1], \"r+b\");\r\n\r\n    if(file == NULL)\r\n    {\r\n        printf(\"File does not exist.\\n\");\r\n        return 3;\r\n    }\r\n\r\n    fseek(file, 0L, SEEK_END);\r\n    int64_t size = ftell(file);\r\n    if(size % INDEX_ENTRY_WIDTH != 0)\r\n    {\r\n        printf(\"Invalid index file!\\n\");\r\n        return 1;\r\n    }\r\n    int64_t numEntries = size / INDEX_ENTRY_WIDTH;\r\n\r\n    int64_t i;\r\n\r\n    for(i = 0; i < numEntries; i++)\r\n    {\r\n        freadIndexEntryAt(file, i, &current);\r\n        if(hashcmp(current.hash, max.hash) < 0) // Current is less than max\r\n        {\r\n            printf(\"NOT SORTED!!!!\\n\");\r\n            return 2;\r\n        }\r\n        max = current;\r\n        if(i % 10000000 == 0)\r\n        {\r\n            printf(\"%d...\\n\", i);\r\n        }\r\n    }\r\n\r\n    printf(\"ALL SORTED!\\n\");\r\n}\r\n'),(139,3,'root2',1372951309,'zzzzz','..........\r\nHHHHHHHHHH\r\n\r\n|||||||||'),(140,3,'root2',1372959414,'',''),(141,3,'root2',1372959422,'',''),(142,3,'root2',1372959436,'',''),(143,3,'root2',1372959439,'',''),(144,3,'root2',1372959442,'',''),(145,3,'root2',1372959446,'',''),(146,3,'root2',1372959449,'',''),(147,3,'root2',1372959453,'',''),(148,3,'root2',1372962185,'',''),(149,3,'root2',1372962255,'Re: ','On Jul 04, 2013, 18:23 UTC, root2 wrote:\r\n> \r\n\r\nand a reply!!!'),(150,3,'root2',1372962272,'Re: ','On Jul 04, 2013, 18:24 UTC, root2 wrote:\r\n> On Jul 04, 2013, 18:23 UTC, root2 wrote:\r\n> > \r\n> \r\n> and a reply!!!\r\nasdfasdf');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `read_status`
--

DROP TABLE IF EXISTS `read_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `read_status` (
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `has_read` tinyint(1) NOT NULL,
  KEY `user_id` (`user_id`,`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `read_status`
--

LOCK TABLES `read_status` WRITE;
/*!40000 ALTER TABLE `read_status` DISABLE KEYS */;
INSERT INTO `read_status` VALUES (2,139,1),(2,115,1),(2,145,1),(2,124,1),(2,21,1),(2,20,1),(2,113,1),(2,123,1),(2,103,1),(2,102,1),(2,112,1),(2,95,1),(2,96,1),(2,98,1),(2,97,1),(2,99,1),(2,19,1),(2,83,1),(2,37,1),(2,125,1),(2,52,1),(2,82,1),(2,18,1),(2,81,1),(2,51,1),(2,80,1),(2,36,1),(2,79,1),(2,50,1),(2,78,1),(2,16,1),(2,73,1),(2,72,1),(2,34,1),(2,17,1),(2,141,1),(2,140,1),(2,142,1),(2,144,1),(2,143,1),(2,146,1),(2,147,1),(2,148,1),(2,149,1),(2,150,1),(2,114,1),(2,131,1),(2,137,1),(2,105,1),(2,110,1),(2,107,1),(2,106,1),(2,111,1),(2,108,1),(2,138,1),(2,14,1),(2,65,1),(2,43,1),(2,152,1);
/*!40000 ALTER TABLE `read_status` ENABLE KEYS */;
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
INSERT INTO `replies` VALUES (9,13),(9,14),(9,15),(9,16),(9,17),(9,18),(9,19),(9,20),(9,21),(9,95),(9,102),(9,103),(9,113),(9,115),(9,123),(9,124),(9,139),(9,140),(9,141),(9,142),(9,143),(9,144),(9,145),(9,146),(9,147),(9,148),(13,31),(13,41),(13,61),(13,104),(13,131),(14,32),(14,43),(14,65),(15,33),(15,45),(15,69),(16,34),(16,47),(16,73),(17,35),(17,49),(17,77),(18,36),(18,51),(18,81),(19,37),(19,53),(19,85),(20,38),(20,55),(20,89),(21,39),(21,57),(21,93),(31,40),(31,59),(31,100),(32,42),(32,63),(33,44),(33,67),(34,46),(34,71),(35,48),(35,75),(36,50),(36,79),(37,52),(37,83),(38,54),(38,87),(39,56),(39,91),(40,58),(41,60),(42,62),(43,64),(44,66),(45,68),(46,70),(47,72),(48,74),(49,76),(50,78),(51,80),(52,82),(53,84),(54,86),(55,88),(56,90),(57,92),(95,96),(95,99),(96,97),(96,98),(102,105),(105,106),(105,108),(106,107),(108,110),(108,111),(110,112),(113,114),(117,118),(118,119),(120,121),(121,122),(124,125),(124,126),(125,127),(126,138),(131,137),(148,149),(149,150),(151,152);
/*!40000 ALTER TABLE `replies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES ('class.anonymous','1'),('class.default','0'),('recaptcha.onregister','1'),('recaptcha.private_key','6LfNxuMSAAAAALg3l1-c6y9AMGk8gcaBWZYUO9x7'),('recaptcha.public_key','6LfNxuMSAAAAAMCg4_IBrD00ee1-OeW5kA9BazEo');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_classes`
--

DROP TABLE IF EXISTS `user_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_classes`
--

LOCK TABLES `user_classes` WRITE;
/*!40000 ALTER TABLE `user_classes` DISABLE KEYS */;
INSERT INTO `user_classes` VALUES (1,'Anonymous'),(0,'Default');
/*!40000 ALTER TABLE `user_classes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-07-04 13:29:53
