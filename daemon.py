# Python daemon for VCR
#
# Distributed under the
#
# MIT License
#
# Copyright (c) 2020 Jared
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
# http://patorjk.com/software/taag/#p=display&c=bash&f=Big%20Money-ne&t=encoding
#
#

import json
import mariadb
import os
import sys
import time

def new_extension(src, new_ext):
    filename, file_extension = os.path.splitext(src)
    return filename + new_ext

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

def encode_h265(src):
    out = new_extension(src, ".h265.mp4")

def encode_h264(src):
    out = new_extension(src, ".h264.mp4")

def encode_opus(src):
    out = new_extension(src, ".opus")

#                           /$$
#                          |__/
#   /$$$$$$/$$$$   /$$$$$$  /$$ /$$$$$$$
#  | $$_  $$_  $$ |____  $$| $$| $$__  $$
#  | $$ \ $$ \ $$  /$$$$$$$| $$| $$  \ $$
#  | $$ | $$ | $$ /$$__  $$| $$| $$  | $$
#  | $$ | $$ | $$|  $$$$$$$| $$| $$  | $$
#  |__/ |__/ |__/ \_______/|__/|__/  |__/
#
#
#

def main():
    # 1. does config.json say we are using the DB backend?
    # 1.a.1. was a command line option specified for rebuilding the file listing DB? if not ask user what to do
    # 1.a.2. was a command line option specified for processing the encoding queue DB? if not ask user what to do
    # 2. if we aren't using the DB we must be solely here to do the file based encoding queue. notify the user as such



    pass



if __name__ == "__main__":
    main()
