<?php

class SectionDAO {
    public function retrieveAll() {
        $sql = "SELECT * FROM section ORDER BY course ASC, section ASC";

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':school', $school, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Section($row['course'], $row['section'], $row['day'], $row['start'], $row['end'], $row['instructor'], $row['venue'], $row['size']);

        }
        return $result; 
    }

    public function getAllSection() {
        $sql = "SELECT section.course, title, section, day, start, end, instructor, venue FROM section INNER JOIN course ON section.course=course.course";

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':school', $school, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($result, $row);
        }
        return $result;
    }

    public function getSectionBySchool($school) {
        $sql = "SELECT section.course, title, section, day, start, end, instructor, venue FROM section INNER JOIN course ON section.course=course.course WHERE school=:school ORDER BY section.course";

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':school', $school, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($result, $row);
        }
        return $result;
    }

    public function getSectionByCourse($course) {
        $sql = "SELECT section.course, title, section, day, start, end, instructor, venue, school FROM section INNER JOIN course ON section.course=course.course WHERE section.course=:course ORDER BY section.course";
    
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($result, $row);
        }
        return $result;
    }
    
    public function addSection($section) {
        $sql = "INSERT IGNORE INTO section (course, section, day, start, end, instructor, venue, size, minBid) VALUES (:course, :section, :day, :start, :end, :instructor, :venue, :size, :minBid)";
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':course', $section->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section->section, PDO::PARAM_STR);
        $stmt->bindParam(':day', $section->day, PDO::PARAM_INT);
        $stmt->bindParam(':start', $section->start, PDO::PARAM_STR);
        $stmt->bindParam(':end', $section->end, PDO::PARAM_STR);
        $stmt->bindParam(':instructor', $section->instructor, PDO::PARAM_STR);
        $stmt->bindParam(':venue', $section->venue, PDO::PARAM_STR);
        $stmt->bindParam(':size', $section->size, PDO::PARAM_INT);
        $stmt->bindParam(':minBid', $section->minBid, PDO::PARAM_STR);

        $isAddOK = False;
        if ($stmt->execute()) {
            $isAddOK = True;
        }

        return $isAddOK;
    }

    public function getSection($course, $section){
        $sql = 'select * from section where course=:course AND section=:section';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
                
        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new Section($row['course'], $row['section'], $row['day'], $row['start'], $row['end'], $row['instructor'], $row['venue'], $row['size'], $row['minBid']);
        }
    }
	
	 public function removeAll() {
        $sql = 'SET FOREIGN_KEY_CHECKS=0; TRUNCATE TABLE section; SET FOREIGN_KEY_CHECKS=1;';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
        $count = $stmt->rowCount();
    }    

    public function checkIfClassTimetableClash($course1, $section1, $course2, $section2){
        $sql = 'select * from section where course=:course AND section=:section';
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
                
        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':course', $course1, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section1, PDO::PARAM_STR);
        $stmt->execute();

        $sql2 = 'select * from section where course=:course AND section=:section';
                
        $stmt2 = $conn->prepare($sql2);
        $stmt2->setFetchMode(PDO::FETCH_ASSOC);
        $stmt2->bindParam(':course', $course2, PDO::PARAM_STR);
        $stmt2->bindParam(':section', $section2, PDO::PARAM_STR);
        $stmt2->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC) AND $row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            if ($row['day'] == $row2['day'] AND ((($row2['start'] >= $row['start'] AND $row2['start'] < $row['end']) OR ($row2['end'] > $row['start'] AND $row2['end'] <= $row['end'])) OR (($row['start'] >= $row2['start'] AND $row['start'] < $row2['end']) OR ($row['end'] > $row2['start'] AND $row['end'] <= $row2['end'])))){
                //Classes timetable clash
                return TRUE;
            }

            else{
                //Classes timetable NO clashes
                return FALSE;
            }
        }
    }
    
    public function checkIfExamTimetableClash($course1, $course2){
        $sql = 'select * from course where course=:course';
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
                
        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':course', $course1, PDO::PARAM_STR);
        $stmt->execute();

        $sql2 = 'select * from course where course=:course';
                
        $stmt2 = $conn->prepare($sql2);
        $stmt2->setFetchMode(PDO::FETCH_ASSOC);
        $stmt2->bindParam(':course', $course2, PDO::PARAM_STR);
        $stmt2->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC) AND $row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            if ($row['examDate'] == $row2['examDate'] AND ((($row2['examStart'] >= $row['examStart'] AND $row2['examStart'] < $row['examEnd']) OR ($row2['examEnd'] > $row['examStart'] AND $row2['examEnd'] <= $row['examEnd'])) OR (($row['examStart'] >= $row2['examStart'] AND $row['examStart'] < $row2['examEnd']) OR ($row['examEnd'] > $row2['examStart'] AND $row['examEnd'] <= $row2['examEnd'])))){
                //Exam timetable clash
                return TRUE;
            }

            else{
                //Exam timetable NO clashes
                return FALSE;
            }
        }
    }

    public function updateSection($section) {
        $sql = 'UPDATE section SET course=:course, section=:section, day=:day, start=:start, end=:end, instructor=:instructor, venue=:venue, size=:size, minBid=:minBid WHERE course=:course AND section=:section';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $section->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section->section, PDO::PARAM_STR);
        $stmt->bindParam(':day', $section->day, PDO::PARAM_INT);
        $stmt->bindParam(':start', $section->start, PDO::PARAM_STR);
        $stmt->bindParam(':end', $section->end, PDO::PARAM_STR);
        $stmt->bindParam(':instructor', $section->instructor, PDO::PARAM_STR);
        $stmt->bindParam(':venue', $section->venue, PDO::PARAM_STR);
        $stmt->bindParam(':size', $section->size, PDO::PARAM_INT);
        $stmt->bindParam(':minBid', $section->minBid, PDO::PARAM_STR);

        $isUpdateOk = False;
        if ($stmt->execute()) {
            $isUpdateOk = True;
        }

        return $isUpdateOk;
    }
	
}


