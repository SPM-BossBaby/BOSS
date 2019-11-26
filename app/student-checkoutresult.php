<?php

require_once 'include/common.php';
require_once 'include/protect.php';

// check if student here
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit;
}
$studentDAO = new StudentDAO;
$checkstudent = $studentDAO->getStudent($_SESSION['userid']);
if ($checkstudent == FALSE) {
    $_SESSION['error'] = 'You do not have permission to view this page!';
    header("Location: login.php");
    exit;
}
$_SESSION['edollar'] = $checkstudent->edollar;

date_default_timezone_set('Asia/Singapore');


$BiddingRoundDAO = new BiddingRoundDAO();
$active = $BiddingRoundDAO->activeRound();

$CourseDAO = new CourseDAO();

$SectionDAO = new SectionDAO();

if ($_SESSION['checkoutresult'] == 'success') { // check if the checkout is successful
    $bidsplaced = TRUE;
} else {
    $bidsplaced = FALSE;
}
$statusarray = $_SESSION['checkoutarray']; // either success bids or error messages

if (isset($_SESSION['isUpdate'])) { 
    $_SESSION['activetab'] = 'details'; // if it is a update of bids, user will be redirected to the bids placed page
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Student Checkout Result</title>

    <link rel="Icon" href="images/BIOS.png">
    <meta charset="utf-8">
    <!-- Bootstrap required meta tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- Bootstrap Script -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <!-- Fullcalendar -->
    <link href='https://unpkg.com/@fullcalendar/core@4.3.1/main.min.css' rel='stylesheet' />
    <link href='https://unpkg.com/@fullcalendar/daygrid@4.3.0/main.min.css' rel='stylesheet' />
    <link href='https://unpkg.com/@fullcalendar/timegrid@4.3.0/main.min.css' rel='stylesheet' />
    <script src='https://unpkg.com/@fullcalendar/core@4.3.1/main.min.js'></script>
    <script src='https://unpkg.com/@fullcalendar/interaction@4.3.0/main.min.js'></script>
    <script src='https://unpkg.com/@fullcalendar/daygrid@4.3.0/main.min.js'></script>
    <script src='https://unpkg.com/@fullcalendar/timegrid@4.3.0/main.min.js'></script>

    <!-- Jquery CDN -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <!-- JS script -->
    <script type="text/javascript" src="include/scripts/studentcheckout.js"></script>

    <!-- Icon Script -->
    <script src="https://kit.fontawesome.com/b1b5f42ae7.js"></script>

    <style>
        <?php require_once('include/styles/studentcheckout.css') ?>
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
            <a class="navbar-brand" href="studentbios.php">
                <img src="images/MUbios.png" alt="BIOS" height="60" class="d-inline-block align-top">
                <h1 class="d-inline">Student Dashboard</h1>
            </a>
            <ul class="navbar-nav flex-row ml-md-auto d-none d-md-flex">
                <li class="nav-item mx-3" id='balance'>
                    <i class="fas fa-wallet"></i>
                    Available Balance: e$<?php echo $_SESSION['edollar'] ?>
                </li>
                <li class="nav-item" id='welcome-nav'>
                    Welcome, <?php echo $_SESSION['username'] ?>
                    <i class="fas fa-user"></i>
                </li>
            </ul>
            <form action="logout.php">
                <button class="btn" type="submit"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
    </nav>  
    <div class="container-fluid d-flex flex-column">
        <div class="row justify-content-center pt-3">
            <?php
            if ($bidsplaced) { // if the first calling of updatebid fails, all the bids will be unsuccessful
                echo "<h2>Bids Checkout Result</h2>";
            } else {
                echo "
                <h2>Bids Unsuccessful</h2>";
            }
            ?>
        </div>
        <div class="row justify-content-center">
            <div class="col-8 col-m-12 border rounded pb-3">
                <div class="row container-fluid m-0 pt-3">
                    <table class="table">
                        <thead>
                            <tr>
                                <?php
                                if ($bidsplaced) {
                                    echo '
                                    <th colspan="4" class="text-center table-light table-borderless border-top-0 pb-0">
                                        <h4>Bids</h4>
                                    </th>';                                    
                                } else {
                                    echo '
                                    <th colspan="4" class="text-center table-light table-borderless border-top-0 pb-0">
                                        <h4>Error List</h4>
                                    </th>';
                                } ?>                                
                                </th>
                            </tr>
                            <tr class='table-bordered'>
                                <th>Course</th>
                                <th>Section</th>
                                <th>Bid</th>
                                <?php
                                if ($bidsplaced) {
                                    echo "<th>Result</th>";                                    
                                } else {
                                    echo "<th>Reason</th>";
                                } ?>                            
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($bidsplaced) {
                                foreach ($statusarray as $coursecode => $bidinfo) {
                                    $coursearray = explode("_", $coursecode);
                                    $code = $coursearray[0];
                                    $section = $coursearray[1];
                                    $bidamt = number_format((float)$bidinfo[1]->amount, 2, '.', '');
                                    $bidstatus = $bidinfo[0]; // bid type - either new bid or update bid
                                    if (substr($bidstatus, 0, 13) === '<b>Bid Placed') { // check whether is it a success or failed bid 
                                        $bidresult = 'style="background-color: #d4edda"';
                                    } else {
                                        $bidresult = 'style="background-color: #f8d7da"';
                                    }
                                    echo "
                                    <tr class='table-bordered'>
                                        <td>{$code}</td>
                                        <td>{$section}</td>
                                        <td>{$bidamt}</td>
                                        <td {$bidresult}>{$bidstatus}</td>
                                    </tr>
                                    ";
                                };
                            } else {
                                foreach ($statusarray as $coursecode => $bidinfo) {
                                    $coursearray = explode("_", $coursecode);
                                    $code = $coursearray[0];
                                    $section = $coursearray[1];
                                    $bidamt = number_format((float)$bidinfo[1], 2, '.', '');
                                    $errorlist = '';
                                    foreach ($bidinfo[0] as $anerror) {
                                        $errorlist .= $anerror . '<br>';
                                    }
                                    rtrim('<br>', $errorlist);
                                    
                                    echo "
                                    <tr class='table-danger table-bordered''>
                                        <td>{$code}</td>
                                        <td>{$section}</td>
                                        <td>e\${$bidamt}</td>
                                        <td>{$errorlist}</td>
                                    </tr>
                                    ";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="row container-fluid d-flex justify-content-center m-0 p-0">
                <div>
                    <button class="btn" type="button" onclick="location.href='studentbios.php'"><i class="fas fa-home"></i> Home</button>
                </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>