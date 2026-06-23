-- SQL Dump of oim database schema
-- Created for local backup and deployment to Hostinger

SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Table structure for table `sections`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sections` (
  `section_id` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `section_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`section_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `items`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `items` (
  `item_id` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `item_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `models`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `models` (
  `model_id` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `item_id` varchar(11) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `model_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`model_id`),
  KEY `models_items_FK` (`item_id`),
  CONSTRAINT `models_items_FK` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `password_otp`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `password_otp` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `otp` varchar(6) COLLATE utf8mb4_general_ci NOT NULL,
  `expiry` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `password_otp_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `stock_rcpt`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `stock_rcpt` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `requisitions`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `requisitions` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- View structure for view `stock_quantity`
-- --------------------------------------------------------
DROP VIEW IF EXISTS `stock_quantity`;
CREATE VIEW `stock_quantity` AS 
select `stock_rcpt`.`model_id` AS `model_id`, sum(`stock_rcpt`.`quantity`) AS `total` 
from `stock_rcpt` 
group by `stock_rcpt`.`model_id`;

-- --------------------------------------------------------
-- View structure for view `requisition_stock_summary`
-- --------------------------------------------------------
DROP VIEW IF EXISTS `requisition_stock_summary`;
CREATE VIEW `requisition_stock_summary` AS 
select `r`.`section_id` AS `section_id`,`r`.`item_id` AS `item_id`,`r`.`model_id` AS `model_id`,`r`.`quantity` AS `requisition_qty`,greatest((ifnull(sum(`s`.`quantity`),0) - `r`.`quantity`),0) AS `stock_qty`,curdate() AS `today_date`,`la`.`last_approve_date` AS `last_approve_date` 
from ((`requisitions` `r` left join `stock_rcpt` `s` on(((`r`.`item_id` = `s`.`item_id`) and (`r`.`model_id` = `s`.`model_id`)))) left join (select `requisitions`.`section_id` AS `section_id`,max(`requisitions`.`approve_date`) AS `last_approve_date` from `requisitions` group by `requisitions`.`section_id`) `la` on((`r`.`section_id` = `la`.`section_id`))) 
group by `r`.`section_id`,`r`.`item_id`,`r`.`model_id`,`r`.`quantity`,`la`.`last_approve_date`;

SET FOREIGN_KEY_CHECKS = 1;
