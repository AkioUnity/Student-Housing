-- ============================================================
-- ============== CREATION OF THE TABLE pm_coupon =============
-- ============================================================

	CREATE TABLE pm_coupon(
		id int NOT NULL AUTO_INCREMENT,
		title varchar(250),
		code varchar(50),
        discount double DEFAULT 0,
        discount_type varchar(10),
		rooms text,
		checked int DEFAULT 0,
		publish_date int,
		unpublish_date int,
		PRIMARY KEY(id)
	) ENGINE=INNODB DEFAULT CHARSET=utf8;
