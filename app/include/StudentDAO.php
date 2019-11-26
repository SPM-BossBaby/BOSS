<?php

class StudentDAO {
    
    public function getStudent($userid) {
        $sql = 'select * from student where userid=:userid';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->execute();
        while($row = $stmt->fetch()) {
            return new Student($row['userid'], $row['password'], $row['name'], $row['school'], $row['edollar']);
        }
    }

    public function getStudentBiddableModule($userid,$bidRound = '0') {

        if ($bidRound == FALSE) {
            return FALSE;
        }

        if ($bidRound == '1'){
            $sql = 'Select course.* from course where course NOT IN (SELECT course FROM student JOIN course_completed ON student.userid = course_completed.userid JOIN course ON course.course = course_completed.code where BINARY student.userid=:userid)  AND course IN (SELECT course FROM student JOIN course ON course.school = student.school where course.school = student.school AND BINARY student.userid=:userid) AND course NOT IN (SELECT distinct(course.course) FROM course join prerequisite on course.course = prerequisite.course where prerequisite NOT IN (SELECT course FROM student JOIN course_completed ON student.userid = course_completed.userid JOIN course ON course.course = course_completed.code where BINARY student.userid=:userid))';
        }
        else{
            $sql = 'Select course.* from course where course NOT IN (SELECT course FROM student JOIN course_completed ON student.userid = course_completed.userid JOIN course ON course.course = course_completed.code where BINARY student.userid=:userid) AND course NOT IN (SELECT distinct(course.course) FROM course join prerequisite on course.course = prerequisite.course where prerequisite NOT IN (SELECT course FROM student JOIN course_completed ON student.userid = course_completed.userid JOIN course ON course.course = course_completed.code where BINARY student.userid=:userid))';
        }
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
                
        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Course($row['course'], $row['school'], $row['title'], $row['description'], $row['examDate'], $row['examStart'], $row['examEnd']);
        }
        return $result;
    }

    public function retrieveAll() {
        $sql = 'select * from student ORDER BY userid ASC';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();


        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Student($row['userid'], $row['password'], $row['name'], $row['school'], $row['edollar']);
        }
        return $result;
    }

    public function addStudent($student) {
        $sql = "INSERT IGNORE INTO student (userid, password, name, school, edollar) VALUES (:userid, :password, :name, :school, :edollar)";

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);
        
        // $student->password = password_hash($student->password,PASSWORD_DEFAULT);

        $stmt->bindParam(':userid', $student->userid, PDO::PARAM_STR);
        $stmt->bindParam(':password', $student->password, PDO::PARAM_STR);
        $stmt->bindParam(':name', $student->name, PDO::PARAM_STR);
        $stmt->bindParam(':school', $student->school, PDO::PARAM_STR);
        $stmt->bindParam(':edollar', $student->edollar, PDO::PARAM_STR);

        $isAddOK = False;
        if ($stmt->execute()) {
            $isAddOK = True;
        }

        return $isAddOK;
    }

    public function updateStudent($student) {
        $sql = 'UPDATE student SET userid=:userid, password=:password, name=:name, school=:school, edollar=:edollar WHERE userid=:userid';
        
        $connMgr = new ConnectionManager();           
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);
        
        // $student->password = password_hash($student->password,PASSWORD_DEFAULT);

        $stmt->bindParam(':userid', $student->userid, PDO::PARAM_STR);
        $stmt->bindParam(':password', $student->password, PDO::PARAM_STR);
        $stmt->bindParam(':name', $student->name, PDO::PARAM_STR);
        $stmt->bindParam(':school', $student->school, PDO::PARAM_STR);
        $stmt->bindParam(':edollar', $student->edollar, PDO::PARAM_STR);

        $isUpdateOk = False;
        if ($stmt->execute()) {
            $isUpdateOk = True;
        }

        return $isUpdateOk;
    }

    public function refundStudentEdollar($userid, $edollar) {
        $sql = 'UPDATE student SET edollar=edollar+:edollar WHERE userid=:userid';
        
        $connMgr = new ConnectionManager();           
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':edollar', $edollar, PDO::PARAM_STR);

        $isUpdateOk = False;
        if ($stmt->execute()) {
            $isUpdateOk = True;
        }

        return $isUpdateOk;
    }

    public function checkStudentBiddedSection($userid){
        $sql = 'select * from bid where userid=:userid';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        
        $stmt->execute();
        $count = $stmt->rowCount();

        return $count;
    }
	
	 public function removeAll() {
        $sql = 'SET FOREIGN_KEY_CHECKS=0; TRUNCATE TABLE student; SET FOREIGN_KEY_CHECKS=1;';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
        $count = $stmt->rowCount();
    }   
	
}


