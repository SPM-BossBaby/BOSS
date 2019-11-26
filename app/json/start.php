<?php

require_once '../include/start_round.php';
require_once '../include/common.php';

$errors = array();

require_once '../include/protect_json.php';

if(empty($errors)){
    $errors = startRound();
}

if (empty($errors)) {
    $biddingRoundDAO = new BiddingRoundDAO();
    $activeRound = $biddingRoundDAO->activeRound();
    $result = ["status" => "success", "round" => (int) $activeRound->roundNo];
} else {
    $result = ["status" => "error", "message" => $errors];
}

header("Content-Type:application/json");
echo json_encode($result,JSON_PRETTY_PRINT);
?>