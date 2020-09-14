<?php
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
#                         /$$     /$$     /$$
#                        | $$    | $$    |__/
#    /$$$$$$$  /$$$$$$  /$$$$$$ /$$$$$$   /$$ /$$$$$$$   /$$$$$$   /$$$$$$$
#   /$$_____/ /$$__  $$|_  $$_/|_  $$_/  | $$| $$__  $$ /$$__  $$ /$$_____/
#  |  $$$$$$ | $$$$$$$$  | $$    | $$    | $$| $$  \ $$| $$  \ $$|  $$$$$$
#   \____  $$| $$_____/  | $$ /$$| $$ /$$| $$| $$  | $$| $$  | $$ \____  $$
#   /$$$$$$$/|  $$$$$$$  |  $$$$/|  $$$$/| $$| $$  | $$|  $$$$$$$ /$$$$$$$/
#  |_______/  \_______/   \___/   \___/  |__/|__/  |__/ \____  $$|_______/
#                                                       /$$  \ $$
#                                                      |  $$$$$$/
#                                                       \______/

# track runtime
$start = microtime(true);
$rustart = getrusage();

# constants
define("CONFIG_JSON_LOCATION", "G:\\config.json");
define("FILESYSTEM_BASE", 'G:\\');
define("PATH_FFMPEG", 'C:\\Users\\jared\\Downloads\\ffmpeg-4.2.1-win64-static\\bin\\ffmpeg.exe');
define("PATH_H264_QUEUE", "G:\\queue_h264.txt");
define("PATH_H265_QUEUE", "G:\\queue_h265.txt");
define("PATH_M4A_QUEUE", "G:\\queue_m4a.txt");
define("PATH_OPUS_QUEUE", "G:\\queue_opus.txt");

# load config file
$config = json_decode(file_get_contents(CONFIG_JSON_LOCATION));
// var_dump($config->db->user);

# setup db connection
$db = NULL;
if($config->use_db == 1) {
    $db = new mysqli(
        $config->db->host,
        $config->db->user,
        $config->db->password,
        $config->db->database
    );
}

# variables
$title = "Highwind's Stash";

$backgrounds = [
    "black-pearl.jpg",
    "camera.jpg",
    "dvd.jpg",
    "daftpunk.jpg",
    "darwin.jpg",
    "motoko.jpg",
    "pinkfloyd.jpg",
    "radiohead.jpg",
    "vcr.jpg",
];

$search_type = '';
$dump_path = '';
$feature = "none";

$jquery_code_to_run = "";
$buttons = array();
$next_file_to_play = "";

$extensions = [
    ".aac",
    ".avi",
    ".flac",
    ".iso",
    ".m4a",
    ".m4b",             // uncommon audio format
    ".m4v",
    ".mkv",
    ".mp3",
    ".mp4",
    ".ogg",
    ".opus",
    ".smc",
    ".wma",
    ".webm",            // used for VP9
    ".y4m"              // uncompressed video
];

$white_list = [
    "Emulation + ROMs\\" ,
    // "D&D\\",
    "Books\\"
];

$excludes = [
    "Backups+Temp",
    "Software+Utilities",
    "Dropbox"
];

# default to not show next and prev buttons
$show_prev_next = false;



//  /$$         /$$                   /$$
// | $$        | $$                  | $$
// | $$$$$$$  /$$$$$$   /$$$$$$/$$$$ | $$
// | $$__  $$|_  $$_/  | $$_  $$_  $$| $$
// | $$  \ $$  | $$    | $$ \ $$ \ $$| $$
// | $$  | $$  | $$ /$$| $$ | $$ | $$| $$
// | $$  | $$  |  $$$$/| $$ | $$ | $$| $$
// |__/  |__/   \___/  |__/ |__/ |__/|__/
//
//  /$$
// | $$
// | $$$$$$$   /$$$$$$   /$$$$$$$  /$$$$$$
// | $$__  $$ |____  $$ /$$_____/ /$$__  $$
// | $$  \ $$  /$$$$$$$|  $$$$$$ | $$$$$$$$
// | $$  | $$ /$$__  $$ \____  $$| $$_____/
// | $$$$$$$/|  $$$$$$$ /$$$$$$$/|  $$$$$$$
// |_______/  \_______/|_______/  \_______/

define('THEME_TOP' , <<<EOL

<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>%%TITLE%%</title>

  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link href="css/full-width-pics.css" rel="stylesheet">

  <!-- https://stackoverflow.com/questions/9789723/css-text-overflow-in-a-table-cell -->

  <style>

.text-overflow-dynamic-container {
    position: relative;
    max-width: 100%;
    padding: 0 !important;
    display: -webkit-flex;
    display: -moz-flex;
    display: flex;
    vertical-align: text-bottom !important;
}
.text-overflow-dynamic-ellipsis {
    position: absolute;
    white-space: nowrap;
    overflow-y: visible;
    overflow-x: hidden;
    text-overflow: ellipsis;
    -ms-text-overflow: ellipsis;
    -o-text-overflow: ellipsis;
    max-width: 100%;
    min-width: 0;
    width:100%;
    top: 0;
    left: 0;
}
.text-overflow-dynamic-container:after,
.text-overflow-dynamic-ellipsis:after {
    content: '-';
    display: inline;
    visibility: hidden;
    width: 0;
}

  </style>

</head>

<body>

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
      <a class="navbar-brand" href="/">Highwind's Stash</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="./?c=n">NEW</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./?c=l">Links</a>
          </li>
          <li class="nav-item active">
            <a class="nav-link" href="/">Home
              <span class="sr-only">(current)</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./?c=a">Audio Books</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./?c=b">Books</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./?c=e">Emulation</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./?c=v">Movies+TV</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./?c=m">Music</a>
          </li>

        </ul>
      </div>
    </div>
  </nav>


EOL
);

