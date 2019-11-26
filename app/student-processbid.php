<?php

require_once "include/common.php";
require_once "include/update_bid.php";
require_once "include/delete_bid.php";

$bidDAO = new BidDAO();
$CourseDAO = new CourseDAO;
$studentDAO = new StudentDAO;

$biddetails = json_decode($_POST['biddetails']);

var_dump($biddetails);

if (isset($_POST['updatebid'])) {
    $title = $CourseDAO->getCourse($biddetails->code)->title;
    $biddetails = (array) $biddetails;    
    $biddetails['title'] = $title;
    $biddetails['course'] = $biddetails['code'];
    unset($biddetails['code']);
    $_SESSION['bidcart'] = [$biddetails];
    $_SESSION['isUpdate'] = TRUE;
    header('Location:studentcheckout.php');
    exit;
}
if (isset($_POST['deletebid'])) {
    // $biddetails = (array) $biddetails; 
    // $biddetails['course'] = $biddetails['code'];
    // unset($biddetails['code']);
    // $biddetails = (object) $biddetails;
    $deletebidresult = deleteBid($biddetails);
}

$_SESSION['deletebidresult'] = ['delete' => $deletebidresult, 'biddetails' => $biddetails];
$_SESSION['activetab'] = 'details';
header('Location: studentbios.php');

?>
