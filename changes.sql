INSERT INTO `business_settings` (`type`, `value`, `created_at`, `updated_at`) VALUES ('support', 'support', '2023-03-02 13:59:36', '2023-03-02 13:59:36');

ALTER TABLE `users` ADD `fcm_token` TEXT NULL DEFAULT NULL AFTER `loyalty_point`;

ALTER TABLE `users` ADD `firebase_auth_id` TEXT NULL DEFAULT NULL AFTER `fcm_token`, ADD `auth_access_token` TEXT NULL DEFAULT NULL AFTER `firebase_auth_id`;

ALTER TABLE `products` ADD `product_unique_code` VARCHAR(200) NULL DEFAULT NULL AFTER `code`, ADD `specification` TEXT NULL DEFAULT NULL AFTER `product_unique_code`, ADD `faq` TEXT NULL DEFAULT NULL AFTER `specification`;

CREATE TABLE `keepr`.`connected_device` (`id` INT NOT NULL AUTO_INCREMENT , `device_id` INT NOT NULL , `mac_id` VARCHAR(200) NULL DEFAULT NULL , `user_id` INT NOT NULL , `status` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '1 = connected,0 = disconnected' , `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `device_uuid` MEDIUMTEXT NULL DEFAULT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `products` ADD `device_id` VARCHAR(200) NOT NULL AFTER `faq`;

ALTER TABLE `connected_device` ADD `device_name` VARCHAR(200) NULL DEFAULT NULL AFTER `device_id`;

ALTER TABLE `product_stocks` ADD `mac_id` VARCHAR(200) NULL DEFAULT NULL AFTER `product_id`;

ALTER TABLE `product_stocks` ADD `status` TINYINT NOT NULL DEFAULT '1' COMMENT '1=active,0=inactive' AFTER `qty`;

CREATE TABLE `keepr`.`device_trackings` ( `id` INT NOT NULL AUTO_INCREMENT , `user_id` INT NOT NULL , `mac_id` VARCHAR(200) NOT NULL , `distance` BIGINT(20) NULL DEFAULT NULL , `status` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '1=active,0=inactive' , `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `device_trackings` ADD `lat` VARCHAR(200) NULL DEFAULT NULL AFTER `distance`, ADD `lan` VARCHAR(200) NULL DEFAULT NULL AFTER `lat`;

CREATE TABLE `keepr`.`device_requests` ( `id` INT NOT NULL AUTO_INCREMENT , `user_id` INT NOT NULL , `mac_id` VARCHAR(200) NOT NULL , `status` TINYINT NOT NULL DEFAULT '0' COMMENT '0=lost,1=found' , `last_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `product_stocks` CHANGE `created_at` `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, CHANGE `updated_at` `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;

UPDATE `shipping_methods` SET `title` = 'Shipping method 1' WHERE `shipping_methods`.`id` = 2;

INSERT INTO `shipping_methods` (`id`, `creator_id`, `creator_type`, `title`, `cost`, `duration`, `status`, `created_at`, `updated_at`) VALUES (NULL, '1', 'admin', 'Shipping method 2', '10.00', '1 Month', '1', '2023-03-18 12:11:40', '2023-03-18 12:11:40');

CREATE TABLE `keepr`.`countries` ( `id` INT NOT NULL AUTO_INCREMENT , `code` VARCHAR(10) NULL DEFAULT NULL , `name` VARCHAR(200) NULL DEFAULT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

CREATE TABLE `keepr`.`shipping_method_rates` ( `id` INT NOT NULL AUTO_INCREMENT , `shipping_id` INT NOT NULL , `country_code` VARCHAR(10) NOT NULL , `normal_rate` INT NOT NULL , `express_rate` INT NOT NULL , `status` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '0=inactive,1=active' , `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;

CREATE TABLE `keepr`.`device_tracking_log` ( `id` INT NOT NULL AUTO_INCREMENT , `mac_id` VARCHAR(200) NULL DEFAULT NULL , `lat` VARCHAR(200) NULL DEFAULT NULL , `lan` VARCHAR(200) NULL DEFAULT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;


ALTER TABLE `users` ADD `add_shipping_address` VARCHAR(250) NULL DEFAULT NULL AFTER `auth_access_token`;

ALTER TABLE `users` ADD `is_billing_address_same` TINYINT(1) NULL DEFAULT NULL COMMENT '1=same,0=not' AFTER `add_shipping_address`;


CREATE TABLE `keepr`.`checkout_info` ( `id` INT NOT NULL AUTO_INCREMENT , `product_id` INT NOT NULL , `customer_id` INT NOT NULL , `mac_ids` TEXT NULL DEFAULT NULL , `total_order` INT NULL DEFAULT NULL , `total_amount` DOUBLE NOT NULL DEFAULT '0' , `tax_amount` DOUBLE NOT NULL DEFAULT '0' , `last_updated_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `checkout_info` CHANGE `product_id` `product_id` TEXT NULL DEFAULT NULL;

ALTER TABLE `users` ADD `state` VARCHAR(50) NULL DEFAULT NULL AFTER `city`;

ALTER TABLE `users` ADD `shipping_country` VARCHAR(50) NULL DEFAULT NULL AFTER `is_billing_address_same`, ADD `shipping_city` VARCHAR(50) NULL DEFAULT NULL AFTER `shipping_country`, ADD `shipping_state` VARCHAR(50) NULL DEFAULT NULL AFTER `shipping_city`, ADD `shipping_zip` VARCHAR(20) NULL DEFAULT NULL AFTER `shipping_state`;

ALTER TABLE `users` ADD `shipping_name` VARCHAR(80) NULL DEFAULT NULL AFTER `shipping_zip`, ADD `shipping_email` VARCHAR(80) NULL DEFAULT NULL AFTER `shipping_name`, ADD `shipping_phone` VARCHAR(25) NULL DEFAULT NULL AFTER `shipping_email`;

ALTER TABLE `connected_device` ADD `distance` VARCHAR(100) NULL DEFAULT NULL AFTER `device_uuid`;

ALTER TABLE `users` ADD `phone_code` VARCHAR(10) NULL DEFAULT NULL AFTER `shipping_phone`, ADD `shipping_phone_code` VARCHAR(10) NULL DEFAULT NULL AFTER `phone_code`;

ALTER TABLE `products` ADD `rssi` VARCHAR(200) NULL DEFAULT NULL AFTER `device_id`;