define('THEME_BOTTOM', <<<EOL

    </div>
  </section>

  <!-- Footer -->
  <footer class="py-5 bg-dark">
    <div class="container">
      <a href="https://github.com/jwd83/VCR"><p class="m-0 text-center text-white" style="
      -moz-transform: rotate(-180deg);
    -ms-transform: rotate(-180deg);
    -o-transform: rotate(-180deg);
    -webkit-transform: rotate(-180deg);
    transform: rotate(-180deg);

    ">Copyleft &copy;</p></a>
    </div>
    <!-- /.container -->
  </footer>

  <!-- Bootstrap core JavaScript -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>

</html>

EOL );

//   /$$                 /$$
//  | $$                | $$
//  | $$$$$$$   /$$$$$$ | $$  /$$$$$$   /$$$$$$   /$$$$$$
//  | $$__  $$ /$$__  $$| $$ /$$__  $$ /$$__  $$ /$$__  $$
//  | $$  \ $$| $$$$$$$$| $$| $$  \ $$| $$$$$$$$| $$  \__/
//  | $$  | $$| $$_____/| $$| $$  | $$| $$_____/| $$
//  | $$  | $$|  $$$$$$$| $$| $$$$$$$/|  $$$$$$$| $$
//  |__/  |__/ \_______/|__/| $$____/  \_______/|__/
//                          | $$
//                          | $$
//                          |__/
//   /$$$$$$                                 /$$     /$$
//  /$$__  $$                               | $$    |__/
// | $$  \__//$$   /$$ /$$$$$$$   /$$$$$$$ /$$$$$$   /$$  /$$$$$$  /$$$$$$$   /$$$$$$$
// | $$$$   | $$  | $$| $$__  $$ /$$_____/|_  $$_/  | $$ /$$__  $$| $$__  $$ /$$_____/
// | $$_/   | $$  | $$| $$  \ $$| $$        | $$    | $$| $$  \ $$| $$  \ $$|  $$$$$$
// | $$     | $$  | $$| $$  | $$| $$        | $$ /$$| $$| $$  | $$| $$  | $$ \____  $$
// | $$     |  $$$$$$/| $$  | $$|  $$$$$$$  |  $$$$/| $$|  $$$$$$/| $$  | $$ /$$$$$$$/
// |__/      \______/ |__/  |__/ \_______/   \___/  |__/ \______/ |__/  |__/|_______/

function new_extension($src, $new_ext) {
    $without_extension = substr($src, 0, strrpos($src, "."));
    return $without_extension . $new_ext;
}


function human_filesize($bytes, $decimals = 2) {
  $sz = 'BKMGTP';
  $factor = floor((strlen($bytes) - 1) / 3);
  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

function writeFileToDBQueue($src, $type, $res = '0') {
    global $db;

    // echo "WRITING TO DB QUEUE";
    # run query
    $stmt = $db->prepare('INSERT INTO encoder_queue (path, codec, resolution) VALUES (?,?,?)');
    $stmt->bind_param("sss", $src, $type, $res);
    $stmt->execute();
}

function writeFileToQueue($src) {
    if($GLOBALS['config']->use_db == 1) {
        writeFileToDBQueue($src, "h264");
    } else {
        file_put_contents(PATH_H264_QUEUE, $src . PHP_EOL, FILE_APPEND);
    }
}

function writeFileToHEVCQueue($src) {
    if($GLOBALS['config']->use_db == 1) {
        writeFileToDBQueue($src, "h265");
    } else {
        file_put_contents(PATH_H265_QUEUE, $src . PHP_EOL, FILE_APPEND);
    }
}

function writeFileToAACQueue($src) {
    if($GLOBALS['config']->use_db == 1) {
        writeFileToDBQueue($src, "aac");
    } else {
        file_put_contents(PATH_M4A_QUEUE, $src . PHP_EOL, FILE_APPEND);
    }
}

function writeFileToOpusQueue($src) {
    if($GLOBALS['config']->use_db == 1) {
        writeFileToDBQueue($src, "opus");
    } else {
        file_put_contents(PATH_OPUS_QUEUE, $src . PHP_EOL, FILE_APPEND);
    }
}

function reencodeVideo($src) {
    $src = FILESYSTEM_BASE . hexToStr($src);
    if(file_exists($src)) {
        $command = PATH_FFMPEG . " -i \"$src\" -codec copy \"$src.re.mp4\"";
        exec($command);
    }
}

function reencodeVideoHTML($src) {
    $src = FILESYSTEM_BASE . hexToStr($src);
    if(file_exists($src)) {
        writeFileToQueue($src);
    }
}

function reencodeVideoHEVC($src) {
    $src = FILESYSTEM_BASE . hexToStr($src);
    if(file_exists($src)) {
        writeFileToHEVCQueue($src);
    }
}

function reencodeAudioAAC($src) {
    $src = FILESYSTEM_BASE . hexToStr($src);
    if(file_exists($src)) {
        writeFileToAACQueue($src);
    }
}

function reencodeAudioOpus($src) {
    $src = FILESYSTEM_BASE . hexToStr($src);
    if(file_exists($src)) {
        writeFileToOpusQueue($src);
    }
}



function drawHeader($banner = false)
{
    global $backgrounds, $title;

    // global
    if(isset($_REQUEST['file'])) {
        $title = pathinfo(hexToStr($_REQUEST['file']))['basename'];
    }

    echo str_replace("%%TITLE%%", "$title", THEME_TOP) ;

    if ($banner !== false)
    {
        ?>
<!-- Header - set the background image for the header in the line below -->
<header class="py-5 bg-image-full" style="background-image: url('/img/<?= $backgrounds[array_rand ($backgrounds)]; ?>');">

<div class="container">
<h1 style="
    color:#fff;
    margin-bottom: 30px;
    margin-top: 30px;
    font-size: 5em;
    opacity: 0.7;
    -webkit-text-stroke: 3px black;
    font-style: italic;
    font-family: serif;
    font-weight: bolder;

    ">

    &ldquo;<?= $banner ?>&rdquo;
</h1>
</div>
</header>

        <?php
    }

    echo
    '
    <!-- Content section -->
    <section class="py-5">
    <div class="container">
    ';
}

function strToHex($string){
    $hex='';
    for ($i=0;
        $i < strlen($string); $i++){
        $hex .= dechex(ord($string[$i]));
    }
    return $hex;



}

function hexToStr($hex){
    $string='';
    for ($i=0; $i < strlen($hex)-1; $i+=2){
        $string .= chr(hexdec($hex[$i].$hex[$i+1]));
    }
    return $string;
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);

    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

function startsWith ($string, $startString)
{
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
}

function getDirContents($dir, &$results = array()) {
    $files = scandir($dir);

    foreach ($files as $key => $value)
    {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);

        if (!is_dir($path))
        {
            $results[] = $path;
        }
        else if ($value != "." && $value != "..")
        {
            getDirContents($path, $results);
            $results[] = $path;
        }
    }

    return $results;
}

