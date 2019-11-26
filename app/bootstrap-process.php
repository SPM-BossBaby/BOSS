<?php
# edit the file included below. the bootstrap logic is there
require_once 'include/bootstrap.php';
require_once 'include/common.php';
// require_once 'include/protect.php';

$filename = $_FILES["bootstrap-file"]["name"];
$ext = pathinfo($filename, PATHINFO_EXTENSION); 
if( $ext != 'zip') {
    $bootstrap_result = [
        "status" => "error",
        "num-record-loaded" => [
            [$filename => 0]
        ],        
        "error" => [
            [
            'file' => $filename,
            'line' => 'all',
            'message' => ['invalid file type']
            ]
        ]
    ];
} else {
    $bootstrap_result = doBootstrap();
}

if (isset($_POST['adminpage'])) {

    // $BiddingRoundDAO = new BiddingRoundDAO;
    // $removeall = $BiddingRoundDAO->removeAll();
    // $bootstrap_result['bidtable'] = $removeall;

    // $currdate = date('Y-m-d H:i:s');
    // $bootstrap_result['isStartOK'] = FALSE;
    
    // if ($bootstrap_result['status'] == 'success') {
    //     $BiddingRound = new BiddingRound($name = '1', $active = 1, $start = $currdate, $end = NULL);
    //     $isStartOK = $BiddingRoundDAO->startRound($BiddingRound);
    //     $bootstrap_result['isStartOK'] = $isStartOK;
    // }
    
    $_SESSION['activetab'] = 'bootstrap';
    $_SESSION['bootstrap'] = $bootstrap_result;
    header('Location: adminbios.php');
    exit;
} else {
    unset($bootstrap_result['bidtable']);
    unset($bootstrap_result['isStartOK']);
    header('Content-Type: application/json');
    echo json_encode($bootstrap_result, JSON_PRETTY_PRINT);
}

?>