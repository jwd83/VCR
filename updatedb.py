import os
#import sqlite3
import mariadb
import json


# cur = mariadb connection
# path = base path to walk
def build_file_list(cur, path):
    pass


def main():
    # load config data
    with open('config.json') as f:
        config = json.load(f)


    # Connect to MariaDB Platform
    try:
        conn = mariadb.connect(
            user =      config['db']['user'],
            password =  config['db']['password'],
            host =      config['db']['host'],
            port =      int(config['db']['port']),
            database =  config['db']['database']
        )
    except mariadb.Error as e:
        print(f"Error connecting to MariaDB Platform: {e}")
        sys.exit(1)

    # Get Cursor
    cur = conn.cursor()

    # disable autocommit


    print ("Connected to MariaDB")


if __name__ == '__main__':
	main()
