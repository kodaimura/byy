import core.db as db


def get_by(where: {str:any}) -> [{str:any}]:
	return db.select("category", ["id", "category_name"], where)

def insert(values: {str:any}):
	db.insert("category", values)

def update(values: {str:any}, where: {str:any}):
	db.update("category", values, where)
