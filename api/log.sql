
drop table cm_commission;

CREATE TABLE `cm_commission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sn` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `salonid` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `cm_commission_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ordersn` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `type` tinyint(2) NOT NULL DEFAULT '1',
  `salonid` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `rate` decimal(4,2) NOT NULL DEFAULT '0.00',
  `grade` tinyint(2) DEFAULT NULL COMMENT '店铺当前等级 1特级店2A级店3B级店4C级店4淘汰店',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1207 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

