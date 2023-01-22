import core.db as db  


def get_by(where: {str:any}) -> [{str:any}]:
	db.select("product",
		["id", "product_name", "category_id", "price",
		 "comment", "quantity", "production_area",
		 "display_flg", "stock_flg", "recommend_flg", "img_url"], 
		where
	)

def insert_and_get_rowid(values: {str:any}):
	return db.insert_and_get_rowid("product", values)

def update(values: {str:any}, where: {str:any}):
	db.update("product", values, where)

def delete(where: {str:any}):
	db.update("product", where)