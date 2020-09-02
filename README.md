# V C R

# oǝpᴉɅ ʎʇᴉϽ ɹǝʌᴉꓤ

A single file PHP script for WAMP to share and encode queue your local files online along with Python daemons to process said reencode requests.

![VCR](https://cdn.mos.cms.futurecdn.net/w48feV8za6DRPBSuvyvpPB-1200-80.jpg)

## Features

* single file PHP front end for viewing and managing your media WAMP/XAMPP
* python background daemon to handle h264/h265 video and .opus audio reencoding requests
* re-container video files to mp4 container (may fix playback in some browsers. new files end in .re.mp4)
* Autoplay your collection of audio and video files where supported by your browser.
* Download shared files remotely. 
* Next and Previous buttons while in watch or listen mode
* Precache next file to speed up track switching in listen mode (pre-caching starts after 10s on a listen page)
* Randomly selected background image for header in settings. Can use single list element for static
* Link to timestamp in video using t= in URL
* Can use realtime filesystem data or MariaDB backend

## Setup Notes

### sshd_config

If you are using a reverse SSH tunnel to bring your server online make sure 
change "GatewayPorts no" to "GatewayPorts yes", it may need to be uncommented
(remove leading # from line)

### add VCR user

While at a command prompt at "C:\wamp64\bin\apache\apache2.4.41\bin" (or the like)

htpasswd "c:\wamp64\gdlogins" user_name_here



## TODO
* adjust opus encoder to do 48kbps per channel instead of flat 96kbps. quadrophonic and surround audio is getting set to 96 when it shuold be 192 for quadrophonic or 288 for 5.1
* ~~create an outbound link/favorites page~~ (basic links page working, add categories?)
* ~~move popular searches to config.json~~
  * maybe make all categories and generalized config data in config.json. 
* merge python database daemon into encoding daemon
  * multiprocessing lib to split off encoding and fs update workers
* move encoding queue to database daemon
* look at making a config.json to share between PHP/Python
* setup dynamic DNS
* general SSL certificate 
* ~~SQLite/MariaDB backend for faster file searches/queue consolidation~~
  * have daemon check modified time of base paths to see when it's time to rescan?
* look at directly converting to opus on demand
* Switch to https from http. Lets encrypt or self signed
* Look into RTMP live streaming/encoding video/audio realtime.
* Pick random jpg file from /img/ to use as header image
* cleanup code to use URL_BASE
* if the queue files are empty don't rewrite them
* add link timestamp into audio as well as video
* better tagline(s)
* look into qbt integration. submit magnet -> python daemon feeds to qbt web ?
