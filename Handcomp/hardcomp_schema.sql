-- SQL Dump of hardcomp database schema
-- Created for local backup and deployment to Hostinger

SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Table structure for table `sections`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sections` (
  `section_id` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `section_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('Section Officer','Branch Officer','ITSC','AMC') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `designation` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `group_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `section` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mobile` bigint DEFAULT NULL,
  `name_section` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `password_otp`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `password_otp` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `otp` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `expiry` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `hw_inventory`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `hw_inventory` (
  `id` int DEFAULT NULL,
  `type` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sub_type` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `category` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `make` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hw_number` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_of_purchase` date DEFAULT NULL,
  `processor` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ram` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hdd` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hw_number_2` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `issued_to` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `working` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_of_issue` date DEFAULT NULL,
  `purpose` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sec_store` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `placed` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `additional_info` text COLLATE utf8mb4_general_ci,
  `amc` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `under_warranty` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `colour_bw` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `certificate` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `office` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `complaint`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `complaint` (
  `comp_no` int NOT NULL AUTO_INCREMENT,
  `forwarded_by` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `device` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `serial_no` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `complaint` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `remarks` text COLLATE utf8mb4_general_ci,
  `received_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `assigned_time` timestamp NULL DEFAULT NULL,
  `ongoing_time` timestamp NULL DEFAULT NULL,
  `resolved_time` timestamp NULL DEFAULT NULL,
  `assigned_to` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  PRIMARY KEY (`comp_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS = 1;
