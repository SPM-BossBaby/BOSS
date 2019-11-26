<?php

function bidStatus($course, $section) {

    $biddingRoundDAO = new BiddingRoundDAO();
    $sectionDAO = new SectionDAO();
    $bidDAO = new BidDAO();
    $enrolledDAO = new EnrolledDAO();
    $studentDAO = new StudentDAO();

    $round = $biddingRoundDAO->getCurrentRound();

    // get vacancy
    $vacancy = $sectionDAO->getSection($course, $section)->size - $enrolledDAO->checkSectionEnrolledNo($course, $section);

    // Get all bid from course and section of given round
    $allBidsCourseSection = $bidDAO->retrieveBidFromCourseSectionByRoundNo($course, $section, $round->roundNo);
    
    // Round 1
    if ($round->roundNo == 1){
        // During round 1
        if($round->active == 1){
            if (sizeof($allBidsCourseSection) == 0){ // if there is no bids placed
                $min_bid = 10.0;
            } elseif (sizeof($allBidsCourseSection) < $vacancy){ // if number of bids placed is lesser than the number of vacancy 
                $min_bid = (float) end($allBidsCourseSection)->amount; // set the min bid as the lowest succssful bid amount
            } elseif (sizeof($allBidsCourseSection) >= $vacancy){ // if number of bids placed is more than the number of vacancy
                $min_bid = (float) $allBidsCourseSection[$vacancy-1]->amount; // set the min bid as the clearing price
            }
        }
        // After Round 1
        else{
            if (sizeof($allBidsCourseSection) == 0){
                $min_bid = 10.0;
            } else {
                for($i=sizeof($allBidsCourseSection)-1; $i>=0; $i-=1){
                    if($allBidsCourseSection[$i]->status == "success"){ // iterate backwards to look for the last successful bid 
                        $min_bid = (float) $allBidsCourseSection[$i]->amount; // set the min bid as the last successful bid amount
                        break;
                    }
                }
            }
        }
    //Round 2
    } else {
        // During Round 2
        if($round->active == 1){
            $min_bid = (float) $sectionDAO->getSection($course, $section)->minBid;
        }
        // After Round 2
        else {
            if (sizeof($allBidsCourseSection) == 0){
                $min_bid = 10.0;
            } else {
                for($i=sizeof($allBidsCourseSection)-1; $i>=0; $i-=1){
                    if($allBidsCourseSection[$i]->status == "success"){
                        $min_bid = (float) $allBidsCourseSection[$i]->amount;
                        break;
                    }
                }
            }
        }
    }

    // get student bids with given course and section
    $bidData = [];
    if ($round->roundNo == 2 && $round->active == 0){ // After Round 2
        $tempData = $enrolledDAO->getEnrolledFromCourseSectionOrderByAmount($course, $section); // get all the enrolled bids
        foreach($tempData as $data){
            $student = $studentDAO->getStudent($data->userid);
            $bidData[] = ["userid" => $data->userid, "amount" => (float) $data->amount, "balance" => (float) $student->edollar, "status" => 'success'];
        }
    } else {
        $tempData = $bidDAO->retrieveBidFromCourseSectionByRoundNo($course, $section, $round->roundNo); // get all the bids from round 2
        foreach($tempData as $data){
            $student = $studentDAO->getStudent($data->userid);
            $bidData[] = ["userid" => $data->userid, "amount" => (float) $data->amount, "balance" => (float) $student->edollar, "status" => $data->status];
        }
    }
    $result = ["status" => "success", "vacancy" => $vacancy, "min-bid-amount" => $min_bid, "students" => $bidData];
    return $result;
}

?>