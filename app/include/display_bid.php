<?php
require_once 'common.php';

function displayBid($course, $section) {

    $bid = new Bid();
    $biddingRoundDAO = new BiddingRoundDAO();
    $bidDAO = new BidDAO();

    $error = array();

    $activeRound = $biddingRoundDAO->activeRound();
    //if active round, get round number
    if ($activeRound != FALSE){
        $roundNo = $activeRound->roundNo;
    } else {
    //if inactive round, get last round number
        $roundNo = $biddingRoundDAO->getLastRound();
    }

    $bids =[];
    $bidData = $bidDAO->retrieveBidFromCourseSectionByRoundNo($course, $section, $roundNo);
    $row = 1;
    if($activeRound){ //if active round, display all bids in current round with status of "-"
        foreach($bidData as $bid) {
            $bids[] = array('row' => $row, 'userid' => $bid->userid , 'amount' => (float) $bid->amount, 'result' => "-");
            $row++;
        }
        $result = ["status" => "success", "bids" => $bids];
    } else { 
        foreach($bidData as $bid) { //change status accordingly
            if ($bid->status == "success") {
                $status = "in";
            } elseif ($bid->status == "fail") {
                $status = "out";
            } else {
                $status = "-";
            }
            //display all bids with changed status
            $bids[] = array('row' => $row, 'userid' => $bid->userid , 'amount' => (float) $bid->amount, 'result' => $status);
            $row++;
        }
        $result = ["status" => "success", "bids" => $bids];
    }

    return $result;

}


?>