function echoCell($str, $italic = FALSE) {
    if ($str == '-') {
        echo "\n<td style=\"text-align: center;\">\n";
    } else {
        echo "\n<td>\n";
    }

    if($italic) {
        echo "<em>$str</em>";
    } else {
        echo "$str";
    }

    echo "\n</td>\n";
}

function getDbButtons($base_path, $current_file) {
    global $extensions, $db;

    $path_to_find = substr(hexToStr($current_file), strlen($base_path) + 1);
    // echo $current_file . "<br>\n" . hexToStr($current_file) . "<br>\n$base_path<br>\n$path_to_find";

    $hit = 0;
    $hit_checks = 0;
    $prev_file = "";
    $button_array = array();

    $button_array['previous'] = "";
    $button_array['next'] = "";
    $button_array['previous_raw'] = "";
    $button_array['next_raw'] = "";

    # run query
    $stmt = $db->prepare('SELECT * FROM files WHERE parent = ? ORDER BY path');
    $stmt->bind_param("s", $base_path);
    $stmt->execute();
    $stmt->bind_result($r_id, $r_parent, $r_path, $r_size, $r_modified);

    # look for our match
    while($stmt->fetch()) {
        $path = $base_path . "/" . $r_path;
        if($hit == 1) {
            $button_array['previous'] = strToHex($prev_file);
            $button_array['next'] = strToHex($path);
            $button_array['previous_raw'] = $prev_file;
            $button_array['next_raw'] = str_replace('\\', '/', $path);
            return $button_array;
        } else {
            if($path_to_find == $r_path) {
                $hit = 1;
            } else {
                $prev_file = $path;
            }
        }
    }

    return $button_array;
}

function getButtons($base_path, $current_file) {
    global $extensions;

    if($GLOBALS['config']->use_db == 1) {
        return getDbButtons($base_path, $current_file);
    }

    $hit = 0;
    $hit_checks = 0;
    $prev_file = "";
    $button_array = array();

    $button_array['previous'] = "";
    $button_array['next'] = "";
    $button_array['previous_raw'] = "";
    $button_array['next_raw'] = "";

    // $files = getDirContents($base_path, $junk , true);  // using a named argument
    $files = getDirContents($base_path);

    // filter valid file extensions
    foreach($files as $key => $path) {
        $valid_extension = 0;
        foreach($extensions as $ext) {
            if (endsWith(strtolower($path), strtolower($ext))) {
                $valid_extension = 1;
            }
        }
        if($valid_extension == 0) {
            unset($files[$key]);
        }
    }

    // generate prev/next button data
    foreach($files as $file) {
        if(!is_dir($file)) {
            $path = substr(trim($file), 3);
            if($hit == 1) {
                $button_array['previous'] = strToHex($prev_file);
                $button_array['next'] = strToHex($path);
                $button_array['previous_raw'] = $prev_file;
                $button_array['next_raw'] = str_replace('\\', '/', $path);
                return $button_array;
            }
            else
            {
                if(strToHex($path) == $current_file) {
                    $hit = 1;
                } else {
                    $prev_file = $path;
                }
            }
        }

    }

    return $button_array;
}

function drawResultNotes($base_path) {

    echo "<h1>$base_path Results</h1>\n";

    if($_REQUEST['c'] == 'v') {
        echo "
<hr>
<h3>Notes</h3>
[direct] direct link to the video file. download or copy link address.<br>
[watch] place file in an html5 video tag player. your mileage may vary.<br>
[r] replace container with mp4. lossless but may not play in html5 video player.<br>
[h264] reencode using h264 &amp; AAC audio. This is an html5 &lt;video&gt; safe format. new files end in .h264.mp4<br>
[h265] reencode using h265 &amp; original audio in a matroska MKV container. new files end in .h265.mkv<br>
<hr>
";
    }

    if($_REQUEST['c'] == 'm') {
        echo "
<hr>
<h3>Notes</h3>
[direct] direct link to the video file. download or copy link address.<br>
[listen] place file in an html5 audio tag player. your mileage may vary.<br>
[opus] lossy reencode file as .opus<br>
<hr>
";
    }

}

