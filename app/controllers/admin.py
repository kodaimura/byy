from flask import (
	Blueprint, 
	render_template, 
	request, 
	redirect, 
	make_response,
	jsonify
)
from flask_jwt_extended import (
	create_access_token,
	set_access_cookies,
	unset_access_cookies,
	jwt_required
)
import models.general as general

bp_admin = Blueprint("bp_admin", __name__, template_folder="templates", url_prefix='/admin')


@bp_admin.get("/login")
def admin_login_page():
	return render_template("admin-login.html")


@bp_admin.post("/login")
def admin_login():
	password = general.get_admin_password()
	if request.form["password"] != password:

		return render_template(
			"admin-login.html",
			error= "パスワードが異なります。"
		)

	access_token = create_access_token(identity="admin")
	response = make_response(redirect("/admin/products"))
	set_access_cookies(response, access_token)

	return response


@bp_admin.get("/logout")
def admin_logout():
    response = make_response(redirect("/admin/login"))
    unset_access_cookies(response)
    return response

@bp_admin.get("/products")
@jwt_required()
def admin_page():
	return render_template("admin-products.html")


@bp_admin.post("/products")
@jwt_required()
def register_product():
	return render_template("admin-products.html")


@bp_admin.get("/general")
@jwt_required()
def general_page():
	password = general.get_admin_password()
	tax = general.get_tax()
	return render_template(
		"admin-general.html",
		password=password,
		tax=tax
	)


@bp_admin.post("/password")
@jwt_required()
def update_admin_password():
	password = request.json["password"]
	print(password)
	try:
		general.update_admin_password(password)
		return "success", 200
	except:
		return "error", 500


@bp_admin.post("/tax")
@jwt_required()
def update_tax():
	tax = request.json["tax"]
	try:
		general.update_tax(tax)
		return "success", 200
	except:
		return "error", 500

