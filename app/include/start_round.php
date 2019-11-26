<?php

require_once 'common.php';

function startRound(){
    $biddingRound = new BiddingRound();
    $biddingRoundDAO = new BiddingRoundDAO();

    $activeRound = $biddingRoundDAO->activeRound();
    $lastRound = $biddingRoundDAO->getLastRound();

    $error = array();

    if (is_null($lastRound && is_null($activeRound))) {
        $lastRound = 0;
        $activeRound == FALSE;
    }
    if ($lastRound >= 2 && $activeRound == FALSE) {
        $error[] = "round 2 ended";
    } elseif ( $lastRound <= 2 && $activeRound == FALSE) {
        $roundNo = $lastRound+1;
        $roundstart = date('Y-m-d H:i:s');
        $startRound = $biddingRoundDAO->startRound(new BiddingRound($roundNo, 1, $roundstart, $end = NULL));
        if (!$startRound) {
            $error[] = "<message details what error>";
        }
    }
    return $error;
}