function dbShowEverything($stmt) {

    $stmt->execute();

    $stmt->bind_result($r_id, $r_parent, $r_path, $r_size, $r_modified);

    // todo better content type detection based on category flags to be added to config.json

    echo '<hr><table>';
    while ($stmt->fetch()) {
        $path = $r_parent . "\\" . $r_path;

        echo '<tr>';
        echoCell('<a href="'.$path.'">[direct]</a>');

        switch($r_parent) {
            case 'Movies+TV':
                // if the file was previously transcoded by this tool don't re-encode it again
                // to prevent generation loss
                if(endsWith($path, ".mp4") || endsWith($path, ".webm")) {
                    echoCell('<a href="?c=v&file='.strToHex($path).'">[watch]</a>');
                } else {
                    echocell('-');
                }

                if(
                    endsWith($path, "h264.mp4") ||
                    endsWith($path, "h265.mkv") ||
                    endsWith($path, "vp9.webm") ||
                    endsWith($path, "av1.webm")
                ) {
                    echocell('-');
                } else {
                    echoCell('<a href="?c=t&file='.strToHex($path).'">[transcode]</a>');
                }
                break;
            case 'Music':
                echoCell('<a href="?c=m&file='.strToHex($path).'">[listen]</a>');
                echoCell('<a href="?c=o&file='.strToHex($path).'">[opus]</a>');
                break;
            case 'Emulation + ROMs':
                echocell('-');
                echocell('-');
                break;
            case 'Books':
                echocell('-');
                echocell('-');
                break;
            case 'Audio Books':
                echoCell('<a href="?c=a&file='.strToHex($path).'">[listen]</a>');
                echocell('-');
                break;
            default:
                echocell('-');
                echocell('-');
                break;

        }
        echoCell(human_filesize($r_size), TRUE);
        echoCell($path);
        echo '<tr>';
    }
    echo '</table>';
}

function dbResultsEverything($query) {
    global $db;

    $search_str = "%".$query."%";

    $stmt = $db->prepare('SELECT * FROM files WHERE path like ? ORDER BY parent DESC, path');
    $stmt->bind_param("s",  $search_str);

    dbShowEverything($stmt);
}

function dbResults($base_path, $optional_feature = "none", $optional_reference = "none", $query = "none") {
    global $extensions, $white_list, $excludes, $db;

    drawResultNotes($base_path);

    $stmt = NULL;
    $search_str = "%".$query."%";

    if($query != 'none') {
        $stmt = $db->prepare('SELECT * FROM files WHERE parent = ? AND path like ? ORDER BY path');
        $stmt->bind_param("ss", $base_path, $search_str);
    } else {
        $stmt = $db->prepare('SELECT * FROM files WHERE parent = ? ORDER BY path');
        $stmt->bind_param("s", $base_path);
    }

    $stmt->execute();

    echo "<table>\n";
    $stmt->bind_result($r_id, $r_parent, $r_path, $r_size, $r_modified);
    while ($stmt->fetch()) {
        $path = $base_path . "/" . $r_path;
        echo "<tr>\n";
        echoCell('<a href="'.$path.'">[direct]</a>');
        if($optional_feature != 'none') {
            if($optional_reference == 'v') {
                if(endsWith($path, "h265.mkv")) {
                    echocell('-');
                } else {
                    echoCell('<a href="?c='.$optional_reference.'&file='.strToHex($path).'">['.$optional_feature.']</a>');
                }
            } else {
                echoCell('<a href="?c='.$optional_reference.'&file='.strToHex($path).'">['.$optional_feature.']</a>');
            }
        }
        if($optional_reference == 'v') {
            if(
                endsWith($path, "h264.mp4") ||
                endsWith($path, "h265.mkv") ||
                endsWith($path, "vp9.webm") ||
                endsWith($path, "av1.webm")
            ) {
                echocell('-');
            } else {
                echoCell('<a href="?c=t&file='.strToHex($path).'">[transcode]</a>');
            }
        }
        if($optional_reference == 'm') {
            if(endsWith($path, ".m4a") || endsWith($path, ".aac") || endsWith($path, ".opus")) {
                echocell('-');
                // echocell('-');
            } else {
                echoCell('<a href="?c=o&file='.strToHex($path).'">[opus]</a>');
                // echoCell('<a href="?c=4&file='.strToHex($path).'">[aac]</a>');
            }
        }
        echoCell(human_filesize($r_size), TRUE);
        // echoCell(substr($path,  strlen($base_path)-2));
        echoCell($r_path);
        echo "</tr>\n";
    }
    $stmt->close();

    echo "</table>\n";
}

