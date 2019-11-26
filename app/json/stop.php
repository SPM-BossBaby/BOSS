<?php

require_once '../include/stop_round.php';
require_once '../include/common.php';

$errors = array();

require_once '../include/protect_json.php';

if(empty($errors)){
    $errors = stopRound();
}

if (empty($errors)) {
    $result = ["status" => "success"];
} else {
    $result = ["status" => "error", "message" => $errors];
    
}

header("Content-Type:application/json");
echo json_encode($result,JSON_PRETTY_PRINT);
?>