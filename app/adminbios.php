<?php

// Importing required files
require_once 'include/common.php';
// require_once 'include/protect.php';

// Verifying that an admin is logged in, else redirect to login page
if ($_SESSION['userid'] != 'admin') {
    $_SESSION['error'] = 'You do not have permission to view this page!';
    header("Location: login.php");
    exit;
}

// set timezone
date_default_timezone_set('Asia/Singapore');

// checking which tab to default to
if (isset($_SESSION['activetab'])) {
    if ($_SESSION['activetab'] == 'roundmgmt') {
        $activeround = 'active';
        $activehome = '';
        $activebid = '';
        $activebootstrap = '';
    } elseif ($_SESSION['activetab'] == 'home') {
        $activehome = 'active';
        $activeround = '';
        $activebid = '';
        $activebootstrap = '';
    } elseif ($_SESSION['activetab'] == 'bid') {
        $activebid = 'active';
        $activehome = '';
        $activeround = '';
        $activebootstrap = '';
    } elseif ($_SESSION['activetab'] == 'bootstrap') {
        $activebootstrap = 'active';
        $activeround = '';
        $activebid = '';
        $activehome = '';
    };
} else {
    $activehome = 'active';
    $activeround = '';
    $activebid = '';
    $activebootstrap = '';
}

unset($_SESSION['activetab']);

// BiddingRoundDAO
$BiddingRoundDAO = new BiddingRoundDAO();
$allrounds = array();

?>

<!DOCTYPE html>
<html lang="en">

<!-- #region header -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Dashboard</title>

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

    <!-- Icon Script -->
    <script src="https://kit.fontawesome.com/b1b5f42ae7.js"></script>

    <script>
        $('#uploadbtn').on('click', function() {
            var $this = $(this);
            $this.button('loading');
        });
    </script>

    <!-- Page specific css -->
    <style>
        <?php require_once('include/styles/admin.css') ?>
    </style>
</head>
<!-- #endregion -->

<!-- #region body -->

