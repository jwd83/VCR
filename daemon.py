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
# http://patorjk.com/software/taag/#p=display&c=bash&f=Big%20Money-ne&t=sample

import datetime
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

# globals
PATH_FFMPEG =           "C:\\Users\\jared\\Downloads\\ffmpeg-4.2.1-win64-static\\bin\\ffmpeg.exe"
PATH_FFPROBE =          "C:\\Users\\jared\\Downloads\\ffmpeg-4.2.1-win64-static\\bin\\ffprobe.exe"

PATH_QUEUE_H264 =       "G:\\queue_h264.txt"
PATH_QUEUE_H265 =       "G:\\queue_h265.txt"
PATH_QUEUE_M4A =        "G:\\queue_m4a.txt"
PATH_QUEUE_OPUS =       "G:\\queue_opus.txt"

MSG_QUEUE =             "Reading the queues"

QUERY_QUEUE_NEXT =      "SELECT * FROM encoder_queue ORDER BY id LIMIT 1"
QUERY_DELETE_NEXT =     "DELETE FROM encoder_queue WHERE id = ?"

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

def timestamp():
    return "[" + datetime.datetime.fromtimestamp(time.time()).strftime('%Y-%m-%d %H:%M:%S') + " UTC] "

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


# return true if an item was found in the queue
def next_file_from_db():
    # get next file by executing QUERY_QUEUE_NEXT
    # if we got a file from the queue delete it's position in the queue by running QUERY_DELETE_NEXT with the id returned from QUERY_QUEUE_NEXT
    # pass

    cur.execute(QUERY_QUEUE_NEXT)

    res = cur.fetchone()

    # expect 3 fields in a response
    if(res != None):
        # position 0: id
        # position 1: path
        # position 2: encoding format

        # delete the record returned from the queue
        cur.execute(QUERY_DELETE_NEXT, (res[0], ))

        # process the record
        if(res[2] == 'opus'): encode_opus(res[1])
        if(res[2] == 'h264'): encode_h264(res[1])
        if(res[2] == 'h265'): encode_h265(res[1])

        return True
    return False



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
    global conn, cur, config
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
    except mariadb.Error as e:
        print(f"Error connecting to MariaDB Platform: {e}")
        sys.exit(1)

#                           /$$   /$$     /$$
#                          | $$  | $$    |__/
#   /$$$$$$/$$$$  /$$   /$$| $$ /$$$$$$   /$$
#  | $$_  $$_  $$| $$  | $$| $$|_  $$_/  | $$
#  | $$ \ $$ \ $$| $$  | $$| $$  | $$    | $$
#  | $$ | $$ | $$| $$  | $$| $$  | $$ /$$| $$
#  | $$ | $$ | $$|  $$$$$$/| $$  |  $$$$/| $$
#  |__/ |__/ |__/ \______/ |__/   \___/  |__/
#
#    /$$$$$$   /$$$$$$   /$$$$$$   /$$$$$$$  /$$$$$$   /$$$$$$$ /$$$$$$$
#   /$$__  $$ /$$__  $$ /$$__  $$ /$$_____/ /$$__  $$ /$$_____//$$_____/
#  | $$  \ $$| $$  \__/| $$  \ $$| $$      | $$$$$$$$|  $$$$$$|  $$$$$$
#  | $$  | $$| $$      | $$  | $$| $$      | $$_____/ \____  $$\____  $$
#  | $$$$$$$/| $$      |  $$$$$$/|  $$$$$$$|  $$$$$$$ /$$$$$$$//$$$$$$$/
#  | $$____/ |__/       \______/  \_______/ \_______/|_______/|_______/
#  | $$
#  | $$
#  |__/

def file_queue():
    # config is empty once multiprocessing is called, we must reload it
    global config

    try:
        # load config data
        with open('config.json') as f:
            config = json.load(f)
        print(timestamp() + "starting file based queue")
        while True:
            pass
    except:
        print(timestamp() + "error in file based queue")

