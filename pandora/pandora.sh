#!/bin/bash
export XDG_CONFIG_HOME=/home/pi/pianobar_users/$1
#rm /tmp/pianobar /tmp/pianobar_out
#touch /tmp/pianobar_out
#chmod 777 /tmp/pianobar_out
#mkfifo /tmp/pianobar
#screen -d -m -S pandora pianobar &>/tmp/pianobar_out
nohup pianobar &>/tmp/pianobar_out &disown
