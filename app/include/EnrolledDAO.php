<?php

class EnrolledDAO {
    
    public function addEnrolled($bid) {
        $sql = "INSERT IGNORE INTO enrolled (userid, code, section, amount, roundNo) VALUES (:userid, :code, :section, :amount, :roundNo)";
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':userid', $bid->userid, PDO::PARAM_STR);
        $stmt->bindParam(':code', $bid->code, PDO::PARAM_STR);
        $stmt->bindParam(':section', $bid->section, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $bid->amount, PDO::PARAM_STR);
        $stmt->bindParam(':roundNo', $bid->roundNo, PDO::PARAM_INT);

        $isAddOK = False;
        if ($stmt->execute()) {
            $isAddOK = True;
        }

        return $isAddOK;
    }

    public function enrollAll($bids){
        $isAddOK = False;
        foreach($bids as $bid){

            $sql = "INSERT IGNORE INTO enrolled (userid, code, section, amount, roundNo) VALUES (:userid, :code, :section, :amount, :roundNo)";
        
            $connMgr = new ConnectionManager();
            $conn = $connMgr->getConnection();
            $stmt = $conn->prepare($sql);
            
            $stmt->bindParam(':userid', $bid->userid, PDO::PARAM_STR);
            $stmt->bindParam(':code', $bid->code, PDO::PARAM_STR);
            $stmt->bindParam(':section', $bid->section, PDO::PARAM_STR);
            $stmt->bindParam(':amount', $bid->amount, PDO::PARAM_STR);
            $stmt->bindParam(':roundNo', $bid->roundNo, PDO::PARAM_INT);

            $isAddOK = True;
            if (!$stmt->execute()) {
                $isAddOK = False;
            }
        }
        return $isAddOK;
    }

    public function retrieveAll() {
        $sql = 'select * from enrolled ORDER BY code ASC, userid ASC';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();


        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Enrolled($row['userid'], $row['code'], $row['section'], $row['amount'], $row['roundNo']);
        }
        return $result;
    }

    public function getEnrolledFromUser($userid) {
        $sql = 'select userid, code, section, amount, roundNo from enrolled where userid=:userid';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();


        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Enrolled($row['userid'], $row['code'], $row['section'], $row['amount'], $row['roundNo']);
        }
        return $result;
    }

    public function getEnrolledByRoundNo($roundNo) {
        $sql = 'select userid, code, section, amount, roundNo from enrolled where roundNo=:roundNo ORDER BY code ASC, userid ASC';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':roundNo', $roundNo, PDO::PARAM_INT);
        $stmt->execute();

        $result = array();


        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Enrolled($row['userid'], $row['code'], $row['section'], $row['amount'], $row['roundNo']);
        }
        return $result;
    }

    public function getEnrollment($userid, $code, $section) {
        $sql = 'select userid, code, section, amount, roundNo from enrolled where userid=:userid and code=:code and section=:section';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new Enrolled($row['userid'], $row['code'], $row['section'], $row['amount'], $row['roundNo']);
        }
        return $result;
    }

    public function getEnrolledFromCourseSection($code, $section) {
        $sql = 'select userid, code, section, amount, roundNo from enrolled where code=:code and section=:section ORDER BY userid ASC';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Enrolled($row['userid'], $row['code'], $row['section'], $row['amount'], $row['roundNo']);
        }
        return $result;
    }

    public function getEnrolledFromCourseSectionOrderByAmount($code, $section) {
        $sql = 'select userid, code, section, amount, roundNo from enrolled where code=:code and section=:section ORDER BY amount DESC, userid ASC';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Enrolled($row['userid'], $row['code'], $row['section'], $row['amount'], $row['roundNo']);
        }
        return $result;
    }

    public function deleteEnrollment($userid, $course, $section) {
        $sql = 'DELETE FROM enrolled WHERE userid=:userid AND code=:course AND section=:section';
        
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

    public function checkEnrolledUser($userid,$code) {
        $sql = 'select userid, code, section, amount, roundNo from enrolled where userid=:userid AND code=:code';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 0){
            return FALSE;
        }
        return TRUE;
    }

    public function checkSectionEnrolledNo($code, $section){
        $sql = 'select * from enrolled where code=:code AND section=:section';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);
        
        $stmt->execute();
        $count = $stmt->rowCount();

        return $count;
    }
	
	public function removeAll() {
        $sql = 'SET FOREIGN_KEY_CHECKS=0; TRUNCATE TABLE enrolled; SET FOREIGN_KEY_CHECKS=1;';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
        $count = $stmt->rowCount();
    }
}


