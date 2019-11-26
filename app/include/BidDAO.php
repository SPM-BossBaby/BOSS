<?php

class BidDAO {

    public function retrieveAll() {
        $sql = 'select * from bid';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Bid($row['userid'], $row['amount'], $row['code'], $row['section'], $row['status'], $row['roundNo']);
        }
        return $result;
    }

    public function addBid($bid) {
        $sql = "INSERT IGNORE INTO bid (userid, amount, code, section, status, roundNo) VALUES (:userid, :amount, :code, :section, :status, :roundNo)";
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':userid', $bid->userid, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $bid->amount, PDO::PARAM_STR);
        $stmt->bindParam(':code', $bid->code, PDO::PARAM_STR);
        $stmt->bindParam(':section', $bid->section, PDO::PARAM_STR);
        $stmt->bindParam(':status', $bid->status, PDO::PARAM_STR);
        $stmt->bindParam(':roundNo', $bid->roundNo, PDO::PARAM_INT);

        $isAddOK = FALSE;
        if ($stmt->execute()) {
            $isAddOK = TRUE;
        }

        return $isAddOK;
    }

    public function getBidFromUser($userid) {
        $sql = 'select userid, amount, code, section, status, roundNo from bid where userid=:userid';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Bid($row['userid'], $row['amount'], $row['code'], $row['section'], $row['status'], $row['roundNo']);
        }
        return $result;
    }

    public function getBidFromUserByRoundNo($userid, $roundno) {
        $sql = 'select userid, amount, code, section, status, roundNo from bid where userid=:userid and roundno=:roundno';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':roundno', $roundno, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Bid($row['userid'], $row['amount'], $row['code'], $row['section'], $row['status'], $row['roundNo']);
        }
        return $result;
    }

    public function getBidFromUserBySuccessPending($userid) {
        $sql = 'select * from bid where userid=:userid and (status ="success" OR status ="pending")';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Bid($row['userid'], $row['amount'], $row['code'], $row['section'], $row['status'], $row['roundNo']);
        }
        return $result;
    }

    public function checkBid($userid, $code, $roundNo) {
        $sql = 'select userid, amount, code, section, status, roundNo from bid where userid=:userid AND code=:code AND roundNo=:roundNo';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->bindParam(':roundNo', $roundNo, PDO::PARAM_STR);
        $stmt->execute();

        while($row = $stmt->fetch()) {
            return new Bid($row['userid'], $row['amount'], $row['code'], $row['section'], $row['status'], $row['roundNo']);
        }
        return FALSE;
    }

    public function checkBidforDelete($userid, $code, $section, $roundNo) {
        $sql = 'select userid, amount, code, section, status, roundNo from bid where userid=:userid AND code=:code AND section=:section AND roundNo=:roundNo';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);
        $stmt->bindParam(':roundNo', $roundNo, PDO::PARAM_STR);
        $stmt->execute();

        while($row = $stmt->fetch()) {
            return new Bid($row['userid'], $row['amount'], $row['code'], $row['section'], $row['status'], $row['roundNo']);
        }
        return FALSE;
    }

    public function getUniqueCourseSection($roundno) {
        $sql = 'SELECT DISTINCT code, section FROM bid WHERE roundNo = :roundno';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':roundno', $roundno, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = ["code"=>$row['code'], "section"=>$row['section']];
        }
        return $result;
    }

    public function retrieveBidFromCourseSection($code, $section) {
        $sql = 'SELECT * FROM bid WHERE code=:code AND section=:section';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Bid($row['userid'], $row['amount'], $row['code'], $row['section'], $row['status'], $row['roundNo']);
        }
        return $result;
    }

    public function retrieveBidFromCourseSectionByRoundNo($code, $section, $roundno) {
        $sql = 'SELECT * FROM bid WHERE code=:code AND section=:section AND roundNo = :roundno ORDER BY amount DESC, userid ASC';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);
        $stmt->bindParam(':roundno', $roundno, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Bid($row['userid'], $row['amount'], $row['code'], $row['section'], $row['status'], $row['roundNo']);
        }
        return $result;
    }

    public function retrieveAllByRound($roundno) {
        $sql = 'SELECT * FROM bid WHERE roundNo = :roundno order by code ASC, section ASC, amount DESC, userid ASC';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':roundno', $roundno, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Bid($row['userid'], $row['amount'], $row['code'], $row['section']);
        }
        return $result;
    }
    
    public function retrieveBidFromRoundByStatus($status, $roundno) {
        $sql = 'SELECT * FROM bid WHERE status = :status AND roundNo = :roundno ORDER BY amount DESC, userid ASC';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':roundno', $roundno, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Bid($row['userid'], $row['amount'], $row['code'], $row['section'], $row['status'], $row['roundNo']);
        }
        return $result;
    }

	 public function removeAll() {
        $sql = 'SET FOREIGN_KEY_CHECKS=0; TRUNCATE TABLE bid; SET FOREIGN_KEY_CHECKS=1;';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
        $count = $stmt->rowCount();
    }

    public function updateBid($bid) {
        $sql = 'UPDATE bid SET userid=:userid, amount=:amount, code=:code, section=:section, status=:status, roundNo=:roundNo WHERE userid=:userid AND code=:code AND section=:section AND roundNo=:roundNo';

        $connMgr = new ConnectionManager();           
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':userid', $bid->userid, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $bid->amount, PDO::PARAM_STR);
        $stmt->bindParam(':code', $bid->code, PDO::PARAM_STR);
        $stmt->bindParam(':section', $bid->section, PDO::PARAM_STR);
        $stmt->bindParam(':status', $bid->status, PDO::PARAM_STR);
        $stmt->bindParam(':roundNo', $bid->roundNo, PDO::PARAM_INT);

        $isUpdateOk = False;
        if ($stmt->execute()) {
            $isUpdateOk = True;
        }
        return $isUpdateOk;
    }

    public function updateBidStatus($userid, $course, $section, $status) {
        $sql = 'UPDATE bid SET status=:status WHERE userid=:userid AND code=:code AND section=:section';

        $connMgr = new ConnectionManager();           
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':code', $course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);

        $isUpdateOk = False;
        if ($stmt->execute()) {
            $isUpdateOk = True;
        }
        return $isUpdateOk;
    }

    public function deleteBid($userid, $course, $section) {
        $sql = 'DELETE FROM bid WHERE userid=:userid AND code=:course AND section=:section';
        
        $connMgr = new ConnectionManager();           
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);

        $isDeleteOk = False;
        if ($stmt->execute()) {
            $isDeleteOk = True;
        }

        return $isDeleteOk;
    }

    public function sortBid($bid){

        function bidComparator($object1, $object2) { 
            return $object1->userid > $object2->userid; 
        } 

        usort($bid, 'bidComparator');

        return $bid;
    }
}


