CREATE TABLE IF NOT EXISTS general (
	key1 varchar(20) NOT NULL,
	key2 varchar(20),
	value varchar(30) NOT NULL,
	remarks varchar(100),
	create_at datetime default current_timestamp,
	update_at datetime default current_timestamp on update current_timestamp
);

CREATE TABLE IF NOT EXISTS category (
	category_id int PRIMARY KEY AUTO_INCREMENT,
	category_name varchar(30) NOT NULL,
	create_at datetime default current_timestamp,
	update_at datetime default current_timestamp on update current_timestamp
);

CREATE TABLE IF NOT EXISTS product (
	product_id int PRIMARY KEY AUTO_INCREMENT,
	category_id int NOT NULL,
	product_name varchar(30) NOT NULL,
	production_area varchar(20),
	unit_quantity varchar(20),
	unit_price int NOT NULL,
	comment varchar(300),
	display_flg char(1) NOT NULL DEFAULT '1',
	stock_flg char(1) NOT NULL DEFAULT '1',
	recommend_flg char(1) NOT NULL DEFAULT '0',
	img_name varchar(50),
	seq int DEFAULT 0,
	recommend_seq int DEFAULT 0,
	create_at datetime default current_timestamp,
	update_at datetime default current_timestamp on update current_timestamp
);

CREATE TABLE IF NOT EXISTS order_history (
	customer_id varchar(100),
	product_id int,
	order_count int NOT NULL DEFAULT 0,
	create_at datetime default current_timestamp,
	update_at datetime default current_timestamp on update current_timestamp,
	PRIMARY KEY(customer_id, product_id)
);

CREATE TABLE IF NOT EXISTS customer (
	customer_id varchar(100),
	customer_name varchar(50),
	visit_count int NOT NULL DEFAULT 0,
	total_payment int NOT NULL DEFAULT 0,
	create_at datetime default current_timestamp,
	update_at datetime default current_timestamp on update current_timestamp,
	PRIMARY KEY(customer_id)
);

CREATE TABLE IF NOT EXISTS slot_daily (
	customer_id varchar(100),
	result char(2),
	used_flg char(1),
	create_at datetime default current_timestamp,
	update_at datetime default current_timestamp on update current_timestamp,
	PRIMARY KEY(customer_id)
);
