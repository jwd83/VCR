<?php

include dirname(__FILE__) . "vcrlib.php";

$search_type = '';
$dump_path = '';
$feature = "none";

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
    drawHeader($dump_path);

    # player switch
    if(isset($_REQUEST['file'])) {
        switch($_REQUEST['c']){
            case 'a':
            case 'm':
                $src = '/gd/' . hexToStr($_REQUEST['file']);
                echo '<h2>Attempting to play '.$src.'</h2>';
                echo 'For best results view in Chrome. If your browser is unable to play this file you may need to download the file or copy this link into VLC: ';
                echo '<a href="'.$src.'">copy me</a><br><br><br>';
                echo '
<audio autoplay controls>
<source src="'.$src.'">
Your browser does not support the audio element.
</audio>
';
                break;

            case 'v':
                $src = '/gd/' . hexToStr($_REQUEST['file']);
                echo '<h2>Attempting to play '.$src.'</h2>';
                echo 'For best results view in Chrome. If your browser is unable to play this file you may need to download the file or copy this link into VLC: ';
                echo '<a href="'.$src.'">copy me</a><br><br><br>';
                echo '
<video controls width=800 autoplay>
<source src="'.$src.'">
</video>
<hr>
';            
            break;
        }
    }

    echo '
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