function dumpPath($base_path, $optional_feature = "none", $optional_reference = "none", $query = "none") {
    global $extensions, $white_list, $excludes;

    drawResultNotes($base_path);

    $base_path = dirname(__FILE__) . $base_path;

    # generate list of files into this array
    $files = getDirContents($base_path);

    # setup variables to filter our list for our static html cache page

    $matched = 0;
    $excluded = 0;
    $skipped = 0;
    $total = 0;

    foreach($files as $file)
    {
        $match = '';
        $total++;
        $valid = 0;

        if(!is_dir($file)) {

            $path = substr(trim($file), 3);

            // check for a valid file extension
            foreach($extensions as $ext)
            {
                if (endsWith(strtolower($path), strtolower($ext)))
                {
                    $valid = 1;
                }
            }

            // check if the start of a path matches the whitelist
            foreach($white_list as $wl)
            {
                if (startsWith(strtolower($path), strtolower($wl)))
                {
                    $valid = 1;
                }
            }

            // check against query
            if($query != 'none' && $valid == 1) {
                $valid = 0;
                if (strpos(strtolower($path), strtolower($query)) !== false) {
                    $valid = 1;
                }
            }

            // check that it's not in an excluded path
            foreach($excludes as $exclude)
            {
                if (strpos(strtolower($path), strtolower($exclude)) !== false) {
                    $excluded++;
                    if($valid == 1) {
                        $matched--;
                        $skipped++;

                    }
                    $valid = 0;
                }
            }
        }

        if($valid === 1)
        {
            $matched++;

            echo "<tr>\n";
            echoCell('<a href="'.$path.'">[direct]</a>');
            if($optional_feature != 'none') {
                if($optional_reference == 'v') {
                    if(endsWith($path, "h265.mkv")) {
                        echocell('-');
                    } else {
                        echoCell('<a href="?c='.$optional_reference.'&file='.strToHex($path).'">['.$optional_feature.']</a>');
                    }
                } else {
                    echoCell('<a href="?c='.$optional_reference.'&file='.strToHex($path).'">['.$optional_feature.']</a>');
                }
            }
            if($optional_reference == 'v') {
                if(endsWith($path, "h264.mp4") || endsWith($path, "h265.mkv")) {
                    echocell('-');
                    echocell('-');
                    echocell('-');
                } else {
                    echoCell('<a href="?c=r&file='.strToHex($path).'">[r]</a>');
                    echoCell('<a href="?c=5&file='.strToHex($path).'">[h264]</a>');
                    echoCell('<a href="?c=n&file='.strToHex($path).'">[h265]</a>');
                }
            }
            if($optional_reference == 'm') {
                if(endsWith($path, ".m4a") || endsWith($path, ".aac") || endsWith($path, ".opus")) {
                    echocell('-');
                    // echocell('-');
                } else {
                    echoCell('<a href="?c=o&file='.strToHex($path).'">[opus]</a>');
                    // echoCell('<a href="?c=4&file='.strToHex($path).'">[aac]</a>');
                }
            }
            echoCell(human_filesize(filesize($path)));
            echoCell(substr($path,  strlen($base_path)-2));
            echo "</tr>\n";
        }
    }
    echo "</table><hr><div>$matched matched of $total files searched, $skipped of $excluded excluded</div>";
}

function rutime($ru, $rus, $index) {
    return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
     -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
}

function drawFooter() {
    global $start, $rustart;

    echo "<hr>";
    $ru = getrusage();
    $time_elapsed_secs = microtime(true) - $start;

    echo "This process used " . rutime($ru, $rustart, "utime") . " ms for its computations.<br>\n";
    echo "It spent " . rutime($ru, $rustart, "stime") . " ms in system calls.<br>\n";
    echo '<b>Total Execution Time:</b> '.number_format($time_elapsed_secs,4).' seconds';

    echo THEME_BOTTOM;
}

function buildPlaybackLink($file_in_hex, $type = "") {

    # determine type
    if(strlen($type) == 0) {
        if(isset($_REQUEST['c'])) {
            $type = $_REQUEST['c'];
        }

    }

    # filename must already be in hex format
    return "/?c=" . $type . "&file=".$file_in_hex;
}


function getSuggestions($category) {
    global $config;

    $sugs = '';

    foreach($config->categories as $cat) {
        if($cat->type == $category) {
            foreach($cat->searches as $q)
            $sugs .= '<a href="/?c='.$category.'&q='.$q.'">'.$q.'</a>, ';
        }
    }

    return $sugs;
}

function pageIndex() {
    drawHeader("Stay Awhile and Listen");
    echo '

<h2>Please enjoy your stay and DM requests</h2>
<hr>
<h2>See <a href="/?c=n">what\'s new</a></h2>
Search:
<form method="GET">
<input type="text" name="q">
<select name="c">
  <option value="z" selected>Everything</option>
  <option value="a">Audio Books</option>
  <option value="b">Books</option>
  <option value="e">Emulation</option>
  <option value="v">Movies+TV</option>
  <option value="m">Music</option>
</select>
<input type="submit" value="Search">
</form>
<h2 style="margin-top: 1em;">Categories &amp; Popular Searches</h2>

<div class="container">

  <div class="row">
    <div class="col-sm">
      <h4 style="margin-top: 1em;"><a href="?c=m">Music</a></h4>
      '.getSuggestions("m").'
    </div>
    <div class="col-sm">
      <h4 style="margin-top: 1em;"><a href="?c=v">Movies+TV</a></h4>
      '.getSuggestions("v").'
    </div>
  </div>

  <div class="row">
    <div class="col-sm">
      <h4 style="margin-top: 1em;"><a href="?c=a">Audio Books</a></h4>
      '.getSuggestions("a").'
    </div>
    <div class="col-sm">
      <h4 style="margin-top: 1em;"><a href="?c=b">Books</a></h4>
      '.getSuggestions("b").'
    </div>
  </div>


  <div class="row">
    <div class="col-sm">
      <h4 style="margin-top: 1em;"><a href="?c=e">Emulation</a></h4>
      '.getSuggestions("e").'
    </div>
    <div class="col-sm">
      &nbsp;

    </div>
  </div>

</div>

';


    drawFooter();
}

function pageM4AAudioQueue() {
    global $search_type, $dump_path, $feature, $show_prev_next;

    $original_file = "";
    $new_file = "";
    if(isset($_REQUEST['file'])) {

        $original_file = hexToStr($_REQUEST['file']);
        reencodeAudioAAC($_REQUEST['file']);
        $_REQUEST['file'] =  strToHex(new_extension($original_file, ".aac"));
        $new_file = hexToStr($_REQUEST['file']);
        $watch_url = buildPlaybackLink($_REQUEST['file'], "m");
    }
    $search_type = 'm';
    $dump_path = 'Music';
    $feature = 'listen';

    drawHeader($dump_path);

    echo "Scheduling conversion of<br><br>$original_file<br><br>to<br><br><a href=\"$watch_url\">$new_file</a>";

    drawSearchBox();
    drawFooter();

}


