alter table cm_positions add department_id int(10) unsigned not null default 1 after id; 
#删除唯一约束
alter table cm_positions drop index positions_title_unique;
alter table cm_departments drop index departments_title_unique;

INSERT INTO `cm_permissions` (`id`, `inherit_id`, `title`, `slug`, `status`, `description`, `note`, `sort`, `show`, `created_at`, `updated_at`)
VALUES
	(51, 43, '店铺导出', 'salon.export', 1, NULL, NULL, NULL, 2, '2015-08-11 10:11:14', '2015-08-11 10:11:51'),
	(52, 26, '商户导出', 'merchant.export', 1, NULL, NULL, NULL, 2, '2015-08-11 10:11:51', '2015-08-11 10:11:51'),
	(53, 32, '转付单导出', 'shop_count.export', 1, NULL, NULL, 0, 2, '2015-08-11 10:11:51', '2015-08-11 10:11:51'),
	(54, 31, '代收单导出', 'shop_count.delegate_export', 1, NULL, NULL, 0, 2, '2015-08-11 10:11:51', '2015-08-11 10:11:51'),
	(55, 31, '往来余额导出', 'shop_count.balance_export', 1, NULL, NULL, 0, 2, '2015-08-11 10:11:51', '2015-08-11 10:11:51');
