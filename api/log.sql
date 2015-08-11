alter table cm_positions add department_id int(10) unsigned not null default 1 after id; 
#删除唯一约束
alter table cm_positions drop index positions_title_unique;
alter table cm_departments drop index departments_title_unique;