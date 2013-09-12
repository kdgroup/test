ALTER TABLE `#__enmasse_order_item`
ADD ( `attr_info` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL );

CREATE TABLE IF NOT EXISTS `#__enmasse_deal_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `price` double NOT NULL,
  `origin_price` double NOT NULL,
  `deal_id` bigint(20) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `code` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;