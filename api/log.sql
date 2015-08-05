ALTER TABLE `cm_managers` CHANGE `status` `status` TINYINT(2) NOT NULL DEFAULT 1;
ALTER TABLE `cm_roles` CHANGE `status` `status` TINYINT(2) NOT NULL DEFAULT 1;
ALTER TABLE `cm_permissions` CHANGE `status` `status` TINYINT(2) NOT NULL DEFAULT 1;