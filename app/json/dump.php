<?php
require_once '../include/common.php';
// require_once '../include/protect_json.php';

$courseDAO = new CourseDAO();
$sectionDAO = new SectionDAO();
$studentDAO = new StudentDAO();
$prerequisiteDAO = new PrerequisiteDAO();
$bidDAO = new BidDAO();
$biddingRound = new BiddingRoundDAO();
$courseCompletedDAO = new CourseCompletedDAO();
$enrolledDAO = new EnrolledDAO();

$all_course = $courseDAO->getAllCourse(); 
$course = [];

//--dump course--
//1. Alphabetical order of the course prefix (eg. ACCTxxx, ECONxxx, ISxxx)
//2. Numerical order of the course code. eg (IS100, IS101, IS200, IS306)
foreach ($all_course as $courses){
    $examDate = $courses->examDate;
    $newExamDate = date("Ymd", strtotime($examDate));
    $examStart = strtotime($courses->examStart);
    $newExamStart = date ('Gi', $examStart); 
    $examEnd = strtotime($courses->examEnd);
    $newExamEnd = date ('Gi', $examEnd);
    $course[] = array('course' => $courses->course, 'school' => $courses->school, 'title' => $courses->title, 'description' => $courses->description,  'exam date' => $newExamDate, 'exam start' => $newExamStart, 'exam end' => $newExamEnd);
}

//--dump section--
//1. Order of the course code.
//2. Numerical order of the section number. eg (S01, S02... S09, S10, S11)
$all_section = $sectionDAO->retrieveAll();
$section = [];
$dayofweek = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday','Sunday');
foreach ($all_section as $sections){
    $start = strtotime($sections->start);
    $newStart = date ('Gi', $start);
    $end = strtotime($sections->end);
    $newEnd = date ('Gi', $end); 
    $section[] = array('course' => $sections->course, 'section' => $sections->section, 'day' => $dayofweek[(int) $sections->day], 'start' => $newStart, 'end' => $newEnd, 'instructor' => $sections->instructor, 'venue' => $sections->venue, 'size' => (int) $sections->size);
}

//--dump student--
//1. Alphabetical order of the userid
$all_student = $studentDAO->retrieveAll();
$student = [];
foreach ($all_student as $students){
    $student[] = array('userid' => $students->userid, 'password' => $students->password, 'name' => $students->name, 'school' => $students->school, 'edollar' => (float) $students->edollar);
}

//--dump prerequisite--
//1. Order of the course code.
//2. Order of the prerequisite code.
$all_prerequisite = $prerequisiteDAO->retrieveAll();
$prerequisite = [];
foreach ($all_prerequisite as $prerequisites){
    $prerequisite[] = array('course' => $prerequisites->course, 'prerequisite' => $prerequisites->prerequisite);
}

//--dump bid--
//1. Order of the course code.
//2. order of the section code (S1, S2, S3 ..)
//3. Highest bid to Lowest bid
//4. username
$roundNo = $biddingRound->getCurrentRound()->roundNo;
$all_bid = $bidDAO->retrieveAllByRound($roundNo);
$bid = [];
foreach ($all_bid as $bids){
    $bid[] = array('userid' => $bids->userid, 'amount' => (float) $bids->amount, 'course' => $bids->code, 'section' => $bids->section);
}

//--dump courseCompleted--
//1. Order of the course code.
//2. Alphabetical order of the userid.
$all_course_completed = $courseCompletedDAO->retrieveAll();
$courseCompleted = [];
foreach ($all_course_completed as $courseCompleteds){
    $courseCompleted[] = array('userid' => $courseCompleteds->userid, 'course' => $courseCompleteds->code);
}

//--dump enrolled--
//1. Order of the course code.
//2. Alphabetical order of the students' userid
$recentRound = $biddingRound->getCurrentRound();

if($recentRound->roundNo == 2 && $recentRound->active == 0) {
    $all_enrolled = $enrolledDAO->getEnrolledByRoundNo($recentRound->roundNo);
    $enrolled = [];
    foreach ($all_enrolled as $enrolleds){
        $enrolled[] = array('userid' => $enrolleds->userid, 'course' => $enrolleds->code, 'section' => $enrolleds->section, 'amount' => (float) $enrolleds->amount);
    }
} elseif ($recentRound->roundNo == 2) {
    $all_enrolled = $enrolledDAO->getEnrolledByRoundNo($recentRound->roundNo-1);
    $enrolled = [];
    foreach ($all_enrolled as $enrolleds){
        $enrolled[] = array('userid' => $enrolleds->userid, 'course' => $enrolleds->code, 'section' => $enrolleds->section, 'amount' => (float) $enrolleds->amount);
    }
} else {
    $all_enrolled = $enrolledDAO->getEnrolledByRoundNo($recentRound->roundNo);
    $enrolled = [];
    foreach ($all_enrolled as $enrolleds){
        $enrolled[] = array('userid' => $enrolleds->userid, 'course' => $enrolleds->code, 'section' => $enrolleds->section, 'amount' => (float) $enrolleds->amount);
    }
}


$result = ["status" => "success", "course" => $course, "section" => $section, "student" => $student, "prerequisite" => $prerequisite, "bid" => $bid, "completed-course" => $courseCompleted, "section-student" => $enrolled];

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);

?>