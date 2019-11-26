<?php
require_once 'common.php';
require_once 'ClearingLogic.php';

function dropSection($bid, $json = FALSE) {

    $enrolledDAO = new EnrolledDAO();
    $studentDAO = new StudentDAO();
    $bidDAO = new BidDAO();

    if ($enrolledDAO->getEnrollment($bid->userid, $bid->code, $bid->section) === null){
        $result = ["status" => "success"];
        return $result;
    } else {
        $enrolled_edollar = floatval ($enrolledDAO->getEnrollment($bid->userid, $bid->code, $bid->section)->amount);
        $enrolledDAO->deleteEnrollment($bid->userid, $bid->code, $bid->section);
        $bidDAO->updateBidStatus($bid->userid, $bid->code, $bid->section, "fail");

        if ($studentDAO->refundStudentEdollar($bid->userid, $enrolled_edollar)){
            $result = ["status" => "success"];
            doClearingLogic($json);
            return $result;
        }
    }
    if (!empty($errors)){
        $result = ["status" => "error", "message" => $errors];
        return $result;
    }
}

?>