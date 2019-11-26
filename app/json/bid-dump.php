<?php
require_once '../include/display_bid.php';
require_once '../include/common.php';

$error = array();

if (isset($_GET['r'])) {
    $data = $_GET['r'];
    $decode_data = json_decode($data, true);

    $bid = new Bid();
    $courseDAO = new CourseDAO();
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
    #endregion

    #region Field Validation
    if (empty($errors)) {
        $bid->course = $decode_data['course'];
        $bid->section = $decode_data['section'];

        // Invalid Course: Course code does not exist in the system's records
        if ($courseDAO->getCourse($bid->course) === null){
            $errors[] = "invalid course";
        }

        // Invalid Section: No such section ID exists for the particular course. Only check if course is valid
        else{
            if ($sectionDAO->getSection($bid->course, $bid->section) === null){
                $errors[] = "invalid section";
            }
        }
    }
    #endregion

    if (empty($errors)) {
        $result = displayBid($bid->course, $bid->section);
        header('Content-Type: application/json');
        echo json_encode($result, JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);
    } else {
        $result = ["status" => "error", "message" => $errors];
        header('Content-Type: application/json');
        echo json_encode($result, JSON_PRETTY_PRINT);
    }
}


?>