<?php

include dirname(__FILE__) . "vcrlib.php";

if(isset($_REQUEST['file']))
{
	$src = '/gd/' . hexToStr($_REQUEST['file']);
	echo '<h2>Attempting to play '.$src.'</h2>';
	echo 'If your browser is unable to play this file you may need to download the file or copy this link into VLC: ';
	echo '<a href="'.$src.'">copy me</a><br><br><br>';

	echo '
<video controls width=800 autoplay>
    <source src="'.$src.'">
</video>
<hr>
';
}

dumpPath("Movies+TV");

endScriptTimer();
