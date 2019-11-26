<?php

require_once 'include/common.php';
require_once 'include/protect.php';

// check if student here
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit;
}
$BiddingRoundDAO = new BiddingRoundDAO;
$BidDAO = new BidDAO;
$SectionDAO = new SectionDAO;
$CourseDAO = new CourseDAO;
$EnrollDAO = new EnrolledDAO;
$StudentDAO = new StudentDAO;



$checkstudent = $StudentDAO->getStudent($_SESSION['userid']);
if ($checkstudent == FALSE) {
    $_SESSION['error'] = 'You do not have permission to view this page!';
    header("Location: login.php");
    exit;
}

# gets active round
$active = $BiddingRoundDAO->activeRound();

# check which school the student is in
$studentsch = $checkstudent->school;

# get all school names
$allschool = $CourseDAO->getAllSchool();

# get student edollar
$_SESSION['edollar'] = $checkstudent->edollar;

# changes default tab to view past bidding round if no active round and has prev round
if (count($BiddingRoundDAO->retrieveAll()) != 0 && !$active) {
    $_SESSION['activetab'] = 'past';
}

# sets default timezone
date_default_timezone_set('Asia/Singapore');

# sets default tab
if (isset($_SESSION['activetab'])) {
    if ($_SESSION['activetab'] == 'details') {
        $activemgmtbid = 'active';
        $activeplanbid = '';
        $activepast = '';
        $activeenrol = '';
    } elseif ($_SESSION['activetab'] == 'planbid') {
        $activeplanbid = 'active';
        $activemgmtbid = '';
        $activepast = '';
        $activeenrol = '';
    } elseif ($_SESSION['activetab'] == 'past') {
        $activepast = 'active';
        $activemgmtbid = '';
        $activeplanbid = '';
        $activeenrol = '';
    } elseif ($_SESSION['activetab'] == 'enrolment') {
        $activeenrol = 'active';
        $activepast = '';
        $activemgmtbid = '';
        $activeplanbid = '';
    };
} else {
    $activeplanbid = 'active';
    $activemgmtbid = '';
    $activepast = '';
    $activeenrol = '';
}
unset($_SESSION['activetab']);
unset($_SESSION['isUpdate']);


# placeholder for all courses
$allcourses = array();

# get student biddable modules and bids
if ($active != FALSE) {
    $studentbidded = $BidDAO->getBidFromUserByRoundNo($_SESSION['userid'], $active->roundNo);
    $biddablecourses = $StudentDAO->getStudentBiddableModule($_SESSION['userid'], $active->roundNo);
    
    if ($biddablecourses != FALSE) { 
        foreach ($biddablecourses as $acourse) {
        $allcourses = array_merge($allcourses, $SectionDAO->getSectionByCourse($acourse->course));
        }
    }
}
foreach ($allcourses as $key => $acourse) {
    $minbid = $SectionDAO->getSection($acourse['course'], $acourse['section'])->minBid;
    $size = $SectionDAO->getSection($acourse['course'], $acourse['section'])->size;
    $vacancy = (float) $size - count($EnrollDAO->getEnrolledFromCourseSection($acourse['course'], $acourse['section']));
    $allcourses[$key]['minbid'] = $minbid;
    $allcourses[$key]['vacancy'] = $vacancy;
}

# used to disable certain buttons when round is not active
$disabled = "";
if ($active == FALSE) {
    $disabled = "disabled";
}

# used to store events for the calendar
$calendarjson = array();

# gets student enroled courses
$enrolledcourses = $EnrollDAO->getEnrolledFromUser($_SESSION['userid']);