function pageOpusAudioQueue() {
    global $search_type, $dump_path, $feature, $show_prev_next;

    $original_file = "";
    $new_file = "";
    if(isset($_REQUEST['file'])) {

        $original_file = hexToStr($_REQUEST['file']);
        reencodeAudioOpus($_REQUEST['file']);
        $_REQUEST['file'] =  strToHex(new_extension($original_file, ".opus"));
        $new_file = hexToStr($_REQUEST['file']);
        $watch_url = buildPlaybackLink($_REQUEST['file'], "m");
    }
    $search_type = 'm';
    $dump_path = 'Music';
    $feature = 'listen';

    drawHeader($dump_path);

    echo "Scheduling conversion of<br><br>$original_file<br><br>to<br><br><a href=\"$watch_url\">$new_file</a>";

    drawSearchBox();
    drawFooter();

}

function pageVideoTranscodeSetup()
{
    drawHeader("Transcode");

?>
<h2>Transcode Options</h2>
Configure transcoding options for...<br><br> <?=  hexToStr($_REQUEST['file']) ?>
<hr>
<form method="POST" action="/?c=u">
<input type="hidden" name="file" value="<?= $_REQUEST['file'] ?>">
<h3>Encoder</h3>
<input type="radio" name="codec" value="h264" checked> h264 - <em>recommended</em>, widely supported, fast to encode, html5 video compatible<br>
<input type="radio" name="codec" value="h265"> h265 - slow to encode, smaller filesizes, most noticable at higher resolution<br>
<input type="radio" name="codec" value="vp9"> vp9 - very slow, runs in 2 pass mode, generates .webm, reasonly html5 video compatible<br>
<input type="radio" name="codec" value="av1"> av1 - slow <b>AF</b>, "state of the art". convert about a minute of 1080p video per hour, generates .webm, somewhat html5 video compatible<br>
<h3>Vertical Resolution</h3>
<input type="radio" name="resolution" value="0" checked> original - ignore<br>
<input type="radio" name="resolution" value="2160"> 2160 - aka 4K, Ultra HD or UHD<br>
<input type="radio" name="resolution" value="1440"> 1440 - aka 2K, Quad HD or QHD<br>
<input type="radio" name="resolution" value="1080"> 1080 - aka FHD or Full HD<br>
<input type="radio" name="resolution" value="720"> 720 - aka HD<br>
<input type="radio" name="resolution" value="480"> 480 - aka DVD<br>
<input type="radio" name="resolution" value="360"> 360 - aka SD<br>
<input type="radio" name="resolution" value="240"> 240 - yikes...<br>
<br>
<input type="submit">
</form>
<?php
    drawFooter();
}

function pageVideoTranscodeQueue() {
    drawHeader("Transcode");
?>
Scheduling transcode of...<br><br> <?=  hexToStr($_REQUEST['file']) ?>

<br>
<br>
Check back soon!


<?php

    writeFileToDBQueue(hexToStr($_REQUEST['file']), $_REQUEST['codec'], $_REQUEST['resolution']);
    // var_dump($_REQUEST);
    drawFooter();
}

function pageH265VideoQueue() {
    global $search_type, $dump_path, $feature, $show_prev_next;

    $original_file = "";
    $new_file = "";


    if(isset($_REQUEST['file'])) {

        $original_file = hexToStr($_REQUEST['file']);
        reencodeVideoHEVC($_REQUEST['file']);
        $_REQUEST['file'] =  strToHex(new_extension($original_file, ".h265.mkv"));
        $new_file = hexToStr($_REQUEST['file']);
        $watch_url = buildPlaybackLink($_REQUEST['file'], "v");
    }
    $search_type = 'v';
    $dump_path = 'Movies+TV';
    $feature = 'watch';


    drawHeader($dump_path);

    echo "Scheduling conversion of<br><br>$original_file<br><br>to<br><br><a href=\"$watch_url\">$new_file</a>";

    drawSearchBox();
    drawFooter();

}

function pageH264VideoQueue() {
    global $search_type, $dump_path, $feature, $show_prev_next;

    $original_file = "";
    $new_file = "";


    if(isset($_REQUEST['file'])) {

        $original_file = hexToStr($_REQUEST['file']);
        reencodeVideoHTML($_REQUEST['file']);
        $_REQUEST['file'] =  strToHex(new_extension($original_file, ".h264.mp4"));
        $new_file = hexToStr($_REQUEST['file']);
        $watch_url = buildPlaybackLink($_REQUEST['file'], "v");
    }
    $search_type = 'v';
    $dump_path = 'Movies+TV';
    $feature = 'watch';


    drawHeader($dump_path);

    echo "Scheduling conversion of<br><br>$original_file<br><br>to<br><br><a href=\"$watch_url\">$new_file</a>";

    drawSearchBox();
    drawFooter();

}

function pageWhatsNew() {
    global $db;
    $stmt = $db->prepare('SELECT * FROM files ORDER BY modified DESC LIMIT 250');

    drawHeader("What's New");
    echo '<h1>The 250 newest additions...</h1>';
    drawSearchEverythingBox();
    dbShowEverything($stmt);
    drawFooter();
}

function pageLinks() {
    global $config;
    drawHeader("Links");

    echo "\n<ul>\n";
    foreach ($config->links as $link) {
        echo '
<li>
<a href="'.$link->url.'"><b>'.$link->title.'</b></a><ul><li>'.$link->description.'</li></ul>
</li>
';

    }
    echo "\n</ul>\n";
    drawFooter();
}

function pageContainerSwap() {
    global $search_type, $dump_path, $feature, $show_prev_next;

    $search_type = 'v';
    $dump_path = 'Movies+TV';
    $feature = 'watch';


    drawHeader($dump_path);

    if(isset($_REQUEST['file'])) {
        reencodeVideo($_REQUEST['file']);
        $_REQUEST['file'] .= strToHex(".re.mp4");
    }
    drawVideoPlayer();
    drawSearchBox();
    drawFooter();

}

