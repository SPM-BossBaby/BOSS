<?php
require_once '../include/display_section.php';
require_once '../include/common.php';

$errors = array();
  
if (isset($_GET['r'])) {
    $data = $_GET['r'];
    $decode_data = json_decode($data,true);
    
    $enrolled = new Enrolled();
    $bidDAO = new BidDAO();
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
        $enrolled->code = $decode_data['course'];
        $enrolled->section = $decode_data['section'];

        //Invalid Course: Course code does not exist in the system's records
        if ($courseDAO->getCourse($enrolled->code) === null){
            $errors[] = "invalid course";
        }

        //Invalid Section: No such section ID exists for the particular course. Only check if course is valid
        else{
            if ($sectionDAO->getSection($enrolled->code, $enrolled->section) === null){
                $errors[] = "invalid section";
            }
        }
    }
    #endregion

    if (empty($errors)) {
        $result = displaySection($enrolled->code, $enrolled->section);
    } else {
        $result = ["status" => "error", "message" => $errors];
    }
    header("Content-Type:application/json");
    echo json_encode($result,JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);
}
?>