def db_queue():
    # config is empty once multiprocessing is called, we must reload it
    global config

    try:
        # load config data
        with open('config.json') as f:
            config = json.load(f)
        print(timestamp() + "dbq: starting db based queue")
        setup_db()

        while True:
            print(timestamp() + "dbq: checking queue")
            if not next_file_from_db():
                time.sleep(5)

    except:
        print(timestamp() + "dbq: error in db based queue")

def db_filesystem():
    # config is empty once multiprocessing is called, we must reload it
    global config

    try:
        # load config data
        with open('config.json') as f:
            config = json.load(f)

        print("starting db based file system monitor")
        setup_db()
        while True:
            print(timestamp() + "dbfs: Rebuilding file index...")
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
            print(timestamp() + "dbfs: Rebuilding complete.")

            time.sleep(60)

    except e:
        print(timestamp() + "error in db based file system monitor")

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
#
#
#
#                        /$$$$$$
#                       /$$__  $$
#   /$$    /$$ /$$$$$$ | $$  \ $$
#  |  $$  /$$//$$__  $$|  $$$$$$$
#   \  $$/$$/| $$  \ $$ \____  $$
#    \  $$$/ | $$  | $$ /$$  \ $$
#     \  $/  | $$$$$$$/|  $$$$$$/
#      \_/   | $$____/  \______/
#            | $$
#            | $$
#            |__/
def encode_vp9(src):
    out = new_extension(src, ".vp9.webm")

    #  C:\Users\jared\Downloads\ffmpeg-4.2.1-win64-static\bin\ffmpeg.exe -i "G:\Movies+TV/Grandma's.Boy.[2006]DVDRip.H264(BINGOWINGZ-UKB-RG)\sample\sample.mp4"  -c:v libvpx-vp9 -b:v 0 -crf 30 -pass 1 -an -f null NUL && ^ C:\Users\jared\Downloads\ffmpeg-4.2.1-win64-static\bin\ffmpeg.exe -i "G:\Movies+TV/Grandma's.Boy.[2006]DVDRip.H264(BINGOWINGZ-UKB-RG)\sample\sample.mp4"  -c:v libvpx-vp9 -b:v 0 -crf 30 -pass 2 -c:a libopus  "G:\Movies+TV/Grandma's.Boy.[2006]DVDRip.H264(BINGOWINGZ-UKB-RG)\sample\sample.vp9.webm"



    # ffmpeg -i input.mp4 -c:v libvpx-vp9 -b:v 0 -crf 30 -pass 1 -an -f null NUL && ^ ffmpeg -i input.mp4 -c:v libvpx-vp9 -b:v 0 -crf 30 -pass 2 -c:a libopus output.webm
    command = ""
    # pass 1
    # command += PATH_FFMPEG
    # command += " -i \"" + src + "\" "       # specify input file
    # command += " -c:v libvpx-vp9 -b:v 0 -crf 30 -pass 1 -an -f null test"

    # command += " -c:v libvpx-vp9 -b:v 0 -crf 30 -pass 1 -an -f null NUL && ^ "
    # # pass 2
    command += PATH_FFMPEG
    command += " -i \"" + src + "\" "       # specify input file
    command += " -c:v libvpx-vp9 -b:v 0 -crf 30 -pass 2 -c:a libopus "
    command += " \"" + out + "\" "          # specify output file

    # print the command that will be executed
    print(timestamp() + command)
    # run command
    os.system(command)


