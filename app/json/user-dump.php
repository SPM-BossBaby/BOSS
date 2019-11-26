<?php
require_once '../include/common.php';

$errors = array();

if (isset($_GET['r'])) {
    $data = $_GET['r'];
    $decode_data = json_decode($data,true);

    $student = new Student();
    $studentDAO = new StudentDAO();
    
    #region Common Validation
    require_once '../include/protect_json.php';
    if (!array_key_exists("userid", $decode_data)){
        $errors[] = "missing userid";
    } elseif ($decode_data['userid'] == ""){
        $errors[] = "blank userid";
    }
    #endregion

    #region Field Validation
    if (empty($errors)){
        $username = $decode_data['userid'];

        //Invalid UserID: the userid is not found in the system records
        if ($studentDAO->getStudent($username) === null){
            $errors[] = "invalid userid";
        }
    }
    #endregion

    if (empty($errors)) {
        $student = $studentDAO->getStudent($username);
        $result = ["status" => "success", "userid" => $student->userid, "password" => $student->password, "name" => $student->name, "school" => $student->school, "edollar" => (float) $student->edollar];
    } else {
        $result = ["status" => "error", "message" => $errors];
    }
    
    header("Content-Type:application/json");
    echo json_encode($result,JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);
}
?>