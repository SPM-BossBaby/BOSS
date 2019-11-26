<?php
require_once 'common.php';
require_once 'ClearingLogic.php';

function updateBid($decode_data=[], $studentbid = FALSE, $actualbid = FALSE, $json = FALSE) {

    #region Declare DAO
    $bid = new Bid();
    $bidDAO = new BidDAO();
    $biddingRoundDAO = new BiddingRoundDAO(); 
    $enrolledDAO = new EnrolledDAO();
    $courseDAO = new CourseDAO();
    $sectionDAO = new SectionDAO();
    $studentDAO = new StudentDAO();
    $prerequisiteDAO = new PrerequisiteDAO();
    $courseCompletedDAO = new CourseCompletedDAO();
    #endregion

    $errors = array();

    $biddingRound = $biddingRoundDAO->getCurrentRound();

    $bid->userid = $decode_data['userid'];
    $bid->amount = $decode_data['amount'];
    $bid->course = $decode_data['course'];
    $bid->section = $decode_data['section'];
    $bid->roundNo = $biddingRound->roundNo;

    $duplicateBid = ($bidDAO->checkBid($bid->userid, $bid->course, $biddingRound->roundNo) != null);

    //round ended: there is no active round.*Error*
    if ($biddingRound->active <> 1){
        $errors[] = "round ended";
    }

    if(empty($errors)){
        //Bid too low: the amount must be more than the minimum bid (only applicable for round 2)
        if ($biddingRound->roundNo == 2 && $biddingRound->active == 1){
            $section = $sectionDAO->getSection($bid->course, $bid->section);
            if($bid->amount < $section->minBid){
                $errors[] = "bid too low";
            }
        }

        //Insufficient e$: student has not enough e-dollars to place the bid. If it is an update of a previous bid, account for the extra e$ gained back from the cancellation of the previous bid first.    
        //if this is New Bid
        if (!$duplicateBid) {
            $student_info = $studentDAO->getStudent($bid->userid);

            //print_r ($student_info->edollar);
            if ($bid->amount > $student_info->edollar){
                $errors[] = "insufficient e$";
            }
        }
        //if this is Existing Bid
        else{
            $student_info = $studentDAO->getStudent($bid->userid);
            $bid_data = $bidDAO->checkBid($bid->userid, $bid->course, $bid->roundNo);
            if ($bid->amount > $student_info->edollar + $bid_data->amount){
                $errors[] = "insufficient e$";
            }
        }

        //Class timetable clash: The class timeslot for the section clashes with that of a previously bidded section.
        //check class timetable clash only if this is a new bid
        $student_bid = $bidDAO->getBidFromUserBySuccessPending($bid->userid);
        $student_enrolled = $enrolledDAO->getEnrollment($bid->userid, $bid->course, $bid->section);
        $check = TRUE;

        foreach ($student_bid as $bidded){
            if (!($bid->course == $bidded->code && $bid->section != $bidded->section) && ($bid->course != $bidded->code || $bid->section != $bidded->section)){
                if($sectionDAO->checkIfClassTimetableClash($bid->course, $bid->section, $bidded->code, $bidded->section)){
                    $errors[] = "class timetable clash";
                    break;
                }
            }
        }

        if($student_enrolled != null){
            if($bid->course == $student_enrolled->code && $bid->section == $student_enrolled->section){
                if($sectionDAO->checkIfClassTimetableClash($bid->course, $bid->section, $student_enrolled->code, $student_enrolled->section)){
                    $errors[] = "class timetable clash";
                }
            }
            if($bid->course == $student_enrolled->code){
                if($sectionDAO->checkIfExamTimetableClash($bid->course, $bidded->code)){
                    $errors[] = "exam timetable clash";
                    $check = FALSE;
                }
            }
        }
        if($enrolledDAO->checkEnrolledUser($bid->userid, $bid->course) && $check){
            if($sectionDAO->checkIfExamTimetableClash($bid->course, $bidded->code)){
                $errors[] = "exam timetable clash";
            }
        }

        //Exam timetable clash: The exam timeslot for this section clashes with that of a previously bidded section.
        //check exam timetable clash only if this is a new bid
        $student_bid = $bidDAO->getBidFromUserBySuccessPending($bid->userid);

        foreach ($student_bid as $bidded){
            if (!($bid->course == $bidded->code && $bid->section != $bidded->section) && ($bid->course != $bidded->code || $bid->section != $bidded->section)){
                if($sectionDAO->checkIfExamTimetableClash($bid->course, $bidded->code)){
                    $errors[] = "exam timetable clash";
                    break;
                }
            }
        }

        //Incomplete prerequisites: student has not completed the prerequisites for this course.
        if(!$prerequisiteDAO->checkIfStudentCompletePrerequisite($bid->userid, $bid->course)){
            $errors[] = "incomplete prerequisites";
        }

        //course completed: student has already completed this course.
        $completed_course = $courseCompletedDAO->getCourseCompleted($bid->userid);

        foreach ($completed_course as $completed){

            if($bid->course == $completed->code){
                $errors[] = "course completed";
                break;
            }
        }

        //course enrolled: Student has already won a bid for a section in this course in a previous round. *Error*
        if ($enrolledDAO -> checkEnrolledUser($bid->userid,$bid->course)){
            $errors[] = "course enrolled";
        }

        //Student cannot bid for more than 5 sections
        if (!$duplicateBid) {
            if (count($bidDAO->getBidFromUserBySuccessPending($bid->userid)) >= 5) {
                $errors[] = "section limit reached";
            }
        }

        //not own school course: This only happens in round 1 where students are allowed to bid for modules from their own school.
        if ($biddingRound->roundNo == 1){
            if ($courseDAO->getCourse($bid->course)->school != $studentDAO->getStudent($bid->userid)->school){
                $errors[] = "not own school course";
            }
        }

        //no vacancy: there is 0 vacancy for the section that the user is bidding.
        if ($sectionDAO->getSection($bid->course, $bid->section)->size - $enrolledDAO->checkSectionEnrolledNo($bid->course, $bid->section) == 0){
            $errors[] = "no vacancy";
        }
    }

    if ($studentbid !== FALSE && $actualbid == FALSE) {
        if (empty($errors)) {
            $result = ["status" => "success"];
        } else {
            $result = ["status" => "error", "message" => $errors];
        }
        return $result;
    }

    if (empty($errors)) {
        if($biddingRound->roundNo == 1 && $biddingRound->active == 1) {
            //Existing bid
            if ($duplicateBid) {
                $bidData = (object) array('userid' => $bid->userid , 'amount' => $bid->amount, 'code' => $bid->course, 'section'=> $bid->section,  'status' => 'pending', 'roundNo' => $biddingRoundDAO->activeRound()->roundNo);
                
                $old_bid = $bidDAO->checkBid($bid->userid, $bid->course, $biddingRound->roundNo);
                $bidDAO->deleteBid($old_bid->userid, $old_bid->code, $old_bid->section);
                $bidDAO->addBid($bidData);
                
                $currentStudent = $studentDAO->getStudent($bid->userid);
				$studentDAO->updateStudent(new Student($currentStudent->userid, $currentStudent->password, $currentStudent->name, $currentStudent->school, $currentStudent->edollar + $old_bid->amount - $bid->amount));
                
                $result = ["status" => "success"];
                $bidtype = '<b>Bid Placed!</b> Type: Update Bid';
            }

            //new bid
            else {
                $bidData = (object) array('userid' => $bid->userid , 'amount' => $bid->amount, 'code'=> $bid->course, 'section'=> $bid->section, 'status' => 'pending' , 'roundNo' => $biddingRoundDAO->activeRound()->roundNo);
                $bidDAO->addBid($bidData);

                $currentStudent = $studentDAO->getStudent($bid->userid);
                $newEdollar = number_format(($currentStudent->edollar - $bid->amount), 2);
                settype($newEdollar, 'string');
                $studentDAO->updateStudent(new Student($currentStudent->userid, $currentStudent->password, $currentStudent->name, $currentStudent->school, $newEdollar));
                
                $result = ["status" => "success"];
                $bidtype = '<b>Bid Placed!</b> Type: New Bid';
            }
        } elseif ($biddingRound->roundNo == 2 && $biddingRound->active == 1){
            //Existing bid
            if ($duplicateBid) {
                $bidData = (object) array('userid' => $bid->userid , 'amount' => $bid->amount, 'code' => $bid->course, 'section'=> $bid->section,  'status' => 'pending', 'roundNo' => $biddingRoundDAO->activeRound()->roundNo);
                
                $old_bid = $bidDAO->checkBid($bid->userid, $bid->course, $biddingRound->roundNo);
                $bidDAO->deleteBid($old_bid->userid, $old_bid->code, $old_bid->section);
                $bidDAO->addBid($bidData);
                doClearingLogic($json);

                $currentStudent = $studentDAO->getStudent($bid->userid);
				$studentDAO->updateStudent(new Student($currentStudent->userid, $currentStudent->password, $currentStudent->name, $currentStudent->school, $currentStudent->edollar + $old_bid->amount - $bid->amount));

                $result = ["status" => "success"];
                $bidtype = '<b>Bid Placed!</b> Type: Update Bid';
            }

            //new bid
            else {
                $bidData = (object) array('userid' => $bid->userid , 'amount' => $bid->amount, 'code'=> $bid->course, 'section'=> $bid->section, 'status' => 'pending' , 'roundNo' => $biddingRoundDAO->activeRound()->roundNo);
                
                $bidDAO->addBid($bidData);
                doClearingLogic($json = TRUE);

                $currentStudent = $studentDAO->getStudent($bid->userid);
                $newEdollar = number_format(($currentStudent->edollar - $bid->amount), 2);
                settype($newEdollar, 'string');
                $studentDAO->updateStudent(new Student($currentStudent->userid, $currentStudent->password, $currentStudent->name, $currentStudent->school, $newEdollar));
                
                $result = ["status" => "success"];
                $bidtype = '<b>Bid Placed!</b> Type: New Bid';
            }
        }
    } else {
        sort($errors);
        $result = ["status" => "error", "message" => $errors];
    }

    if ($studentbid !== FALSE && $actualbid !== FALSE) {
        if (!isset($bidtype)) {
            $bidtype = '<b>Bid Failed!</b> Conflicts with other bids found:';
            foreach ($errors as $anerror) {
                $bidtype .= (' ' . $anerror . ',');
            }
            rtrim(',', $bidtype);
        }
        return $bidtype;
    }

    return $result; 
}
?>