# fills in calendarjson
foreach ($enrolledcourses as $acourse) {
    $sectioninfo = $SectionDAO->getSection($acourse->code, $acourse->section);
    $starttime = $sectioninfo->start;
    $endtime = $sectioninfo->end;
    $day = $sectioninfo->day;
    if ($day == '7') {
        $day = '0';
    }
    $acourse = (array) $acourse;
    $acourse['title'] = $acourse['code'] . ' ' . $acourse['section'];
    $calendarjson[] = ['title' => $acourse['title'], 'startTime' => $starttime, 'endTime' => $endtime, 'daysOfWeek' => $day, 'backgroundColor' => '#34bd1e'];
}
if (isset($studentbidded)) {
    foreach ($studentbidded as $acourse) {
        $sectioninfo = $SectionDAO->getSection($acourse->code, $acourse->section);
        $starttime = $sectioninfo->start;
        $endtime = $sectioninfo->end;
        $day = $sectioninfo->day;
        if ($day == '7') {
            $day = '0';
        }
        $acourse = (array) $acourse;
        $acourse['title'] = $acourse['code'] . ' ' . $acourse['section'];
        $calendarjson[] = ['title' => $acourse['title'], 'startTime' => $starttime, 'endTime' => $endtime, 'daysOfWeek' => $day, 'backgroundColor' => '#bd28ba'];
    }
}
$_SESSION['calendarjson'] = json_encode($calendarjson);
$biddingcart = array();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Student Dashboard</title>

    <link rel="Icon" href="images/BIOS.png">
    <meta charset="utf-8">
    <!-- Bootstrap required meta tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- Jquery CDN -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>    
    <!-- Bootstrap Script -->
    <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> -->
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

    <!-- JS script -->
    <script type="text/javascript" src="include/scripts/studentbios.js"></script>

    <script>
        // loads calendar
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            
            var calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: ['interaction', 'dayGrid', 'timeGrid'],
                contentHeight: 'auto',
                height: 'parent',
                defaultView: 'timeGridWeek',
                defaultDate: new Date(),
                minTime: '08:00:00',
                maxTime: '24:00:00',
                allDaySlot: false,
                slotDuration: '01:00:00',
                firstDay: 1,
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: <?php echo $_SESSION['calendarjson'] ?>
            });

            calendar.render();
        });
    </script>

    <!-- Icon Script -->
    <script src="https://kit.fontawesome.com/b1b5f42ae7.js"></script>

    <style>
        <?php require_once('include/styles/studentbios.css') ?>
    </style>
</head>

