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

ALTER TABLE `users` ADD `shipping_country_iso` VARCHAR(20) NULL DEFAULT NULL AFTER `country_iso`;

ALTER TABLE `orders` CHANGE `transaction_ref` `transaction_ref` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `product_stocks` ADD `is_purchased` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '1= purchased, 0= not' AFTER `status`;

ALTER TABLE `shipping_methods` CHANGE `duration` `normal_duration` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `shipping_methods` ADD `express_duration` VARCHAR(20) NULL DEFAULT NULL AFTER `normal_duration`;

CREATE TABLE `keepr`.`api_versions` ( `id` INT NOT NULL AUTO_INCREMENT , `android_platform` TINYINT(1) NOT NULL DEFAULT '0' , `ios_platform` TINYINT(1) NOT NULL DEFAULT '0' , `old_version` VARCHAR(20) NULL DEFAULT NULL , `new_version` VARCHAR(20) NULL DEFAULT NULL , `current_version` VARCHAR(20) NULL DEFAULT NULL , `status` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '2= force update, 1=normal update,0=no need for update' , `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;

INSERT INTO `api_versions` (`id`, `android_platform`, `ios_platform`, `old_version`, `new_version`, `current_version`, `status`, `updated_at`) VALUES (NULL, '1', '0', '1.0', '1.0.2', '1.0.1', '1', '2023-04-24 13:22:38'), (NULL, '0', '1', '1.0', '1.0.2', '1.0.1', '1', '2023-04-24 13:22:38');

ALTER TABLE `api_versions` CHANGE `android_platform` `platform` TINYINT(1) NOT NULL DEFAULT '0';

ALTER TABLE `states` CHANGE `latitude` `latitude` INT(11) NULL DEFAULT NULL;
ALTER TABLE `states` CHANGE `longitude` `longitude` INT(11) NULL DEFAULT NULL;
CREATE TABLE `keepr`.`tax_calculation` (`id` INT(11) NOT NULL AUTO_INCREMENT , `country` VARCHAR(100) NULL DEFAULT NULL , `type` VARCHAR(50) NULL DEFAULT NULL , `tax_amt` VARCHAR(255) NULL DEFAULT NULL , `tax_type` VARCHAR(50) NULL DEFAULT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `tax_calculation` CHANGE `tax_amt` `tax_amt` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;

ALTER TABLE `products` CHANGE `colors` `colors` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;

CREATE TABLE `keepr`.`email_templates` ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(200) NULL DEFAULT NULL , `subject` VARCHAR(200) NULL DEFAULT NULL , `body` TEXT NULL DEFAULT NULL , `status` TINYINT NOT NULL DEFAULT '1' COMMENT '1=active,0=inactive' , `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `update_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;

INSERT INTO `email_templates` (`id`, `name`, `subject`, `body`, `status`, `created_at`, `update_at`) VALUES (NULL, 'Order', 'Your Order is Placed', '<h1>Your Order is Placed</h1>', '1', current_timestamp(), current_timestamp());

ALTER TABLE `email_templates` CHANGE `update_at` `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE `product_stocks` ADD `uuid` MEDIUMTEXT NULL DEFAULT NULL AFTER `updated_at`, ADD `major` INT NULL DEFAULT NULL AFTER `uuid`, ADD `minor` INT NULL DEFAULT NULL AFTER `major`;

ALTER TABLE `connected_device` ADD `major` INT NULL DEFAULT NULL AFTER `distance`, ADD `minor` INT NULL DEFAULT NULL AFTER `major`;

ALTER TABLE `device_trackings` ADD `uuid` MEDIUMTEXT NULL DEFAULT NULL AFTER `updated_at`, ADD `major` INT NULL DEFAULT NULL AFTER `uuid`, ADD `minor` INT NULL DEFAULT NULL AFTER `major`;

ALTER TABLE `device_requests` ADD `uuid` MEDIUMTEXT NULL DEFAULT NULL AFTER `last_updated`, ADD `major` INT NULL DEFAULT NULL AFTER `uuid`, ADD `minor` INT NULL DEFAULT NULL AFTER `major`;

ALTER TABLE `products` ADD `uuid` MEDIUMTEXT NULL DEFAULT NULL AFTER `rssi`;

ALTER TABLE `orders` ADD `tax_amount` VARCHAR(10) NULL DEFAULT NULL AFTER `third_party_delivery_tracking_id`, ADD `tax_title` VARCHAR(50) NULL DEFAULT NULL AFTER `tax_amount`, ADD `shipping_rate_id` INT(10) NULL DEFAULT NULL AFTER `tax_title`, ADD `shipping_mode` VARCHAR(50) NULL DEFAULT NULL AFTER `shipping_rate_id`;


CREATE TABLE `keepr`.`api_logs` ( `log_id` INT NOT NULL AUTO_INCREMENT , `user_id` INT NULL DEFAULT NULL , `appRequestData` MEDIUMTEXT NULL DEFAULT NULL , `appDeviceData` MEDIUMTEXT NULL DEFAULT NULL , `appResponse` MEDIUMTEXT NULL DEFAULT NULL , `appService` MEDIUMTEXT NULL DEFAULT NULL , `appCreatedDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`log_id`)) ENGINE = InnoDB;

ALTER TABLE `device_tracking_log` ADD `minor` VARCHAR(20) NULL DEFAULT NULL AFTER `lan`, ADD `major` VARCHAR(20) NULL DEFAULT NULL AFTER `minor`, ADD `uuid` VARCHAR(250) NULL DEFAULT NULL AFTER `major`;

ALTER TABLE `device_trackings` CHANGE `mac_id` `mac_id` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;

ALTER TABLE `orders` ADD `uuid` MEDIUMTEXT NULL DEFAULT NULL AFTER `shipping_mode`, ADD `major` VARCHAR(20) NULL DEFAULT NULL AFTER `uuid`, ADD `minor` VARCHAR(20) NULL DEFAULT NULL AFTER `major`;