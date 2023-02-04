import sqlite3
import config.config as config


dbname = config.DB_NAME + ".db"
_db_connection = sqlite3.connect(dbname, check_same_thread=False)

def get_dbcon():
	return _db_connection


def select(table: str,  cols: [str], where: {str:any}) -> [{str:any}]:
	con = get_dbcon()
	cur = con.cursor()

	sql = "select " + ",".join(cols) + " from " + table + " " 
	sql += make_sql_where(where) + ";"
	cur.execute(sql)

	return select_results_to_dicts(cols, cur.fetchall())


def insert(table: str, values: {str:any}):
	con = get_dbcon()
	cur = con.cursor()
	keys = values.keys()

	sql = "insert into " + table + "(" + ",".join(keys) 
	sql += ") values(:" + ",:".join(keys) + ");"

	cur.execute(sql, values)
	con.commit()


def insert_and_get_rowid(table: str, values: {str:any}):
	con = get_dbcon()
	cur = con.cursor()
	keys = values.keys()

	sql = "insert into " + table + "(" + ",".join(keys) 
	sql += ") values(:" + ",:".join(keys) + ");"

	cur.execute(sql, values)
	cur.execute("select last_insert_rowid();")
	rowid = cur.fetchall()[0][0]
	con.commit()

	return rowid


def update(table: str, values: {str:any}, where: {str:any}):
	con = get_dbcon()
	cur = con.cursor()

	sql = "update " + table + " set "

	for k in values.keys():
		sql += k + "=:" + k + ","

	sql = sql.rstrip(",") + " "
	sql += make_sql_where(where) + ";"

	cur.execute(sql, values)
	con.commit()


def upsert(table: str, values: {str:any}):
	con = get_dbcon()
	cur = con.cursor()
	keys = values.keys()

	sql = "replace into " + table + "(" + ",".join(keys) 
	sql += ") values(:" + ",:".join(keys) + ");"

	cur.execute(sql, values)
	con.commit()


def multi_upsert(table: str, values: [{str:any}]):
	con = get_dbcon()
	cur = con.cursor()
	keys = values.keys()

	sql = "replace into " + table + "(" + ",".join(keys) 
	sql += ") values(:" + ",:".join(keys) + ");"

	cur.executemany(sql, values)
	con.commit()


def execute(sql: str, values: {str:any}):
	con = get_dbcon()
	cur = con.cursor()
	cur.execute(sql, values)
	con.commit()


def multi_execute(sql: str, values: [{str:any}]):
	con = get_dbcon()
	cur = con.cursor()
	cur.executemany(sql, values)
	con.commit()


def delete(table: str, where: {str:any}):
	con = get_dbcon()
	cur = con.cursor()

	sql = "delete from " + table + " " + make_sql_where(where) + ";"

	cur.execute(sql)
	con.commit()


def make_sql_where(dic: {str:any}) -> str:
	if dic == {}:
		return ""

	s = "where "
	for k, v in dic.items():
		if type(v) == str:
			s += k + " = '" + v + "' and "
		else:
			s += k + " = " + str(v) + " and "

	return s.rstrip("and ")


def select_results_to_dicts(cols, results) -> [{str:any}]:
	ls = []
	for item in results:
		d_item = dict(zip(cols, item))
		ls.append(d_item)

	return ls