<h1>
    <a href="a.php">Audio Books</a> | 
    <a href="b.php">Books</a> | 
    <a href="e.php">Emulation</a> | 
    <a href="m.php">Music</a> |
    <a href="v.php">Videos</a>
</h1>
<hr>
<?php



# track runtime
$start = microtime(true);
$rustart = getrusage();

# settings

// $path_list_html = "list.html";
$extensions = [
    ".avi", 
    ".flac",
    ".mkv", 
    ".mp3", 
    ".mp4", 
    ".ogg", 
    ".iso", 
    ".smc",
    ".wma",

    ".whitelistme"
];
$white_list = ["Emulation + ROMs\\" ,"D&D\\", "Books\\"];
$excludes = ["Backups+Temp", "Software+Utilities", "Dropbox"];

function strToHex($string){
    $hex='';
    for ($i=0; $i < strlen($string); $i++){
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

function dumpPath($base_path) {
    global $extensions, $white_list, $excludes;

    echo "<h1>$base_path</h1>";

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
            echo '
<a href="v.php?file='.strToHex($path).'">[v]</a>
<a href="'.$path.'">'.$path.'</a>
<br>
';
        }
    }

    echo "<hr><div>$matched matched of $total files searched, $skipped of $excluded excluded</div>";
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

function endScriptTimer() {
    global $start, $rustart;

    echo "<hr>";
    $ru = getrusage();
    $time_elapsed_secs = microtime(true) - $start;



    echo "This process used " . rutime($ru, $rustart, "utime") . " ms for its computations.<br>\n";
    echo "It spent " . rutime($ru, $rustart, "stime") . " ms in system calls.<br>\n";
    echo '<b>Total Execution Time:</b> '.$time_elapsed_secs.' seconds';


}

