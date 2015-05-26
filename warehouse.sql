-- MySQL dump 10.13  Distrib 5.5.40, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: hightecs_warehouse
-- ------------------------------------------------------
-- Server version	5.5.40-0ubuntu1

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
-- Table structure for table `CT5`
--

DROP TABLE IF EXISTS `CT5`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CT5` (
  `id` int(11) NOT NULL,
  `id_fotografia` int(11) DEFAULT NULL,
  `livello` varchar(22) DEFAULT NULL,
  `parent_id` varchar(7) DEFAULT NULL,
  `ref_design` varchar(50) DEFAULT NULL,
  `ref_design_orig` varchar(16) DEFAULT NULL,
  `pn_supplier` varchar(15) DEFAULT NULL,
  `pn_manufacturer` varchar(20) DEFAULT NULL,
  `description` varchar(81) DEFAULT NULL,
  `sn_supplier` varchar(15) DEFAULT NULL,
  `sn_manufacturer` varchar(17) DEFAULT NULL,
  `sn` varchar(26) DEFAULT NULL,
  `system` varchar(5) DEFAULT NULL,
  `id_places` int(7) DEFAULT NULL,
  `location` varchar(7) DEFAULT NULL,
  `id_parts` int(10) DEFAULT NULL,
  `id_compatible` int(10) DEFAULT NULL,
  `id_items` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Foglio1`
--

DROP TABLE IF EXISTS `Foglio1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Foglio1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `livello` varchar(17) DEFAULT NULL,
  `ref_design` varchar(14) DEFAULT NULL,
  `pn_supplier` varchar(11) DEFAULT NULL,
  `pn_manufacturer` varchar(17) DEFAULT NULL,
  `description` varchar(80) DEFAULT NULL,
  `sn` varchar(25) DEFAULT NULL,
  `system` varchar(3) DEFAULT NULL,
  `id_places` varchar(10) DEFAULT NULL,
  `location` varchar(12) DEFAULT NULL,
  `id_compatible` int(3) DEFAULT NULL,
  `id_parts` int(11) DEFAULT NULL,
  `id_items` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=193 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Theatre`
--

