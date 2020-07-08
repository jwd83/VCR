import time
import os

PATH_FFMPEG = "C:\\Users\\jared\\Downloads\\ffmpeg-4.2.1-win64-static\\bin\\ffmpeg.exe"
PATH_QUEUE = "G:\\nqueue.txt"


# https://trac.ffmpeg.org/wiki/HWAccelIntro
#
#
# h265 software = libx265
# h264 hardware = h264_nvenc
# h265 hardware = hevc_nvenc
#
#
# command
# G:\psexec.exe -d C:\Users\jared\Downloads\ffmpeg-4.2.1-win64-static\bin\ffmpeg.exe -i "G:\Movies+TV\workaholics\Season.1\Workaholics.S01E06.HDTV.XviD-ASAP.The.Strike.avi" -vcodec h264 -acodec aac "G:\Movies+TV\workaholics\Season.1\Workaholics.S01E06.HDTV.XviD-ASAP.The.Strike.avi.html.mp4"

# PATH_FFMPEG
# -i "G:\Movies+TV\workaholics\Season.1\Workaholics.S01E06.HDTV.XviD-ASAP.The.Strike.avi"
# -vcodec h264 -acodec aac 
# "G:\Movies+TV\workaholics\Season.1\Workaholics.S01E06.HDTV.XviD-ASAP.The.Strike.avi.html.mp4"

def get_next_file():
    with open(PATH_QUEUE, 'r') as fin:
        data = fin.read().splitlines(True)
    with open(PATH_QUEUE, 'w') as fout:
        fout.writelines(data[1:])
    if(len(data) > 0):
        return data[0].strip()
    else:
        return ""


def main():
    print("Reading the queue...")

    while True:

        input_src = get_next_file()

        if(input_src != ""):
            print("Prepaing command...")
            # specify path to ffmpeg
            command = PATH_FFMPEG 

            # specify input file
            command += " -i \"" + input_src + "\" "


            # setup h265 quality factor 28
            command += " -c:v libx265 -crf 28 "

            # setup hevc nvenc
            # command += " -c:v hevc_nvenc "
            
            # setup 128k aac audio
            command += " -c:a aac -b:a 128k "

            # specify option: Do not overwrite output files, and exit immediately if a specified output file already exists.
            command += " -n "

            # specify output file
            command += "\"" + input_src + ".nvenc.mp4\""

            # print the command that will be executed
            print(command)

            # run command
            os.system(command)

            print("Reading the queue")

        # wait to check the file again
        time.sleep(3)
        # print(".",end =" ")
        # print(".")

if __name__ == "__main__":
    main()