import logging
from flask import Flask, Blueprint, redirect, render_template
from flask_jwt_extended import (
    JWTManager,
    create_access_token, 
    set_access_cookies, 
    unset_access_cookies, 
    verify_jwt_in_request,
    jwt_required,
    current_user,
)

from controllers.admin import bp_admin
from controllers.customer import bp_customer
import config.config as conf

app = Flask(__name__, static_folder="static", template_folder="templates")
app.register_blueprint(bp_admin)
app.register_blueprint(bp_customer)

app.config["JWT_SECRET_KEY"] = conf.JWT_SECRET_KEY
app.config["JWT_ACCESS_TOKEN_EXPIRES"] =  conf.JWT_ACCESS_TOKEN_EXPIRES
app.config["JWT_TOKEN_LOCATION"] = ["cookies"]
app.config["JWT_COOKIE_SECURE"] = conf.JWT_COOKIE_SECURE
app.config['JWT_COOKIE_CSRF_PROTECT'] = False


jwt = JWTManager(app)

# return jwt sub
@jwt.user_identity_loader
def user_identity_lookup(user):
    return user

@jwt.additional_claims_loader
def add_claims_to_access_token(user):
    return {
        "user_name": user
    }

@jwt.user_lookup_loader
def user_lookup_callback(_jwt_header, jwt_data):
    return {
        "user_name": jwt_data["user_name"]
    }

@jwt.unauthorized_loader
def custom_unauthorized_response(_err):
    return redirect("/admin/login")


if __name__ == "__main__":
    app.run(debug=True, host=conf.APP_HOST, port=conf.APP_PORT)