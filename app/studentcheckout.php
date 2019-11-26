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
$_SESSION['activeround'] = $active;

$BidDAO = new BidDAO;
$EnrolledDAO = new EnrolledDAO;
$CourseDAO = new CourseDAO();

$SectionDAO = new SectionDAO();

$disabled = '';
$errormsgarray = array();
$checkdupcourse = array();


$currentbids = $BidDAO->getBidFromUserByRoundNo($_SESSION['userid'], $active->roundNo);
if (count($currentbids) >= 5 && !isset($_SESSION['isUpdate'])) {
    $errormsgarray[] = 'Maximum number of biddable modules is 5';
    $disabled = 'disabled';
}
if (isset($_SESSION['bidcart'])) {
    $bidcart = $_SESSION['bidcart'];
} else {
    $bidcart = array();
}
if (count($currentbids) + count($bidcart) >  5 && !isset($_SESSION['isUpdate'])) {
    $errormsgarray[] = 'Maximum number of biddable modules is 5';
    $disabled = 'disabled';
}
if (count($bidcart) > 5 && !isset($_SESSION['isUpdate'])) {
    $errormsgarray[] = 'Maximum number of biddable modules is 5';
    $disabled = 'disabled';
}
$userenrolled = $EnrolledDAO->getEnrolledFromUser($_SESSION['userid']);
if ((count($userenrolled) > 5 && !isset($_SESSION['isUpdate'])) || (count($userenrolled) + count($bidcart) > 5 && !isset($_SESSION['isUpdate']))) {
    $errormsgarray[] = 'Maximum number of enrolled and biddable modules is 5';
    $disabled = 'disabled';
}
foreach ($bidcart as $anitem) {
    $checkdupcourse[] = $anitem['course'];
}
if (count($checkdupcourse) !== count(array_unique($checkdupcourse))) {
    $errormsgarray[] = 'Can only bid for 1 section per course';
    $disabled = 'disabled';
}
if (empty($bidcart)) {
    $disabled = 'disabled';
}
if (isset($_SESSION['isUpdate'])) {
    $bidded = $_SESSION['bidcart'][0]['amount'];
} else {
    $bidded = 0;
}

$errormsgarray = array_unique($errormsgarray);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Student Checkout</title>

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
    <script>        
        $(document).ready(function(){
            var remaining = <?php echo $_SESSION['edollar'] ?>;
            $('#remaining').text(remaining.toFixed(2))
            $("#checkoutform").on('input', '#bid-total',function(){
                var remaining = <?php echo $_SESSION['edollar'] ?>;
                var bidded = <?php echo $bidded ?>;
                $('#checkoutform #bid-total').each(function(){
                    var eachVal = $(this).val();
                    if($.isNumeric(eachVal)){
                        remaining -= parseFloat(eachVal);
                        remaining += bidded;
                    }
                });
            $('#remaining').text(remaining.toFixed(2));
            if (remaining < 0) {
                $("#submitcart").attr("disabled", true);
                $("#submitcart").html("<i class='fas fa-exclamation-triangle'></i> Insufficient Credit!");
            } else {
                $("#submitcart").attr("disabled", false);
                $("#submitcart").html("<i class='fas fa-shopping-cart'></i> Bid");
            }
        });
    });
    </script>

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
            if (isset($_SESSION['isUpdate'])) {
                echo "
                <h2>Update Bid</h2>";
            } else {
                echo "
                <h2>Checkout</h2>";
            }
            ?>
        </div>
        <div class="row justify-content-center">
            <div class="col-8 col-m-12 border rounded pb-3">
                <?php
                if (count($errormsgarray) > 0) {
                    echo "
                    <div class='alert alert-danger mx-3 mt-3 mb-0' role='alert'>
                        <h4 class='alert-heading'>Cart Error!</h4>
                        <hr>";
                    foreach ($errormsgarray as $anerror) {
                        echo "
                        <p>{$anerror}</p>";
                    }
                    echo "
                    </div>";
                }
                ?>
                <form method="POST" id="checkoutform" action="student-processcheckout.php">
                    <div class="row container-fluid m-0 pt-3">
                        <?php
                        if (empty($_SESSION['bidcart'])) {
                            echo "
                            <div class='container-fluid m-0 p-0 flex text-center border border-danger rounded '>
                                <h4 class='m-0 py-4'>No items in bidding cart</h4>
                            </div>";
                        } else {
                            echo '
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Course</th>
                                        <th>Title</th>
                                        <th>Section</th>
                                        ';
                                        if (isset($_SESSION['isUpdate'])) {
                                            echo '
                                            <th>Old Bid Amount</th>';
                                        }
                                        echo '
                                        <th>Bid Amount</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                    
                                    foreach ($_SESSION['bidcart'] as $abid) {
                                        $name = $abid['course'] . "_" . $abid['section'];
                                        echo "
                                        <tr>
                                            <td>{$abid['course']}</td>
                                            <td>{$abid['title']}</td>
                                            <td>{$abid['section']}</td>
                                            ";
                                            if ($disabled != '') {
                                                echo "
                                                <td>Unable to bid</td>";
                                            }elseif (isset($_SESSION['isUpdate'])) {
                                                echo "
                                                <td>e\${$abid['amount']}</td>
                                                <input type='hidden' id='bidded' value='{$abid['amount']}'>
                                                <td>e$<input type='number' onchange='setTwoNumberDecimal(this)' required min='10' step='0.01' id='bid-total' name='$name' placeholder={$abid['amount']}></td>";
                                            } else {
                                            echo "
                                                <td>e$<input type='number' onchange='setTwoNumberDecimal(this)' required min='10' step='0.01' id='bid-total' name='$name' placeholder=0.00></td>";
                                            }
                                        echo "</tr>";
                                            
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="row container-fluid d-flex flex-row-reverse m-0">
                        <h4>Total Bid Amount: e$<output id="result"></output></h4>
                    </div>
                    <div class="row container-fluid d-flex flex-row-reverse m-0">
                        <h4>Remaining Balance: e$<output id="remaining"></output></h4>
                    </div>
                    <div class="row container-fluid d-flex justify-content-between m-0">
                        <div>
                            <button class="btn" type="button" onclick="location.href='studentbios.php'"><i class="fas fa-arrow-left"></i> Back</button>
                        </div>
                        <div>
                            <button class="btn" <?php echo $disabled ?> type="submit" id='submitcart'><i class="fas fa-shopping-cart"></i> Bid</button>                        
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>