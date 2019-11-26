<?php

require_once '../include/update_bid.php';
require_once '../include/common.php';

$errors = array();

if (isset($_GET['r'])) {
    $data = $_GET['r'];
    $decode_data = json_decode($data, TRUE);

    $bid = new Bid();
    $courseDAO = new CourseDAO();
    $sectionDAO = new SectionDAO();
    $studentDAO = new StudentDAO();

    #region Common Validation
    if (!array_key_exists("amount", $decode_data)){
        $errors[] = "missing amount";
    } elseif ($decode_data['amount'] == ""){
        $errors[] = "blank amount";
    }
    if (!array_key_exists("course", $decode_data)){
        $errors[] = "missing course";
    } elseif ($decode_data['course'] == ""){
        $errors[] = "blank course";
    }
    if (!array_key_exists("section", $decode_data)){
        $errors[] = "missing section";
    } elseif ($decode_data['section'] == ""){
        $errors[] = "blank section";
    }
    require_once '../include/protect_json.php';
    if (!array_key_exists("userid", $decode_data)){
        $errors[] = "missing userid";
    } elseif ($decode_data['userid'] == ""){
        $errors[] = "blank userid";
    }
    #endregion

    #region Field Validation
    if (empty($errors)){
        $bid->userid = $decode_data['userid'];
        $bid->amount = $decode_data['amount'];
        $bid->course = $decode_data['course'];
        $bid->section = $decode_data['section'];

        //Invalid Amount: the amount must be a positive number >= e$10.00 and not more than 2 decimal places.
        if ($bid->amount < 10 || preg_match('/\.\d{3,}/', $bid->amount)){
            $errors[] = "invalid amount";
        }
        
        //Invalid Course: the course code is not found in the system records
        if ($courseDAO->getCourse($bid->course) === null){
            $errors[] = "invalid course";
        } else {
            //Invalid Section: the section code is not found in the system records. Only check if the course code is valid.
            if ($sectionDAO->getSection($bid->course, $bid->section) === null){
                $errors[] = "invalid section";
            }
        }

        //Invalid UserID: the userid is not found in the system records
        if ($studentDAO->getStudent($bid->userid) === null){
            $errors[] = "invalid userid";
        }
    }
    #endregion
    
    if (empty($errors)) {
        $result = updateBid($decode_data, FALSE, FALSE, TRUE);
        header('Content-Type: application/json');
        echo json_encode($result, JSON_PRETTY_PRINT);
    } else {
        $result = ["status" => "error", "message" => $errors];
        header('Content-Type: application/json');
        echo json_encode($result, JSON_PRETTY_PRINT);
    }
}

?>