<body>
    <!--Navbar-->
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
            <li class="nav-item m-auto" id='welcome-nav'>
                Welcome, <?php echo $_SESSION['username'] ?>
                <i class="fas fa-user"></i>
            </li>
        </ul>
        <form action="logout.php">
            <button class="btn" type="submit"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </form>
    </nav>
    <div class="container-fluid d-flex flex-column">
        <div class="row">
            <div class="col-xs-12 col-12">
                <nav>
                    <div class="nav nav-tabs nav-justified" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link <?php echo $activeplanbid ?>" id="nav-planbid-tab" data-toggle="tab" href="#nav-planbid" role="tab" aria-controls="nav-planbid" aria-selected="true">Plan & Bid</a>
                        <a class="nav-item nav-link <?php echo $activemgmtbid ?>" id="nav-mgmtbid-tab" data-toggle="tab" href="#nav-mgmtbid" role="tab" aria-controls="nav-mgmtbid" aria-selected="false">Manage Bids</a>
                        <a class="nav-item nav-link <?php echo $activepast ?>" id="nav-past-tab" data-toggle="tab" href="#nav-past" role="tab" aria-controls="nav-past" aria-selected="false">Past Bidding Results</a>
                        <a class="nav-item nav-link <?php echo $activeenrol ?>" id="nav-enrolment-tab" data-toggle="tab" href="#nav-enrolment" role="tab" aria-controls="nav-enrolment" aria-selected="false">Enrolments</a>
                    </div>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="container-fluid tab-content mx-auto py-3 px-auto px-sm-0" id="nav-tabContent">
                <div class="tab-pane fade show flex-grow-1 <?php echo $activeplanbid ?>" id="nav-planbid" role="tabpanel" aria-labelledby="nav-planbid-tab">
                    <div class="container-fluid">
                        <!-- #region main content row -->
                        <div class="row">
                            <div class="col-lg-6 col-m-12" id="overview-container">
                                <div class="row pl-3">
                                    <?php
                                    if ($active != FALSE) {
                                        $roundstart = $active->start;
                                        $roundstart = new DateTime($roundstart, new DateTimeZone('UTC'));
                                        $roundstart->setTimezone(new DateTimeZone('Asia/Singapore'));
                                        $roundstart = $roundstart->format('d M Y H:i');
                                        echo "
                                        <h2 style='text-align: top'> Bidding Round {$active->roundNo}</h2>
                                        <p style='text-align: bottom'>&nbsp Started on: {$roundstart}</p>";
                                    } else {
                                        echo "
                                        <h2>No Active Bidding Round</h2>";
                                    }
                                    ?>
                                </div>
                                <div class="row pl-3 pr-2">
                                    <!-- #region available courses card -->
                                    <div class="card shadow container-fluid px-0">
                                        <div class="card-header" id='availablecourseHeader'>
                                            <a data-toggle="collapse" href="#availableCourse" role="button" aria-expanded="true" aria-controls="avilableCourse">
                                                Available Courses
                                            </a>
                                        </div>
                                        <div id="availableCourse" class="collapse show" aria-labelledby="availablecourseHeader">
                                            <div class="card-body container-fluid p-0 m-0 overflow-auto">
                                                <div class="row p-0 m-0">
                                                    <nav class="container-fluid nav nav-fill p-0 m-0 navbar-sticky-top">
                                                        <div class="container-fluid nav nav-tabs nav-justified p-0" id="nav-tab" role="tablist">
                                                            <?php
                                                            foreach ($allschool as $aschool) {
                                                                if ($aschool == $studentsch) {
                                                                    echo "
                                                                    <a class='nav-item nav-link active' id='nav-{$aschool}-tab' data-toggle='tab' href='#nav-{$aschool}' role='tab' aria-controls='nav-{$aschool}' aria-selected='true'>{$aschool}</a>
                                                                    ";
                                                                } else {
                                                                    echo "
                                                                    <a class='nav-item nav-link' id='nav-{$aschool}-tab' data-toggle='tab' href='#nav-{$aschool}' role='tab' aria-controls='nav-{$aschool}' aria-selected='false'>$aschool</a>
                                                                    ";
                                                                }
                                                            }
                                                            ?>
                                                            <a class="nav-item nav-link" id="nav-search-tab" data-toggle="tab" href="#nav-search" role="tab" aria-controls="nav-search" aria-selected="false">Search</a>
                                                        </div>
                                                    </nav>
                                                </div>
                                                <!-- #region Available courses tabs -->
                                                <div class="row p-0 m-0 container-fluid tab-content" id="nav-tabContent">
                                                    <?php
                                                    foreach ($allschool as $aschool) {
                                                        if ($aschool == $studentsch) {
                                                            echo "
                                                            <div class='tab-pane fade show flex-grow-1 active' id='nav-{$aschool}' role='tabpanel' aria-labelledby='nav-{$aschool}-tab'>
                                                            ";
                                                        } else {
                                                            echo "
                                                            <div class='tab-pane fade show flex-grow-1' id='nav-{$aschool}' role='tabpanel' aria-labelledby='nav-{$aschool}-tab'>
                                                            ";
                                                        }

                                                        $schoolcourses = array();

                                                        foreach ($allcourses as $acourse) {
                                                            if ($acourse['school'] == $aschool) {
                                                                $schoolcourses[] = $acourse;
                                                            }
                                                        }

                                                        if (!$active || ($active->roundNo == '1' && $studentsch != $aschool || count($schoolcourses) == 0)) {
                                                            echo "<h4 class='p-3'>No courses available</h4>";
                                                        } else {?>
                                                            <table class='table p-0 m-0'>
                                                                <thead style="background-color: #C69200">
                                                                    <tr>
                                                                        <th scope="col" style="text-align:center;">Pin</th>
                                                                        <th scope="col" style="text-align:center;">Code</th>
                                                                        <th scope="col" style="text-align:center;">Title</th>
                                                                        <th scope="col" style="text-align:center;">Section</th>
                                                                        <th scope="col" style="text-align:center;">Day</th>
                                                                        <th scope="col" style="text-align:center;">Time</th>
                                                                        <th scope="col" style="text-align:center;">Vacancy</th>
                                                                        <th scope="col" style="text-align:center;">Min Bid</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    foreach ($schoolcourses as $key => $value) {
                                                                        $id = $value['course'] . $value['section'];
                                                                        $acourse = json_encode($value);
                                                                        $day = date('D', strtotime("Sunday +{$value['day']} days"));
                                                                        $starttime = date('H:i', strtotime($value['start']));
                                                                        $endtime = date('H:i', strtotime($value['end']));
                                                                        echo "
                                                                        <tr>                                                                        
                                                                            <td><form><input type='checkbox' class='form-check-input position-static m-0' value='$acourse'></form></td>
                                                                            <td>{$value['course']}</td>
                                                                            <td>{$value['title']}</td>
                                                                            <td>{$value['section']}</td>
                                                                            <td>{$day}</td>
                                                                            <td>{$starttime}-{$endtime}</td>
                                                                            <td>{$value['vacancy']}</td>
                                                                            <td>{$value['minbid']}</td>
                                                                        </tr>
                                                                        ";
                                                                    }
                                                                    ?>
                                                                </tbody>
                                                            </table>
                                                            <?php } ?>
                                                        </div>
                                                    <?php
                                                    }
                                                    ?>                                                    
                                                    <!-- #region Others Tab -->
                                                    <div class="tab-pane fade show flex-grow-1" id="nav-search" role="tabpanel" aria-labelledby="nav-search-tab">
                                                        <?php
                                                        if (!$active) {
                                                            echo "<h4 class='p-3'>No courses available</h4>";
                                                        } else {?>
                                                        <div class="row container-fluid d-flex justify-content-around py-2 px-0 m-0">
                                                        <input type="text" id="searchCourse" onkeyup="searchCourse()" placeholder="Search course code..">                                                        
                                                        </div>
                                                        <table class='table p-0 m-0' id='searchTable'>
                                                            <thead style="background-color: #ff8a8a">
                                                                <tr>
                                                                    <th scope="col" style="text-align:center;">Pin</th>
                                                                    <th scope="col" style="text-align:center;">Code</th>
                                                                    <th scope="col" style="text-align:center;">Title</th>
                                                                    <th scope="col" style="text-align:center;">Section</th>
                                                                    <th scope="col" style="text-align:center;">Day</th>
                                                                    <th scope="col" style="text-align:center;">Time</th>
                                                                    <th scope="col" style="text-align:center;">Vacancy</th>
                                                                    <th scope="col" style="text-align:center;">Min Bid</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                foreach ($allcourses as $key => $value) {
                                                                    $id = $value['course'] . $value['section'];
                                                                    $acourse = json_encode($value);
                                                                    $day = date('D', strtotime("Sunday +{$value['day']} days"));
                                                                    $starttime = date('H:i', strtotime($value['start']));
                                                                    $endtime = date('H:i', strtotime($value['end']));
                                                                    echo "
                                                                    <tr>                                                                        
                                                                        <td><form><input type='checkbox' class='form-check-input position-static m-0' value='$acourse'></form></td>
                                                                        <td>{$value['course']}</td>
                                                                        <td>{$value['title']}</td>
                                                                        <td>{$value['section']}</td>
                                                                        <td>{$day}</td>
                                                                        <td>{$starttime}-{$endtime}</td>
                                                                        <td>{$value['vacancy']}</td>
                                                                        <td>{$value['minbid']}</td>
                                                                    </tr>
                                                                    ";
                                                                }
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                        <?php } ?>
                                                    </div>
                                                    <!-- #endregion -->
                                                </div>
                                                <!-- #endregion -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card shadow container-fluid px-0">
                                        <div class="card-header" id='biddingCartHeader'>
                                            <a data-toggle="collapse" href="#biddingcart" role="button" aria-expanded="true" aria-controls="biddingCart">
                                                Bidding Cart
                                            </a>
                                            <div class="d-inline-flex float-right">
                                                <!-- <form action='studentcheckout.php'> -->
                                                <button class="btn m-0 py-0 px-1" <?= $disabled ?> type="button" onclick="checkout()" id="checkoutbtn"><i class="fas fa-shopping-cart"></i> Checkout</button>
                                                <!-- </form> -->
                                            </div>
                                        </div>
                                        <div class="collapse show" id="biddingcart" class="collapse" aria-labelledby="biddingCartHeader">
                                            <h6 class="p-3">Bidding Cart Empty</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-m-12 pl-2" id="overview-container">
                                <div class="card shadow">
                                    <div class="card-header">
                                        Timetable (<i class="fas fa-square" style="color: #34bd1e"></i> Enrolled, <i class="fas fa-square" style="color: #bd28ba"></i> Bidded)
                                    </div>
                                    <div class="card-body overflow-auto p-0" id="calendarbody">
                                        <div id="calendar"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade show flex-grow-1 <?php echo $activemgmtbid ?>" id="nav-mgmtbid" role="tabpanel" aria-labelledby="nav-mgmtbid-tab">
                    <div class="container-fluid">
                        <!-- #region main content row -->
                        <div class="row px-3">
                            <?php
                            if ($active != FALSE) {
                                echo "
                                <h2 style='text-align: top'> Bidding Round {$active->roundNo}</h2>
                                <p style='text-align: bottom'>&nbsp Started on: {$roundstart}</p>";
                            } else {
                                echo "
                                <h2>No Active Bidding Round</h2>";
                            }
                            ?>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-8 col-m-12 border rounded py-3">
                                <div class="row justify-content-center">
                                    <h3>Placed Bids</h3>
                                </div>
                                <div class="row container-fluid m-0 py-3">
                                    <?php
                                    if (isset($_SESSION['deletebidresult'])) {
                                        ?>
                                        <div class="alert alert-info container-fluid" role="alert">
                                        <?php
                                            if ($_SESSION['deletebidresult']['delete']['status'] == 'success') {
                                                echo "
                                                <h4>Bid successfully deleted</h4>
                                                <hr>";
                                                $refundamt = ($_SESSION['deletebidresult']['biddetails'])->amount;
                                                echo "
                                                <p>e\$$refundamt has been refunded into your wallet.</p>
                                                ";
                                            } else {
                                                echo "
                                                <h4>Failed to delete bid</h4>
                                                <hr>";
                                                foreach ($_SESSION['deletebidresult']['delete']['message'] as $amessage) {
                                                    echo "
                                                    <p>{$amessage}</p>";
                                                }
                                            }
                                            ?>
                                        </div>
                                    <?php
                                    }
                                        unset($_SESSION['deletebidresult']);
                                        if ($active == FALSE) {
                                            echo "
                                            <div class='container-fluid m-0 p-0 flex text-center border border-danger rounded '>
                                                <h4 class='m-0 py-4'>Bidding round is inactive.</h4>
                                            </div>";
                                        } else {
                                            if (empty($studentbidded)) {
                                                echo "
                                                <div class='container-fluid m-0 p-0 flex text-center border border-danger rounded '>
                                                    <h4 class='m-0 py-4'>No bids placed.</h4>
                                                </div>";
                                            } else {
                                                echo '
                                                <table class="table table-bordered">
                                                    <thead class="thead-light text-center">
                                                        <tr>
                                                            <th>Course</th>
                                                            <th>Title</th>
                                                            <th>Section</th>
                                                            ';
                                                if ($active->roundNo == '2') {
                                                    echo '
                                                            <th>Min Bid</th>
                                                            <th>Vacancy</th>';
                                                }
                                                echo '
                                                            <th>Bid Amount</th>
                                                            <th>Status</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>';
                                                foreach ($studentbidded as $abid) {                                            
                                                    $biddetails = json_encode($abid);
                                                    $code = $abid->code;
                                                    $title = $CourseDAO->getCourse($code)->title;
                                                    $section = $abid->section;
                                                    $amount = $abid->amount;
                                                    $status = $abid->status;
                                                    if ($status == 'pending') {
                                                        $status = '<td style="background-color: #fff3cd">Pending</td>';
                                                    } elseif ($status == 'success') {
                                                        $status = '<td style="background-color: #d4edda">Success</td>';
                                                    } else {
                                                        $status = '<td style="background-color: #f8d7da">Fail</td>';
                                                    }
                                                    echo "
                                                            <form method='POST' id='biddetailsform' action='student-processbid.php'>
                                                                <input type='hidden' name='biddetails' value={$biddetails}>
                                                                <tr>
                                                                    <td>{$code}</td>
                                                                    <td>{$title}</td>
                                                                    <td>{$section}</td>
                                                    ";
                                                    if ($active->roundNo == '2') {
                                                        $minbid = $SectionDAO->getSection($code, $section)->minBid;
                                                        $size = $SectionDAO->getSection($code, $section)->size;
                                                        $vacancy = (float) $size - count($EnrollDAO->getEnrolledFromCourseSection($code, $section));
                                                        echo "
                                                                    <td>{$minbid}</td>
                                                                    <td>{$vacancy}</td>";
                                                    }
                                                    echo "
                                                                    <td>{$amount}</td>
                                                                    {$status}
                                                                    <td>";
                                                    ?>
                                                    <button class="btn btn-warning py-0" type="submit" id='updatebid' name='updatebid'><i class="fas fa-pen"></i> Update</button>
                                                    <button class="btn btn-danger py-0" type="submit" id='deletebid' name='deletebid' onclick="return confirm('Are you sure you want to delete?\nThis action cannot be undone!');"><i class="fas fa-trash-alt"></i> Delete</button>
                                                    <?php
                                                    echo "</td>
                                                        </tr>
                                                    </form>";
                                                }
                                            }
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade show flex-grow-1 <?php echo $activepast ?>" id="nav-past" role="tabpanel" aria-labelledby="nav-past-tab">
                    <div class="container-fluid">
                        <!-- #region main content row -->
                        <div class="row px-3">
                                <h2 style='text-align: top'> View Past Bidding Results</h2>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-8 col-m-12 border rounded py-3">
                                <div class="row justify-content-center">
                                    <?php
                                    if ($BiddingRoundDAO->getLastRound() == '' || ($active && $BiddingRoundDAO->getLastRound() == '1')) {
                                        echo '
                                        <h3>No Past Bidding Rounds</h3>
                                        ';
                                        $lastround = 0;
                                    } else {
                                        if ($active) {
                                            $lastround = $BiddingRoundDAO->getLastRound() - 1;
                                        } else {
                                            $lastround = $BiddingRoundDAO->getLastRound();
                                        }
                                        
                                        echo "
                                        <h3>Round {$lastround} Bidding Results</h3>
                                        ";
                                    
                                    ?>
                                </div>
                                <div class="row container-fluid m-0 py-3">
                                <?php
                                $roundresults = $BidDAO->getBidFromUserByRoundNo($_SESSION['userid'], $lastround);
                                if (empty($roundresults)) {
                                    echo "
                                    <div class='container-fluid m-0 p-0 flex text-center border border-danger rounded '>
                                        <h4 class='m-0 py-4'>No bids placed during previous round.</h4>
                                    </div>";
                                } else {
                                    echo '
                                    <table class="table table-bordered ">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Course</th>
                                                <th>Title</th>
                                                <th>Section</th>
                                                <th>Bid Amount</th>
                                                <th>Result</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
                                    foreach ($roundresults as $abid) {
                                            $code = $abid->code;
                                            $title = $CourseDAO->getCourse($code)->title;
                                            $section = $abid->section;
                                            $amount = $abid->amount;
                                            $status = $abid->status;
                                            if ($status == 'pending') {
                                                $status = '<td style="background-color: #fff3cd">Pending</td>';
                                            } elseif ($status == 'success') {
                                                $status = '<td style="background-color: #d4edda">Success</td>';
                                            } else {
                                                $status = '<td style="background-color: #f8d7da">Fail</td>';
                                            }
                                            echo "
                                            <tr>
                                                <td>{$code}</td>
                                                <td>{$title}</td>
                                                <td>{$section}</td>
                                                <td>{$amount}</td>
                                                {$status}
                                            </tr>
                                            ";
                                        }
                                    }
                                ?>
                                        </tbody>
                                    </table>                                
                                <?php
                                };
                                ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade show flex-grow-1 <?php echo $activeenrol ?>" id="nav-enrolment" role="tabpanel" aria-labelledby="nav-enrolment-tab">
                    <div class="container-fluid">
                        <!-- #region main content row -->
                        <div class="row px-3">
                                <h2 style='text-align: top'>Current Enrolment</h2>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-8 col-m-12 border rounded py-3">
                                <div class="row justify-content-center">
                                    <h3>Enrolments</h3>
                                </div>
                                <div class="row container-fluid m-0 py-3">
                                <?php
                                if (isset($_SESSION['deleteresult'])) {
                                    ?>
                                    <div class="alert alert-info container-fluid" role="alert">
                                        <?php
                                        if ($_SESSION['deleteresult']['result']['status'] == 'error' && $_SESSION['deleteresult']['result']['message'] == ['no such enrollment record']) {
                                            echo "
                                            <h4>Failed to drop module</h4>
                                            <hr>
                                            ";
                                        } else {
                                            echo "
                                            <h4>Module dropped successfully</h4>
                                            <hr>
                                            ";
                                            if ($_SESSION['deleteresult']['result']['status'] == 'success') {
                                                $refundamt = ($_SESSION['deleteresult']['biddetails'])->amount;
                                                echo "
                                                <p>e\$$refundamt has been refunded into your wallet.</p>
                                                ";
                                            } else {
                                                echo "
                                                <p>Failed to refund wallet.</p>";
                                            }
                                        }
                                        ?>
                                        </div>
                                        <?php
                                    unset($_SESSION['deleteresult']);
                                }
                                if (empty($enrolledcourses)) {
                                    echo "
                                    <div class='container-fluid m-0 p-0 flex text-center border border-danger rounded '>
                                        <h4 class='m-0 py-4'>No enrolled courses.</h4>
                                    </div>";
                                } else {
                                    echo '
                                    <table class="table table-bordered ">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Course</th>
                                                <th>Title</th>
                                                <th>Section</th>
                                                <th>Bid Amount</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
                                    foreach ($enrolledcourses as $acourse) {
                                        $coursedetails = json_encode($acourse);
                                        $code = $acourse->code;
                                        $title = $CourseDAO->getCourse($code)->title;
                                        $section = $acourse->section;
                                        $amount = $acourse->amount;                                        
                                        echo "
                                                <form method='POST' id='biddetailsform' action='student-processdelete.php'>
                                                    <input type='hidden' name='biddetails' value={$coursedetails}>
                                                    <tr>
                                                        <td>{$code}</td>
                                                        <td>{$title}</td>
                                                        <td>{$section}</td>
                                                        <td>{$amount}</td>
                                                        <td>";
                                        ?>
                                        <button class="btn btn-danger py-0" type="submit" id='dropcourse' name='dropcourse' <?php echo $disabled ?> onclick="return confirm('Are you sure you want to drop this section?\nThis action cannot be undone!');"><i class="fas fa-trash-alt"></i> Drop Section</button>
                                        <?php
                                        if ($disabled != '') {
                                            echo "
                                            <br><small>Only available during active round!</small>";
                                        }
                                                echo "</td>
                                                    </tr>
                                                </form>";
                                    }
                                }
                                ?>
                                        </tbody>
                                    </table>
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