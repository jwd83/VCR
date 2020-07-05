<?php
// 
// shout outs to all my homies in VA
// 
// ascii art from patorjk.com
// 
// http://patorjk.com/software/taag/#p=display&c=c%2B%2B&f=Big%20Money-ne&t=begins

//                        /$$     /$$     /$$                              
//                       | $$    | $$    |__/                              
//   /$$$$$$$  /$$$$$$  /$$$$$$ /$$$$$$   /$$ /$$$$$$$   /$$$$$$   /$$$$$$$
//  /$$_____/ /$$__  $$|_  $$_/|_  $$_/  | $$| $$__  $$ /$$__  $$ /$$_____/
// |  $$$$$$ | $$$$$$$$  | $$    | $$    | $$| $$  \ $$| $$  \ $$|  $$$$$$ 
//  \____  $$| $$_____/  | $$ /$$| $$ /$$| $$| $$  | $$| $$  | $$ \____  $$
//  /$$$$$$$/|  $$$$$$$  |  $$$$/|  $$$$/| $$| $$  | $$|  $$$$$$$ /$$$$$$$/
// |_______/  \_______/   \___/   \___/  |__/|__/  |__/ \____  $$|_______/ 
//                                                      /$$  \ $$          
//                                                     |  $$$$$$/          
//                                                      \______/           

define("URL_BASE", "/gd/");


# track runtime
$start = microtime(true);
$rustart = getrusage();


$suggestions["a"] = ["plato", "sagan", "darwin", "martin", "tyson", "pinker", "dawkins", "harris", "tzu", "Dalai"];
$suggestions["m"] = ["daft", "radiohead","floyd", "eilish", "chili" ];
$suggestions["v"] = ["shell", "robot","Are You Afraid Of The Dark" , "Attack on Titan", "BATMAN", "marvel", "avenger", "outlander"];

$search_type = '';
$dump_path = '';
$feature = "none";

$jquery_code_to_run = "";
$buttons = array();
$next_file_to_play = "";


// $path_list_html = "list.html";
$extensions = [
    ".avi", 
    ".flac",
    ".m4b", // uncommon audio format
    ".mkv", 
    ".mp3", 
    ".mp4", 
    ".m4v", 
    ".ogg", 
    ".iso", 
    ".smc",
    ".wma",

    ".whitelistme"
];
$white_list = [
    "Emulation + ROMs\\" ,
    "D&D\\", 
    "Books\\"
];
$excludes = ["Backups+Temp", "Software+Utilities", "Dropbox"];



//  /$$         /$$                   /$$
// | $$        | $$                  | $$
// | $$$$$$$  /$$$$$$   /$$$$$$/$$$$ | $$
// | $$__  $$|_  $$_/  | $$_  $$_  $$| $$
// | $$  \ $$  | $$    | $$ \ $$ \ $$| $$
// | $$  | $$  | $$ /$$| $$ | $$ | $$| $$
// | $$  | $$  |  $$$$/| $$ | $$ | $$| $$
// |__/  |__/   \___/  |__/ |__/ |__/|__/
                                      
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

  <title>Highwind's Stash</title>

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
      <a class="navbar-brand" href="/gd/">Highwind's Stash</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item active">
            <a class="nav-link" href="/gd/">Home
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
            <a class="nav-link" href="./?c=m">Music</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./?c=v">Movies+TV</a>
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

# settings

