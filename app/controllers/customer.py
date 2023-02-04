from flask import (
	Blueprint, 
	render_template, 
	request, 
	redirect, 
	make_response,
	jsonify
)
import json
import requests

import models.general as general
import models.product as product
import models.category as category
import models.customer as customer
import models.order as order

bp_customer = Blueprint("bp_customer", __name__, template_folder="templates")


@bp_customer.get("/products")
def products_page():
	categories = category.get_by({})
	products = product.get_by({"display_flg":"1"})
	recommends = product.get_by({"display_flg":"1", "recommend_flg":"1"})
	return render_template(
		"products.html",
		categories=categories,
		products=products,
		recommends=recommends,
	)

@bp_customer.post("/orders")
def post_order():
	access_token = request.json["access_token"]

	response = requests.get(
		"https://api.line.me/v2/profile", 
		headers={"Authorization": "Bearer {0}".format(access_token)}
	).json()
	
	userId = response["userId"]
	userName = response["displayName"]
	orders = json.loads(request.json["order"])

	total_price = 0
	for x in orders:
		order.save({
			"product_id": x["id"],
			"customer_id": userId,
			"count": x["order_quantity"]
		})
		total_price += int(x["order_quantity"]) * int(x["price"])
		
	customer.save({
		"customer_id": userId,
		"customer_name": userName,
		"visits_count": 1,
		"cumulative_payment": total_price
	})

	return "", 200