<?php
require_once 'common.php';

function displaySection($course, $section) {

    $enrolledDAO = new EnrolledDAO();
    $biddingRoundDAO = new BiddingRoundDAO();

    $students =[];

    $activeRound = $biddingRoundDAO->activeRound();

    if ($activeRound != FALSE){
        if ($activeRound->roundNo > 1) {
            $roundNo = $activeRound->roundNo - 1;
        }
    } else {
        if ($biddingRoundDAO->getLastRound() > 1) {
            $roundNo = $biddingRoundDAO->getLastRound();
        }
    }

    $enrolledData = $enrolledDAO->getEnrolledFromCourseSection($course, $section);
    foreach($enrolledData as $enroll) {
        $students[] = array('userid' => $enroll->userid , 'amount' => (float) $enroll->amount);
    }

    $result = ["status" => "success", "students" => $students];
    return $result;
}