<?php

require_once 'common.php';

function uploadEnrolled($json = FALSE) {
    if (!$json) {
        require_once 'protect.php';
    }
    $biddingRoundDAO = new BiddingRoundDAO();
    $bidDAO = new BidDAO();
    $enrolledDAO = new EnrolledDAO();
    $studentDAO = new StudentDAO();

    // Add all successful bids into enrolled from round
    $activeRoundNo = $biddingRoundDAO->activeRound()->roundNo;
    $allFailedBids = $bidDAO->retrieveBidFromRoundByStatus("fail", $activeRoundNo);
    for($i = 0; $i < count($allFailedBids); $i++){
        $studentDAO->refundStudentEdollar($allFailedBids[$i]->userid, floatval ($allFailedBids[$i]->amount));
    }
    $allSuccessfulBids = $bidDAO->retrieveBidFromRoundByStatus("success", $activeRoundNo);
    if (count($allSuccessfulBids) == 0) {
        return TRUE;
    }
    return $enrolledDAO->enrollAll($allSuccessfulBids);
}

?>