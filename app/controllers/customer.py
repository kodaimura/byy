from flask import (
	Blueprint, 
	render_template, 
	request, 
	redirect, 
	make_response,
	jsonify
)
import models.general as general
import models.product as product
import models.category as category

bp_customer = Blueprint("bp_customer", __name__, template_folder="templates")


@bp_customer.get("/order")
def order_page():
	return render_template("order.html")


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