<?php

include dirname(__FILE__) . "vcrlib.php";

$search_type = '';
$dump_path = '';
$feature = "none";

$jquery_code_to_run = "";
$buttons = array();
$next_file_to_play = "";



if(!isset($_REQUEST['c'])) {
    drawHeader("Stay Awhile and Listen");

    echo "<h1>Please enjoy your stay and DM requests</h1>";
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


