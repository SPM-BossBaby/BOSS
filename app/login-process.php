<?php

require_once 'include/common.php';

$userid = $_POST['userid'];
$password = $_POST['password'];

$admindao = new AdminDAO;
$admin = $admindao->getAdmin($userid);

if (!empty($admin)) {
    foreach ($admin as $anadmin) {
        if (strlen($userid) == strlen($anadmin->userid) && $userid === $anadmin->userid && $anadmin->authenticate($password)) {
            $_SESSION['userid'] = $userid;
            $_SESSION['username'] = $anadmin->name;
            echo 'hi';
            header("Location: adminbios.php");
            exit();
        }
    }
}

$studentdao = new StudentDAO();
$student = $studentdao -> getStudent($userid);


if (!empty($student)) {
    if (strlen($userid) == strlen($student->userid) && $userid === $student->userid && $student->authenticate($password)) {
        $_SESSION['userid'] = $userid;
        $_SESSION['username'] = $student->name;
        $_SESSION['edollar'] = $student->edollar;

        header("Location: studentbios.php");
        exit();
    }
}

$_SESSION['error'] = 'Invalid username or password!';

header('Location:login.php');

?>