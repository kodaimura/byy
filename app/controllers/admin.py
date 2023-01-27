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
import os
import models.general as general
import models.product as product
import models.category as category

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
	try:
		general.update_admin_password(password)
		return "", 200
	except:
		return "", 500


@bp_admin.post("/tax")
@jwt_required()
def update_tax():
	tax = request.json["tax"]
	try:
		general.update_tax(tax)
		return "", 200
	except:
		return "", 500


@bp_admin.get("/products")
@jwt_required()
def admin_page():
	products = product.get_by({})
	categories = category.get_by({})
	return render_template(
		"admin-products.html",
		products=products,
		categories=categories
	)


@bp_admin.post("/products")
@jwt_required()
def register_product():
	product_id  = product.insert_and_get_rowid(request.form)
	img = request.files["img"]
	save_product_img(product_id, img)
	return redirect("/admin/products")


@bp_admin.post("/products/<product_id>")
@jwt_required()
def update_product(product_id):
	product.update(request.json, {"id":product_id})
	return "", 200


@bp_admin.delete("/products/<product_id>")
@jwt_required()
def delete_product(product_id):
	result = product.get_img_name({"id":product_id})
	if len(result) == 1:
		product.delete({"id":product_id})
		os.remove("./static/img/" + result[0]["img_name"])
	return "", 200


@bp_admin.post("/products/img")
@jwt_required()
def update_product_img():
	product_id = request.form.get("id")
	result = product.get_img_name({"id":product_id})
	if len(result) == 1:
		os.remove("./static/img/" + result[0]["img_name"])

	img = request.files["img"]
	save_product_img(product_id, img)
	return redirect("/admin/products")


def save_product_img(product_id, img):
	_, ext = os.path.splitext(img.filename)
	img_name = "product-" + str(product_id) + ext

	img.save(os.path.join("./static/img", img_name))
	product.update({"img_name":img_name}, {"id":product_id})
