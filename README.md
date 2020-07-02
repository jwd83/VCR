# VCR

## TODO

### Next / Previous buttons

Now that autoplay is working add next and previous button

### Audio/Audio Book - Pre-cache next track when listening to current one

## Setup Notes

### sshd_config

change "GatewayPorts no" to "GatewayPorts yes", it may need to be uncommented (remove leading # from line)

### add VCR user

While at a command prompt at "C:\wamp64\bin\apache\apache2.4.41\bin" (or the like)

htpasswd "c:\wamp64\gdlogins" user_name_here


