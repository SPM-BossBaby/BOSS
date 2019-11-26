<?php

class PrerequisiteDAO {
    
    public function addPrerequisite($prerequisite) {
        $sql = "INSERT IGNORE INTO prerequisite (course, prerequisite) VALUES (:course, :prerequisite)";
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':course', $prerequisite->course, PDO::PARAM_STR);
        $stmt->bindParam(':prerequisite', $prerequisite->prerequisite, PDO::PARAM_STR);

        $isAddOK = False;
        if ($stmt->execute()) {
            $isAddOK = True;
        }

        return $isAddOK;
    }

    public function retrieveAll() {
        $sql = 'SELECT * FROM prerequisite ORDER BY course ASC, prerequisite ASC';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch()) {
            $result[] = new Prerequisite($row['course'], $row['prerequisite']);
        }
                 
        return $result;
    }

    public function getPrerequisite($course) {
        $sql = 'SELECT course, prerequisite  FROM prerequisite WHERE `course` = :course ORDER BY `prerequisite`';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch()) {
            $result[] = new Prerequisite($row['course'], $row['prerequisite']);
        }
            
                 
        return $result;
    }

     public function checkIfStudentCompletePrerequisite($userid, $course){
        $sql = 'select prerequisite from prerequisite where course=:course';
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->execute();

        $sql2 = 'select code from course_completed where userid=:userid';

        $stmt2 = $conn->prepare($sql2);
        $stmt2->setFetchMode(PDO::FETCH_ASSOC);
        $stmt2->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt2->execute();

        $hasCompletedPrerequisite = 0;
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                if ($row['prerequisite'] == $row2['code']){
                    $hasCompletedPrerequisite = 1;
                }                   
            }
            if ($hasCompletedPrerequisite == 0){
                return FALSE;
            }
            $hasCompletedPrerequisite = 0;
        }
        return TRUE;
     }
	
	 public function removeAll() {
        $sql = 'SET FOREIGN_KEY_CHECKS=0; TRUNCATE TABLE prerequisite; SET FOREIGN_KEY_CHECKS=1;';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
        $count = $stmt->rowCount();
    }    
	
}


