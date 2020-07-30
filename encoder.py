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
#
# command
# G:\psexec.exe -d C:\Users\jared\Downloads\ffmpeg-4.2.1-win64-static\bin\ffmpeg.exe -i "G:\Movies+TV\workaholics\Season.1\Workaholics.S01E06.HDTV.XviD-ASAP.The.Strike.avi" -vcodec h264 -acodec aac "G:\Movies+TV\workaholics\Season.1\Workaholics.S01E06.HDTV.XviD-ASAP.The.Strike.avi.html.mp4"

# PATH_FFMPEG
# -i "G:\Movies+TV\workaholics\Season.1\Workaholics.S01E06.HDTV.XviD-ASAP.The.Strike.avi"
# -vcodec h264 -acodec aac 
# "G:\Movies+TV\workaholics\Season.1\Workaholics.S01E06.HDTV.XviD-ASAP.The.Strike.avi.html.mp4"

import os
import sys
import time

PATH_FFMPEG = "C:\\Users\\jared\\Downloads\\ffmpeg-4.2.1-win64-static\\bin\\ffmpeg.exe"
PATH_QUEUE_H264 = "G:\\queue.txt"
PATH_QUEUE_H265 = "G:\\nqueue.txt"
MSG_QUEUE = "Reading the queues"

def queue_msg():
    print(MSG_QUEUE, end="")

def next_file_h264():
    with open(PATH_QUEUE_H264, 'r') as fin:
        data = fin.read().splitlines(True)
    with open(PATH_QUEUE_H264, 'w') as fout:
        fout.writelines(data[1:])
    if(len(data) > 0):
        return data[0].strip()
    else:
        return ""

def next_file_h265():
    with open(PATH_QUEUE_H265, 'r') as fin:
        data = fin.read().splitlines(True)
    with open(PATH_QUEUE_H265, 'w') as fout:
        fout.writelines(data[1:])
    if(len(data) > 0):
        return data[0].strip()
    else:
        return ""

def process_h264():

    input_src = next_file_h264()

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
    input_src = next_file_h265()

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
        command += " -acodec aac "

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
        process_h264()
        process_h265()
        sys.stdout.write('.')
        sys.stdout.flush()

        # print(".", end="")
        time.sleep(3)

if __name__ == "__main__":
    main()