#   /$$        /$$$$$$   /$$$$$$  /$$$$$$$
#  | $$       /$$__  $$ /$$__  $$| $$____/
#  | $$$$$$$ |__/  \ $$| $$  \__/| $$
#  | $$__  $$  /$$$$$$/| $$$$$$$ | $$$$$$$
#  | $$  \ $$ /$$____/ | $$__  $$|_____  $$
#  | $$  | $$| $$      | $$  \ $$ /$$  \ $$
#  | $$  | $$| $$$$$$$$|  $$$$$$/|  $$$$$$/
#  |__/  |__/|________/ \______/  \______/
def encode_h265(src):
    encode_vp9(src)
    return

    out = new_extension(src, ".h265.mkv")

    # ffmpeg options
    command = PATH_FFMPEG                   # path to ffmpeg executable
    command += " -i \"" + src + "\" "       # specify input file
    command += " -c:v libx265 "             # video codec: h265
    command += " -crf 23 "                  # quality factor
    # command += " -preset veryslow  "        # encoding speed
    command += " -c:a copy "                # audio codec: losslessy copy audio track (no reencode!)
    command += " -n "                       # do not overwrite files, exit immediately if specified output already exists
    command += " \"" + out + "\" "          # specify output file

    # print the command that will be executed
    print(timestamp() + command)
    # run command
    os.system(command)



#   /$$        /$$$$$$   /$$$$$$  /$$   /$$
#  | $$       /$$__  $$ /$$__  $$| $$  | $$
#  | $$$$$$$ |__/  \ $$| $$  \__/| $$  | $$
#  | $$__  $$  /$$$$$$/| $$$$$$$ | $$$$$$$$
#  | $$  \ $$ /$$____/ | $$__  $$|_____  $$
#  | $$  | $$| $$      | $$  \ $$      | $$
#  | $$  | $$| $$$$$$$$|  $$$$$$/      | $$
#  |__/  |__/|________/ \______/       |__/
def encode_h264(src):
    out = new_extension(src, ".h264.mp4")

    # ffmpeg options
    command = PATH_FFMPEG                   # path to ffmpeg executable
    command += " -i \"" + src + "\" "       # specify input file
    command += " -vcodec h264 "             # video codec: h264
    command += " -acodec aac "              # audio codec: aac
    command += " -n "                       # do not overwrite files, exit immediately if specified output already exists
    command += "\"" + out + "\""            # specify output file

    # print the command that will be executed
    print(timestamp() + command)

    # run command
    os.system(command)

#    /$$$$$$   /$$$$$$  /$$   /$$  /$$$$$$$
#   /$$__  $$ /$$__  $$| $$  | $$ /$$_____/
#  | $$  \ $$| $$  \ $$| $$  | $$|  $$$$$$
#  | $$  | $$| $$  | $$| $$  | $$ \____  $$
#  |  $$$$$$/| $$$$$$$/|  $$$$$$/ /$$$$$$$/
#   \______/ | $$____/  \______/ |_______/
#            | $$
#            | $$
#            |__/
def encode_opus(src):
    # ffprobe -i "FILE_PATH" -show_entries stream=channels -select_streams a:0 -of compact=p=0:nk=1 -v 0
    # use ffprobe to detect channels
    channels_command = PATH_FFPROBE                     # path to ffprobe executable
    channels_command += " -i \"" + src + "\" "          # specify input file
    # remaining flags
    channels_command += " -show_entries stream=channels -select_streams a:0 -of compact=p=0:nk=1 -v 0"
    channel_count = int(os.popen(channels_command).read().strip())


    if(channel_count < 2): channel_count = 2
    if(channel_count > 128): channel_count = 128 # dolby atmos specifies a maximum of 128 channels
    print("Channels: " + str(channel_count))

    # generate output file path
    out = new_extension(src, ".opus")

    # ffmpeg options
    command = PATH_FFMPEG                                   # path to ffmpeg executable
    command += " -i \"" + src + "\" "                       # specify input file
    command += " -c:a libopus "                             # specify opus codec
    command += " -b:a " + str(channel_count*48) + "k "      # bitrate
    command += " -map -0:v? "                               # strip video data
    command += " -map -0:s? "                               # strip subtitles
    command += " -map -0:d? "                               # strip misc data (note: this does not appear to strip metadata)
    command += " -n "                                       # do not overwrite files, exit immediately if specified output already exists
    command += "\"" + out + "\""                            # specify output file

    # print the command that will be executed
    print(timestamp() + command)
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

        # periodically check on other processes
        time.sleep(10)

# le boilerplate
if __name__ == "__main__":
    main()
