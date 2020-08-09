# V C R

# oǝpᴉɅ ʎʇᴉϽ ɹǝʌᴉꓤ

A PHP script for WAMP to share your local files online along with a Python daemon to manage html video reencode requests.

![VCR](https://cdn.mos.cms.futurecdn.net/w48feV8za6DRPBSuvyvpPB-1200-80.jpg)

## Features

* single file PHP front end for viewing and managing your media WAMP
* python background daemon to handle h264/h265 video and .opus audio reencoding requests
* re-container video files to mp4 container (may fix playback in some browsers. new files end in .re.mp4)
* Autoplay your collection of audio and video files where supported by your browser.
* Download shared files remotely. 
* Next and Previous buttons while in watch or listen mode
* Precache next file to speed up track switching in listen mode (pre-caching starts after 10s on a listen page)
* Randomly selected background image for header in settings. Can use single list element for static
* Link to timestamp in video using t= in URL

## TODO
* setup dynamic DNS
* general SSL certificate 
* ~~remove aac audio encoding~~ isntead offer 128k opus option? 
* SQLite/MariaDB backend for faster file searches/queue consolidation
* look at directly converting to opus on demand
* Switch to https from http. Lets encrypt or self signed
* Look into RTMP live streaming/encoding video/audio realtime.
* Pick random jpg file from /img/ to use as header image
* cleanup code to use URL_BASE
* if the queue files are empty don't rewrite them

## Setup Notes

### sshd_config

change "GatewayPorts no" to "GatewayPorts yes", it may need to be uncommented (remove leading # from line)

### add VCR user

While at a command prompt at "C:\wamp64\bin\apache\apache2.4.41\bin" (or the like)

htpasswd "c:\wamp64\gdlogins" user_name_here


