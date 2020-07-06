# V C R
# oǝpᴉɅ ʎʇᴉϽ ɹǝʌᴉꓤ

A wamp script to share your local files online.

## Features

* php front end for WAMP
* python background daemon to handle video re-encoding requests
* reencode files to mp4 container (may fix playback in some browsers. new files end in .re.mp4
* Autoplay your collection of audio and video files where supported by your browser.
* Download shared files remotely. 
* Next and Previous buttons while in watch or listen mode
* Precache next file to speed up track switching in listen mode (pre-caching starts after 10s on a listen page)

## TODO

### Switch to https from http. Lets encrypt or self signed

### Schedule video reencodes

Created a reencode queue that goes to mp4 container in a browser friendly codec.

## Setup Notes

### sshd_config

change "GatewayPorts no" to "GatewayPorts yes", it may need to be uncommented (remove leading # from line)

### add VCR user

While at a command prompt at "C:\wamp64\bin\apache\apache2.4.41\bin" (or the like)

htpasswd "c:\wamp64\gdlogins" user_name_here


