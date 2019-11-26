<?php

require_once 'include/common.php';
require_once 'include/ClearingLogic.php';
require_once 'include/UploadEnrolled.php';

if (isset($_POST['endround'])) { //if end round
    $BiddingRoundDAO = new BiddingRoundDAO();
    $roundno = $BiddingRoundDAO->activeRound()->roundNo;
    
    if ($roundno == 1) { //do clearing logic 1 for round 1
        try {
            doClearingLogic();
            $_SESSION['clearingresult'] = TRUE;
        } catch (Exception $e) { //if try false
            $_SESSION['clearingresult'] = FALSE; //to not display web error messages
        }
        
    }

    $_SESSION['enrolresult'] = uploadEnrolled(); //for enrolled students

    $_SESSION['isEndOK'] = $BiddingRoundDAO->endRound(); //ends round
    $_SESSION['activetab'] = 'roundmgmt';

    header('Location:adminbios.php');
    exit;
}

if (isset($_POST['startround'])) { //to start round

    $currdate = date('Y-m-d H:i:s');

    $BiddingRoundDAO = new BiddingRoundDAO();
    $BiddingRound = new BiddingRound($name = $_POST['roundNo'], $active = 1, $start = $currdate, $end = NULL);
    
    $_SESSION['isStartOK'] = $BiddingRoundDAO->startRound($BiddingRound);

    $_SESSION['activetab'] = 'roundmgmt';

    header('Location:adminbios.php');
    exit;

}

if (isset($_POST['removeall'])) { //deletes bidding round table
    $BiddingRoundDAO = new BiddingRoundDAO();
    $_SESSION['removeall'] = $BiddingRoundDAO->removeAll();
    
    header('Location:adminbios.php');
    exit;
}

?>