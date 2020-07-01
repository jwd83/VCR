<?php

include dirname(__FILE__) . "vcrlib.php";
?>
Search: 
<form method="GET">
	<input type="text" name="q">
	<input type="submit">
</form>
<br>
<a href="e.php?all=1">[All Files]</a>
<hr>
<?php
if(isset($_REQUEST['q'])) {
	dumpPath("Emulation + ROMs", "none", "none", $_REQUEST['q']);

}

if(isset($_REQUEST['all'])) {
	dumpPath("Emulation + ROMs");
}

endScriptTimer();