function drawHeader($banner = false) 
{
    // global 
    echo THEME_TOP;
    if ($banner !== false) 
    {
        ?>
<!-- Header - set the background image for the header in the line below -->
<header class="py-5 bg-image-full" style="background-image: url('/gd/black-pearl.jpg');">



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
<!-- <img class="img-fluid d-block mx-auto" src="/gd/pirate-symbol.jpg" alt="" style="opacity: 0"> -->
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

function echoCell($str) {
//     echo '

// <td>
//   <span class="text-overflow-dynamic-container">
//     <span class="text-overflow-dynamic-ellipsis" title="'.htmlentities($str).'">
//       '.$str.'
//     </span>
//   </span>

// </td>
// ';   
 echo '

<td>
      '.$str.'

</td>
';
}

function getButtons($base_path, $current_file) {

    $hit = 0;
    $hit_checks = 0;
    $prev_file = "";
    $button_array = array();

    $button_array['previous'] = "";
    $button_array['next'] = "";

    $files = getDirContents($base_path);

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


function dumpPath($base_path, $optional_feature = "none", $optional_reference = "none", $query = "none") {
    global $extensions, $white_list, $excludes;

    echo "<h1>$base_path Results</h1>\n<table>\n";

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

            // if there is a query set forget everything we just did and check against the query
            if($query != 'none') {
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
                echoCell('<a href="?c='.$optional_reference.'&file='.strToHex($path).'">['.$optional_feature.']</a>');
            }
            echoCell(substr($path,  strlen($base_path)-2));
            echo "</tr>\n";
        }
    }
    echo "</table><hr><div>$matched matched of $total files searched, $skipped of $excluded excluded</div>";
}

function refreshList() {

    global $extensions, $white_list, $excludes;

    if(isset($_REQUEST["refresh"])) 
    {
        # generate list of files into this array
        $files = getDirContents(dirname(__FILE__));
        

        # write this array to list.txt
        file_put_contents('list.txt', implode(PHP_EOL, $files));


        # setup variables to filter our list for our static html cache page

        $matched = 0;
        $excluded = 0;
        $skipped = 0;
        $total = 0;

        # create a static html cache
        $my_file = fopen($path_list_html, "w");



        foreach($files as $file) 
        {
            $total++;
            $valid = 0;

            $path = substr(trim($file), 3);

            // check for a valid file extension

            foreach($extensions as $ext) 
            {
                if (endsWith(strtolower($path), strtolower($ext))) 
                {
                    $matched++;
                    $valid = 1;
                }
            }

            // check if it's in a whitelisted path


            foreach($white_list as $wl) 
            {
                if (strpos(strtolower($path), strtolower($wl)) !== false) {
                    $matched++;
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


            if($valid === 1) 
            {
                fwrite($my_file, '
    <div>
        <a href="'.$path.'">[d]</a>
        <a href="v.php?file='.$path.'">[v]</a>
        <a href="a.php?file='.$path.'">[a]</a>
        <a href="'.$path.'">'.$path.'</a>
    </div>
    ');
            }
        }

        fwrite($my_file, "<hr><div>$matched matched of $total files searched, $skipped of $excluded excluded</div>");

        fclose($my_file);
    }
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
    echo '<b>Total Execution Time:</b> '.$time_elapsed_secs.' seconds';

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
    return "/gd/?c=" . $type . "&file=".$file_in_hex;
}


// showSuggestions($category ){
//     $sugs = $GLOBALS['suggestions'];
//     foreach($sugs[$category] as $suggestion) {
        
//     }
// }


function showSuggestions($category) {
    global $suggestions;
    foreach($suggestions[$category] as $q) { 
        echo '<li><a href="'.URL_BASE.'?c='.$category.'&q='.$q.'">'.$q.'</a>';
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
    drawHeader("Stay Awhile and Listen");

    echo "<h1>Please enjoy your stay and DM requests</h1>\n";
    echo "<h2>Suggestions to get started</h2>\n";
    echo "<h4>Music Suggestions</h4>\n";
    showSuggestions("m");
    echo "<h4>Movies+TV</h4>\n";
    showSuggestions("v");

} else {
    # config switch
    switch($_REQUEST['c']){
        case 'v':
            $search_type = 'v';
            $dump_path = 'Movies+TV';
            $feature = 'watch';
            break;
        case 'a':
            $search_type = 'a';
            $dump_path = 'Audio Books';
            $feature = 'listen';        
            break;
        case 'm':
            $search_type = 'm';
            $dump_path = 'Music';
            $feature = 'listen';      
            break;
        case 'b':
            $search_type = 'b';
            $dump_path = 'Books';
            # no $feature
            break;
        case 'e':
            $search_type = 'e';
            $dump_path = 'Emulation + ROMs';
            # no $feature
            break;
    }


    // if a media file is requested look up the files for it's forward & back buttons
    if(isset($_REQUEST['file']) && isset($_REQUEST['c'])) {
        if($_REQUEST['c'] == 'a' || $_REQUEST['c'] == 'm' || $_REQUEST['c'] = 'v') {

            $buttons = getButtons($dump_path, $_REQUEST['file']);
            $next_file_to_play = $buttons['next'];
        }
    }

    drawHeader($dump_path);


    # player switch
    if(isset($_REQUEST['file'])) {
        echo '

<h3>
    <a href="'.buildPlaybackLink($buttons['previous']).'">⏮️Previous</a> | 
    <a href="'.buildPlaybackLink($buttons['next']).'">Next⏭</a>
</h3>

';
        

        $src = '/gd/' . str_replace('\\', '/', hexToStr($_REQUEST['file'])); ;

        switch($_REQUEST['c']){
            case 'a':
            case 'm':
                echo '<h2>Attempting to play '.$src.'</h2>';
                echo 'For best results view in Chrome. If your browser is unable to play this file you may need to download the file or copy this link into VLC: ';
                echo '<a href="'.$src.'">copy me</a><br><br><br>';
                echo '
<audio autoplay controls id="player" class="player" preload="auto">
<source src="'.$src.'">
Your browser does not support the audio element.
</audio>
<!-- Preload the next song -->
';
                break;

            case 'v':

                echo '<h2>Attempting to play '.$src.'</h2>';
                echo 'For best results view in Chrome. If your browser is unable to play this file you may need to download the file or copy this link into VLC: ';
                echo '<a href="'.$src.'">copy me</a><br><br><br>';
                echo '
<video controls width=800 autoplay id="player" class="player">
<source src="'.$src.'">
</video>

';
            break;
        }
    }

    echo '
<hr>
Search: 
<form method="GET">
<input type="hidden" name="c" value="'.$search_type.'">
<input type="text" name="q">
<input type="submit">
</form>
<br>
<a href="./?c='.$search_type.'&all=1">[All Files]</a>
<hr>
';



    if(isset($suggestions[$search_type])) {
        echo "<h2>Sample Searches</h2>\n";
        showSuggestions($search_type);
    }


    # if there is a valid dump path list our files
    if(strlen($dump_path) > 1) {

        if(isset($_REQUEST['q'])) {
            dumpPath($dump_path, $feature, $search_type, $_REQUEST['q']);

        }

        if(isset($_REQUEST['all'])) {
            dumpPath($dump_path, $feature, $search_type);    
        }
    }
}

drawFooter();

if(isset($_REQUEST['file']) && isset($_REQUEST['c'])) {
    if($_REQUEST['c'] == 'a' || $_REQUEST['c'] == 'm' || $_REQUEST['c'] = 'v') {


        // autoplay code

        $jquery_code_to_run .= "

// autoplay binding



$(\"#player\").bind(
    'ended', 
    function() {
         window.location.href = \"".buildPlaybackLink($buttons['next'])."\";
     }
);

";

        # preload the next song
        if($_REQUEST['c'] == 'a' || $_REQUEST['c'] == 'm') {
            $jquery_code_to_run .= '

setTimeout(
    function() 
    {
        $("body").append(
            \'<audio src="/gd/'. $buttons['next_raw'] . '" preload="auto">\'
        );
    }, 
    10000
);
            ';

        }
    }
}



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


