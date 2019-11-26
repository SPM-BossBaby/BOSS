<?php

require_once '../include/delete_bid.php';
require_once '../include/common.php';

$errors = array();

if (isset($_GET['r'])) {
    $data = $_GET['r'];
    $decode_data = json_decode($data, TRUE);

    $bid = new Bid();
    $courseDAO = new CourseDAO();
    $studentDAO = new StudentDAO();
    $sectionDAO = new SectionDAO();

    #region Common Validation
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
    if (empty($errors)) {
        $bid->userid = $decode_data['userid'];
        $bid->code = $decode_data['course'];
        $bid->section = $decode_data['section'];

        //Invalid Course: Course code does not exist in the system's records
        if ($courseDAO->getCourse($bid->code) === null){
            $errors[] = "invalid course";
        }

        //Invalid Section: No such section ID exists for the particular course. Only check if course is valid
        else{
            if ($sectionDAO->getSection($bid->code, $bid->section) === null){
                $errors[] = "invalid section";
            }
        }

        //Invalid UserID: userid does not exist in the system's records
        if ($studentDAO->getStudent($bid->userid) === null){
            $errors[] = "invalid userid";
        }
    }
    #endregion
    
    //no such bid: No such bid exists in the system's records. Check only if there is an (1) active bidding round, and (2) course, userid and section are valid and (3)the round is currently active.
    if (empty($errors)){
        $result = deleteBid($bid, TRUE);
    } else {
        $result = [ 
            "status" => "error", "message" => $errors
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($result, JSON_PRETTY_PRINT);
}

?>