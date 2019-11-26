<?php

class CourseDAO {

    public function getAllCourse() {
        $sql = 'select * from course ORDER BY course ASC';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $result = array();
        
        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
             $result[] = new  Course($row['course'], $row['school'], $row['title'], $row['description'], $row['examDate'], $row['examStart'], $row['examEnd']);
        }
        return $result;
    }

    public function getAllSchool() {
        $sql = 'SELECT DISTINCT(school) FROM course';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $result = array();
        
        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
             $result[] = $row['school'];
        }
        return $result;
    }

    public function getCourse($course) {
        $sql = 'select course, school, title, description, examDate, examStart, examEnd from course where course=:course';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        
        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new Course($row['course'], $row['school'], $row['title'], $row['description'], $row['examDate'], $row['examStart'], $row['examEnd']);
        }
    }
    
    public function addCourse($course) {
        $sql = "INSERT IGNORE INTO course (course, school, title, description, examDate, examStart, examEnd) VALUES (:course, :school, :title, :description, :examDate, :examStart, :examEnd)";
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':course', $course->course, PDO::PARAM_STR);
        $stmt->bindParam(':school', $course->school, PDO::PARAM_STR);
        $stmt->bindParam(':title', $course->title, PDO::PARAM_STR);
        $stmt->bindParam(':description', $course->description, PDO::PARAM_STR);
        $stmt->bindParam(':examDate', $course->examDate, PDO::PARAM_STR);
        $stmt->bindParam(':examStart', $course->examStart, PDO::PARAM_STR);
        $stmt->bindParam(':examEnd', $course->examEnd, PDO::PARAM_STR);

        $isAddOK = False;
        if ($stmt->execute()) {
            $isAddOK = True;
        }

        return $isAddOK;
    }
	
	 public function removeAll() {
        $sql = 'SET FOREIGN_KEY_CHECKS=0; TRUNCATE TABLE course; SET FOREIGN_KEY_CHECKS=1;';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
        $count = $stmt->rowCount();
    }    
	
}


