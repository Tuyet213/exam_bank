-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: exam_bank
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `bac_dao_taos`
--

DROP TABLE IF EXISTS `bac_dao_taos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bac_dao_taos` (
  `id` varchar(6) NOT NULL,
  `ten` varchar(255) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bac_dao_taos`
--

LOCK TABLES `bac_dao_taos` WRITE;
/*!40000 ALTER TABLE `bac_dao_taos` DISABLE KEYS */;
INSERT INTO `bac_dao_taos` VALUES ('CNTT63','C├┤ng nghß╗ç th├┤ng tin (K63)',1,'2025-05-06 17:21:03','2025-05-06 17:21:03');
/*!40000 ALTER TABLE `bac_dao_taos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bien_ban_hops`
--

DROP TABLE IF EXISTS `bien_ban_hops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bien_ban_hops` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_ct_ds_dang_ky` bigint(20) unsigned DEFAULT NULL,
  `thoi_gian` datetime NOT NULL,
  `noi_dung` text DEFAULT NULL,
  `dia_diem` varchar(255) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cap` varchar(255) DEFAULT NULL,
  `id_ds_dang_ky` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bien_ban_hops_id_ct_ds_dang_ky_foreign` (`id_ct_ds_dang_ky`),
  KEY `bien_ban_hops_id_ds_dang_ky_foreign` (`id_ds_dang_ky`),
  CONSTRAINT `bien_ban_hops_id_ct_ds_dang_ky_foreign` FOREIGN KEY (`id_ct_ds_dang_ky`) REFERENCES `c_t_d_s_dang_kies` (`id`),
  CONSTRAINT `bien_ban_hops_id_ds_dang_ky_foreign` FOREIGN KEY (`id_ds_dang_ky`) REFERENCES `d_s_dang_kies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bien_ban_hops`
--

LOCK TABLES `bien_ban_hops` WRITE;
/*!40000 ALTER TABLE `bien_ban_hops` DISABLE KEYS */;
/*!40000 ALTER TABLE `bien_ban_hops` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bo_mons`
--

DROP TABLE IF EXISTS `bo_mons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bo_mons` (
  `id` varchar(6) NOT NULL,
  `ten` varchar(255) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `id_khoa` varchar(6) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bo_mons_id_khoa_foreign` (`id_khoa`),
  CONSTRAINT `bo_mons_id_khoa_foreign` FOREIGN KEY (`id_khoa`) REFERENCES `khoas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bo_mons`
--

LOCK TABLES `bo_mons` WRITE;
/*!40000 ALTER TABLE `bo_mons` DISABLE KEYS */;
INSERT INTO `bo_mons` VALUES ('admin','Admin',0,'admin',NULL,NULL),('CNPM','C├┤ng nghß╗ç phß║ºn mß╗üm',1,'CNTT',NULL,NULL),('dbcl','─Éß║úm bß║úo chß║Ñt l╞░ß╗úng',0,'DBCL',NULL,NULL),('KTTT','Kß╗╣ thuß║¡t t├áu thß╗ºy',1,'KTGT','2025-05-06 17:20:10','2025-05-06 17:20:10'),('MMT','Mß║íng m├íy t├¡nh',1,'CNTT','2025-05-06 17:20:29','2025-05-06 17:20:29');
/*!40000 ALTER TABLE `bo_mons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c_t_d_s_dang_kies`
--

DROP TABLE IF EXISTS `c_t_d_s_dang_kies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c_t_d_s_dang_kies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_ds_dang_ky` bigint(20) unsigned NOT NULL,
  `id_hoc_phan` varchar(6) NOT NULL,
  `loai_ngan_hang` tinyint(1) NOT NULL DEFAULT 0,
  `hinh_thuc_thi` varchar(255) DEFAULT NULL,
  `so_luong` int(11) NOT NULL DEFAULT 0,
  `trang_thai` varchar(255) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `c_t_d_s_dang_kies_id_ds_dang_ky_foreign` (`id_ds_dang_ky`),
  KEY `c_t_d_s_dang_kies_id_hoc_phan_foreign` (`id_hoc_phan`),
  CONSTRAINT `c_t_d_s_dang_kies_id_ds_dang_ky_foreign` FOREIGN KEY (`id_ds_dang_ky`) REFERENCES `d_s_dang_kies` (`id`),
  CONSTRAINT `c_t_d_s_dang_kies_id_hoc_phan_foreign` FOREIGN KEY (`id_hoc_phan`) REFERENCES `hoc_phans` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c_t_d_s_dang_kies`
--

LOCK TABLES `c_t_d_s_dang_kies` WRITE;
/*!40000 ALTER TABLE `c_t_d_s_dang_kies` DISABLE KEYS */;
INSERT INTO `c_t_d_s_dang_kies` VALUES (1,1,'SOT111',1,'Trß║»c nghiß╗çm',150,'Approved',1,'2025-05-06 17:27:00','2025-05-06 17:30:47'),(2,1,'SOT555',1,'Tß╗▒ luß║¡n',100,'Approved',1,'2025-05-06 17:27:00','2025-05-06 17:34:03');
/*!40000 ALTER TABLE `c_t_d_s_dang_kies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c_t_de_this`
--

DROP TABLE IF EXISTS `c_t_de_this`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c_t_de_this` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_de_thi` bigint(20) unsigned NOT NULL,
  `id_cau_hoi` bigint(20) unsigned NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `c_t_de_this_id_de_thi_foreign` (`id_de_thi`),
  KEY `c_t_de_this_id_cau_hoi_foreign` (`id_cau_hoi`),
  CONSTRAINT `c_t_de_this_id_cau_hoi_foreign` FOREIGN KEY (`id_cau_hoi`) REFERENCES `cau_hois` (`id`),
  CONSTRAINT `c_t_de_this_id_de_thi_foreign` FOREIGN KEY (`id_de_thi`) REFERENCES `de_this` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c_t_de_this`
--

LOCK TABLES `c_t_de_this` WRITE;
/*!40000 ALTER TABLE `c_t_de_this` DISABLE KEYS */;
/*!40000 ALTER TABLE `c_t_de_this` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cau_hois`
--

DROP TABLE IF EXISTS `cau_hois`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cau_hois` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cau_hoi` text NOT NULL,
  `muc_do` varchar(255) DEFAULT NULL,
  `id_ct_ds_dang_ky` bigint(20) unsigned NOT NULL,
  `id_chuan_dau_ra` bigint(20) unsigned NOT NULL,
  `id_chuong` bigint(20) unsigned NOT NULL,
  `phan_loai` varchar(255) NOT NULL,
  `diem` double NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cau_hois_id_ct_ds_dang_ky_foreign` (`id_ct_ds_dang_ky`),
  KEY `cau_hois_id_chuan_dau_ra_foreign` (`id_chuan_dau_ra`),
  KEY `cau_hois_id_chuong_foreign` (`id_chuong`),
  CONSTRAINT `cau_hois_id_chuan_dau_ra_foreign` FOREIGN KEY (`id_chuan_dau_ra`) REFERENCES `chuan_dau_ras` (`id`),
  CONSTRAINT `cau_hois_id_chuong_foreign` FOREIGN KEY (`id_chuong`) REFERENCES `chuongs` (`id`),
  CONSTRAINT `cau_hois_id_ct_ds_dang_ky_foreign` FOREIGN KEY (`id_ct_ds_dang_ky`) REFERENCES `c_t_d_s_dang_kies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cau_hois`
--

LOCK TABLES `cau_hois` WRITE;
/*!40000 ALTER TABLE `cau_hois` DISABLE KEYS */;
/*!40000 ALTER TABLE `cau_hois` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chuan_dau_ras`
--

DROP TABLE IF EXISTS `chuan_dau_ras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chuan_dau_ras` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ten` varchar(255) NOT NULL,
  `noi_dung` text NOT NULL,
  `id_hoc_phan` varchar(6) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chuan_dau_ras_id_hoc_phan_foreign` (`id_hoc_phan`),
  CONSTRAINT `chuan_dau_ras_id_hoc_phan_foreign` FOREIGN KEY (`id_hoc_phan`) REFERENCES `hoc_phans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chuan_dau_ras`
--

LOCK TABLES `chuan_dau_ras` WRITE;
/*!40000 ALTER TABLE `chuan_dau_ras` DISABLE KEYS */;
INSERT INTO `chuan_dau_ras` VALUES (1,'a','aaaaaaaaa','SOT111',1,'2025-05-06 17:22:03','2025-05-06 17:22:03'),(2,'b','bbbbbbbbb','SOT111',1,'2025-05-06 17:22:03','2025-05-06 17:22:03'),(3,'c','cccccccccccccc','SOT555',1,'2025-05-06 17:26:05','2025-05-06 17:26:05'),(4,'d','dddddd─æ','SOT555',1,'2025-05-06 17:26:05','2025-05-06 17:26:05');
/*!40000 ALTER TABLE `chuan_dau_ras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chuc_vus`
--

DROP TABLE IF EXISTS `chuc_vus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chuc_vus` (
  `id` varchar(6) NOT NULL,
  `ten` varchar(255) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chuc_vus`
--

LOCK TABLES `chuc_vus` WRITE;
/*!40000 ALTER TABLE `chuc_vus` DISABLE KEYS */;
INSERT INTO `chuc_vus` VALUES ('admin','Admin',1,NULL,NULL),('gv','Giß║úng vi├¬n',1,NULL,NULL),('nvdbcl','Nh├ón vi├¬n ph├▓ng ─æß║úm bß║úo chß║Ñt l╞░ß╗úng',1,NULL,NULL),('tbm','Tr╞░ß╗ƒng bß╗Ö m├┤n',1,NULL,NULL),('tk','Tr╞░ß╗ƒng khoa',1,NULL,NULL);
/*!40000 ALTER TABLE `chuc_vus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chuong_chuan_dau_ras`
--

DROP TABLE IF EXISTS `chuong_chuan_dau_ras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chuong_chuan_dau_ras` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_chuong` bigint(20) unsigned NOT NULL,
  `id_chuan_dau_ra` bigint(20) unsigned NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chuong_chuan_dau_ras_id_chuong_foreign` (`id_chuong`),
  KEY `chuong_chuan_dau_ras_id_chuan_dau_ra_foreign` (`id_chuan_dau_ra`),
  CONSTRAINT `chuong_chuan_dau_ras_id_chuan_dau_ra_foreign` FOREIGN KEY (`id_chuan_dau_ra`) REFERENCES `chuan_dau_ras` (`id`),
  CONSTRAINT `chuong_chuan_dau_ras_id_chuong_foreign` FOREIGN KEY (`id_chuong`) REFERENCES `chuongs` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chuong_chuan_dau_ras`
--

LOCK TABLES `chuong_chuan_dau_ras` WRITE;
/*!40000 ALTER TABLE `chuong_chuan_dau_ras` DISABLE KEYS */;
INSERT INTO `chuong_chuan_dau_ras` VALUES (1,1,1,1,'2025-05-06 17:22:03','2025-05-06 17:22:03'),(2,1,2,1,'2025-05-06 17:22:03','2025-05-06 17:22:03'),(3,2,2,1,'2025-05-06 17:22:03','2025-05-06 17:22:03'),(4,3,3,1,'2025-05-06 17:26:05','2025-05-06 17:26:05'),(5,4,4,1,'2025-05-06 17:26:05','2025-05-06 17:26:05');
/*!40000 ALTER TABLE `chuong_chuan_dau_ras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chuongs`
--

DROP TABLE IF EXISTS `chuongs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chuongs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ten` varchar(255) NOT NULL,
  `id_hoc_phan` varchar(6) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chuongs_id_hoc_phan_foreign` (`id_hoc_phan`),
  CONSTRAINT `chuongs_id_hoc_phan_foreign` FOREIGN KEY (`id_hoc_phan`) REFERENCES `hoc_phans` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chuongs`
--

LOCK TABLES `chuongs` WRITE;
/*!40000 ALTER TABLE `chuongs` DISABLE KEYS */;
INSERT INTO `chuongs` VALUES (1,'1','SOT111',1,'2025-05-06 17:22:03','2025-05-06 17:22:03'),(2,'2','SOT111',1,'2025-05-06 17:22:03','2025-05-06 17:22:03'),(3,'1','SOT555',1,'2025-05-06 17:26:05','2025-05-06 17:26:05'),(4,'2','SOT555',1,'2025-05-06 17:26:05','2025-05-06 17:26:05');
/*!40000 ALTER TABLE `chuongs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d_s_dang_kies`
--

DROP TABLE IF EXISTS `d_s_dang_kies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d_s_dang_kies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_bo_mon` varchar(6) NOT NULL,
  `hoc_ki` varchar(255) DEFAULT NULL,
  `nam_hoc` varchar(255) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `d_s_dang_kies_id_bo_mon_foreign` (`id_bo_mon`),
  CONSTRAINT `d_s_dang_kies_id_bo_mon_foreign` FOREIGN KEY (`id_bo_mon`) REFERENCES `bo_mons` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d_s_dang_kies`
--

LOCK TABLES `d_s_dang_kies` WRITE;
/*!40000 ALTER TABLE `d_s_dang_kies` DISABLE KEYS */;
INSERT INTO `d_s_dang_kies` VALUES (1,'CNPM','2','2024-2025',1,'2025-05-06 17:27:00','2025-05-06 17:27:00');
/*!40000 ALTER TABLE `d_s_dang_kies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d_s_g_v_bien_soans`
--

DROP TABLE IF EXISTS `d_s_g_v_bien_soans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d_s_g_v_bien_soans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_ct_ds_dang_ky` bigint(20) unsigned NOT NULL,
  `id_vien_chuc` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `so_gio` double NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `d_s_g_v_bien_soans_id_ct_ds_dang_ky_foreign` (`id_ct_ds_dang_ky`),
  KEY `d_s_g_v_bien_soans_id_vien_chuc_foreign` (`id_vien_chuc`),
  CONSTRAINT `d_s_g_v_bien_soans_id_ct_ds_dang_ky_foreign` FOREIGN KEY (`id_ct_ds_dang_ky`) REFERENCES `c_t_d_s_dang_kies` (`id`),
  CONSTRAINT `d_s_g_v_bien_soans_id_vien_chuc_foreign` FOREIGN KEY (`id_vien_chuc`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d_s_g_v_bien_soans`
--

LOCK TABLES `d_s_g_v_bien_soans` WRITE;
/*!40000 ALTER TABLE `d_s_g_v_bien_soans` DISABLE KEYS */;
INSERT INTO `d_s_g_v_bien_soans` VALUES (1,1,'GV001','2025-05-06 17:27:00','2025-05-06 17:27:00',0),(2,1,'GV002','2025-05-06 17:27:00','2025-05-06 17:27:00',0),(3,2,'GV001','2025-05-06 17:27:00','2025-05-06 17:27:00',0);
/*!40000 ALTER TABLE `d_s_g_v_bien_soans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `d_s_hops`
--

DROP TABLE IF EXISTS `d_s_hops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `d_s_hops` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_bien_ban_hop` bigint(20) unsigned NOT NULL,
  `id_nhiem_vu` bigint(20) unsigned NOT NULL,
  `id_vien_chuc` varchar(6) NOT NULL,
  `so_gio` decimal(8,2) DEFAULT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `d_s_hops_id_bien_ban_hop_foreign` (`id_bien_ban_hop`),
  KEY `d_s_hops_id_nhiem_vu_foreign` (`id_nhiem_vu`),
  KEY `d_s_hops_id_vien_chuc_foreign` (`id_vien_chuc`),
  CONSTRAINT `d_s_hops_id_bien_ban_hop_foreign` FOREIGN KEY (`id_bien_ban_hop`) REFERENCES `bien_ban_hops` (`id`),
  CONSTRAINT `d_s_hops_id_nhiem_vu_foreign` FOREIGN KEY (`id_nhiem_vu`) REFERENCES `nhiem_vus` (`id`),
  CONSTRAINT `d_s_hops_id_vien_chuc_foreign` FOREIGN KEY (`id_vien_chuc`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `d_s_hops`
--

LOCK TABLES `d_s_hops` WRITE;
/*!40000 ALTER TABLE `d_s_hops` DISABLE KEYS */;
/*!40000 ALTER TABLE `d_s_hops` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dap_ans`
--

DROP TABLE IF EXISTS `dap_ans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dap_ans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_cau_hoi` bigint(20) unsigned NOT NULL,
  `dap_an` varchar(255) NOT NULL,
  `diem` decimal(4,2) NOT NULL DEFAULT 0.00,
  `trang_thai` tinyint(1) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dap_ans_id_cau_hoi_foreign` (`id_cau_hoi`),
  CONSTRAINT `dap_ans_id_cau_hoi_foreign` FOREIGN KEY (`id_cau_hoi`) REFERENCES `cau_hois` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dap_ans`
--

LOCK TABLES `dap_ans` WRITE;
/*!40000 ALTER TABLE `dap_ans` DISABLE KEYS */;
/*!40000 ALTER TABLE `dap_ans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `de_this`
--

DROP TABLE IF EXISTS `de_this`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `de_this` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_hoc_phan` varchar(6) NOT NULL,
  `id_lop_hoc_phan` bigint(20) unsigned NOT NULL,
  `hoc_ky` tinyint(4) NOT NULL,
  `nam` smallint(6) NOT NULL,
  `ngay_thi` date NOT NULL,
  `loai` varchar(255) NOT NULL,
  `su_dung_tai_lieu` tinyint(1) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `de_this_id_hoc_phan_foreign` (`id_hoc_phan`),
  KEY `de_this_id_lop_hoc_phan_foreign` (`id_lop_hoc_phan`),
  CONSTRAINT `de_this_id_hoc_phan_foreign` FOREIGN KEY (`id_hoc_phan`) REFERENCES `hoc_phans` (`id`),
  CONSTRAINT `de_this_id_lop_hoc_phan_foreign` FOREIGN KEY (`id_lop_hoc_phan`) REFERENCES `lop_hoc_phans` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `de_this`
--

LOCK TABLES `de_this` WRITE;
/*!40000 ALTER TABLE `de_this` DISABLE KEYS */;
/*!40000 ALTER TABLE `de_this` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gio_quy_dois`
--

DROP TABLE IF EXISTS `gio_quy_dois`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gio_quy_dois` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gio` tinyint(4) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `loai_de_thi` varchar(255) DEFAULT NULL,
  `loai_hanh_dong` varchar(255) DEFAULT NULL,
  `so_luong` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gio_quy_dois`
--

LOCK TABLES `gio_quy_dois` WRITE;
/*!40000 ALTER TABLE `gio_quy_dois` DISABLE KEYS */;
INSERT INTO `gio_quy_dois` VALUES (1,3,1,'2025-05-06 17:22:33','2025-05-06 17:22:33','0','0',15),(2,1,1,'2025-05-06 17:22:55','2025-05-06 17:22:55','1','0',2),(3,2,1,'2025-05-06 17:23:07','2025-05-06 17:23:07','0','1',15),(4,2,1,'2025-05-06 17:23:22','2025-05-06 17:23:22','1','1',2);
/*!40000 ALTER TABLE `gio_quy_dois` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hoc_phans`
--

DROP TABLE IF EXISTS `hoc_phans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hoc_phans` (
  `id` varchar(6) NOT NULL,
  `ten` varchar(255) NOT NULL,
  `so_tin_chi` tinyint(3) unsigned NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `id_bo_mon` varchar(6) NOT NULL,
  `id_bac_dao_tao` varchar(6) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hoc_phans_id_bo_mon_foreign` (`id_bo_mon`),
  KEY `hoc_phans_id_bac_dao_tao_foreign` (`id_bac_dao_tao`),
  CONSTRAINT `hoc_phans_id_bac_dao_tao_foreign` FOREIGN KEY (`id_bac_dao_tao`) REFERENCES `bac_dao_taos` (`id`),
  CONSTRAINT `hoc_phans_id_bo_mon_foreign` FOREIGN KEY (`id_bo_mon`) REFERENCES `bo_mons` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hoc_phans`
--

LOCK TABLES `hoc_phans` WRITE;
/*!40000 ALTER TABLE `hoc_phans` DISABLE KEYS */;
INSERT INTO `hoc_phans` VALUES ('SOT111','Nhß║¡p m├┤n lß║¡p tr├¼nh',3,1,'CNPM','CNTT63','2025-05-06 17:22:03','2025-05-06 17:22:03'),('SOT555','Kß╗╣ thuß║¡t lß║¡p tr├¼nh',3,1,'CNPM','CNTT63','2025-05-06 17:26:05','2025-05-06 17:26:05');
/*!40000 ALTER TABLE `hoc_phans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `khoas`
--

DROP TABLE IF EXISTS `khoas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `khoas` (
  `id` varchar(6) NOT NULL,
  `ten` varchar(255) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `khoas`
--

LOCK TABLES `khoas` WRITE;
/*!40000 ALTER TABLE `khoas` DISABLE KEYS */;
INSERT INTO `khoas` VALUES ('admin','Admin',0,NULL,NULL),('CNTT','C├┤ng nghß╗ç th├┤ng tin',1,NULL,NULL),('DBCL','─Éß║úm bß║úo chß║Ñt l╞░ß╗úng',0,NULL,NULL),('KTGT','Kß╗╣ thuß║¡t giao th├┤ng',1,'2025-05-06 17:19:46','2025-05-06 17:19:46');
/*!40000 ALTER TABLE `khoas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lop_hoc_phans`
--

DROP TABLE IF EXISTS `lop_hoc_phans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lop_hoc_phans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ten` varchar(255) NOT NULL,
  `ky_hoc` varchar(255) NOT NULL,
  `nam_hoc` varchar(255) NOT NULL,
  `id_khoa` varchar(6) NOT NULL,
  `id_hoc_phan` varchar(6) NOT NULL,
  `id_vien_chuc` varchar(6) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lop_hoc_phans_id_khoa_foreign` (`id_khoa`),
  KEY `lop_hoc_phans_id_hoc_phan_foreign` (`id_hoc_phan`),
  KEY `lop_hoc_phans_id_vien_chuc_foreign` (`id_vien_chuc`),
  CONSTRAINT `lop_hoc_phans_id_hoc_phan_foreign` FOREIGN KEY (`id_hoc_phan`) REFERENCES `hoc_phans` (`id`),
  CONSTRAINT `lop_hoc_phans_id_khoa_foreign` FOREIGN KEY (`id_khoa`) REFERENCES `khoas` (`id`),
  CONSTRAINT `lop_hoc_phans_id_vien_chuc_foreign` FOREIGN KEY (`id_vien_chuc`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lop_hoc_phans`
--

LOCK TABLES `lop_hoc_phans` WRITE;
/*!40000 ALTER TABLE `lop_hoc_phans` DISABLE KEYS */;
/*!40000 ALTER TABLE `lop_hoc_phans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ma_trans`
--

DROP TABLE IF EXISTS `ma_trans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ma_trans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_chuan_dau_ra` bigint(20) unsigned NOT NULL,
  `id_chuong` bigint(20) unsigned NOT NULL,
  `diem` double NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `ma_trans_id_chuan_dau_ra_foreign` (`id_chuan_dau_ra`),
  KEY `ma_trans_id_chuong_foreign` (`id_chuong`),
  CONSTRAINT `ma_trans_id_chuan_dau_ra_foreign` FOREIGN KEY (`id_chuan_dau_ra`) REFERENCES `chuan_dau_ras` (`id`),
  CONSTRAINT `ma_trans_id_chuong_foreign` FOREIGN KEY (`id_chuong`) REFERENCES `chuongs` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ma_trans`
--

LOCK TABLES `ma_trans` WRITE;
/*!40000 ALTER TABLE `ma_trans` DISABLE KEYS */;
/*!40000 ALTER TABLE `ma_trans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2025_02_18_031743_create_khoas_table',1),(2,'2025_02_18_031910_create_bo_mons_table',1),(3,'2025_02_18_031945_create_chuc_vus_table',1),(4,'2025_02_18_031956_create_bac_dao_taos_table',1),(5,'2025_02_18_032007_create_hoc_phans_table',1),(6,'2025_02_18_032014_create_chuongs_table',1),(7,'2025_02_18_032024_create_chuan_dau_ras_table',1),(8,'2025_02_18_032045_create_ma_trans_table',1),(9,'2025_02_18_032100_create_d_s_dang_kies_table',1),(10,'2025_02_18_032107_create_gio_quy_dois_table',1),(11,'2025_02_18_032108_create_cache_table',1),(12,'2025_02_18_032108_create_jobs_table',1),(13,'2025_02_18_032108_create_permission_tables',1),(14,'2025_02_18_032108_create_users_table',1),(15,'2025_02_18_032109_create_c_t_d_s_dang_kies_table',1),(16,'2025_02_18_032114_create_lops_table',1),(17,'2025_02_18_032126_create_cau_hois_table',1),(18,'2025_02_18_032133_create_dap_ans_table',1),(19,'2025_02_18_032139_create_de_this_table',1),(20,'2025_02_18_032147_create_c_t_de_this_table',1),(21,'2025_02_18_032156_create_nhiem_vus_table',1),(22,'2025_02_18_032203_create_bien_ban_hops_table',1),(23,'2025_02_18_032210_create_d_s_hops_table',1),(24,'2025_02_22_072954_create_chuong_chuan_dau_ras_table',1),(25,'2025_03_05_161619_add_able_to_user',1),(26,'2025_03_06_161650_add_timestamps_to_chuandaura',1),(27,'2025_03_10_075020_add_cap_column_into_bienbanhop',1),(28,'2025_03_10_075207_remove_hocphi_from_hocphan',1),(29,'2025_03_10_075354_revise_gio_qui_doi',1),(30,'2025_03_12_100527_rm_sluong_from_lophocphans',1),(31,'2025_03_23_172433_create_table_thong_baos',1),(32,'2025_03_23_172913_update_table_thong_baos',1),(33,'2025_03_29_150653_modify_model_id_to_string',1),(34,'2025_03_30_194612_update_sessions_table_user_id_type',1),(35,'2025_04_03_162619_add_ten_column_to_d_s_dang_kies_table',1),(36,'2025_04_13_110946_add_hoc_ki_to_d_s_dang_kies_table',1),(37,'2025_04_13_111122_remove_ten_from_d_s_dang_kies_table',1),(38,'2025_04_13_160025_add_loai_ngan_hang_and_so_luong_to_c_t_d_s_dang_kies_table',1),(39,'2025_04_13_160036_add_fields_to_ctdsdangky',1),(40,'2025_04_14_011703_modify_d_s_dang_kies_table',1),(41,'2025_04_16_121211_update_bien_ban_hops_table_allow_null_fields',1),(42,'2025_04_16_122043_update_d_s_hops_table_allow_null_so_gio',1),(43,'2025_04_17_073629_add_hinh_thuc_thi_to_c_t_d_s_dang_kies_table',1),(44,'2025_04_17_084935_add_id_ds_dang_ky_to_bien_ban_hops_table',1),(45,'2025_04_26_173937_remove_id_vien_chuc_from_ctdsdangky',1),(46,'2025_04_26_173942_create_d_s_g_v_bien_soans_table',1),(47,'2025_04_26_191428_remove_so_gio_from_c_t_d_s_dang_kies_table',1),(48,'2025_04_26_191435_add_so_gio_to_d_s_g_v_bien_soans_table',1),(49,'2025_05_01_000002_change_id_hoc_phan_to_string',1),(50,'2025_05_01_030356_add_muc_do_to_cau_hois_table',1),(51,'2025_05_01_055648_add_timestamps_to_chuongs_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` varchar(255) NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User','admin'),(2,'App\\Models\\User','GV003'),(3,'App\\Models\\User','NV001'),(4,'App\\Models\\User','GV002'),(5,'App\\Models\\User','GV001');
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nhiem_vus`
--

DROP TABLE IF EXISTS `nhiem_vus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nhiem_vus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ten` varchar(255) NOT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nhiem_vus`
--

LOCK TABLES `nhiem_vus` WRITE;
/*!40000 ALTER TABLE `nhiem_vus` DISABLE KEYS */;
INSERT INTO `nhiem_vus` VALUES (1,'Chß╗º tß╗ïch',1,NULL,NULL),(2,'Th╞░ k├╜',1,NULL,NULL),(3,'C├ín bß╗Ö phß║ún biß╗çn',1,NULL,NULL);
/*!40000 ALTER TABLE `nhiem_vus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'Tß║ío ng├ón h├áng c├óu hß╗Åi/─æß╗ü thi','web','2025-05-06 17:18:06','2025-05-06 17:18:06'),(2,'Xuß║Ñt ─æß╗ü thi','web','2025-05-06 17:18:06','2025-05-06 17:18:06'),(3,'Duyß╗çt danh s├ích ─æ─âng k├╜','web','2025-05-06 17:18:06','2025-05-06 17:18:06');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(1,2),(2,1),(2,2),(3,1),(3,3);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin','web','2025-05-06 17:18:06','2025-05-06 17:18:06'),(2,'Giß║úng vi├¬n','web','2025-05-06 17:18:06','2025-05-06 17:18:06'),(3,'Nh├ón vi├¬n P.─ÉBCL','web','2025-05-06 17:18:06','2025-05-06 17:18:06'),(4,'Tr╞░ß╗ƒng Bß╗Ö M├┤n','web','2025-05-06 17:18:07','2025-05-06 17:18:07'),(5,'Tr╞░ß╗ƒng Khoa','web','2025-05-06 17:18:07','2025-05-06 17:18:07');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `thong_baos`
--

DROP TABLE IF EXISTS `thong_baos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `thong_baos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `files` varchar(255) DEFAULT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `thong_baos`
--

LOCK TABLES `thong_baos` WRITE;
/*!40000 ALTER TABLE `thong_baos` DISABLE KEYS */;
/*!40000 ALTER TABLE `thong_baos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` varchar(6) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `sdt` varchar(10) NOT NULL,
  `dia_chi` varchar(255) NOT NULL,
  `ngay_sinh` date NOT NULL,
  `gioi_tinh` tinyint(1) NOT NULL,
  `id_bo_mon` varchar(6) NOT NULL,
  `id_chuc_vu` varchar(6) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `able` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_id_bo_mon_foreign` (`id_bo_mon`),
  KEY `users_id_chuc_vu_foreign` (`id_chuc_vu`),
  CONSTRAINT `users_id_bo_mon_foreign` FOREIGN KEY (`id_bo_mon`) REFERENCES `bo_mons` (`id`),
  CONSTRAINT `users_id_chuc_vu_foreign` FOREIGN KEY (`id_chuc_vu`) REFERENCES `chuc_vus` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('admin','Admin','tuyet.htn.63cntt@ntu.edu.vn',NULL,'$2y$12$qm7iST/H0uM3HobmAUFJ6.Ikstyr9iEpq7TOvWk/sWzU2.jOcXt3u','0905123456','H├á Nß╗Öi','2000-01-01',0,'admin','admin',NULL,'2025-05-06 17:18:10','2025-05-06 17:18:10',1),('GV001','Nguyß╗àn V─ân A','huynhtuyet0201032019@gmail.com',NULL,'$2y$12$SiIpIgLwVNuuwWo57SPtV..2BAnZMDnnb8jnUioWRgEk9xLxMKCaq','0905123456','H├á Nß╗Öi','2000-01-01',0,'CNPM','tk',NULL,'2025-05-06 17:18:10','2025-05-06 17:18:10',1),('GV002','Nguyß╗àn V─ân B','nguyenvanb@gmail.com',NULL,'$2y$12$QMwBhT02YOR0iX8FyeiEWOdbcdWgQHKtIU7L/QWM8u.cZf175Ute.','0905123456','H├á Nß╗Öi','2000-01-01',0,'CNPM','tbm',NULL,'2025-05-06 17:18:10','2025-05-06 17:18:10',1),('GV003','Nguyen V─ân D','nguyenvand@gmail.com',NULL,'$2y$12$YcHCI30RWUJwdyx9PptEZOIYrF/TE/3cfabum1z2oibVuGpp1VamS','0937467868','Th├ánh phß╗æ Cß║ºn Th╞í-Huyß╗çn V─⌐nh Thß║ính-X├ú V─⌐nh Trinh-','2025-05-06',1,'CNPM','gv',NULL,'2025-05-06 17:36:28','2025-05-06 17:36:28',1),('NV001','Nguyß╗àn V─ân C','nguyenvanc@gmail.com',NULL,'$2y$12$1LrxKGHlytxkdghWx2ZRvukP3J.P0qVfyFauPV/4ohTNjRc2eFZWe','0905123456','H├á Nß╗Öi','2000-01-01',0,'DBCL','nvdbcl',NULL,'2025-05-06 17:18:10','2025-05-06 17:18:10',1);
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

-- Dump completed on 2025-05-07  0:41:34
