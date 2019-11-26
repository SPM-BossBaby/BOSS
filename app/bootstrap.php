<?php
require_once 'include/common.php';
#require_once 'include/protect.php';

?>

<form id='bootstrap-form' action="bootstrap-process.php" method="post" enctype="multipart/form-data">
	Bootstrap file: 
	<input id='bootstrap-file' type="file" accept=".zip" name="bootstrap-file"></br>
	<input type="submit" name="submit" value="Import">
</form>
