ALTER TABLE `#__enmasse_deal` ADD `tier_pricing` TINYINT( 3 ) NOT NULL DEFAULT '0' AFTER `price` ,
ADD `price_step` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL AFTER `tier_pricing` 