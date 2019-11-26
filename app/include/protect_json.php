<?php
require_once 'token.php';
require_once 'common.php';

$token = '';

if (isset($_REQUEST['token'])) {
	$token = $_REQUEST['token'];
}
else{
	$errors[] = "missing token";
}
if(empty($errors) && (empty($token) || $token == "")) {
	$errors[] = "blank token";
}
else if (empty($errors) && verify_token($_REQUEST['token']) != "admin") {
	$errors[] = "invalid token";
}

?>