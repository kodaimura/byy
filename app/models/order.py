import core.db as db  


def get_by(where: {str:any}) -> [{str:any}]:
	return db.select("order_history",
		["customer_id", "product_id", "count"], 
		where
	)

def save(values: {str:any}):
	where = {
	"customer_id": values["customer_id"],"product_id": values["product_id"]
	}
	rows = db.select("order_history", ["count"], where)

	if not rows:
		db.insert("order_history", values)
	else:
		values["count"] = values["count"] + int(rows[0]["count"])
		db.update("order_history", values, where)


def delete(where: {str:any}):
	db.delete("order_history", where)