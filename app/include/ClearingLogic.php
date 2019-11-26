<?php

require_once 'common.php';

function doClearingLogic($json = FALSE) {
    if (!$json) {
        require_once 'protect.php';
    }
    $biddingRoundDAO = new BiddingRoundDAO();
    $bidDAO = new BidDAO();
    $sectionDAO = new SectionDAO();
    $enrolledDAO = new EnrolledDAO();

    $activeRoundNo = $biddingRoundDAO->activeRound()->roundNo;
    $uniqueCourseSection = $bidDAO->getUniqueCourseSection($activeRoundNo);

    foreach($uniqueCourseSection as $value){
        $allBidsCourseSection = $bidDAO->retrieveBidFromCourseSectionByRoundNo($value["code"], $value["section"], $activeRoundNo);
        $section = $sectionDAO->getSection($value["code"], $value["section"]);
        
        $sortclass = new Sort();

        // Get all the bids in that round for that section
        $allBidsCourseSection = $sortclass->sort_it($allBidsCourseSection,"sort_bid_amount");

        // Get the remaining vacancy of the section
        $sizeleftover = $section->size - $enrolledDAO->checkSectionEnrolledNo($value["code"], $value["section"]);

        // If the number of bids is more than the remaining vacancy
        if(sizeof($allBidsCourseSection) >= $sizeleftover){
            // Set to remove the removeIndex to be the remaining vacancy
            $removeIndex = $sizeleftover-1;
            $clearingPrice = $allBidsCourseSection[$removeIndex]->amount;
            // Find if the bids higher than the clearing bid is the same
            for($i = $removeIndex; $i >= 0; $i--) {
                if ($allBidsCourseSection[$i]->amount == $clearingPrice) {
                    $removeIndex = $i;
                } else {
                    break;
                }
            }
            
            // If the remaining vacancy is not the same as the number of bids (more than)
            if($sizeleftover != sizeof($allBidsCourseSection)){
                // if removeIndex is the same as the sizeleftover e.g left with 5 slots, the clearing bid is the 5th bid
                // and the price of the next bid is not the clearing bid
                // and in round 1 (Move removeIndex to the next number)
                if($removeIndex == $sizeleftover-1 && $allBidsCourseSection[$sizeleftover]->amount != $clearingPrice && $activeRoundNo == 1) {
                    $removeIndex++;
                } else if ($allBidsCourseSection[$sizeleftover]->amount != $clearingPrice && $activeRoundNo == 2) {
                    $removeIndex = $sizeleftover;
                }
            } else {
                if($activeRoundNo == 2){
                    $removeIndex = $sizeleftover;
                } else if ($removeIndex == $sizeleftover-1){
                    $removeIndex++; 
                }
            }

            for($i = 0; $i < $removeIndex; $i++) {
                $bidDAO->updateBid(new Bid($allBidsCourseSection[$i]->userid, $allBidsCourseSection[$i]->amount, $allBidsCourseSection[$i]->code, $allBidsCourseSection[$i]->section, "success", $allBidsCourseSection[$i]->roundNo));
            }

            if ($activeRoundNo == 2 && $section->minBid < (float)$clearingPrice + 1){
                $minbid = number_format($clearingPrice+1,2,'.','');
                $sectionDAO->updateSection(new Section($section->course, $section->section, $section->day, $section->start, $section->end, $section->instructor, $section->venue, $section->size, $minbid));
            }

            for($i = $removeIndex; $i < sizeof($allBidsCourseSection); $i++) {
                $bidDAO->updateBid(new Bid($allBidsCourseSection[$i]->userid, $allBidsCourseSection[$i]->amount, $allBidsCourseSection[$i]->code, $allBidsCourseSection[$i]->section, "fail", $allBidsCourseSection[$i]->roundNo));
            }
        } else {
            // loop into each bid chance status to successful
            for($i = 0; $i < sizeof($allBidsCourseSection); $i++) {
                $bidDAO->updateBid(new Bid($allBidsCourseSection[$i]->userid, $allBidsCourseSection[$i]->amount, $allBidsCourseSection[$i]->code, $allBidsCourseSection[$i]->section, "success", $allBidsCourseSection[$i]->roundNo));
            }
        }
    }
}

?>