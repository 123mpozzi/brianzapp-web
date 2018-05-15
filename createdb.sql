-- MySQL dump 10.16  Distrib 10.1.26-MariaDB, for Win32 (AMD64)
--
-- Host: localhost    Database: dbproci
-- ------------------------------------------------------
-- Server version	10.1.26-MariaDB

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
-- Table structure for table `comune`
--

DROP TABLE IF EXISTS `comune`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comune` (
  `cap` int(5) NOT NULL,
  `nome` varchar(250) NOT NULL,
  PRIMARY KEY (`cap`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comune`
--

LOCK TABLES `comune` WRITE;
/*!40000 ALTER TABLE `comune` DISABLE KEYS */;
/*!40000 ALTER TABLE `comune` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifica`
--

DROP TABLE IF EXISTS `notifica`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifica` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `titolo` varchar(40) NOT NULL,
  `descrizione` varchar(250) DEFAULT NULL,
  `stelle` enum('1','2','3') DEFAULT '1',
  `pdf` varchar(250) NOT NULL,
  `data` datetime(6) DEFAULT NULL,
  `id_provenienza` int(10) NOT NULL,
  `id_utente` int(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_utente` (`id_utente`),
  KEY `id_provenienza` (`id_provenienza`),
  CONSTRAINT `notifica_ibfk_1` FOREIGN KEY (`id_utente`) REFERENCES `utente` (`id`),
  CONSTRAINT `notifica_ibfk_2` FOREIGN KEY (`id_provenienza`) REFERENCES `provenienza` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifica`
--

LOCK TABLES `notifica` WRITE;
/*!40000 ALTER TABLE `notifica` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifica` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifica_comune`
--

DROP TABLE IF EXISTS `notifica_comune`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifica_comune` (
  `id_notifica` int(20) NOT NULL,
  `cap_comune` int(5) NOT NULL,
  KEY `cap_comune` (`cap_comune`),
  KEY `id_notifica` (`id_notifica`) USING BTREE,
  CONSTRAINT `notifica_comune_ibfk_1` FOREIGN KEY (`cap_comune`) REFERENCES `comune` (`cap`),
  CONSTRAINT `notifica_comune_ibfk_2` FOREIGN KEY (`id_notifica`) REFERENCES `notifica` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifica_comune`
--

LOCK TABLES `notifica_comune` WRITE;
/*!40000 ALTER TABLE `notifica_comune` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifica_comune` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `provenienza`
--

DROP TABLE IF EXISTS `provenienza`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `provenienza` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nome` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `provenienza`
--

LOCK TABLES `provenienza` WRITE;
/*!40000 ALTER TABLE `provenienza` DISABLE KEYS */;
/*!40000 ALTER TABLE `provenienza` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utente`
--

DROP TABLE IF EXISTS `utente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utente` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `user` varchar(20) NOT NULL,
  `password` char(64) NOT NULL,
  `token` char(60) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utente`
--

LOCK TABLES `utente` WRITE;
/*!40000 ALTER TABLE `utente` DISABLE KEYS */;
/*!40000 ALTER TABLE `utente` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-05-15 10:38:20
