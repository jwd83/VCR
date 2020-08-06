# https://trac.ffmpeg.org/wiki/HWAccelIntro
#
#
# h265 software = libx265           # best for offline rendering of files
# h264 hardware = h264_nvenc        # h264 nvidia hardware encoder - bad for filesize
# h265 hardware = hevc_nvenc        # h265 nvidia hardware encoder - bad for filesize
#
#
# selecting an h265 encoder, simple answer is software x265 is superior for stored files
# hardware 265 is only better for realtime livestreaming
#
# https://www.reddit.com/r/linux/comments/4wncug/hevc_nvenc_ffmpeg_huge_file_size/


import os
import sqlite3
import sys
import time

PATH_FFMPEG = "C:\\Users\\jared\\Downloads\\ffmpeg-4.2.1-win64-static\\bin\\ffmpeg.exe"
PATH_QUEUE_H264 = "G:\\queue_h264.txt"
PATH_QUEUE_H265 = "G:\\queue_h265.txt"
PATH_QUEUE_M4A = "G:\\queue_m4a.txt"
PATH_QUEUE_OPUS = "G:\\queue_opus.txt"
MSG_QUEUE = "Reading the queues"


def new_extension(src, new_ext):
    filename, file_extension = os.path.splitext(src)
    return filename + new_ext



def queue_msg():
    print(MSG_QUEUE, end="")


def next_file(queue_file):
    with open(queue_file, 'r') as fin:
        data = fin.read().splitlines(True)
    with open(queue_file, 'w') as fout:
        fout.writelines(data[1:])
    if(len(data) > 0):
        return data[0].strip()
    else:
        return ""

def process_opus():
    # https://wiki.hydrogenaud.io/index.php?title=Opus#Music_encoding_quality
    input_src = next_file(PATH_QUEUE_OPUS)
    while(input_src != ""):
        command = PATH_FFMPEG


        # specify input file
        command += " -i \"" + input_src + "\" "

        # specify option: opus audio codec
        command += " -c:a libopus -b:a 96k "


        command += " -map -0:v? "   # strip video data
        command += " -map -0:s? "   # strip subtitles
        command += " -map -0:d? "   # strip misc data (note: this does not appear to strip metadata)

        # specify option: Do not overwrite output files, and exit immediately if a specified output file already exists.
        command += " -n "

        # specify output file
        command += "\"" + new_extension(input_src, ".opus") + "\""

        # print the command that will be executed
        print(command)

        # run command
        os.system(command)

        queue_msg()

        # process the next file in the queue
        input_src = next_file(PATH_QUEUE_OPUS)



def process_m4a():
    input_src = next_file(PATH_QUEUE_M4A)
    if(input_src != ""):
        command = PATH_FFMPEG


        # specify input file
        command += " -i \"" + input_src + "\" "

        # specify option: aac audio codec
        command += " -c:a aac -b:a 160k "

        # specify option: copy video codec (for album images)
        # ffmpeg will otherwise try to convert images/video/mjpeg data to h264 which will fail
        command += " -c:v copy "

        # Progressive Download (faststart option)
        # By default the MP4 muxer writes the 'moov' atom after the audio stream ('mdat' atom) at the
        # end of the file. This results in the user requiring to download the file completely before
        # playback can occur. Relocating this moov atom to the beginning of the file can facilitate
        # playback before the file is completely downloaded by the client.
        # You can do this with the -movflags +faststart option:
        command += " -movflags +faststart "

        # specify option: Do not overwrite output files, and exit immediately if a specified output file already exists.
        command += " -n "

        # specify output file
        command += "\"" + new_extension(input_src, ".aac") + "\""

        # print the command that will be executed
        print(command)

        # run command
        os.system(command)

        queue_msg()

def process_h264():

    input_src = next_file(PATH_QUEUE_H264)

    if(input_src != ""):
        print("Prepaing command...")
        # specify path to ffmpeg
        command = PATH_FFMPEG

        # specify input file
        command += " -i \"" + input_src + "\" "

        # specify option: h264 video codec
        command += " -vcodec h264 "

        # specify option: aac audio codec
        command += " -acodec aac "

        # specify option: Do not overwrite output files, and exit immediately if a specified output file already exists.
        command += " -n "

        # specify output file
        command += "\"" + input_src + ".h264.mp4\""

        # print the command that will be executed
        print(command)

        # run command
        os.system(command)

        queue_msg()

def process_h265():
    input_src = next_file(PATH_QUEUE_H265)

    if(input_src != ""):
        # print("Prepaing command...")
        # specify path to ffmpeg
        command = PATH_FFMPEG

        # specify input file
        command += " -i \"" + input_src + "\" "


        # setup h265 quality factor 28
        command += " -c:v libx265 -crf 28 "
        # command += " -c:v libx265 -preset ultrafast "

        # setup hevc nvenc (generally avoid this)
        # software h265 is much better.
        # command += " -c:v hevc_nvenc -crf 28"
        # command += " -c:v hevc_nvenc "

        # setup 128k aac audio
        # command += " -c:a aac -b:a 128k "

        # specify option: aac audio codec
        # command += " -acodec aac "
        # specify option: copy audio track (don't reencode!)
        command += " -c:a copy "

        # specify option: Do not overwrite output files, and exit immediately if a specified output file already exists.
        command += " -n "

        # specify output file
        command += "\"" + input_src + ".h265.mp4\""
        # command += "\"" + input_src + ".libx265.h265.mp4\""
        # command += "\"" + input_src + ".nvenc.h265.mp4\""

        # print the command that will be executed
        print(command)

        # run command
        os.system(command)

        queue_msg()


def main():
    queue_msg()
    while True:
        process_opus()
        process_m4a()
        process_h264()
        process_h265()
        sys.stdout.write('.')
        sys.stdout.flush()

        # print(".", end="")
        time.sleep(3)

if __name__ == "__main__":
    main()