function pageVideoPlayer() {

    global $search_type, $dump_path, $feature, $show_prev_next;

    $search_type = 'v';
    $dump_path = 'Movies+TV';
    $feature = 'watch';

    drawHeader($dump_path);

    if(isset($_REQUEST['file'])) {
        $show_prev_next = true;
        solveButtons();
        drawButtons();
        drawVideoPlayer();
        addAutoplayJquery();

    }
    drawSearchBox();
    drawSearchResults();
    drawFooter();
}

function pageSearchAll() {
    drawHeader('Everything');
    drawSearchEverythingBox();
    drawSearchEverythingResults();
    drawFooter();
}

function pageAudioBook() {
    global $search_type, $dump_path, $feature, $show_prev_next;

    $search_type = 'a';
    $dump_path = 'Audio Books';
    $feature = 'listen';
    $show_prev_next = true;

    drawHeader($dump_path);

    if(isset($_REQUEST['file'])) {
        $show_prev_next = true;
        solveButtons();
        drawButtons();
        drawAudioPlayer();
        addAutoplayJquery();
        addPrecacheJquery();

    }
    drawSearchBox();
    drawSearchResults();
    drawFooter();
}

function pageMusicPlayer() {
    global $search_type, $dump_path, $feature, $show_prev_next;

    $search_type = 'm';
    $dump_path = 'Music';
    $feature = 'listen';
    $show_prev_next = true;

    drawHeader($dump_path);

    if(isset($_REQUEST['file'])) {
        $show_prev_next = true;
        solveButtons();
        drawButtons();
        drawAudioPlayer();
        addAutoplayJquery();
        addPrecacheJquery();
    }
    drawSearchBox();
    drawSearchResults();
    drawFooter();
}

function pageBooks(){
    global $search_type, $dump_path, $feature, $show_prev_next;

    $search_type = 'b';
    $dump_path = 'Books';


    drawHeader($dump_path);
    drawSearchBox();
    drawSearchResults();
    drawFooter();
}

function pageEmulation(){
    global $search_type, $dump_path, $feature, $show_prev_next;

    $search_type = 'e';
    $dump_path = 'Emulation + ROMs';

    drawHeader($dump_path);
    drawSearchBox();
    drawSearchResults();
    drawFooter();

}

function solveButtons() {
    global $buttons, $next_file_to_play, $dump_path;

    if(isset($_REQUEST['file'])) {
        $buttons = getButtons($dump_path, $_REQUEST['file']);
        $next_file_to_play = $buttons['next'];
    }
}

function addAutoplayJquery() {
    global $jquery_code_to_run, $buttons;
    $jquery_code_to_run .= "
// autoplay binding
$(\"#player\").bind(
    'ended',
    function() {
         window.location.href = \"".buildPlaybackLink($buttons['next'])."\";
     }
);

";
}


function addPrecacheJquery() {
    global $jquery_code_to_run, $buttons;

    $jquery_code_to_run .= '

setTimeout(
    function()
    {
        $("body").append(
            \'<audio src="/'. addslashes($buttons['next_raw'])  . '" preload="auto">\'
        );
    },
    10000
);
';
}

function drawButtons() {
    global $show_prev_next,  $buttons;

    if($show_prev_next) {
        echo '

<h3>
    <a href="'.buildPlaybackLink($buttons['previous']).'">⏮️Previous</a> |
    <a href="'.buildPlaybackLink($buttons['next']).'">Next⏭</a>
</h3>

';
    }
}

function drawAudioPlayer() {
    global $src, $title;

    echo '
<h2>Playing '.$title.'</h2>
<em>'.$src.'</em><br>
<br>
<audio autoplay controls id="player" class="player" preload="auto">
<source src="'.$src.'">
Your browser does not support the audio element.
</audio>
<br>
<br>
For best results view in Chrome. If your browser is unable to play this file you may need to download the file or copy this direct link into VLC:<br>
<br>
<a href="'.$src.'">[direct link]</a><br>

<!-- Preload the next song -->
';


}

function drawVideoPlayer() {
    global $src, $title;

    $t = 0;

    if(isset($_REQUEST['t'])) $t = (int) $_REQUEST['t'];

    echo '
<h2>Playing '.$title.'</h2>
For best results view in Chrome. If your browser is unable to play this file you may need to download the file or copy this direct link into VLC:<br>
<br>
<a href="'.$src.'">[direct link]</a><br>
<br>
<br>
<video controls width=800 autoplay id="player" class="player">
<source src="'.$src.'#t='.$t.'">
Your browser does not support the video element.
</video>
';
}

function drawSearchEverythingBox($default = 'z') {
    $selected['a'] = ($default == 'a' ? " selected" : "");
    $selected['b'] = ($default == 'b' ? " selected" : "");
    $selected['e'] = ($default == 'e' ? " selected" : "");
    $selected['m'] = ($default == 'm' ? " selected" : "");
    $selected['v'] = ($default == 'v' ? " selected" : "");
    $selected['z'] = ($default == 'z' ? " selected" : "");

    echo '
<hr>
<h2>Search</h2>
<form method="GET">
<input type="text" name="q" autofocus>
<select name="c">
  <option value="z"'.$selected['z'].'>Everything</option>
  <option value="a"'.$selected['a'].'>Audio Books</option>
  <option value="b"'.$selected['b'].'>Books</option>
  <option value="e"'.$selected['e'].'>Emulation</option>
  <option value="v"'.$selected['v'].'>Movies+TV</option>
  <option value="m"'.$selected['m'].'>Music</option>
</select>
<input type="submit" value="Search">
</form>
';

    if($default != 'z') {
        echo '<a href="./?c='.$default.'&all=1">[All Files]</a>';
    }
}

function drawSearchBox() {

    global $search_type;

    drawSearchEverythingBox($search_type);

}

function drawSearchEverythingResults() {
    if(isset($_REQUEST['q'])) {
        dbResultsEverything($_REQUEST['q']);
    }
}

