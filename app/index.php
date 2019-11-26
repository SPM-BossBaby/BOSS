<?php

// header('Location: login.php');

require_once 'include/common.php';

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}
$studentDAO = new StudentDAO;
$checkstudent = $studentDAO->getStudent($_SESSION['userid']);
if ($checkstudent != FALSE) {
    header("Location: studentbios.php");
    exit;
} else {
    $adminDAO = new AdminDAO;
    $checkadmin = $adminDAO->getAdmin($_SESSION['userid']);
    if ($checkadmin != FALSE) {
        header('Location: adminbios.php');
        exit();
    } else {
        header('Location: login.php');
        exit();
    }
}

?>