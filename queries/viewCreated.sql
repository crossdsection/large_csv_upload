#[mysqld] //Config
#innodb_locks_unsafe_for_binlog=1

CREATE TABLE `Sale_Percentage_Per_Year` AS
SELECT `year`, SUM(`sales`) as `sales`, SUM(`sales`) * 100 / t.s AS `salespercentage`
FROM `tempname1dbs`
CROSS JOIN (SELECT SUM(`sales`) AS s FROM `tempname1dbs`) t GROUP BY `year`