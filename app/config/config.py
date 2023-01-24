from dotenv import dotenv_values
import os


# "local" or ""
env = os.getenv("EXEC_ENV")

config = dotenv_values("config/env/{}.env".format(env))

APP_HOST = config["APP_HOST"]
APP_PORT = config["APP_PORT"]

DB_NAME = config["DB_NAME"]
DB_HOST = config["DB_HOST"]
DB_PORT = config["DB_PORT"]
DB_USER = config["DB_USER"]

DB_PASSWORD = config["DB_PASSWORD"]

JWT_SECRET_KEY = config["JWT_SECRET_KEY"]
JWT_ACCESS_TOKEN_EXPIRES = 2592000 #1month
JWT_COOKIE_SECURE = True

if env == "local":
	JWT_COOKIE_SECURE = False