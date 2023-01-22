from flask import (
	Blueprint, 
	render_template, 
	request, 
	redirect, 
	make_response,
	jsonify
)
import hashlib
from flask_jwt_extended import (
	create_access_token,
	set_access_cookies,
	unset_access_cookies,
	jwt_required
)


bp_admin = Blueprint("bp_admin", __name__, template_folder="templates", url_prefix='/admin')


@bp_admin.get("/login")
def admin_login_page():
	return render_template("admin-login.html")


@bp_admin.get("/logout")
def admin_logout():
    response = make_response(redirect("/admin/login"))
    unset_access_cookies(response)
    return response


@bp_admin.post("/login")
def admin_login():
	if request.form["password"] != "admin":

		return render_template(
			"admin-login.html",
			error= "パスワードが異なります。"
		)

	access_token = create_access_token(identity="admin")
	response = make_response(redirect("/admin/products"))
	set_access_cookies(response, access_token)

	return response

@bp_admin.get("/products")
@jwt_required()
def admin_page():
	return render_template("admin-products.html")