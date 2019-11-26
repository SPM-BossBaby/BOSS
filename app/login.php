<?php
require_once 'include/common.php';
require_once 'include/token.php';

// error variable
$error = '';
// for previous page
// checks if error is present
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}
?>

<!doctype html>
<html lang="en">

<head>
    <title>BIOS Log In</title>
    <link rel="Icon" href="images/BIOS.png">
    <meta charset="utf-8">
    <!-- Bootstrap required meta tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- Bootstrap Script -->
    <!--jquery-->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <!--javascript library-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- Icon Script -->
    <script src="https://kit.fontawesome.com/b1b5f42ae7.js"></script>

    <!-- User defined CSS stylesheet -->
    <style>
        <?php require_once('include/styles/login.css') ?>
    </style>

</head>

<body>

    <!-- container for log in box -->
    <div class="modal-dialog text-center">
        <div class="col-sm-8 main-section">
            <div class="modal-content">
                <!-- container for school logo -->
                <div class="col-12">
                    <img src="images/MUbios.png" class="img-fluid" alt="MU BIOS" id="bios-logo">
                </div>

                <!-- log in form -->
                <form method='POST' class="col-12" action="login-process.php">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Enter Username" name="userid" required data-error-msg="Username required!">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" placeholder="Enter Password" name="password" required data-error-msg="Password required!">
                    </div>
                    <?php

                    // shows errors if present
                    if (!empty($error)) {
                        echo "
                        <div class='alert alert-danger' role='alert' id='alert'>
                        $error
                        </div>";
                    };
                    unset($_SESSION['error']);
                    if (isset($_GET['logout'])) {
                        echo "
                            <div class='alert alert-success' role='alert' id='alert'>
                            Logged out successfully!
                            </div>
                            ";
                    };
                    ?>
                    <button type="submit" class="btn" name='Login'><i class="fas fa-sign-in-alt" id="login-icon"></i>Log In</button><!-- fas = font awesome-->
                </form>

                <!-- forgot password -->
                <div class="col-12 forgot">
                    <!-- <a href="forgot.php">Forgot Password?</a> -->
                </div>


            </div>
            <!--End of modal content -->
        </div>
    </div>


</body>

</html>