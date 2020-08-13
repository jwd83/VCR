import os
import mariadb
import json

conn = 0
cur = 0
config = 0

# cur = mariadb connection
# path = base path to walk
def build_file_list(path, filter_extensions = False):
    # clear existing entires
    try:
        cur.execute(
            "DELETE FROM files WHERE parent = ?",
            (path, )
        )
        conn.commit()
    except mariadb.Error as e:
        print(f"Error connecting to MariaDB Platform: {e}")
        sys.exit(1)

    # track if we will add a file
    add_file = False

    # solve target path
    target_path = config['root_path'] + path

    # calcualte the string split position we will use later
    insert_split_position = len(target_path) + 1

    # get list of all files in each folder
    for dirpath, dirs, files in os.walk(target_path):
        # iterate through the files in each folder
        for file in files:

            full_path = dirpath + "\\" + file

            if not filter_extensions:
                add_file = True

            else:
                _, file_ext = os.path.splitext(full_path)
                file_ext = file_ext.lower()
                if file_ext in config['extensions']:
                    add_file = True
                else:
                    add_file = False

            if add_file:
                file_size = os.stat(full_path).st_size
                cur.execute(
                    "INSERT INTO files (parent, path, size) VALUES (?,?,?);",
                    (path, full_path[insert_split_position:], file_size)
                )

    # write to database
    conn.commit()

def main():
    global config, conn, cur
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

    # disable autocommit
    conn.autocommit = False

    # Get Cursor
    cur = conn.cursor()

    print("Connected to MariaDB")
    print(config['root_path'])
    print(config['extensions'])

    build_file_list(
        path = "Music",
        filter_extensions = True
    )

    build_file_list(
        path = "Movies+TV",
        filter_extensions = True
    )

    build_file_list(
        path = "Books",
        filter_extensions = False
    )

    build_file_list(
        path = "Emulation + ROMs",
        filter_extensions = False
    )

    build_file_list(
        path = "Audio Books",
        filter_extensions = False
    )



if __name__ == '__main__':
	main()
