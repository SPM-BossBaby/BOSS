<?php

require_once '../include/common.php';
require_once '../include/token.php';

$errors = array();

if (!isset($_POST['username'])) {
    $errors[] = 'missing username';
} else {
    $username = $_POST['username'];
    if (empty($username)) {
        $errors[] = "blank username";
    }
}

if (!isset($_POST['password'])) {
    $errors[] = 'missing password';
} else {
    $password = $_POST['password'];
    if (empty($password)) {
        $errors[] = "blank password";
    }
}

if (empty($errors)) {
    $admindao = new AdminDAO;
    $admin = $admindao->getAdmin($username);
    if (empty($admin)) {
        $errors[] = "invalid username";
    } else {
        foreach ($admin as $anadmin) {
            $checkadminpw = $anadmin->authenticate($password);
            if (!$checkadminpw) {
                $errors[] = "invalid password";
            } else {
                $token = generate_token($username);
            }
        }
    }
}

$sortclass = new Sort();
$errors = $sortclass->sort_it($errors,"common_validation");

if (empty($errors)) {
    $result = ["status" => "success", "token" => $token];
} else {
    $result = ["status" => "error", "message" => $errors];
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);


?>