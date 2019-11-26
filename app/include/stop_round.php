<?php

require_once 'common.php';
require_once '../include/UploadEnrolled.php';
require_once '../include/ClearingLogic.php';

function stopRound(){
    $biddingRound = new BiddingRound();
    $biddingRoundDAO = new BiddingRoundDAO();
    $activeRound = $biddingRoundDAO->activeRound();

    $bidDAO = new BidDAO();

    $error = array();
    
    if ($activeRound != FALSE) {
        // enroll students
        doClearingLogic($json = TRUE);
        uploadEnrolled($json = TRUE);
        $endRound = $biddingRoundDAO -> endRound();
        if (!$endRound) {
            $error[] = "<message details what error>";
        }
    } else {
        $error[] = "round already ended";
    }

    return $error;
}