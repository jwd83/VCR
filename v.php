<?php

include dirname(__FILE__) . "vcrlib.php";
?>
Search: 
<form method="GET">
	<input type="text" name="q">
	<input type="submit">
</form>
<br>
<a href="v.php?all=1">[All Files]</a>
<hr>
<?php
if(isset($_REQUEST['file']))
{
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
}


if(isset($_REQUEST['q'])) {
	dumpPath("Movies+TV", "watch", "v", $_REQUEST['q']);

}

if(isset($_REQUEST['all'])) {
	dumpPath("Movies+TV", "watch", "v");	
}


endScriptTimer();