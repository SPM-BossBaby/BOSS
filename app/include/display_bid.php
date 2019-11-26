<?php
require_once 'common.php';

function displayBid($course, $section) {

    $bid = new Bid();
    $biddingRoundDAO = new BiddingRoundDAO();
    $bidDAO = new BidDAO();

    $error = array();

    $activeRound = $biddingRoundDAO->activeRound();
    if ($activeRound != FALSE){
        $roundNo = $activeRound->roundNo;
    } else {
        $roundNo = $biddingRoundDAO->getLastRound();
    }

    $bids =[];
    $bidData = $bidDAO->retrieveBidFromCourseSectionByRoundNo($course, $section, $roundNo);
    $row = 1;
    if($activeRound){
        foreach($bidData as $bid) {
            $bids[] = array('row' => $row, 'userid' => $bid->userid , 'amount' => (float) $bid->amount, 'result' => "-");
            $row++;
        }
        $result = ["status" => "success", "bids" => $bids];
    } else {
        foreach($bidData as $bid) {
            if ($bid->status == "success") {
                $status = "in";
            } elseif ($bid->status == "fail") {
                $status = "out";
            } else {
                $status = "-";
            }
            $bids[] = array('row' => $row, 'userid' => $bid->userid , 'amount' => (float) $bid->amount, 'result' => $status);
            $row++;
        }
        $result = ["status" => "success", "bids" => $bids];
    }

    return $result;

}


?>