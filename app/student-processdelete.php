<?php

require_once 'include/common.php';
require_once 'include/drop_section.php';



$biddetails = (array) json_decode($_POST['biddetails']);
// // $biddetails['course'] = $biddetails['code'];
// // unset($biddetails['code']);
$biddetails = (object) $biddetails;

$result = dropSection($biddetails);

$_SESSION['deleteresult'] = ['result' => $result,'biddetails' => $biddetails];

$_SESSION['activetab'] = 'enrolment';
header('Location:studentbios.php');


?>