<body>

    <!--Navbar-->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <!-- Navbar logo and header -->
        <a class="navbar-brand" href="adminbios.php">
            <img src="images/MUbios.png" alt="BIOS" height="60" class="d-inline-block align-top">
            <h1 class="d-inline">Admin Dashboard</h1>
        </a>
        <ul class="navbar-nav flex-row ml-md-auto d-none d-md-flex">
            <li class="nav-item">
                Welcome, Admin
                <i class="fas fa-user"></i>
            </li>
        </ul>
        <form action="logout.php">
            <button class="btn" type="submit" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </form>
    </nav>

    <!-- Main tabs -->
    <div class="container-fluid d-flex flex-column">
        <div class="row">
            <div class="col-xs-12 col-12">
                <nav>
                    <div class="nav nav-tabs nav-justified" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link <?php echo $activehome ?>" id="nav-overview-tab" data-toggle="tab" href="#nav-overview" role="tab" aria-controls="nav-overview" aria-selected="true">Overview</a>
                        <a class="nav-item nav-link <?php echo $activeround ?>" id="nav-roundmgmt-tab" data-toggle="tab" href="#nav-roundmgmt" role="tab" aria-controls="nav-roundmgmt" aria-selected="false">Round Management</a>
                        <a class="nav-item nav-link <?php echo $activebid ?>" id="nav-bidmgmt-tab" data-toggle="tab" href="#nav-bidmgmt" role="tab" aria-controls="nav-bidmgmt" aria-selected="false">Bid Management</a>
                        <a class="nav-item nav-link <?php echo $activebootstrap ?>" id="nav-Bootstrap-tab" data-toggle="tab" href="#nav-bootstrap" role="tab" aria-controls="nav-bootstrap" aria-selected="false">Bootstrap</a>                        
                    </div>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="container-fluid tab-content mx-auto py-3 px-auto px-sm-0" id="nav-tabContent">
                <div class="tab-pane fade show flex-grow-1 <?php echo $activehome ?>" id="nav-overview" role="tabpanel" aria-labelledby="nav-overview-tab">
                    <div class="container-fluid">
                        <div class="row pl-3">
                            <h2>Admin Overview</h2>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-m-6 col-sm-12" id="overview-container">
                                <div class="card shadow overflow-auto">
                                    <div class="card-header">
                                        Active Bidding Round
                                    </div>
                                    <div class="card-body">
                                        <?php
                                        $active = $BiddingRoundDAO->activeRound();

                                        if ($active == FALSE) {
                                            echo "<h5>No active bidding round</h5>
                                                <p>Go to Round Management to start a new bidding round.</p>";
                                        } else {
                                            $activedate = $active->start;
                                            $activename = $active->roundNo;

                                            $activedate = new DateTime($activedate, new DateTimeZone('UTC'));
                                            $activedate->setTimezone(new DateTimeZone('Asia/Singapore'));

                                            echo "
                                                <h5>Round {$activename}</h5>
                                                <p>Started on: {$activedate->format('d M Y H:i')}</p>";
                                        };

                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-m-6 col-sm-12" id="overview-container">
                                <div class="card shadow overflow-auto">
                                    <div class="card-header">
                                        Past Bidding Rounds
                                    </div>
                                    <div class="card-body">
                                        <?php
                                        $all = $BiddingRoundDAO->retrieveAll();

                                        if ($all == FALSE) {
                                            if (isset($_SESSION['removeall'])) {
                                                if ($_SESSION['removeall'] === FALSE) {
                                                    echo "
                                                    <div class='alert alert-danger' role='alert' id='alert'>
                                                        Delete failed!
                                                    </div>";
                                                } else {
                                                    echo "
                                                    <div class='alert alert-success' role='alert' id='alert'>
                                                        Deleted all rounds successfully!
                                                    </div>";
                                                };
                                                unset($_SESSION['removeall']);
                                            };
                                            echo "<h5>No previous/active bidding round found</h5>";
                                        } else {

                                            if ($active == false) {
                                                echo "
                                                    <form action='admin-process.php' method='POST'>
                                                        <input type='submit' name='removeall' id='removeall' value='Delete all'>
                                                    </form><br>";
                                            } else {
                                                echo "
                                                    <form action='admin-process.php' method='POST'>
                                                        <input type='submit' name='removeall' id='removeall' value='Delete all' disabled>
                                                        End active round first!
                                                    </form><br>";
                                            }
                                            foreach ($all as $a_round) {

                                                $roundno = $a_round->roundNo;
                                                $allrounds[] = $roundno;
                                                $roundstart = $a_round->start;
                                                $roundstart = new DateTime($roundstart, new DateTimeZone('UTC'));
                                                $roundstart->setTimezone(new DateTimeZone('Asia/Singapore'));
                                                $roundstart = $roundstart->format('d M Y H:i');
                                                $roundend = $a_round->end;
                                                if ($roundend == NULL) {
                                                    $roundend = 'Active Round.';
                                                } else {
                                                    $roundend = new DateTime($roundend, new DateTimeZone('UTC'));
                                                    $roundend->setTimezone(new DateTimeZone('Asia/Singapore'));
                                                    $roundend = $roundend->format('d M Y H:i');
                                                }

                                                echo "
                                                    <h5>Round {$roundno}</h5>
                                                    Started on: {$roundstart}<br>
                                                    Ended on: {$roundend}<br><br>";
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade show flex-grow-1 <?php echo $activeround ?>" id="nav-roundmgmt" role="tabpanel" aria-labelledby="nav-roundmgmt-tab">
                    <div class="container-fluid">
                        <div class="row pl-3">
                            <h2>Round Management</h2>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-m-6 col-sm-12" id="overview-container">
                                <div class="card shadow overflow-auto">
                                    <div class="card-header">
                                        Round options
                                    </div>
                                    <div class="card-body">
                                        <?php  //for start round and end round button
                                        $active = $BiddingRoundDAO->activeRound();
                                        if (isset($_SESSION['isEndOK'])) {
                                            echo "
                                                <div class='alert alert-success' role='alert' id='alert'>
                                                    Round ended successfully!
                                                </div>";
                                            unset($_SESSION['isEndOK']);
                                            if (isset($_SESSION['clearingresult']) && $_SESSION['clearingresult']) { //clearing result is success and fail result
                                                echo "
                                                <div class='alert alert-success' role='alert' id='alert'>
                                                    Round 1 cleared successfully!
                                                </div>";
                                            } elseif (isset($_SESSION['clearingresult'])) {
                                                echo "
                                                <div class='alert alert-danger' role='alert' id='alert'>
                                                    Round 1 clearing failed.
                                                </div>";
                                            }
                                            unset($_SESSION['clearingresult']);
                                            if (isset($_SESSION['enrolresult']) && $_SESSION['enrolresult']) {
                                                echo "
                                                <div class='alert alert-success' role='alert' id='alert'>
                                                    Students enroled successfully!
                                                </div>";
                                            } elseif (isset($_SESSION['enrolresult'])) {
                                                echo "
                                                <div class='alert alert-danger' role='alert' id='alert'>
                                                    Students enrolment failed.
                                                </div>";
                                            }
                                            unset($_SESSION['enrolresult']);
                                        };
                                        if (isset($_SESSION['isStartOK'])) { //if round started
                                            echo "
                                                <div class='alert alert-success' role='alert' id='alert'>
                                                    Round started successfully!
                                                </div>";
                                            unset($_SESSION['isStartOK']);
                                        };
                                        if ($active == FALSE) { //no active round
                                            echo "
                                                <h5>Start New Round</h5>
                                                <form action='admin-process.php' method='POST'>
                                                Round Number:
                                                    <select name='roundNo' id='roundNo'>
                                            ";
                                            for ($i=1; $i<=2; $i++) {
                                                if (!(in_array($i, $allrounds))) {
                                                    echo "
                                                    <option value='{$i}'>Round {$i}</option>
                                                    ";
                                                };
                                            }
                                            echo "
                                                    </select>
                                                    <input type='submit' name='startround' id='startround' value='Start Round'>
                                                </form>";
                                        } else {
                                            $_SESSION['roundNo'] = $active->roundNo;
                                            echo "
                                                <h5>End Active Round</h5>
                                                Ending active round will start the clearing process automatically.
                                                <form action='admin-process.php' method='POST'>                                                
                                                    <input type='submit' name='endround' id='endround' value='End Round'>
                                                </form>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-m-6 col-sm-12" id="overview-container">
                                <div class="card shadow overflow-auto">
                                    <div class="card-header">
                                        Active Bidding Round
                                    </div>
                                    <div class="card-body">
                                        <?php
                                        $active = $BiddingRoundDAO->activeRound();

                                        if ($active == FALSE) {
                                            echo "<h5>No active bidding round</h5>
                                                <p>Select a new bidding round number and select 'Start Round'.</p>";
                                        } else {
                                            $activedate = $active->start;
                                            $activename = $active->roundNo;

                                            $activedate = new DateTime($activedate, new DateTimeZone('UTC'));
                                            $activedate->setTimezone(new DateTimeZone('Asia/Singapore'));

                                            echo "
                                                <h5>Round {$activename}</h5>
                                                <p>Started on: {$activedate->format('d M Y H:i')}</p>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade show flex-grow-1" id="nav-bidmgmt" role="tabpanel" aria-labelledby="nav-bidmgmt-tab">
                    <div class="container-fluid">
                        <div class="row pl-3">
                            <h2>Bid Management</h2>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-8 col-m-12 border rounded py-3">
                                <div class="row justify-content-center">
                                    <h3>Placed Bids</h3>
                                </div>
                                <div class="row container-fluid m-0 py-3">
                                    <?php
                                    $BidDAO = new BidDAO;
                                    $allbids = $BidDAO->retrieveAll();                                    
                                    ?>
                                    <table class="table table-bordered">
                                        <thead class="thead-light text-center">
                                            <tr>
                                                <th>Userid</th>
                                                <th>Amount</th>
                                                <th>Course</th>
                                                <th>Section</th>
                                                <th>Status</th>
                                                <th>Round No.</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach ($allbids as $abid) {
                                            echo "
                                            <tr>
                                                <td>{$abid->userid}</td>
                                                <td>{$abid->amount}</td>
                                                <td>{$abid->code}</td>
                                                <td>{$abid->section}</td>
                                                <td>{$abid->status}</td>
                                                <td>{$abid->roundNo}</td>
                                            </tr>
                                            ";
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade show flex-grow-1 <?php echo $activebootstrap ?>" id="nav-bootstrap" role="tabpanel" aria-labelledby="nav-bootstrap-tab">
                    <div class="container-fluid">
                        <div class="row pl-3">
                            <h2>Bootstrap</h2>
                        </div>
                        <div class="row flex-grow-1">
                            <div class="col-lg-6 col-m-12 col-sm-12 " id="overview-container">
                                <div class="card shadow overflow-auto">
                                    <div class="card-header">
                                        Do Bootstrap
                                    </div>
                                    <div class="card-body">
                                        <form id='bootstrap-form' action="bootstrap-process.php" method="POST" enctype="multipart/form-data">
                                            Upload Bootstrap file (.zip) here:<br><br>
                                            <input id='bootstrap-file' type="file" accept=".zip" name="bootstrap-file" required="required"><br><br>
                                            <input type="hidden" name='adminpage' value='TRUE'>
                                            <button type="submit" class="btn" name='Upload' id='uploadbtn' data-loading-text="<span class='spinner-border spinner-border-sm'></span> Processing Bootstrap"><i class="fas fa-upload"></i></i> Upload and Bootstrap</button>
                                        </form>
                                        
                                        <small>This action cannot be undone!</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-m-12 col-sm-12 flex-grow-1" id="overview-container">
                                <div class="card shadow overflow-auto">
                                    <div class="card-header">
                                        Bootstrap Status
                                    </div>
                                    <div class="card-body">
                                        <?php

                                        if (!isset($_SESSION['bootstrap'])) {
                                            echo "
                                                <h4>No Bootstrap Status Found</h4>
                                                <div class='alert alert-warning' role='alert' id='alert'>
                                                    <h5 class='alert-heading'>Bootstrapping will result in the following:</h5>
                                                    <hr>
                                                    <ul>
                                                        <li>All rounds will be deleted, regardless of bootstrap result</li>
                                                        <li>Round 1 will start automatically, regardless of bootstrap result</li>
                                                        <li>Database will be overwritten with bootstrap data</li>
                                                    </ul>
                                                </div>";
                                        } else {
                                            $bootstrapresult = $_SESSION['bootstrap'];
                                            $status = $bootstrapresult['status'];
                                            if ($status == 'success') {
                                                echo "
                                                    <div class='alert alert-success' role='alert' id='alert'>
                                                    <h5 class='alert-heading'>Bootstrap Successful!</h5>
                                                    <hr>";
                                                if ($bootstrapresult['bidtable']) {
                                                    echo "
                                                    <p><i class='fas fa-check'></i> Bidding Round table successfully cleared</p>";
                                                } else {
                                                    echo "
                                                    <p><i class='fas fa-times'></i> Failed to clear Bidding Round table</p>";
                                                }
                                                if ($bootstrapresult['isStartOK']) { //if round 1 start
                                                    echo "
                                                    <hr>
                                                    <p><i class='fas fa-check'></i> Round 1 started successfully</p>";
                                                } else {
                                                    echo "
                                                    <hr>
                                                    <p><i class='fas fa-times'></i> Failed to start Round 1</p>";
                                                }
                                                echo "                                                    
                                                    <hr>
                                                    <p>Files processed:
                                                    ";
                                                foreach ($bootstrapresult['num-record-loaded'] as $afile) {
                                                    foreach ($afile as $key => $value) {
                                                        echo "
                                                        <br><b>{$value}</b> records loaded from <b>{$key}</b>.";
                                                    }
                                                }
                                                echo "</div>";
                                            } else {
                                                echo "
                                                    <div class='alert alert-danger' role='alert' id='alert'>
                                                    <h5 class='alert-heading'>Bootstrap Failed!</h5>
                                                    <hr>";
                                                if ($bootstrapresult['bidtable']) {
                                                    echo "
                                                    <p><i class='fas fa-check'></i> Bidding Round table successfully cleared</p>";
                                                } else {
                                                    echo "
                                                    <p><i class='fas fa-times'></i> Failed to clear Bidding Round table</p>";
                                                }
                                                if ($bootstrapresult['isStartOK']) {
                                                    echo "
                                                    <hr>
                                                    <p><i class='fas fa-check'></i> Round 1 started successfully</p>";
                                                } else {
                                                    echo "
                                                    <hr>
                                                    <p><i class='fas fa-times'></i> Failed to start Round 1</p>";
                                                }
                                                echo "
                                                    <hr>
                                                    <p>Error Messages:
                                                    ";
                                                foreach ($bootstrapresult['error'] as $anerror) {
                                                    echo "
                                                        <br>Line <b>{$anerror['line']}</b> of file <b>'{$anerror['file']}'</b>: ";
                                                    $message_string = '';
                                                    foreach ($anerror['message'] as $amessage) {
                                                        $message_string .= $amessage;
                                                        $message_string .= ', ';
                                                    }
                                                    $message_string =   rtrim($message_string, ', ');
                                                    echo $message_string;
                                                }
                                                echo "
                                                    <hr>
                                                    <p>Files processed:
                                                    ";
                                                foreach ($bootstrapresult['num-record-loaded'] as $afile) {
                                                    foreach ($afile as $key => $value) {
                                                        echo "
                                                            <br><b>{$value}</b> records loaded from <b>{$key}</b>.";
                                                    }
                                                }
                                                echo "</div>";
                                            }
                                            unset($_SESSION['bootstrap']);
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                
            </div>
        </div>
    </div>
</body>

</html>