<?php
require_once 'token.php';
require_once 'common.php';

# check if the username session variable has been set 
# send user back to the login page with the appropriate message if it was not

if(!isset($_SESSION['userid'])) {
	$_SESSION['error'] = 'Please Login';
	header("Location: login.php");
	exit;
}

?>