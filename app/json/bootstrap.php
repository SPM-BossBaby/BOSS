<?php
# edit the file included below. the bootstrap logic is there
require_once '../include/bootstrap.php';
require_once '../include/common.php';

$errors = array();

require_once '../include/protect_json.php';

if (empty($errors)){
        $bootstrap_result = doBootstrap($json = TRUE);
        header('Content-Type: application/json');
        echo json_encode($bootstrap_result, JSON_PRETTY_PRINT);
} else{
    header('Content-Type: application/json');
    echo json_encode($errors, JSON_PRETTY_PRINT);
}

?>