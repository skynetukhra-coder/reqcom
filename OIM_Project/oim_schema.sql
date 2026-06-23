-- MySQL dump 10.13  Distrib 8.0.46, for Win64 (x86_64)
--
-- Host: localhost    Database: oim
-- ------------------------------------------------------
-- Server version	8.0.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `items` (
  `item_id` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `item_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES ('BAT','Battery\r'),('CAR','Cartridge\r'),('DR','Drum\r'),('RL','Roll');
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `models`
--

DROP TABLE IF EXISTS `models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `models` (
  `model_id` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `item_id` varchar(11) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `model_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`model_id`),
  KEY `models_items_FK` (`item_id`),
  CONSTRAINT `models_items_FK` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `models`
--

LOCK TABLES `models` WRITE;
/*!40000 ALTER TABLE `models` DISABLE KEYS */;
INSERT INTO `models` VALUES ('AA','BAT','Mouse Battery'),('AAA','BAT','Keyboard Battery'),('BRO 2365','CAR','Brother HL-L2361dn'),('BROTHER DN-2365 DRUM','DR','Brother HL-L2361DN'),('CAN 057','CAR','Imageclass LBP 226dw'),('CAN 072','CAR','Imageclass LBP172dw'),('HP 131A[B]','CAR','HP Color LaserJet Pro m251n'),('HP 131A[C]','CAR','HP Color LaserJet Pro m251n'),('HP 131A[M]','CAR','HP Color LaserJet Pro m251n'),('HP 131A[Y]','CAR','HP Color LaserJet Pro m251n'),('HP 30','CAR','HP Laserjet Pro n203d'),('HP 32A','DR','HP LaserJet Pro n203d'),('HP 36A','CAR','HP Laserjet Pro 1505'),('HP 680 BLACK','CAR','HP DESKJET-3700'),('HP 680 TRI COLOUR','CAR','HP DESKJET-3700'),('HP 802','CAR','HP DESKJET-2050'),('HP 802 COLOUR','CAR','HP DESKJET-2050'),('HP 860','CAR','HP DESKJET-4268'),('HP 861','CAR','HP DESKJET-4268'),('HP 88A','CAR','HP Laserjet Pro 1007/m202dw'),('HP CE-310A BLACK','CAR','HP COLOUR LASERJET CP-1025'),('HP CE-311 CYAN','CAR','HP COLOUR LASERJET CP-1025'),('HP CE-312A YELLOW','CAR','HP COLOUR LASERJET CP-1025'),('HP CE-313A MA GENTA','CAR','HP COLOUR LASERJET CP-1025'),('HP CE-314A DRUM','DR','HP COLOUR LASERJET CP-1025'),('HP CF-210A BLACK','CAR','HP COLOUR LASERJET PRO M251N'),('HP CF-211 CYAN','CAR','HP COLOUR LASERJET PRO M251N'),('HP CF-212 YELLOW','CAR','HP COLOUR LASERJET PRO M251N'),('HP CF-213 MAGENTA','CAR','HP COLOUR LASERJET PRO M251N'),('HP12A','CAR','HP Laserjet Pro 1020/1022/3050'),('LIPI 6810L','CAR','Lipi Line Matrix Printer 6810L'),('NPG 59','DR','Canon 2006'),('RISO MASTER BLACK INK','RL','RISO INK CV BLACK UA (7220)'),('RISO MASTER MASTER ROLL','RL','RISO MASTER CV B4 (7040)');
/*!40000 ALTER TABLE `models` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_otp`
--

DROP TABLE IF EXISTS `password_otp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_otp` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `otp` varchar(6) COLLATE utf8mb4_general_ci NOT NULL,
  `expiry` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `password_otp_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_otp`
--

LOCK TABLES `password_otp` WRITE;
/*!40000 ALTER TABLE `password_otp` DISABLE KEYS */;
INSERT INTO `password_otp` VALUES (23,233,'908454','2026-05-22 12:08:26');
/*!40000 ALTER TABLE `password_otp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `requisition_stock_summary`
--

DROP TABLE IF EXISTS `requisition_stock_summary`;
/*!50001 DROP VIEW IF EXISTS `requisition_stock_summary`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `requisition_stock_summary` AS SELECT 
 1 AS `section_id`,
 1 AS `item_id`,
 1 AS `model_id`,
 1 AS `requisition_qty`,
 1 AS `stock_qty`,
 1 AS `today_date`,
 1 AS `last_approve_date`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `requisitions`
--

DROP TABLE IF EXISTS `requisitions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `requisitions` (
  `req_id` int NOT NULL AUTO_INCREMENT,
  `section_id` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `item_id` varchar(11) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `model_id` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `requested_by` int DEFAULT NULL,
  `assigned_bo` int DEFAULT NULL,
  `section_forward` varchar(1) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bo_approve` varchar(1) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `itsc_approve` varchar(1) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `approve_date` date NOT NULL,
  PRIMARY KEY (`req_id`),
  KEY `requisitions_models_FK` (`model_id`),
  KEY `requisitions_items_FK` (`item_id`),
  KEY `requisitions_users_FK` (`requested_by`),
  KEY `requisitions_users_FK_1` (`assigned_bo`),
  KEY `requisitions_sections_FK` (`section_id`),
  CONSTRAINT `requisitions_items_FK` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`),
  CONSTRAINT `requisitions_models_FK` FOREIGN KEY (`model_id`) REFERENCES `models` (`model_id`),
  CONSTRAINT `requisitions_sections_FK` FOREIGN KEY (`section_id`) REFERENCES `sections` (`section_id`),
  CONSTRAINT `requisitions_users_FK` FOREIGN KEY (`requested_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `requisitions_users_FK_1` FOREIGN KEY (`assigned_bo`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `requisitions`
--

LOCK TABLES `requisitions` WRITE;
/*!40000 ALTER TABLE `requisitions` DISABLE KEYS */;
INSERT INTO `requisitions` VALUES (1,'BOOK I','CAR','HP 36A',1,233,429,'Y','F','Y','2026-04-17'),(2,NULL,'DR','HP 32A',1,429,429,'Y','F','Y','2026-04-17'),(3,'BOOK I','CAR','HP 131A[M]',1,233,429,'Y','F','Y','2026-04-20'),(4,'LEGAL','CAR','HP 88A',1,233,429,'Y','F','Y','2026-04-20'),(5,'BUDGET','CAR','HP 30',1,233,429,'Y','F','Y','2026-04-24'),(6,NULL,'CAR','HP 88A',1,429,429,'Y','F','Y','2026-04-21'),(7,'DEPOSIT','CAR','HP 36A',1,233,429,'Y','F','Y','2026-04-20'),(8,NULL,'CAR','HP 88A',1,429,429,'Y','F','Y','2026-04-21'),(9,'PEN X','CAR','CAN 057',1,233,429,'Y','F','Y','2026-04-20'),(10,NULL,'CAR','CAN 057',1,429,429,'Y','F','Y','2026-04-20'),(11,'WELFARE','CAR','CAN 057',1,233,429,'Y','F','N','2026-04-20'),(12,NULL,'CAR','CAN 057',1,429,429,'Y','F','Y','2026-04-20'),(13,'INDUSTRY','CAR','HP 36A',1,233,429,'Y','F','Y','2026-04-20'),(14,'BUDGET','CAR','HP 36A',1,233,429,'Y','F','Y','2026-04-21'),(15,'BOOK I','CAR','CAN 057',1,233,429,'Y','F','Y','2026-04-21'),(16,'LEGAL','CAR','CAN 057',1,233,429,'Y','F','Y','2026-04-21'),(17,'PEN X','CAR','CAN 057',1,233,429,'Y','F','Y','2026-04-21'),(18,'BOOK I','CAR','HP 36A',1,233,429,'Y','F','Y','2026-04-21'),(19,NULL,'CAR','CAN 057',1,429,429,'Y','F','Y','2026-04-21'),(20,'AC I','CAR','HP 36A',11,233,429,'Y','F','Y','2026-04-21'),(21,NULL,'CAR','CAN 057',9,429,429,'Y','F','Y','2026-04-21'),(22,'AC II','CAR','HP 88A',10,233,429,'Y','F','Y','2026-04-21'),(23,NULL,'CAR','HP 36A',9,429,429,'Y','F','Y','2026-04-21'),(24,'BOOK I','CAR','CAN 057',25,234,429,'Y','F','Y','2026-04-21'),(25,NULL,'CAR','CAN 057',25,429,429,'Y','F','Y','2026-04-21'),(26,NULL,'CAR','CAN 057',3,429,429,'Y','F','Y','2026-04-21'),(27,NULL,'CAR','CAN 057',3,429,429,'Y','F','Y','2026-04-21'),(28,NULL,'CAR','CAN 057',3,429,429,'Y','F','Y','2026-04-21'),(29,'BOOK I','CAR','CAN 057',10,234,429,'Y','F','Y','2026-04-21'),(30,NULL,'CAR','CAN 057',5,429,429,'Y','F','Y','2026-04-21'),(31,NULL,'CAR','HP 36A',5,429,429,'Y','F','Y','2026-04-21'),(32,NULL,'CAR','HP 36A',4,429,429,'Y','F','Y','2026-04-21'),(33,NULL,'CAR','CAN 057',3,429,429,'Y','F','Y','2026-04-22'),(34,NULL,'CAR','HP 36A',5,429,429,'Y','F','Y','2026-04-22'),(35,NULL,'CAR','CAN 057',5,429,429,'Y','F',NULL,'2026-04-22'),(36,NULL,'CAR','HP 88A',5,429,429,'Y','F','Y','2026-04-22'),(37,'AC I','CAR','HP 36A',10,234,429,'Y','F',NULL,'2026-04-22'),(38,NULL,'BAT','AAA',4,429,429,'Y','F','N','2026-04-22'),(39,'AC I','CAR','CAN 057',2,234,429,'Y','F',NULL,'2026-04-22'),(40,NULL,'CAR','CAN 057',1,429,429,'Y','F',NULL,'2026-04-22'),(41,'BOOK II','CAR','HP 30',15,234,429,'Y','F','Y','2026-04-27'),(42,NULL,'CAR','CAN 057',11,429,429,'Y','F','N','2026-04-27'),(43,NULL,'DR','NPG 59',1,429,429,'Y','F','N','2026-04-27'),(44,'AC I','CAR','HP 36A',10,234,429,'Y','F','Y','2026-04-27'),(45,'AC II','CAR','HP 88A',1,234,429,'Y','F',NULL,'2026-06-01'),(46,'PEN EDP','BAT','AA',10,234,429,'Y','F',NULL,'2026-05-14'),(47,'WELFARE','BAT','AA',5,234,429,'Y','F','Y','2026-04-27'),(48,NULL,'CAR','CAN 057',2,429,429,'Y','F',NULL,'2026-04-27'),(49,'ADMIN II','CAR','HP 88A',1,233,429,'Y','F',NULL,'2026-05-27'),(50,'REC I','BAT','AAA',2,233,429,'Y','F',NULL,'2026-05-27'),(51,NULL,'CAR','HP 860',1,429,429,'Y','F',NULL,'2026-05-27'),(52,'AC I','CAR','BRO 2365',1,233,455,'Y',NULL,NULL,'2026-05-27'),(53,'AC I','CAR','HP 131A[C]',1,233,429,'Y','F',NULL,'2026-06-12'),(54,'AC I','BAT','AA',1,233,429,'Y',NULL,NULL,'2026-06-15'),(55,'REC I','CAR','RISO MASTER MASTER ROLL',2,385,444,'Y','F','Y','2026-06-19'),(56,'REC I','CAR','RISO MASTER BLACK INK',3,385,444,'Y','F','Y','2026-06-19'),(57,'AC II','CAR','HP 131A[B]',1,233,444,'Y','N',NULL,'2026-06-23');
/*!40000 ALTER TABLE `requisitions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sections` (
  `section_id` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `section_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`section_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections`
--

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
INSERT INTO `sections` VALUES ('AC I','ACCOUNT CURRENT-I\r'),('AC II','ACCOUNT CURRENT-II\r'),('AC III','ACCOUNT CURRENT-III\r'),('ADMIN I','ADMINISTRATION-I\r'),('ADMIN II','ADMINISTRATION-II\r'),('ADMIN III','ADMINISTRATION-III\r'),('AGRI','AGRICULTURE ACCOUNTS\r'),('AM','ACCOUNTS MISCELLANEOUS\r'),('AP','APPROPRIATION ACCOUNTS\r'),('BACS01','BRANCH OFFICER (ACCOUNTS-I) [BACS01]\r'),('BACS06','BRANCH OFFICER (ACCOUNTS-VI) [BACS06]\r'),('BACS10','BRANCH OFFICER (ACCOUNTS-X) [BACS10]\r'),('BACS16','PAY AND ACCOUNTS OFFICER-II [BACS16]\r'),('BADM02','BRANCH OFFICER (ADMIN-II) [BADM02]\r'),('BADM03','BRANCH OFFICER (RECORD) [BADM03]\r'),('BADM04','BRANCH OFFICER (LEGAL CELL) [BADM04]\r'),('BADM05','BRANCH OFFICER (ITSC) [BADM05]\r'),('BADM07','BRANCH OFFICER (WELFARE) [BADM07]\r'),('BFND02','BRANCH OFFICER (FUND-II) [BFND02]\r'),('BFND03','BRANCH OFFICER (FUND-III) [BFND03]\r'),('BFND04','BRANCH OFFICER (FUND-XVI) [BFND04]\r'),('BIAD01','INTERNAL AUDIT OFFICER [BIAD01]\r'),('BOOK I','BOOK-I\r'),('BOOK II','BOOK-II\r'),('BOOK III','BOOK-III\r'),('BPEN01','BRANCH OFFICER (PENSION-I) [BPEN01]\r'),('BS I','BROADSHEET-I\r'),('BS II','BROADSHEET-II\r'),('BS III','BROADSHEET-III\r'),('BS IV','BROADSHEET-V\r'),('BSEC01','SECRETARY TO PR. AG. [BSEC01]\r'),('BUDGET','BUDGET\r'),('BW','BOOK WORKS\r'),('CARETAKING','CARETAKING ESTABLISHMENT\r'),('CGF','CORE GROUP FUND\r'),('CR CELL','CR CELL\r'),('DEPOSIT','DEPOSIT\r'),('DIGITIZATION CELL','DIGITIZATION CELL\r'),('FA','FUND ACCOUNTS\r'),('FM','FUND MISCELLANEOUS\r'),('FOREST','FOREST ACCOUNTS\r'),('FUND I','FUND-I\r'),('FUND II','FUND-II\r'),('FUND III','FUND-III\r'),('FUND IV','FUND-IV\r'),('FUND IX','FUND-IX\r'),('FUND V','FUND-V\r'),('FUND VI','FUND-VI\r'),('FUND VII','FUND-VII\r'),('FUND VIII','FUND-VIII\r'),('FUND X','FUND-X\r'),('FUND XI','FUND-XI\r'),('FUND XII','FUND-XII\r'),('FUND XIII','FUND-XIII\r'),('FUND XIV','FUND-XIV\r'),('FUND XV','FUND-XV\r'),('FUND XVI','FUND-XVI\r'),('FUND XVII','FUND-XVII\r'),('GENERAL ADMIN','GENERAL ADMINISTRATION\r'),('GST','GST AND OTHER TAXES\r'),('IAD III','INTERNAL AUDIT DEPARTMENT-III\r'),('IAD IV','INTERNAL AUDIT DEPARTMENT-IV\r'),('IAD V','INTERNAL AUDIT DEPARTMENT-V\r'),('IADI','INTERNAL AUDIT DEPARTMENT-I\r'),('IADII','INTERNAL AUDIT DEPARTMENT-II\r'),('INDUSTRY','INDUSTRY ACCOOUNTS\r'),('IRRIGATION','IRRIGATION ACCOUNTS\r'),('ITSC','IT SUPPORT CELL\r'),('JAIL','JAIL & HOUSING\r'),('LEGAL','LEGAL CELL\r'),('MED I','MEDICAL ACS-I\r'),('MED II','MEDICAL ACS-II\r'),('MED III','MEDICAL ACS-III\r'),('MIDDLEWARE','MIDDLEWARE CELL\r'),('MISC','MISCELLANEOUS ACCOUNTS\r'),('PAO AUDIT','PAO AUDIT PENSION\r'),('PAO COMP','PAO COMPILATION\r'),('PAO FUND','PAO FUND\r'),('PAO NPS','PAO NPS CELL\r'),('PAO SPCL','PAO SPECIAL CELL\r'),('PC I','PENSION CELL-I\r'),('PC II','PENSION CELL-II\r'),('PC III','PENSION CELL-III\r'),('PEN COMP','PENSION COMPILATION\r'),('PEN COORD','PENSION COORDINATION\r'),('PEN EDP','PENSION EDP\r'),('PEN I','PENSION-I\r'),('PEN II','PENSION-II\r'),('PEN III','PENSION-III\r'),('PEN IV','PENSION-IV\r'),('PEN IX','PENSION-IX\r'),('PEN LIB','PENSION LIBRARY\r'),('PEN PAY','PENSION PAYMENT\r'),('PEN V','PENSION-V\r'),('PEN VI','PENSION-VI\r'),('PEN VII','PENSION-VII\r'),('PEN VIII','PENSION-VIII\r'),('PEN X','PENSION-X\r'),('PEN XI','PENSION-XI\r'),('PEN XII','PENSION-XII\r'),('PEN XIII','PENSION-XIII\r'),('PHE','PUBLIC HEALTH ACCOUNTS\r'),('POLICE','POLICE ACCOUNTS\r'),('PPA','PENSION PRE-AUDIT\r'),('PRD','PENSION RECEIPT AND DISPATCH (PRD)\r'),('PRECHECK I','PAO PRECHECK-I\r'),('PRECHECK II','PAO PRECHECK-II\r'),('PWD','PUBLIC WORKS ESTABLISHMENT\r'),('RCC','RECORD COMPUTER CELL\r'),('REC I','RECORD-I\r'),('REC III','RECORD-III\r'),('REC LIB','RECORD LIBRARY\r'),('RRD','RR & RD ACCOUNTS\r'),('section_id','section_name\r'),('SS','SS ACCOUNTS\r'),('TA I','TREASURY ACCOUNTS-I\r'),('TA II','TREASURY ACCOUNTS-II\r'),('TA III','TREASURY ACCOUNTS-III\r'),('TA IV','TREASURY ACCOUNTS-IV\r'),('TRAINING','TRAINING SECTION\r'),('VLC','VOUCHER LEVEL COMPUTERISATION\r'),('WELFARE','WELFARE SECTION\r'),('WM','WORKS MISCELLANEOUS SECTION\r'),('WORKS II','WORKS ACCOUNTS-II\r');
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `stock_quantity`
--

DROP TABLE IF EXISTS `stock_quantity`;
/*!50001 DROP VIEW IF EXISTS `stock_quantity`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `stock_quantity` AS SELECT 
 1 AS `model_id`,
 1 AS `total`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `stock_rcpt`
--

DROP TABLE IF EXISTS `stock_rcpt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_rcpt` (
  `stock_id` int NOT NULL AUTO_INCREMENT,
  `item_id` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `model_id` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `base_price` decimal(10,2) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL,
  `invoice_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `invoice_dt` timestamp NULL DEFAULT NULL,
  `invoice_dtl` longblob,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `remarks` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`stock_id`),
  KEY `stocks_items_FK` (`item_id`),
  KEY `stocks_models_FK` (`model_id`),
  CONSTRAINT `stocks_items_FK` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `stocks_models_FK` FOREIGN KEY (`model_id`) REFERENCES `models` (`model_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_rcpt`
--

LOCK TABLES `stock_rcpt` WRITE;
/*!40000 ALTER TABLE `stock_rcpt` DISABLE KEYS */;
INSERT INTO `stock_rcpt` VALUES (89,'RL','RISO MASTER MASTER ROLL',2,3268.75,6537.50,7714.25,'ESPL/26-27/K396','2026-06-10 07:00:00',_binary 'uploads/invoices/1781784843_Adobe Scan 18 Jun 2026 (1).pdf','2026-06-18 12:14:03','PURCHASE'),(90,'RL','RISO MASTER MASTER ROLL',0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-06-18 12:14:03','OB'),(91,'RL','RISO MASTER BLACK INK',0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-06-23 07:43:08','OB');
/*!40000 ALTER TABLE `stock_rcpt` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('Section Officer','Branch Officer','ITSC') COLLATE utf8mb4_general_ci NOT NULL,
  `created at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `designation` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `group_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `section` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mobile` int DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `users_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=464 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (233,'yashpals','$2y$10$og/dB5cu8rtCTpTQR1r6OucwEb7UuQBiwIvjEcSCm3jZjxnwXTTQG','YASHPAL SINGH','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','RECORD COMPUTER CELL','yashpals.wbl.ae@cag.gov.in',2147483647),(234,'vikask','$2y$10$QIj6uli56ZKfK9vtzzlw0.Q5wUwmd7WboUOPul2E1lmNePW9uRIpy','VIKASH KUMAR-II','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND-XII','vikask.wbl.ae.@cag.gov.in',2147483647),(235,'guptavk','','VIKASH KUMAR GUPTA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND-XV','guptavk.wbl.ae@cag.gov.in',2147483647),(236,'vikashk','','VIKASH KUMAR','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','MIDDLEWARE CELL','vikashk.wbl.ae@cag.gov.in',2147483647),(237,'ujjwalc','','UJJWAL KUMAR CHOWNI','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','','ujjwalc.wbl.ae@cag.gov.in',2147483647),(238,'totankumarm','','TOTAN KUMAR MONDAL','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','VOUCHER LEVEL COMPUTERISATION','totankumarm.wbl.ae@cag.gov.in',2147483647),(239,'acharyatapasi','','TAPASI ACHARYA (BASAK)','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND-XIII','acharyatapasi.wbl.ae@cag.gov.in',2147483647),(240,'moitratapas','','TAPAS MOITRA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION-VIII','moitratapas.wbl.ae@cag.gov.in',2147483647),(241,'tapanr','','TAPAN RAJAK','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','GST AND OTHER TAXES','tapanr.wbl.ae@cag.gov.in',2147483647),(242,'mukhot','','TANMOY MUKHOPADHYAY','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','BUDGET','mukhot.wbl.ae@cag.gov.in',2147483647),(243,'tanmoym','','TANMOY MUKHERJEE','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','IAD','INTERNAL AUDIT DEPARTMENT-I','tanmoym.wbl.ae@cag.gov.in',2147483647),(244,'tamalb','','TAMAL BERA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION-III','tamalb.wbl.ae@cag.gov.in',2147483647),(245,'swetag','','SWETA GHOSH','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','FUND-II','swetag.wbl.ae@cag.gov.in',2147483647),(246,'swarupd','','SWARUP DUTTA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND-VII','swarupd.wbl.ae@cag.gov.in',2147483647),(247,'swarnendug','','SWARNENDU GUHA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','SECRETARIATE','SECRETARY TO PR. AG. [BSEC01]','swarnendug.wbl.ae@cag.gov.in',2147483647),(248,'surswagata','','SWAGATA SUR','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','DIGITIZATION CELL','surswagata.wbl.ae@cag.gov.in',2147483647),(249,'basusutapa','','SUTAPA BASU','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','RECORD LIBRARY','basusutapa.wbl.ae@cag.gov.in',2147483647),(250,'berask','','SUSHANTA KUMAR  BERA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','PAO PRECHECK-II','berask.wbl.ae@cag.gov.in',2147483647),(251,'sushantad','','Sushanta Das','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','CARETAKING ESTABLISHMENT','sushantad.wbl.ae@cag.gov.in',2147483647),(252,'susenjitc','','SUSENJIT CHATTERJEE','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','IT SUPPORT CELL','susenjitc.wbl.ae@cag.gov.in',2147483647),(253,'sunil','','SUNIL KUMAR-I','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND-II','sunilk.wbl.ae@cag.gov.in',2147483647),(254,'sumanp','','SUMAN PAIRA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND-XVII','sumanp.wbl.ae@cag.gov.in',2147483647),(255,'sumanm','','SUMAN MUKHERJEE','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','SS ACCOUNTS','sumanm.wbl.ae@cag.gov.in',2147483647),(256,'sumanb','','SUMAN BHADRA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','ADMINISTRATION-I','sumanb.wbl.ae@cag.gov.in',2147483647),(257,'sukantan','','SUKANTA NAYEK','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','ADMINISTRATION-II','sukantan.wbl.ae@cag.gov.in',2147483647),(258,'sujoykumarg','','SUJOY KUMAR GHOSH','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND-XIV','sujoykumarg.wbl.ae@cag.gov.in',2147483647),(259,'sujayt','','SUJAY TALUKDAR','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION-XI','sujayt.wbl.ae@cag.gov.in',2147483647),(260,'samantask','','SUJAN KANTI SAMANTA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','PAO COMPILATION','samantask.wbl.ae@cag.gov.in',2147483647),(261,'sudiptac','','SUDIPTA CHAKRABORTY','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','ADMINISTRATION-I','sudiptac.wbl.ae@cag.gov.in',2147483647),(262,'sudiptab','','SUDIPTA BHOWMIK','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','LEGAL CELL','sudiptab.wbl.ae@cag.gov.in',2147483647),(263,'sudipd','','SUDIP KR. DASGUPTA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION COORDINATION','sudip_kdg@yahoo.co.in',2147483647),(264,'beheras','','SUCHITRA BEHERA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND-XVII','beheras.wbl.ae@cag.gov.in',2147483647),(265,'raysubir','','SUBIR KUMAR ROY','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','PUBLIC HEALTH ACCOUNTS','raysubir.wbl.ae@cag.gov.in',2147483647),(266,'nandysubir','','SUBIR KR. NANDY','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND-V','nandysubir.wbl.ae@cag.gov.in',2147483647),(267,'subhrojyotib','','SUBHROJYOTI BANERJEE','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION-X','subhrojyotib.wbl.ae@cag.gov.in',2147483647),(268,'subhodeeps','','SUBHODEEP CHATTOPADHYAY','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','PAO AUDIT PENSION','subhodeeps.wbl.ae@cag.gov.in',2147483647),(269,'subhasishde','','SUBHASISH DE','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','','subhasishde.wbl.ae@cag.gov.in',2147483647),(270,'subhasisb','','SUBHASIS BANDYOPADHYAY','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','ADMINISTRATION-I','subhasisb.kol.rti@cag.gov.in',2147483647),(271,'subhasr','','SUBHAS ROY','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','INDUSTRY ACCOOUNTS','subhasr.wbl.ae@cag.gov.in',2147483647),(272,'majumdars','','SUBHAS MAJUMDER','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','BOOK-II','majumdars.wbl.ae@cag.gov.in',2147483647),(273,'mondalsubha','','SUBHAS CH. MANDAL-II','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','INDUSTRY ACCOOUNTS','mondalsubhas.wbl.ae@cag.gov.in',2147483647),(274,'subhankarm','','SUBHANKAR MONDAL','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','PENSION COORDINATION','subhankarm.wbl.ae@cag.gov.in',2147483647),(275,'subhadiproy','','SUBHADIP ROY','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','PAO PRECHECK-I','subhadiproy.wbl.ae@cag.gov.in',2147483647),(276,'nandis','','SUBHABRATA NANDI','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION-I','nandis.wbl.ae@cag.gov.in',2147483647),(277,'srijitan','','SRIJITA NAG','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND MISCELLANEOUS','srijitan.wbl.ae@cag.gov.in',2147483647),(278,'sreyaseei','','SREYASEE INDRA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','DEPOSIT','sreyaseei.wbl.ae@cag.gov.in',2147483647),(279,'souravn','','SOURAV NASKAR','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','ADMINISTRATION-I','souravn.wbl.ae@cag.gov.in',2147483647),(280,'souravg','','SOURAV GHOSH','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','ADMINISTRATION-I','souravg.wbl.ae@cag.gov.in',2147483647),(281,'soumyojits','','SOUMYOJIT SENGUPTA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','RECORD-I','soumyojits.wbl.ae@cag.gov.in',2147483647),(282,'soumyam','','SOUMYA MONDAL','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','ADMINISTRATION-I','soumyam.wbl.ae@cag.gov.in',2147483647),(283,'soumyabratar','','SOUMYA BRATA ROY','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','CORE GROUP FUND','soumyabratar.wbl.ae@cag.gov.in',2147483647),(284,'soumitim','','SOUMITI MODAK','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','BOOK WORKS','soumitim.wbl.ae@cag.gov.in',2147483647),(285,'soumiby','','SOUMI BANDYOPADHYAY','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION-VI','soumiby.wbl.ae@cag.gov.in',2147483647),(286,'soumendrar','','SOUMENDRA RAKSHIT','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','PAO FUND','soumendrar.wbl.ae@cag.gov.in',2147483647),(287,'krsonu','','SONU KUMAR-II','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','SECRETARIATE','SECRETARY TO PR AG (A&E)','sonuk.wbl.ae@cag.gov.in',2147483647),(288,'sonuk','','SONU KUMAR-I','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','ADMINISTRATION-III','sonukr.wbl.ae@cag.gov.in',2147483647),(289,'sonatanp','','SONATAN PURKAIT','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','FUND-I','sonatanp.wbl.ae@cag.gov.in',2147483647),(290,'mondals','','SNEHASISH MONDAL','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','PUBLIC WORKS ESTABLISHMENT','mondals.wbl.ae@cag.gov.in',2147483647),(291,'damsnehasis','','SNEHASIS DAM','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','WELFARE SECTION','damsnehasis.wbl.ae@cag.gov.in',2147483647),(292,'safikula','','SK. SAFIKUL ALAM','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','DIGITIZATION CELL','sk.safikula.wbl.ae@cag.gov.in',2147483647),(293,'skhydara','','SK. HYDAR ALI','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','PENSION-VIII','skhydara.wbl.ae@cag.gov.in',2147483647),(294,'kerais','','SIKANDER KERAI','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','INTERNAL AUDIT DEPARTMENT-II','kerais.wbl.ae@cag.gov.in',2147483647),(295,'shubhenduk','','SHUBHENDU KUILA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','APPROPRIATION ACCOUNTS','shubhenduk.wbl.ae@cag.gov.in',2147483647),(296,'shaileshm','','SHAILESH KUMAR MEHTA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','CR CELL','shaileshm.wbl.ae@cag.gov.in',2147483647),(297,'sawankumard','','SAWAN KUMAR DUTTA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','PENSION CELL-II','sawankumard.wbl.ae@cag.gov.in',2147483647),(298,'sankhadipb','','SANKHADIP BHADURY','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','PAO AUDIT PENSION','sankhadipb.wbl.ae@cag.gov.in',2147483647),(299,'roysankar','','SANKAR ROY','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','ACCOUNT CURRENT-I','roysankar.wbl.ae@cag.gov.in',2147483647),(300,'mukherjeesan','','SANJOY MUKHERJEE','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','SS ACCOUNTS','mukherjeesan.wbl.ae@cag.gov.in',2147483647),(301,'sangitam','','SANGITA MONDAL','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','PENSION-X','sangitam.wbl.ae@cag.gov.in',2147483647),(302,'konars','','SANDIP KONAR','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','PAO COMPUTERISATION','konars.wbl.ae@cag.gov.in',2147483647),(303,'sandeepb','','SANDEEP MULLICK','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','INTERNAL AUDIT DEPARTMENT-III','sandeepb.wbl.ae@cag.gov.in',2147483647),(304,'samratm','$2y$10$rogJUdu63i8Nl0gRRJrsQ.bjXSC0H6fvA1n6aQ6WahHFzXvLPdjji','Samrat Mukherjee','ITSC','0000-00-00 00:00:00','ITSC','ACCOUNTS','BRANCH OFFICER (ITSC) [BADM05]','samratm.wbl.ae@cag.gov.in',2147483647),(305,'sachitkumars','','SACHIT KUMAR SINGH','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','ACCOUNTS MISCELLANEOUS','sachitkumars.wbl.ae@cag.gov.in',2147483647),(306,'sachintap','','Sachinta Patra','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','BOOK WORKS','sachintap.wbl.ae@cag.gov.in',2147483647),(307,'rupah','','RUPA HALDER','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','ACCOUNTS MISCELLANEOUS','rupah.wbl.ae@cag.gov.in',2147483647),(308,'rudranild','','RUDRANIL DAS','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','ADMINISTRATION-I','rudranild.wbl.ae@cag.gov.in',2147483647),(309,'rohitk','','ROHIT KUMAR','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','IAD','INTERNAL AUDIT DEPARTMENT-I','rohitk.wbl.ae@cag.gov.in',2147483647),(310,'rituparnag','','RITUPARNA GHOSH','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND MISCELLANEOUS','rituparnag.wbl.ae@cag.gov.in',2147483647),(311,'reshmas','','RESHMA SHIRIN IQBAL','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND MISCELLANEOUS','reshmas.wbl.ae@cag.gov.in',2147483647),(312,'bandyorb','','RASHBEHARI BANDYOPADHYAY','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','TREASURY ACCOUNTS-I','bandyorb.wbl.ae@cag.gov.in',2147483647),(313,'deraktim','','RAKTIM DE','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','BOOK-I','deraktim.wbl.ae@cag.gov.in',2147483647),(314,'rajibp','','RAJIB PAL','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION-XII','rajibp.wbl.ae@cag.gov.in',2147483647),(315,'prokashcg','','PROKASH CHANDRA GHOSH','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','FOREST ACCOUNTS','prokashcg.wbl.ae@cag.gov.in',2147483647),(316,'bhowmickp','','PROBIR BHOWMICK','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND-I','bhowmickp.wbl.ae@cag.gov.in',2147483647),(317,'priyatoshp','','PRIYATOSH PAL','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','FUND-IX','priyatoshp.wbl.ae@cag.gov.in',2147483647),(318,'pritishb','','PRITISH BANERJEE','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','BOOK-I','pritishb.wbl.ae@cag.gov.in',2147483647),(319,'prithar','','PRITHA RAY CHAUDHURI PAUL','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','ACCOUNT CURRENT-I','prithar.wbl.ae@cag.gov.in',2147483647),(320,'pritamm','','PRITAM MAJUMDER','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION COORDINATION','pritamm.wbl.ae@cag.gov.in',2147483647),(321,'Pritamg','','PRITAM GHOSH','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','IAD','INTERNAL AUDIT DEPARTMENT-III','Pritamg.wbl.ae@cag.gov.in',2147483647),(322,'premp','','PREM PRADIP','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION-IV','premp.wbl.ae@cag.gov.in',2147483647),(323,'pragyal','','PRAGYA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','ADMINISTRATION-II','pragyal.wbl.ae@cag.gov.in',2147483647),(324,'murmup','','PRABIR MURMU','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND-VIII','murmup.wbl.ae@cag.gov.in',2147483647),(325,'piyalis','','PIYALI SADHUKHAN','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','ADMINISTRATION-III','piyalis.wbl.ae@cag.gov.in',2147483647),(326,'dangphilip','','PHILIP DANG','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','BROADSHEET-V','dangphilip.wbl.ae@cag.gov.in',2147483647),(327,'danpartha','','PARTHA SARATHI DAN','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION CELL-III','danpartha.wbl.ae@cag.gov.in',2147483647),(328,'parthac','','PARTHA CHAKRABORTY','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND ACCOUNTS','parthac.wbl.ae@cag.gov.in',2147483647),(329,'pankajm','','PANKAJ MANNA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','PAO COMPUTERISATION','pankajm.wbl.ae@cag.gov.in',2147483647),(330,'pankajt','','PANKAJ KUMAR TIWARI','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','PAO SPECIAL CELL','pankajt.wbl.ae@cag.gov.in',2147483647),(331,'janapk','','PABITRA KUMAR JANA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND-VII','janapk.wbl.ae@cag.gov.in',2147483647),(332,'nishantk','','NISHANT KUMAR','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND-III','nishantk.wbl.ae@cag.gov.in',2147483647),(333,'niranjank','','NIRANJAN KUMAR','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','TREASURY ACCOUNTS-IV','niranjank.wbl.ae@cag.gov.in',2147483647),(334,'nirajp','','NIRAJ KUMAR PANDEY','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','WORKS MISCELLANEOUS SECTION','nirajp.wbl.ae@cag.gov.in',2147483647),(335,'nimaig','','NIMAI GAYEN','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','BOOK-I','nimaig.wbl.ae@cag.gov.in',2147483647),(336,'mrinmoyb','','MRINMOY BERA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION-VIII','mrinmoyb.wbl.ae@cag.gov.in',2147483647),(337,'manojd','','MONOJ DAS','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','PAO COMPILATION','manojd.wbl.ae@cag.gov.in',2147483647),(338,'monishranjanp','','MONISH RANJAN PAUL','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','BOOK-III','monishranjanp.wbl.ae@cag.gov.in',2147483647),(339,'guptameena','','MEENA GUPTA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION PRE-AUDIT','guptameena.wbl.ae@cag.gov.in',2147483647),(340,'kamaly','','MD. KAMAL YOUSUF','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','WORKS MISCELLANEOUS SECTION','md.kamaly.wbl.ae@cag.gov.in',2147483647),(341,'sadhukhanm','','MANTU SADHUKHAN','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','GENERAL ADMINISTRATION','sadhukhanm.wbl.ae@cag.gov.in',2147483647),(342,'manoranjang','','MANORANJAN GHOSH','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION-IX','manoranjang.wbl.ae@cag.gov.in',2147483647),(343,'manojkumarr','','MANOJ KUMAR RAM','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','MEDICAL ACS-III','manojkumarr.wbl.ae@cag.gov.in',2147483647),(344,'manishkumarc','','MANISH KUMAR CHOUDHARY','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','RECORD-III','manishkumarc.wbl.ae@cag.gov.in',2147483647),(345,'mandipkumard','','MANDIP KUMAR DAS','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION EDP','mandipkumard.wbl.ae@cag.gov.in',2147483647),(346,'manashg','','MANASH GHOSH','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','IAD','INTERNAL AUDIT DEPARTMENT-V','manashg.wbl.ae@cag.gov.in',2147483647),(347,'mallikarjunb','','Mallikarjun Banerjee','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','ADMINISTRATION-I','mallikarjunb.wbl.ae@cag.gov.in',2147483647),(348,'bhabakm','','MALAY BHABAK','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','RECORD-III','bhabakm.wbl.ae@cag.gov.in',2147483647),(349,'madhabcg','','MADHAB CHANDRA GHOSH','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND-XIV','madhabcg.wbl.ae@cag.gov.in',2147483647),(350,'madhabb','','MADHAB ADHIKARI','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','ROPA RIVISION CELL','madhabb.wbl.ae@cag.gov.in',2147483647),(351,'ghoshleena','','LEENA GHOSH','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','IRRIGATION ACCOUNTS','ghoshleena.wbl.ae@cag.gov.in',2147483647),(352,'kunalb','','KUNAL BISWAS','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','ACCOUNTS MISCELLANEOUS','kunalb.wbl.ae@cag.gov.in',2147483647),(353,'krishnendus','','KRISHNENDU SAHANA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','BOOK-II','krishnendus.wbl.ae@cag.gov.in',2147483647),(354,'ghoshk','','KOUSIK GHOSH','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','JAIL & HOUSING','ghoshk.wbl.ae@cag.gov.in',2147483647),(355,'mallickk','','KOUSHIK MALLICK','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','PENSION-V','mallickk.wbl.ae@cag.gov.in',2147483647),(356,'kingshukd','','KINGSHUK DASGUPTA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','LEGAL CELL','kingshukd.wbl.ae@cag.gov.in',2147483647),(357,'kaushalk','','KAUSHAL KUMAR','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND-VII','kaushalk.wbl.ae@cag.gov.in',2147483647),(358,'Kaushalkr','','KAUSHAL KUMAR','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','BOOK-II','Kaushalkr.wbl.ae@cag.giv.in',2147483647),(359,'kankanm','','KANKAN MAITRA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER (Adhoc)','ADMINISTRATION','RECORD COMPUTER CELL','kankanm.wbl.ae@cag.gov.in',2147483647),(360,'kanhaiyak','','KANHAIYA KUMAR','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER (Adhoc)','ADMINISTRATION','LEGAL CELL','kanhaiyak.wbl.ae@cag.gov.in',2147483647),(361,'kamaleshky','','KAMALESH KUMAR YADAV','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','ADMINISTRATION-II','kamaleshky.wbl.ae@cag.gov.in',2147483647),(362,'biruakc','','KALICHARAN BIRUA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION PAYMENT','biruakc.wbl.ae@cag.gov.in',2147483647),(363,'jitendras','','JITENDRA SHARMA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION CELL-I','jitendras.wbl.ae@cag.gov.in',2147483647),(364,'jitendrak','','JITENDRA KUMAR-II','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','RECORD COMPUTER CELL','jitendrakr.wbl.ae@cag.gov.in',2147483647),(365,'dasguptaj','','JAYANTA DASGUPTA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','MISCELLANEOUS ACCOUNTS','dasguptaj.wbl.ae@cag.gov.in',2147483647),(366,'janmenjoyr','','Janmenjoy Roy','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','ADMINISTRATION-II','janmenjoyr.wbl.ae@cag.gov.in',2147483647),(367,'indrajitm','','INDRAJIT MONDAL','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','ACCOUNT CURRENT-II','indrajitm.wbl.ae@cag.gov.in',2147483647),(368,'ignasiuss','','IGNASIUS SOREN','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','PENSION COMPILATION','ignasiuss.wbl.ae@cag.gov.in',2147483647),(369,'degoutamk','','GOUTAM KUMAR DE','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','CORE GROUP FUND','degoutamk.wbl.ae@cag.gov.in',2147483647),(370,'goutamd','','GOUTAM DAS','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','RECORD-I','goutamd.wbl.ae@cag.gov.in',2147483647),(371,'biswasgoutam','','GOUTAM BISWAS','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','WORKS ACCOUNTS-II','biswasgoutam.wbl.ae@cag.gov.in',2147483647),(372,'gourangas','','GOURANGA SAHA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND-XVI','gourangas.wbl.ae@cag.gov.in',2147483647),(373,'gourabs','','GOURAB SAHA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','IAD','INTERNAL AUDIT DEPARTMENT-IV','gourabs.wbl.ae@cag.gov.in',2147483647),(374,'gourabk','','GAURAV KARMAKAR','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND-XIV','gourabk.wbl.ae@cag.gov.in',2147483647),(375,'dipakl','','DIPAK PALIT','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION-XII','palitd.wbl.ae@nic.in',2147483647),(376,'dasdilip','','DILIP DAS','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS',' MIDDLEWARE CELL','dasdilip.wbl.ae@cag.gov.in',2147483647),(377,'dharmendrak','','DHARMENDRA KUMAR','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','RR & RD ACCOUNTS','dharmendrak.wbl.ae@cag.gov.in',2147483647),(378,'deepuk','','DEEPU KUMAR','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND-IX','deepuk.wbl.ae@cag.gov.in',2147483647),(379,'debjyotid','','DEBJYOTI DAS','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','ADMINISTRATION-I','debjyotid.wbl.ae@cag.gov.in',2147483647),(380,'debasishchou','','Debasish Choudhury','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','GENERAL ADMINISTRATION','debasishchou.wbl.ae@cag.gov.in',2147483647),(381,'acharyad','','DEBASHIS ACHARYA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','PAO NPS CELL','acharyad.wbl.ae@cag.gov.in',2147483647),(382,'debabratam','','DEBABRATA MALLICK','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','IAD','INTERNAL AUDIT DEPARTMENT-IV','debabratam.wbl.ae@cag.gov.in',2147483647),(383,'mondalcjit','','CHANDRAJIT MANDAL','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND-V','mondalcjit.wbl.ae@cag.gov.in',2147483647),(384,'daschanda','','CHANDAN DAS-I','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','FUND-I','daschandan.wbl.ae@cag.gov.in',2147483647),(385,'bilwaprasadc','$2y$10$GuEiVyJUHiK2PMi5FgjO0.3F58WRuT7qy7X2Qa075DfCAwP4CqRBW','BILWA PRASAD CHATTOPADHYAY','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','RECORD-I','bilwaprasadc.wbl.ae@cag.gov.in',2147483647),(386,'bijoyd','','BIJOY DEY','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','CORE GROUP FUND','bijoyd.wbl.ae@cag.gov.in',2147483647),(387,'surinb','','BIJAY SURIN','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION PAYMENT','surinb.wbl.ae@cag.gov.in',2147483647),(388,'bidyutkumark','','BIDYUT KUMAR KOLEY','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION-VII','bidyutkumark.wbl.ae@cag.gov.in',2147483647),(389,'babulb','','BABUL BISWAS','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','ACCOUNT CURRENT-III','babulb.wbl.ae@cag.gov.in',2147483647),(390,'duttaayan','','AYAN DUTTA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','RECORD-III','duttaayan.wbl.ae@cag.gov.in',2147483647),(391,'chakrabartia','','AYAN CHAKRABARTI','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION-XIII','chakrabartia.wbl.ae@cag.gov.in',2147483647),(392,'avijitg','','AVIJIT GHORUI','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND-V','avijitg.wbl.ae@cag.gov.in',2147483647),(393,'avijitd','','AVIJIT DUTTA - I','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','PENSION-XII','duttaabhijit.wbl.ae@cag.gov.in',2147483647),(394,'atulk','','ATUL KUMAR','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','RECORD-III','atulk.wbl.ae@cag.gov.in',2147483647),(395,'atikramb','','Atikram Basu','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION CELL-III','atikramb.wbl.ae@cag.gov.in',2147483647),(396,'atanud','','ATANU DAS BAGISH','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','ACCOUNTS MISCELLANEOUS','atanud.wbl.ae@cag.gov.in',2147483647),(397,'palasit','','ASIT BARAN PAL','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','INTERNAL AUDIT DEPARTMENT-V','palasit.wbl.ae@cag.gov.in',2147483647),(398,'yadavak','','ASHOK KUMAR YADAV','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','ROPA RIVISION CELL','yadavak.wbl.ae@cag.gov.in',2147483647),(399,'ashishs','','ASHISH SINGHA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION COORDINATION','ashishs.wbl.ae@cag.gov.in',2147483647),(400,'ashimh','','ASHIM KUMAR MANNA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','TRAINING SECTION','ashimkumarm.serly@cag.gov.in',2147483647),(401,'sahaarup','','ARUP SAHA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','FOREST ACCOUNTS','sahaarup.wbl.ae@cag.gov.in',2147483647),(402,'arupghosh','','ARUP GHOSH','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','WORKS MISCELLANEOUS SECTION','arupghosh.wbl.ae@cag.gov.in',2147483647),(403,'aruni','','ARUN INDRA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','PENSION COORDINATION','aruni.wbl.ae@cag.gov.in',2147483647),(404,'arindamn','','ARINDAM NATH','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','ADMINISTRATION-I','arindamn.wbl.ae@cag.gov.in',2147483647),(405,'dearindam','','ARINDAM DE','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','AGRICULTURE ACCOUNTS','dearindam.wbl.ae@cag.gov.in',2147483647),(406,'Anupsinha','','ANUP KUMAR SINHA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION RECEIPT AND DISPATCH (PRD)','Anupsinha.wbl.ae@cag.gov.in',2147483647),(407,'mondalanup','','ANUP KUMAR MANDAL','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION RECEIPT AND DISPATCH (PRD)','mondalanup.wbl.ae@cag.gov.in',2147483647),(408,'antardeepc','','ANTARDEEP CHANDA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','IAD','INTERNAL AUDIT DEPARTMENT-V','antardeepc.wbl.ae@cag.gov.in',2147483647),(409,'anshub','','ANSHU BALA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','BROADSHEET-II','anshub.wbl.ae@cag.gov.in',2147483647),(410,'dasann','','ANNESHA DAS','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','LEGAL CELL','dasann.wbl.ae@cag.gov.in',2147483647),(411,'anmolr','','ANMOL RAI','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','BOOK-III','anmolr.wbl.ae@cag.gov.in',2147483647),(412,'dasankur','','ANKUR DAS','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','ACCOUNTS MISCELLANEOUS','dasankur.wbl.ae@cag.gov.in',2147483647),(413,'ankanp','','ANKAN PAUL','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','ACCOUNTS MISCELLANEOUS','ankanp.wbl.ae@cag.gov.in',2147483647),(414,'aninditad','','ANINDITA DAS','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','PENSION PRE-AUDIT','aninditad.wbl.ae@cag.gov.in',2147483647),(415,'animikhc','','ANIMIKH CHOUDHARY','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','IAD','INTERNAL AUDIT DEPARTMENT-II','animikhc.wbl.ae@cag.gov.in',2147483647),(416,'amritat','','AMRITA TIWARI','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','TREASURY ACCOUNTS-II','amritat.wbl.ae@cag.gov.in',2147483647),(417,'sarkara','','AMITAVA SARKAR','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','RECORD COMPUTER CELL','sarkara.wbl.ae@cag.gov.in',2147483647),(418,'sarkaramit','','AMIT SARKAR','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND-VI','sarkaramit.wbl.ae@cag.gov.in',2147483647),(419,'amitkonar','','AMIT KONAR','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','IT SUPPORT CELL','amitkonar.wbl.ae@cag.gov.in',2147483647),(420,'maitraalok','','ALOK RANJAN MAITRA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','BROADSHEET-III','maitraalok.wbl.ae@cag.gov.in',2147483647),(421,'pradhanalak','','ALAKENDU PRADHAN','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','ACCOUNTS MISCELLANEOUS','pradhanalak.wbl.ae@cag.gov.in',2147483647),(422,'mankiakshay','','AKSHAY MANKI','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','POLICE ACCOUNTS','mankiakshay.wbl.ae@cag.gov.in',2147483647),(423,'ajoyd','','AJOY DAS','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','WORKS MISCELLANEOUS SECTION','ajoyd.wbl.ae@cag.gov.in',2147483647),(424,'basuajoy','','AJOY BASU','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','LEGAL CELL','basuajoy.wbl.ae@cag.gov.in',2147483647),(425,'ajitkumarp','','AJIT KUMAR PATHAK','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','PENSION','PENSION COORDINATION','ajitkumarp.wbl.ae@cag.gov.in',2147483647),(426,'abhishekkg','','ABHISHEK KUMAR GUPTA','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ACCOUNTS','WORKS MISCELLANEOUS SECTION','abhishekkg.wbl.ae@cag.gov.in',2147483647),(427,'dattaabh','','ABHIJIT DATTA - II','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','FUND','FUND-XII','dattaabhi.wbl.ae@cag.gov.in',2147483647),(428,'mondalabdul','','ABDUL WARISH MONDAL','','0000-00-00 00:00:00','ASSTT. ACCOUNTS OFFICER','ADMINISTRATION','TRAINING SECTION','mondalabdul.wbl.ae@cag.gov.in',2147483647),(429,'tapaskd','$2y$10$QIj6uli56ZKfK9vtzzlw0.Q5wUwmd7WboUOPul2E1lmNePW9uRIpy','TAPAS KUMAR DHAR','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','SECRETARIATE','SECRETARY TO PR AG (A&E)','dhartapk.wbl.ae@cag.gov.in',2147483647),(430,'supriyob','','SUPRIYO BANERJEE','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','ACCOUNTS','ACCOUNTS CURRENT-III','bansup.wbl.ae@cag.gov.in',2147483647),(431,'mondalsujit','','SUJIT MONDAL','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','PENSION','PENSION-II','mondalsujit.wbl.ae@cag.gov.in',2147483647),(432,'sensk','','SUJIT KUMAR SEN','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','IAD','INTERNAL AUDIT OFFICER [BIAD01]','sensk.wbl.ae@cag.gov.in',2147483647),(433,'banerjeesuj','','SUJAN BANERJEE','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','ADMINISTRATION','BRANCH OFFICER (WELFARE) [BADM07]','banerjeesuj.wbl.ae@cag.gov.in',2147483647),(434,'roysubrata','','SUBRATA ROY','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','ACCOUNTS','BOOK-I','roysubrata.wbl.ae@cag.gov.in',2147483647),(435,'duttasub','','SUBRATA DATTA','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','ACCOUNTS','APPROPRIATION ACCOUNTS','duttasub.wbl.ae@cag.gov.in',2147483647),(436,'bhattasd','','SUBHRADEEP BHATTACHERYA','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','ACCOUNTS','BRANCH OFFICER (ACCOUNTS-I) [BACS01]','bhattasd.wbl.ae@cag.gov.in',2147483647),(437,'dassubh','','SUBHENDU DAS','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','ACCOUNTS','BRANCH OFFICER (ACCOUNTS-I) [BACS01]','dassubh.wbl.ae@cag.gov.in',2147483647),(438,'chattsd','','SATYADIP CHATTOPADHYAY','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','ADMINISTRATION','ADMINISTRATION-I','chattsd.wbl.ae@cag.gov.in',2147483647),(439,'sensanat','','SANAT SEN','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','FUND','BRANCH OFFICER (FUND-II) [BFND02]','sensanat.wbl.ae@cag.gov.in',2147483647),(440,'basaksnath','','SAMBHU NATH BASAK','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','PENSION','PENSION CELL-I','basaksnath.wbl.ae@cag.gov.in',2147483647),(441,'podderrr','','REBATI RANJAN PODDER','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','ADMINISTRATION','BRANCH OFFICER (ADMIN-II) [BADM02]','podderrr.wbl.ae@cag.gov.in',2147483647),(442,'gangulyr','','RAJA GANGOPADHYAY','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','PENSION','PENSION COORDINATION','gangulyr.wbl.ae@cag.gov.in',2147483647),(443,'pradipk','','PRADIP KUMAR MONDAL-I','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','ACCOUNTS','BRANCH OFFICER (ACCOUNTS-VI) [BACS06]','mondalpk1.wbl.ae@cag.gov.in',2147483647),(444,'sahap','$2y$10$6d0n/gfkEJzSv5K8eH6YGOLCHF7NUYfTMU5XMeKoB40wa5hk0UnUG','PARTHA SAHA','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','ADMINISTRATION','BRANCH OFFICER (RECORD) [BADM03]','sahap.wbl.ae@cag.gov.in',2147483647),(445,'parthad','','PARTHA DAS-II','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','ADMINISTRATION','BRANCH OFFICER (RECORD) [BADM03]','daspartha2.wbl.ae@cag.gov.in',2147483647),(446,'menonps','','PANTHALINGAL SANDIP MENON','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','ADMINISTRATION','BRANCH OFFICER (LEGAL CELL) [BADM04]','menonps.wbl.ae@cag.gov.in',2147483647),(447,'sadhukhann','','NANDADULAL SADHUKHAN','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','FUND','BRANCH OFFICER (FUND-III) [BFND03]','sadhukhann.wbl.ae@cag.gov.in',2147483647),(448,'mitramk','','MRINAL KANTI MITRA','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','PENSION','BRANCH OFFICER (PENSION-I) [BPEN01]','mitramk.wbl.ae@cag.gov.in',2147483647),(449,'mandilk','','LAKSHMI KANTA MANDI','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','FUND','BRANCH OFFICER (FUND-III) [BFND03]','mandilk.wbl.ae@cag.gov.in',2147483647),(450,'kundukc','','KRISHNA CHANDRA KUNDU','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','ACCOUNTS','BRANCH OFFICER (ITSC) [BADM05]','kundukc.wbl.ae@cag.gov.in',2147483647),(451,'duttakk','','KOUSIK  KUMAR DUTTA','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','FUND','FUND-VII','duttakk.wbl.ae@cag.gov.in',2147483647),(452,'jitendran','','JITENDRA NATH DAS','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','ADMINISTRATION','PAY AND ACCOUNTS OFFICER-II [BACS16]','dasjitnath.wbl.ae@cag.gov.in',2147483647),(453,'sarkarij','','INDRAJIT SARKAR','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','PENSION','FUND-VII','sarkarij.wbl.ae@cag.gov.in',2147483647),(454,'barmanhl','','HIRALAL BARMAN','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','ACCOUNTS','BROADSHEET-I','barmanhl.wbl.ae@cag.gov.in',2147483647),(455,'paramanikd','','DEBATOSH PRAMANIK','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','ACCOUNTS','BOOK-I','paramanikd.wbl.ae@cag.gov.in',2147483647),(456,'debasishch','','DEBASISH CHAKRABORTY-I','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','ADMINISTRATION','BRANCH OFFICER (FUND-XVI) [BFND04]','chakrabortydebasish96@gmail.com',2147483647),(457,'roydebasish','','DEBASHIS ROY','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','PENSION','PENSION-IX','roydebasish.wbl.ae@cag.gov.in',2147483647),(458,'prasadb','','BIRENDRA PRASAD','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','ACCOUNTS','PUBLIC WORKS ESTABLISHMENT','prasadb.wbl.ae@cag.gov.in',2147483647),(459,'roybiplab','','BIPLAB ROY','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','ACCOUNTS','BOOK-III','roybiplab.wbl.ae@cag.gov.in',2147483647),(460,'sarkaravi','','AVIJIT SARKAR','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','FUND','FUND MISCELLANEOUS','sarkaravi.wbl.ae@cag.gov.in',2147483647),(461,'bhattaasish','','ASISH BHATTACHARJEE','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','PENSION','PENSION PAYMENT','bhattaasish.wbl.ae@cag.gov.in',2147483647),(462,'majasimk','','ASIM KUMAR MAJUMDAR','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','ACCOUNTS','BRANCH OFFICER (ACCOUNTS-X) [BACS10]','majasimk.wbl.ae@cag.gov.in',2147483647),(463,'nayarak','','ANIL KUMAR NAYAR','','0000-00-00 00:00:00','SR. ACCOUNTS OFFICER','PENSION','PENSION EDP','nayarak.wbl.ae@cag.gov.in',2147483647);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `requisition_stock_summary`
--

/*!50001 DROP VIEW IF EXISTS `requisition_stock_summary`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `requisition_stock_summary` AS select `r`.`section_id` AS `section_id`,`r`.`item_id` AS `item_id`,`r`.`model_id` AS `model_id`,`r`.`quantity` AS `requisition_qty`,greatest((ifnull(sum(`s`.`quantity`),0) - `r`.`quantity`),0) AS `stock_qty`,curdate() AS `today_date`,`la`.`last_approve_date` AS `last_approve_date` from ((`requisitions` `r` left join `stock_rcpt` `s` on(((`r`.`item_id` = `s`.`item_id`) and (`r`.`model_id` = `s`.`model_id`)))) left join (select `requisitions`.`section_id` AS `section_id`,max(`requisitions`.`approve_date`) AS `last_approve_date` from `requisitions` group by `requisitions`.`section_id`) `la` on((`r`.`section_id` = `la`.`section_id`))) group by `r`.`section_id`,`r`.`item_id`,`r`.`model_id`,`r`.`quantity`,`la`.`last_approve_date` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `stock_quantity`
--

/*!50001 DROP VIEW IF EXISTS `stock_quantity`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `stock_quantity` AS select `stock_rcpt`.`model_id` AS `model_id`,sum(`stock_rcpt`.`quantity`) AS `total` from `stock_rcpt` group by `stock_rcpt`.`model_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-23  3:57:11
