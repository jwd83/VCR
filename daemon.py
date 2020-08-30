# Python daemon for VCR
#
# Distributed under the
#
# MIT License
#
# Copyright (c) 2020 Jared De Blander
#
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be included in all
# copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
# SOFTWARE.
#
# ASCII ART FROM
# http://patorjk.com/software/taag/#p=display&c=bash&f=Big%20Money-ne&t=encoding
#
#

import json
import mariadb
import os
import sys
import time
from multiprocessing import Process


#             /$$           /$$                 /$$
#            | $$          | $$                | $$
#    /$$$$$$ | $$  /$$$$$$ | $$$$$$$   /$$$$$$ | $$  /$$$$$$$
#   /$$__  $$| $$ /$$__  $$| $$__  $$ |____  $$| $$ /$$_____/
#  | $$  \ $$| $$| $$  \ $$| $$  \ $$  /$$$$$$$| $$|  $$$$$$
#  | $$  | $$| $$| $$  | $$| $$  | $$ /$$__  $$| $$ \____  $$
#  |  $$$$$$$| $$|  $$$$$$/| $$$$$$$/|  $$$$$$$| $$ /$$$$$$$/
#   \____  $$|__/ \______/ |_______/  \_______/|__/|_______/
#   /$$  \ $$
#  |  $$$$$$/
#   \______/

# setup some globals
PATH_FFMPEG = "C:\\Users\\jared\\Downloads\\ffmpeg-4.2.1-win64-static\\bin\\ffmpeg.exe"
PATH_QUEUE_H264 = "G:\\queue_h264.txt"
PATH_QUEUE_H265 = "G:\\queue_h265.txt"
PATH_QUEUE_M4A = "G:\\queue_m4a.txt"
PATH_QUEUE_OPUS = "G:\\queue_opus.txt"
MSG_QUEUE = "Reading the queues"

config = 0
conn = 0
cur = 0

run_file_queue = False
run_db_queue = False
run_db_filesystem = False

#   /$$                 /$$                                      /$$$$$$
#  | $$                | $$                                     /$$__  $$
#  | $$$$$$$   /$$$$$$ | $$  /$$$$$$   /$$$$$$   /$$$$$$       | $$  \__//$$   /$$ /$$$$$$$   /$$$$$$$  /$$$$$$$
#  | $$__  $$ /$$__  $$| $$ /$$__  $$ /$$__  $$ /$$__  $$      | $$$$   | $$  | $$| $$__  $$ /$$_____/ /$$_____/
#  | $$  \ $$| $$$$$$$$| $$| $$  \ $$| $$$$$$$$| $$  \__/      | $$_/   | $$  | $$| $$  \ $$| $$      |  $$$$$$
#  | $$  | $$| $$_____/| $$| $$  | $$| $$_____/| $$            | $$     | $$  | $$| $$  | $$| $$       \____  $$
#  | $$  | $$|  $$$$$$$| $$| $$$$$$$/|  $$$$$$$| $$            | $$     |  $$$$$$/| $$  | $$|  $$$$$$$ /$$$$$$$/
#  |__/  |__/ \_______/|__/| $$____/  \_______/|__/            |__/      \______/ |__/  |__/ \_______/|_______/
#                          | $$
#                          | $$
#                          |__/

def new_extension(src, new_ext):
    filename, file_extension = os.path.splitext(src)
    return filename + new_ext

def next_file_from_files(queue_file):
    with open(queue_file, 'r') as fin:
        data = fin.read().splitlines(True)
    with open(queue_file, 'w') as fout:
        fout.writelines(data[1:])
    if(len(data) > 0):
        return data[0].strip()
    else:
        return ""

def show_help():
    print("switches")
    print("-h --help     show help menu (this menu)")
    print("-s --system   update filesystem in database")
    print("-q --queue    process queue in database")
    print("-f --file     process file based queues")
    sys.exit(0)

def specified(opt):
    if opt in sys.argv:
        return True
    return False