DROP TABLE IF EXISTS `Theatre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Theatre` (
  `id` int(4) DEFAULT NULL,
  `parent_id` int(4) DEFAULT NULL,
  `ordine` int(4) DEFAULT NULL,
  `livello` varchar(17) DEFAULT NULL,
  `ref_design` varchar(43) DEFAULT NULL,
  `pn_supplier` varchar(9) DEFAULT NULL,
  `pn_manufacturer` varchar(17) DEFAULT NULL,
  `description` varchar(68) DEFAULT NULL,
  `sn` varchar(25) DEFAULT NULL,
  `system` varchar(3) DEFAULT NULL,
  `id_places` int(3) DEFAULT NULL,
  `location` varchar(12) DEFAULT NULL,
  `id_compatible` int(3) DEFAULT NULL,
  `id_parts` varchar(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `_ATTREZZATURE`
--

DROP TABLE IF EXISTS `_ATTREZZATURE`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_ATTREZZATURE` (
  `Materiale` varchar(11) DEFAULT NULL,
  `Descrizione` varchar(41) DEFAULT NULL,
  `QUANT.` int(2) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `_CISS_GIO`
--

DROP TABLE IF EXISTS `_CISS_GIO`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_CISS_GIO` (
  `Materiale` varchar(8) DEFAULT NULL,
  `Descrizione` varchar(40) DEFAULT NULL,
  `QUANT.` int(2) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `_CISS_GRO`
--

DROP TABLE IF EXISTS `_CISS_GRO`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_CISS_GRO` (
  `Materiale` varchar(9) DEFAULT NULL,
  `Descrizione` varchar(44) DEFAULT NULL,
  `QUANT.` int(2) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aggiornamento_fms7`
--

DROP TABLE IF EXISTS `aggiornamento_fms7`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aggiornamento_fms7` (
  `id` int(3) DEFAULT NULL,
  `parent_id` int(3) DEFAULT NULL,
  `livello` varchar(17) DEFAULT NULL,
  `ref_design` varchar(29) DEFAULT NULL,
  `ref_design_orig` varchar(23) DEFAULT NULL,
  `pn_supplier` varchar(16) DEFAULT NULL,
  `pn_manufacturer` varchar(16) DEFAULT NULL,
  `description` varchar(64) DEFAULT NULL,
  `sn_supplier` varchar(17) DEFAULT NULL,
  `sn_manufacturer` varchar(17) DEFAULT NULL,
  `sn` varchar(17) DEFAULT NULL,
  `system` varchar(3) DEFAULT NULL,
  `id_places` int(3) DEFAULT NULL,
  `location` varchar(7) DEFAULT NULL,
  `id_parts` int(4) DEFAULT NULL,
  `id_compatible` int(3) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `compatible_parts`
--

DROP TABLE IF EXISTS `compatible_parts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `compatible_parts` (
  `id_compatible` int(11) NOT NULL,
  `id_parts` int(11) unsigned DEFAULT NULL,
  UNIQUE KEY `id_parts` (`id_parts`),
  UNIQUE KEY `id_compatible` (`id_compatible`,`id_parts`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=envysoft@localhost*/ /*!50003 TRIGGER `compatible_before_insert` BEFORE INSERT ON `compatible_parts`
 FOR EACH ROW BEGIN

   DECLARE id_compatible int;

	IF NEW.id_compatible IS NULL THEN
        IF NEW.id_parts NOT IN  ( SELECT A.id_parts FROM compatible_parts A WHERE NEW.id_parts = A.id_parts) THEN
			SELECT max(id_compatible)+1  INTO id_compatible; 
			SET NEW.id_compatible=id_compatible;
        END IF;
 	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(50) CHARACTER SET latin1 NOT NULL,
  `filename` varchar(80) CHARACTER SET latin1 NOT NULL,
  `content` mediumblob NOT NULL,
  `checksum` varchar(40) CHARACTER SET latin1 NOT NULL,
  `type` varchar(30) CHARACTER SET latin1 NOT NULL,
  `size` int(11) NOT NULL,
  `id_simulators` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `events_types`
--

DROP TABLE IF EXISTS `events_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_files_types` int(11) NOT NULL,
  `name` varchar(256) CHARACTER SET latin1 NOT NULL,
  `content` mediumblob NOT NULL,
  `size` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files_types`
--

DROP TABLE IF EXISTS `files_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `files_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fotografia`
--

DROP TABLE IF EXISTS `fotografia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fotografia` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ordine` int(11) NOT NULL,
  `livello` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `ref_design` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `pn_supplier` varchar(50) CHARACTER SET utf8 NOT NULL,
  `pn_manufacturer` varchar(50) CHARACTER SET utf8 NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `system` varchar(20) CHARACTER SET utf8 NOT NULL,
  `id_places` int(11) NOT NULL,
  `location` varchar(50) CHARACTER SET utf8 NOT NULL,
  `id_parts` int(11) unsigned DEFAULT NULL,
  `id_compatible` int(11) DEFAULT NULL,
  `sn` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `items_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ordine_2` (`ordine`),
  KEY `id_parts` (`id_parts`),
  KEY `id_compatible` (`id_compatible`),
  KEY `ordine` (`ordine`)
) ENGINE=InnoDB AUTO_INCREMENT=6168 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fotografia_back`
--

DROP TABLE IF EXISTS `fotografia_back`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fotografia_back` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ordine` int(11) NOT NULL,
  `livello` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `ref_design` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `pn_supplier` varchar(50) CHARACTER SET utf8 NOT NULL,
  `pn_manufacturer` varchar(50) CHARACTER SET utf8 NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `system` varchar(20) CHARACTER SET utf8 NOT NULL,
  `id_places` int(11) NOT NULL,
  `location` varchar(50) CHARACTER SET utf8 NOT NULL,
  `id_parts` int(11) unsigned DEFAULT NULL,
  `id_compatible` int(11) DEFAULT NULL,
  `sn` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `items_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ordine_2` (`ordine`),
  KEY `id_parts` (`id_parts`),
  KEY `id_compatible` (`id_compatible`),
  KEY `ordine` (`ordine`)
) ENGINE=InnoDB AUTO_INCREMENT=5831 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fotografia_old`
--

DROP TABLE IF EXISTS `fotografia_old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fotografia_old` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ordine` int(11) NOT NULL,
  `livello` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `ref_design` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `pn_supplier` varchar(50) CHARACTER SET utf8 NOT NULL,
  `pn_manufacturer` varchar(50) CHARACTER SET utf8 NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `system` varchar(20) CHARACTER SET utf8 NOT NULL,
  `id_places` int(11) NOT NULL,
  `location` varchar(50) CHARACTER SET utf8 NOT NULL,
  `id_parts` int(11) unsigned DEFAULT NULL,
  `id_compatible` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_parts` (`id_parts`),
  KEY `id_compatible` (`id_compatible`),
  KEY `ordine` (`ordine`)
) ENGINE=InnoDB AUTO_INCREMENT=3580 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gioia`
--

DROP TABLE IF EXISTS `gioia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gioia` (
  `id` int(4) NOT NULL DEFAULT '0',
  `ordine` int(5) DEFAULT NULL,
  `livello` varchar(20) DEFAULT NULL,
  `parent_id` varchar(4) DEFAULT NULL,
  `ref_design` varchar(27) DEFAULT NULL,
  `pn_supplier` varchar(15) DEFAULT NULL,
  `pn_manufacturer` varchar(16) DEFAULT NULL,
  `description` varchar(80) DEFAULT NULL,
  `system` varchar(3) DEFAULT NULL,
  `id_places` int(2) DEFAULT NULL,
  `location` varchar(9) DEFAULT NULL,
  `id_parts` varchar(4) DEFAULT NULL,
  `id_compatible` int(3) DEFAULT NULL,
  `sn` varchar(26) DEFAULT NULL,
  `id_supplier` int(11) DEFAULT NULL,
  `id_movements` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_supllier` (`id_supplier`),
  KEY `id_movements` (`id_movements`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hits`
--

DROP TABLE IF EXISTS `hits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(32) CHARACTER SET latin1 NOT NULL,
  `username` varchar(32) CHARACTER SET latin1 NOT NULL,
  `time` varchar(32) CHARACTER SET latin1 NOT NULL,
  `browser` varchar(32) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `items` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_parts` int(11) unsigned NOT NULL,
  `id_owners` int(11) unsigned NOT NULL DEFAULT '0',
  `id_places` int(11) unsigned NOT NULL,
  `location` varchar(256) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `sn` varchar(64) CHARACTER SET latin1 DEFAULT NULL,
  `spare` tinyint(1) NOT NULL DEFAULT '-1',
  `id_users_creator` int(11) unsigned NOT NULL,
  `id_users_updater` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_parts` (`id_parts`,`sn`),
  KEY `id_parts_2` (`id_parts`),
  CONSTRAINT `items_ibfk_1` FOREIGN KEY (`id_parts`) REFERENCES `parts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16107 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `items_files`
--

DROP TABLE IF EXISTS `items_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `items_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_items` int(10) unsigned NOT NULL,
  `id_files` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `items_statuses`
--

DROP TABLE IF EXISTS `items_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `items_statuses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `movements`
--

DROP TABLE IF EXISTS `movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movements` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `insert_date` datetime NOT NULL,
  `id_places_from` int(11) NOT NULL,
  `id_places_to` int(11) NOT NULL,
  `in_transit` tinyint(1) NOT NULL,
  `note` varchar(200) CHARACTER SET latin1 NOT NULL,
  `id_users` int(11) NOT NULL DEFAULT '0',
  `id_simulators` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7877 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `movements_documents`
--

DROP TABLE IF EXISTS `movements_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movements_documents` (
  `id_movements` int(11) NOT NULL,
  `id_documents` int(11) NOT NULL,
  PRIMARY KEY (`id_movements`,`id_documents`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `movements_items`
--

DROP TABLE IF EXISTS `movements_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movements_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_movements` int(11) unsigned NOT NULL,
  `id_items` int(11) unsigned NOT NULL,
  `new_from_supplier` tinyint(1) NOT NULL DEFAULT '0',
  `to_repair` tinyint(1) NOT NULL DEFAULT '0',
  `replaced_itemId` int(10) unsigned DEFAULT NULL,
  `id_fotografia` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_movements` (`id_movements`,`id_items`),
  KEY `id_items` (`id_items`),
  KEY `id_fotografia` (`id_fotografia`),
  KEY `replaced_itemId` (`replaced_itemId`),
  CONSTRAINT `movements_items_ibfk_1` FOREIGN KEY (`id_movements`) REFERENCES `movements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `movements_items_ibfk_2` FOREIGN KEY (`id_items`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `movements_items_ibfk_3` FOREIGN KEY (`replaced_itemId`) REFERENCES `items` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25453 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=envysoft@localhost*/ /*!50003 TRIGGER `movements_items_insert` BEFORE INSERT ON `movements_items`
 FOR EACH ROW BEGIN
    IF NEW.id_fotografia = '0' THEN
        SET NEW.id_fotografia = NULL;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=envysoft@localhost*/ /*!50003 TRIGGER `movements_items_update` BEFORE UPDATE ON `movements_items`
 FOR EACH ROW BEGIN
    IF NEW.id_fotografia = '0' THEN
        SET NEW.id_fotografia = NULL;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `owners`
--

DROP TABLE IF EXISTS `owners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `owners` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) CHARACTER SET latin1 NOT NULL,
  `id_simulators` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`id_simulators`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `parts`
--

DROP TABLE IF EXISTS `parts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_suppliers` int(11) DEFAULT NULL,
  `id_manufacturers` int(11) DEFAULT NULL,
  `id_simulators` int(11) NOT NULL,
  `description` text CHARACTER SET latin1,
  `pn_manufacturer` varchar(64) CHARACTER SET latin1 DEFAULT NULL,
  `pn_supplier` varchar(64) CHARACTER SET latin1 NOT NULL,
  `cage_code` varchar(64) CHARACTER SET latin1 DEFAULT NULL,
  `shelf_life` int(11) DEFAULT NULL,
  `criticality` tinyint(3) unsigned DEFAULT '0',
  `minimum_quantity` int(10) unsigned DEFAULT NULL,
  `id_users_creator` int(11) unsigned NOT NULL,
  `id_users_updater` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `id_subsystems` int(11) NOT NULL DEFAULT '35',
  `id_subsystems2` int(11) NOT NULL DEFAULT '0',
  `id_subsystems3` int(11) NOT NULL DEFAULT '0',
  `CRD` char(1) CHARACTER SET latin1 NOT NULL DEFAULT 'R',
  `LRU` tinyint(1) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pn_supplier` (`pn_supplier`,`pn_manufacturer`,`id_suppliers`,`id_simulators`)
) ENGINE=InnoDB AUTO_INCREMENT=4773 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `places`
--

DROP TABLE IF EXISTS `places`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `places` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_places_types` int(10) unsigned NOT NULL,
  `name` varchar(256) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  `address` text CHARACTER SET latin1,
  `id_simulators` int(11) NOT NULL DEFAULT '0',
  `contact_name` varchar(256) CHARACTER SET latin1 DEFAULT NULL,
  `contact_email` varchar(256) CHARACTER SET latin1 DEFAULT NULL,
  `id_tspm_systems` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`id_simulators`,`id_places_types`)
) ENGINE=InnoDB AUTO_INCREMENT=771 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `places_types`
--

DROP TABLE IF EXISTS `places_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `places_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `simulators`
--

DROP TABLE IF EXISTS `simulators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `simulators` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subsystems`
--

DROP TABLE IF EXISTS `subsystems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subsystems` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `text` varchar(50) CHARACTER SET latin1 NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `id_simulators` int(11) NOT NULL DEFAULT '1',
  `line_order` tinyint(4) NOT NULL DEFAULT '1',
  `id_tspm_subsystems` tinyint(4) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQUE` (`text`,`id_simulators`)
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_users_types` int(11) NOT NULL,
  `id_simulators` int(10) unsigned NOT NULL,
  `username` varchar(64) CHARACTER SET latin1 NOT NULL,
  `password` varchar(64) CHARACTER SET latin1 NOT NULL,
  `name` varchar(64) CHARACTER SET latin1 DEFAULT NULL,
  `surname` varchar(64) CHARACTER SET latin1 DEFAULT NULL,
  `email` varchar(256) CHARACTER SET latin1 DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `expired` tinyint(1) NOT NULL DEFAULT '1',
  `rp` tinyint(4) NOT NULL DEFAULT '15',
  `id_user_tspm` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `username_2` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_types`
--

DROP TABLE IF EXISTS `users_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-05-26 14:57:46
