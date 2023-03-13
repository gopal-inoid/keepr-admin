INSERT INTO `business_settings` (`type`, `value`, `created_at`, `updated_at`) VALUES ('support', 'support', '2023-03-02 13:59:36', '2023-03-02 13:59:36');

ALTER TABLE `users` ADD `fcm_token` TEXT NULL DEFAULT NULL AFTER `loyalty_point`;

ALTER TABLE `users` ADD `firebase_auth_id` TEXT NULL DEFAULT NULL AFTER `fcm_token`, ADD `auth_access_token` TEXT NULL DEFAULT NULL AFTER `firebase_auth_id`;

ALTER TABLE `products` ADD `product_unique_code` VARCHAR(200) NULL DEFAULT NULL AFTER `code`, ADD `specification` TEXT NULL DEFAULT NULL AFTER `product_unique_code`, ADD `faq` TEXT NULL DEFAULT NULL AFTER `specification`;

CREATE TABLE `keepr`.`connected_device` (`id` INT NOT NULL AUTO_INCREMENT , `device_id` INT NOT NULL , `mac_id` VARCHAR(200) NULL DEFAULT NULL , `user_id` INT NOT NULL , `status` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '1 = connected,0 = disconnected' , `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `device_uuid` MEDIUMTEXT NULL DEFAULT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
