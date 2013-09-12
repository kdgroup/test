ALTER TABLE `#__enmasse_setting` ADD COLUMN `enable_security_code` TINYINT UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE `#__enmasse_invty` ADD COLUMN `security_code` CHAR(6);