function drawSearchResults() {
    global $dump_path, $feature, $search_type;
    # if there is a valid dump path list our files
    if(strlen($dump_path) > 1) {
        if($GLOBALS['config']->use_db == 1) {
            if(isset($_REQUEST['q'])) {
                dbResults($dump_path, $feature, $search_type, $_REQUEST['q']);

            }

            if(isset($_REQUEST['all'])) {
                dbResults($dump_path, $feature, $search_type);
            }

        } else {
            if(isset($_REQUEST['q'])) {
                dumpPath($dump_path, $feature, $search_type, $_REQUEST['q']);

            }

            if(isset($_REQUEST['all'])) {
                dumpPath($dump_path, $feature, $search_type);
            }
        }
    }
}



//                          /$$
//                         |__/
//  /$$$$$$/$$$$   /$$$$$$  /$$ /$$$$$$$
// | $$_  $$_  $$ |____  $$| $$| $$__  $$
// | $$ \ $$ \ $$  /$$$$$$$| $$| $$  \ $$
// | $$ | $$ | $$ /$$__  $$| $$| $$  | $$
// | $$ | $$ | $$|  $$$$$$$| $$| $$  | $$
// |__/ |__/ |__/ \_______/|__/|__/  |__/
//
//
//    /$$$$$$   /$$$$$$   /$$$$$$   /$$$$$$
//   /$$__  $$ |____  $$ /$$__  $$ /$$__  $$
//  | $$  \ $$  /$$$$$$$| $$  \ $$| $$$$$$$$
//  | $$  | $$ /$$__  $$| $$  | $$| $$_____/
//  | $$$$$$$/|  $$$$$$$|  $$$$$$$|  $$$$$$$
//  | $$____/  \_______/ \____  $$ \_______/
//  | $$                 /$$  \ $$
//  | $$                |  $$$$$$/
//  |__/                 \______/
//
//   /$$                           /$$
//  | $$                          |__/
//  | $$$$$$$   /$$$$$$   /$$$$$$  /$$ /$$$$$$$   /$$$$$$$
//  | $$__  $$ /$$__  $$ /$$__  $$| $$| $$__  $$ /$$_____/
//  | $$  \ $$| $$$$$$$$| $$  \ $$| $$| $$  \ $$|  $$$$$$
//  | $$  | $$| $$_____/| $$  | $$| $$| $$  | $$ \____  $$
//  | $$$$$$$/|  $$$$$$$|  $$$$$$$| $$| $$  | $$ /$$$$$$$/
//  |_______/  \_______/ \____  $$|__/|__/  |__/|_______/
//                       /$$  \ $$
//                      |  $$$$$$/
//                       \______/

# our page will begin rendering here.
# see if we are on the homepage otherwise
# generate a page based on the [c] category

if(!isset($_REQUEST['c'])) {
    pageIndex();
} else {
    # config switch

    if(isset($_REQUEST['file'])) {
        $src = '/' . str_replace('\\', '/', hexToStr($_REQUEST['file'])); ;
    }

    switch($_REQUEST['c']){
        case '4': pageM4AAudioQueue();          break;
        case '5': pageH264VideoQueue();         break;
        case 'a': pageAudioBook();              break;
        case 'b': pageBooks();                  break;
        case 'e': pageEmulation();              break;
        case 'l': pageLinks();                  break;
        case 'm': pageMusicPlayer();            break;
        case 'n': pageWhatsNew();               break;
        case 'n': pageH265VideoQueue();         break;
        case 'o': pageOpusAudioQueue();         break;
        case 'r': pageContainerSwap();          break;
        case 't': pageVideoTranscodeSetup();    break;
        case 'u': pageVideoTranscodeQueue();    break;
        case 'v': pageVideoPlayer();            break;
        case 'z': pageSearchAll();              break;
    }
}

//
//
//         /$$  /$$$$$$  /$$   /$$  /$$$$$$   /$$$$$$  /$$   /$$
//        |__/ /$$__  $$| $$  | $$ /$$__  $$ /$$__  $$| $$  | $$
//         /$$| $$  \ $$| $$  | $$| $$$$$$$$| $$  \__/| $$  | $$
//        | $$| $$  | $$| $$  | $$| $$_____/| $$      | $$  | $$
//        | $$|  $$$$$$$|  $$$$$$/|  $$$$$$$| $$      |  $$$$$$$
//        | $$ \____  $$ \______/  \_______/|__/       \____  $$
//   /$$  | $$      | $$                               /$$  | $$
//  |  $$$$$$/      | $$                              |  $$$$$$/
//   \______/       |__/                               \______/
//
//   /$$                           /$$
//  | $$                          |__/
//  | $$$$$$$   /$$$$$$   /$$$$$$  /$$ /$$$$$$$   /$$$$$$$
//  | $$__  $$ /$$__  $$ /$$__  $$| $$| $$__  $$ /$$_____/
//  | $$  \ $$| $$$$$$$$| $$  \ $$| $$| $$  \ $$|  $$$$$$
//  | $$  | $$| $$_____/| $$  | $$| $$| $$  | $$ \____  $$
//  | $$$$$$$/|  $$$$$$$|  $$$$$$$| $$| $$  | $$ /$$$$$$$/
//  |_______/  \_______/ \____  $$|__/|__/  |__/|_______/
//                       /$$  \ $$
//                      |  $$$$$$/
//                       \______/
?>

<!-- Our jquery block -->
<script>
$( document ).ready
(
    function()
    {
// --------------------------------------------------------------------
// Begin $jquery_code_to_run
// --------------------------------------------------------------------
<?= $jquery_code_to_run ?>
// --------------------------------------------------------------------
// End $jquery_code_to_run
// --------------------------------------------------------------------
    }
);
</script>
