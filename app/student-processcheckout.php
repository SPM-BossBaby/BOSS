<?php

require_once "include/common.php";
require_once "include/update_bid.php";

$BidDAO = new BidDAO();
$StudentDAO = new StudentDAO;
$EnrolledDAO = new EnrolledDAO;
$errors = [];
$success = [];
$allSuccessBid = [];
$allBid = $_POST; //request post data
$edollar = $_SESSION['edollar'];
#receive post data

#each post data extract the 4 things (section, course, userid (from sessesion), amount)
foreach ($allBid as $course=>$amount) {
    $coursearray = explode("_", $course);
    $code = $coursearray[0];
    $section = $coursearray[1];
    //the format of how updatebid needs it to be
    $bid = [
        'userid' => $_SESSION['userid'],
        'course' => $code,
        'section' => $section,
        'amount' => $amount
    ];
   
    $result = updatebid($bid, TRUE);
    if ($result['status'] == 'error') {
        $errors[$course] = [$result['message'], $amount];
    } else {
        $success[] = $bid;
    }
}

// print_r($errors);
// echo "<br>";
// print_r($success);
// success is an array of successful 'bid' objects

if (empty($errors)) { //no errors

    foreach($success as $aBid) { //to check if clashes is alr bidded mod
        $bid = new Bid($userid=$aBid['userid'], $amount=$aBid['amount'], $code=$aBid['course'], $section=$aBid['section']);
        $bidtype = updatebid($aBid, TRUE, TRUE); //bid info; returns if can be bidded (according to existing bids); makes the actual bid, return type of bid (according to bids in bid cart)
        $bididentifier = $code=$bid->code . "_" . $section=$bid->section;
        $allSuccessBid[$bididentifier] = [$bidtype, $bid];
    }

    // $currstudent = $studentDAO->getStudent($_SESSION['userid']);
    // var_dump($currstudent);
    // $_SESSION['edollar'] = $currstudent->edollar;

    // echo $_SESSION['edollar'];
    // var_dump($allSuccessBid);

    $_SESSION['checkoutresult'] = 'success';
    $_SESSION['checkoutarray'] = $allSuccessBid;
    // redirect
} else {
    // redirect
    $_SESSION['checkoutresult'] = 'fail'; //rejects entire cart if have error
    $_SESSION['checkoutarray'] = $errors;
}

header('Location:student-checkoutresult.php');


?>