def setup_db():
    global conn, cur

    print("config.json requested database access. connecting to database...")

    # Connect to MariaDB Platform
    try:
        conn = mariadb.connect(
            user =      config['db']['user'],
            password =  config['db']['password'],
            host =      config['db']['host'],
            port =      int(config['db']['port']),
            database =  config['db']['database']
        )

        # disable autocommit
        conn.autocommit = False

        # Get Cursor
        cur = conn.cursor()

    except mariadb.Error as e:
        print(f"Error connecting to MariaDB Platform: {e}")
        sys.exit(1)

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
#
#
#    /$$$$$$   /$$$$$$   /$$$$$$   /$$$$$$$  /$$$$$$   /$$$$$$$ /$$$$$$$  /$$$$$$   /$$$$$$$
#   /$$__  $$ /$$__  $$ /$$__  $$ /$$_____/ /$$__  $$ /$$_____//$$_____/ /$$__  $$ /$$_____/
#  | $$  \ $$| $$  \__/| $$  \ $$| $$      | $$$$$$$$|  $$$$$$|  $$$$$$ | $$$$$$$$|  $$$$$$
#  | $$  | $$| $$      | $$  | $$| $$      | $$_____/ \____  $$\____  $$| $$_____/ \____  $$
#  | $$$$$$$/| $$      |  $$$$$$/|  $$$$$$$|  $$$$$$$ /$$$$$$$//$$$$$$$/|  $$$$$$$ /$$$$$$$/
#  | $$____/ |__/       \______/  \_______/ \_______/|_______/|_______/  \_______/|_______/
#  | $$
#  | $$
#  |__/

def file_queue():
    try:
        print("starting file based queue")
        while True:
            pass
    except:
        print("error in file based queue")

def db_queue():
    # try:
        print("starting db based queue")
        setup_db()

        while True:
            pass
    # except:
    #     print("error in db based queue")

def db_filesystem():
    # try:
        print("starting db based file system monitor")
        setup_db()
        while True:
            print("Rebuilding file index...")
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
            time.sleep(60)

    # except:
        # print("error in db based file system monitor")


#                                                 /$$ /$$
#                                                | $$|__/
#    /$$$$$$  /$$$$$$$   /$$$$$$$  /$$$$$$   /$$$$$$$ /$$ /$$$$$$$   /$$$$$$
#   /$$__  $$| $$__  $$ /$$_____/ /$$__  $$ /$$__  $$| $$| $$__  $$ /$$__  $$
#  | $$$$$$$$| $$  \ $$| $$      | $$  \ $$| $$  | $$| $$| $$  \ $$| $$  \ $$
#  | $$_____/| $$  | $$| $$      | $$  | $$| $$  | $$| $$| $$  | $$| $$  | $$
#  |  $$$$$$$| $$  | $$|  $$$$$$$|  $$$$$$/|  $$$$$$$| $$| $$  | $$|  $$$$$$$
#   \_______/|__/  |__/ \_______/ \______/  \_______/|__/|__/  |__/ \____  $$
#                                                                   /$$  \ $$
#                                                                  |  $$$$$$/
#                                                                   \______/

def encode_h265(src):
    out = new_extension(src, ".h265.mp4")

    # ffmpeg options
    command = PATH_FFMPEG                   # path to ffmpeg executable
    command += " -i \"" + src + "\" "       # specify input file
    command += " -c:a libopus -b:a 96k "    # specify option: opus audio codec
    command += " -map -0:v? "               # strip video data
    command += " -map -0:s? "               # strip subtitles
    command += " -map -0:d? "               # strip misc data (note: this does not appear to strip metadata)
    command += " -n "                       # do not overwrite files, exit immediately if specified output already exists
    command += "\"" + out + "\""            # specify output file

    # print the command that will be executed
    print(command)
    # run command
    os.system(command)

