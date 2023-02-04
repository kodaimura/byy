import core.db as db  


def get_by(where: {str:any}) -> [{str:any}]:
	return db.select("customer",
		["customer_id", "customer_name", "visits_count", 
		 "cumulative_payment", "update_at"], 
		where
	)

def save(values: {str:any}):
	where = {"customer_id": values["customer_id"]}
	rows = db.select("customer", 
		["visits_count", "cumulative_payment"], where)

	if not rows:
		db.insert("customer", values)
	else:
		values["visits_count"] += int(rows[0]["visits_count"])
		values["cumulative_payment"] += int(rows[0]["cumulative_payment"])
		db.update("customer", values, where)


def delete(where: {str:any}):
	db.delete("customer", where)