<?php

require_once 'include/common.php';
require_once 'include/ClearingLogic.php';
require_once 'include/UploadEnrolled.php';

if (isset($_POST['endround'])) {
    $BiddingRoundDAO = new BiddingRoundDAO();
    $roundno = $BiddingRoundDAO->activeRound()->roundNo;
    
    if ($roundno == 1) {
        try {
            doClearingLogic();
            $_SESSION['clearingresult'] = TRUE;
        } catch (Exception $e) {
            $_SESSION['clearingresult'] = FALSE;
        }
        
    }

    $_SESSION['enrolresult'] = uploadEnrolled();

    $_SESSION['isEndOK'] = $BiddingRoundDAO->endRound();
    $_SESSION['activetab'] = 'roundmgmt';

    header('Location:adminbios.php');
    exit;
}

if (isset($_POST['startround'])) {

    $currdate = date('Y-m-d H:i:s');

    $BiddingRoundDAO = new BiddingRoundDAO();
    $BiddingRound = new BiddingRound($name = $_POST['roundNo'], $active = 1, $start = $currdate, $end = NULL);
    
    $_SESSION['isStartOK'] = $BiddingRoundDAO->startRound($BiddingRound);

    $_SESSION['activetab'] = 'roundmgmt';

    header('Location:adminbios.php');
    exit;

}

if (isset($_POST['removeall'])) {
    $BiddingRoundDAO = new BiddingRoundDAO();
    $_SESSION['removeall'] = $BiddingRoundDAO->removeAll();
    
    header('Location:adminbios.php');
    exit;
}

?>