def encode_h264(src):
    out = new_extension(src, ".h264.mp4")
    # ffmpeg options
    command = PATH_FFMPEG                   # path to ffmpeg executable
    command += " -i \"" + src + "\" "       # specify input file
    command += " -c:a libopus -b:a 96k "    # specify option: opus audio codec
    command += " -map -0:v? "               # strip video data
    command += " -map -0:s? "               # strip subtitles
    command += " -map -0:d? "               # strip misc data (note: this does not appear to strip metadata)
    command += " -n "                       # do not overwrite files, exit immediately if specified output already exists
    command += "\"" + out + "\""            # specify output file

    # print the command that will be executed
    print(command)
    # run command
    os.system(command)

def encode_opus(src):
    # generate output file path
    out = new_extension(src, ".opus")

    # ffmpeg options
    command = PATH_FFMPEG                   # path to ffmpeg executable
    command += " -i \"" + src + "\" "       # specify input file
    command += " -c:a libopus -b:a 96k "    # specify option: opus audio codec
    command += " -map -0:v? "               # strip video data
    command += " -map -0:s? "               # strip subtitles
    command += " -map -0:d? "               # strip misc data (note: this does not appear to strip metadata)
    command += " -n "                       # do not overwrite files, exit immediately if specified output already exists
    command += "\"" + out + "\""            # specify output file

    # print the command that will be executed
    print(command)
    # run command
    os.system(command)

#                           /$$
#                          |__/
#   /$$$$$$/$$$$   /$$$$$$  /$$ /$$$$$$$
#  | $$_  $$_  $$ |____  $$| $$| $$__  $$
#  | $$ \ $$ \ $$  /$$$$$$$| $$| $$  \ $$
#  | $$ | $$ | $$ /$$__  $$| $$| $$  | $$
#  | $$ | $$ | $$|  $$$$$$$| $$| $$  | $$
#  |__/ |__/ |__/ \_______/|__/|__/  |__/
#
#
#

def main():
    # 1. does config.json say we are using the DB backend?
    # 1.a.1. was a command line option specified for rebuilding the file listing DB? if not ask user what to do
    # 1.a.2. was a command line option specified for processing the encoding queue DB? if not ask user what to do
    # 2. if we aren't using the DB we must be solely here to do the file based encoding queue. notify the user as such

    # get write access to our globals
    global config, conn, cur, run_file_queue, run_db_queue, run_db_filesystem

    # check if a request for help was specified
    if specified('-h') or specified('--help'):
        show_help()

    # load config data
    with open('config.json') as f:
        config = json.load(f)

    # check if we are using the database backend
    if config['use_db'] == 1:
        setup_db()

        # check what we are doing with the database
        if specified('-s') or specified('--system'): run_db_filesystem = True
        if specified('-q') or specified('--queue'): run_db_queue = True

    # if we aren't using the db backend
    else:
        print("config.json specified no database use.")
        print("processing file based queues.")
        run_file_queue = True


    # allow either mode to process file queue optionally
    if specified('-f') or specified('--file'):
        run_file_queue = True

    if not(run_file_queue or run_db_queue or run_db_filesystem):
        print("db was specified but no db action was specified.")
        print("please review the switches.")
        show_help()

    f = Process(target = file_queue)
    q = Process(target = db_queue)
    s = Process(target = db_filesystem)

    if(run_file_queue): print("config: run file queue")
    if(run_db_queue): print("config: run db queue")
    if(run_db_filesystem): print("config: run db filesystem monitor")


    while True:
        if run_file_queue and not f.is_alive():
            f = Process(target = file_queue)
            f.start()
            time.sleep(1)
        if run_db_queue and not q.is_alive():
            q = Process(target = db_queue)
            q.start()
            time.sleep(1)
        if run_db_filesystem and not s.is_alive():
            s = Process(target = db_filesystem)
            s.start()
            time.sleep(1)

        time.sleep(10)

        sys.stdout.write('.')
        sys.stdout.flush()

# le boilerplate
if __name__ == "__main__":
    main()
