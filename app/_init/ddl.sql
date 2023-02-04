CREATE TABLE IF NOT EXISTS general (
	key1 TEXT NOT NULL,
	key2 TEXT,
	value TEXT NOT NULL,
	remarks TEXT,
	create_at TEXT NOT NULL DEFAULT (DATETIME('now', 'localtime')),
	update_at TEXT NOT NULL DEFAULT (DATETIME('now', 'localtime'))
);

CREATE TABLE IF NOT EXISTS category (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	category_name TEXT NOT NULL,
	create_at TEXT NOT NULL DEFAULT (DATETIME('now', 'localtime')),
	update_at TEXT NOT NULL DEFAULT (DATETIME('now', 'localtime'))
);

CREATE TABLE IF NOT EXISTS product (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	product_name TEXT NOT NULL,
	category_id INTEGER NOT NULL,
	price INTEGER NOT NULL,
	comment TEXT,
	quantity TEXT,
	production_area TEXT,
	display_flg TEXT NOT NULL DEFAULT '1',
	stock_flg TEXT NOT NULL DEFAULT '1',
	recommend_flg TEXT NOT NULL DEFAULT '0',
	img_name TEXT,
	create_at TEXT NOT NULL DEFAULT (DATETIME('now', 'localtime')),
	update_at TEXT NOT NULL DEFAULT (DATETIME('now', 'localtime'))
);

CREATE TABLE IF NOT EXISTS order_history (
	customer_id TEXT,
	product_id INTEGER,
	count INTEGER NOT NULL DEFAULT 0,
	create_at TEXT NOT NULL DEFAULT (DATETIME('now', 'localtime')),
	update_at TEXT NOT NULL DEFAULT (DATETIME('now', 'localtime')),
	PRIMARY KEY(customer_id, product_id)
);

CREATE TABLE IF NOT EXISTS customer (
	customer_id TEXT,
	customer_name TEXT,
	visits_count INTEGER NOT NULL DEFAULT 0,
	cumulative_payment INTEGER NOT NULL DEFAULT 0,
	create_at TEXT NOT NULL DEFAULT (DATETIME('now', 'localtime')),
	update_at TEXT NOT NULL DEFAULT (DATETIME('now', 'localtime')),
	PRIMARY KEY(customer_id)
);


CREATE TRIGGER IF NOT EXISTS trg_general_upd AFTER UPDATE ON general
BEGIN
	UPDATE general
	SET update_at = DATETIME('now', 'localtime')
	WHERE rowid == NEW.rowid;
END;

CREATE TRIGGER IF NOT EXISTS trg_category_upd AFTER UPDATE ON category
BEGIN
	UPDATE category
	SET update_at = DATETIME('now', 'localtime')
	WHERE rowid == NEW.rowid;
END;

CREATE TRIGGER IF NOT EXISTS trg_product_upd AFTER UPDATE ON product
BEGIN
	UPDATE product
	SET update_at = DATETIME('now', 'localtime')
	WHERE rowid == NEW.rowid;
END;

CREATE TRIGGER IF NOT EXISTS trg_customer_upd AFTER UPDATE ON customer
BEGIN
	UPDATE customer
	SET update_at = DATETIME('now', 'localtime')
	WHERE rowid == NEW.rowid;
END;

CREATE TRIGGER IF NOT EXISTS trg_order_history_upd AFTER UPDATE ON order_history
BEGIN
	UPDATE order_history
	SET update_at = DATETIME('now', 'localtime')
	WHERE rowid == NEW.rowid;
END;