import core.db as db  


def update_tax(percent):
	db.update(
		"general", 
		{"value" : percent}, 
		{"key1" : "tax"}
	)

def get_tax():
	return db.select(
		"general", 
		["value"],
		{"key1" : "tax"}
	)[0]['value']

def update_admin_password(password):
	return db.update(
		"general",
		{"value" : password}, 
		{"key1" : "admin-password"}
	)

def get_admin_password():
	return db.select(
		"general",
		["value"],
		{"key1" : "admin-password"}
	)[0]['value']