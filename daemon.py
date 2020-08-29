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

def file_queue():
    pass

def db_queue():
    pass

def db_filesystem():
    pass

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

def encode_h264(src):
    out = new_extension(src, ".h264.mp4")

def encode_opus(src):
    out = new_extension(src, ".opus")

def specified(opt):
    if opt in sys.argv:
        return True
    return False

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

            # check what we are doing
            if specified('-s') or specified('--system'):
                run_db_filesystem = True

            if specified('-q') or specified('--queue'):
                run_db_queue = True

        except mariadb.Error as e:
            print(f"Error connecting to MariaDB Platform: {e}")
            sys.exit(1)

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

    while True:
        if run_file_queue:
            file_queue()
        if run_db_queue:
            db_queue()
        if run_db_filesystem:
            db_filesystem()

# le boilerplate
if __name__ == "__main__":
    main()
