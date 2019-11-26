<?php

class CourseCompletedDAO {
    
    public function addCourseCompleted($course_completed) {
        $sql = "INSERT IGNORE INTO course_completed (userid, code) VALUES (:userid, :code)";
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':userid', $course_completed->userid, PDO::PARAM_STR);
        $stmt->bindParam(':code', $course_completed->code, PDO::PARAM_STR);

        $isAddOK = False;
        if ($stmt->execute()) {
            $isAddOK = True;
        }

        return $isAddOK;
    }

    public function retrieveAll() {
        $sql = 'SELECT * FROM course_completed ORDER BY code ASC, userid ASC';
        
            
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch()) {
            $result[] = new CourseCompleted($row['userid'], $row['code']);
        }

        return $result;
    }

    public function getCourseCompleted($userid) {
        $sql = 'SELECT userid, code  FROM course_completed WHERE `userid` = :userid ORDER BY `code`';
        
            
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch()) {
            $result[] = new CourseCompleted($row['userid'], $row['code']);
        }

        return $result;
    }
	
	 public function removeAll() {
        $sql = 'SET FOREIGN_KEY_CHECKS=0; TRUNCATE TABLE course_completed; SET FOREIGN_KEY_CHECKS=1;';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
        $count = $stmt->rowCount();
    }    
	
}


