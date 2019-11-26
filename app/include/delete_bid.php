<?php
require_once 'common.php';
require_once 'ClearingLogic.php';

function deleteBid($bid, $json = FALSE) {

    $bidDAO = new BidDAO();
    $studentDAO = new StudentDAO();
    $biddingRoundDAO = new BiddingRoundDAO();

    //round ended: The current bidding round has already ended.
    $biddingRound = $biddingRoundDAO->getCurrentRound();

    if ($biddingRound->active == 0){
        $errors[] = "round ended";
    } else {
        if (!$bidDAO->checkBidForDelete($bid->userid, $bid->code, $bid->section, $biddingRound->roundNo)){
            $errors[] = "no such bid";
        }
    }

    if(empty($errors)){
        $bid_edollar = $bidDAO->checkBid($bid->userid, $bid->code, $biddingRound->roundNo)->amount;
        $bidDAO->deleteBid($bid->userid, $bid->code, $bid->section);

        if ($studentDAO->refundStudentEdollar($bid->userid, (float)$bid_edollar)){
            $result = ["status" => "success"];
            if($biddingRound->roundNo == 2){
                doClearingLogic($json);
            }
            return $result;
        }
        else{
            $errors[] = "refund edollar failed";
        }
    }

    if(!empty($errors)){
        $result = [ 
            "status" => "error", "message" => $errors
        ];
        return $result;
    }
}

?>