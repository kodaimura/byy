from flask import (
	Blueprint, 
	render_template, 
	request, 
	redirect, 
	make_response,
	jsonify
)


bp_customer = Blueprint("bp_customer", __name__, template_folder="templates")


@bp_customer.get("/order")
def order_page():
	return render_template("order.html")