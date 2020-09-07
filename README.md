# V C R

# oǝpᴉɅ ʎʇᴉϽ ɹǝʌᴉꓤ

A single file PHP script for WAMP to share and encode queue your local files online along with Python daemons to process said reencode requests.

![VCR](https://cdn.mos.cms.futurecdn.net/w48feV8za6DRPBSuvyvpPB-1200-80.jpg)

## Features

* PHP single file front end for viewing and managing your media WAMP/XAMPP
* Python single file background daemon to handle h264/h265 video and .opus audio reencoding requests
  * Opus reencodes to 48kbps per channel with 96k minimum.
    * 96k stereo audio
    * 192k quadrophonic
    * 384k octophonic
* re-container video files to mp4 container (may fix playback in some browsers. new files end in .re.mp4)
* Autoplay your collection of audio and video files where supported by your browser.
* Download shared files remotely. 
* Next and Previous buttons while in watch or listen mode
* Precache next file to speed up track switching in listen mode (pre-caching starts after 10s on a listen page)
* Randomly selected background image for header in settings. Can use single list element for static
* Link to timestamp in video using t= in URL
* Can use realtime filesystem data for simple setup or MariaDB/MySQL backend for enhanced performance

## Requirements
* Python 3
  * Mariadb/Mysql driver
* XAMPP or WAMP
* FFmpeg
  * https://ffmpeg.zeranoe.com/builds/
  * Sadly ffmpeg.zeranoe.com will close on Sep 18, 2020, and all builds will be removed
  * going forward eugeneware provides static builds at https://github.com/eugeneware/ffmpeg-static
  * You may be able to build it yourself by using the tools over at https://github.com/m-ab-s/media-autobuild_suite


## Setup Notes

### sshd_config

If you are using a reverse SSH tunnel to bring your server online make sure 
change "GatewayPorts no" to "GatewayPorts yes", it may need to be uncommented
(remove leading # from line)

### add VCR user

While at a command prompt at "C:\wamp64\bin\apache\apache2.4.41\bin" (or the like)

htpasswd "c:\wamp64\gdlogins" user_name_here

## TODO
* support intel SVT encoder
* add advanced encoding toggles (like the 5.1 remap)
* log encoding result if possible?
* detect 5.1 and change encoding options when necessary (libopus/av1/vp9 i'm looking at you)
  * command += "-af \"channelmap=channel_layout=5.1\""  # 5.1 fix... https://trac.ffmpeg.org/ticket/5718 yikes 4 years running bug in libopus
* add a view that display ffprobe output about a file
* add file-based queue support to daemon.py
* categories on links page?
* maybe make all categories and generalized config data in config.json. 
* setup dynamic DNS
* Switch to https from http. Lets encrypt or self signed
* look at directly converting to opus on demand
* look into gapless queueing of audio tracks. precaching and redirect helps but perhaps make the player page more ajaxy
* Look into RTMP live streaming/encoding video/audio realtime.
* Pick random jpg file from /img/ to use as header image
* cleanup code to use URL_BASE
* if the queue files are empty don't rewrite them
* add link timestamp into audio as ~~well as video~~
* better tagline(s)
* look into qbt integration. submit magnet -> python daemon feeds to qbt web ?
