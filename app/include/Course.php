<?php

class Course {
    // property declaration
    public $course;
    public $school;
    public $title;
    public $description;
    public $examDate;
    public $examStart;
    public $examEnd;
    
    public function __construct($course='', $school='', $title='', $description='', $examDate='', $examStart='', $examEnd='') {
        $this->course = $course;
        $this->school = $school;
        $this->title = $title;
        $this->description = $description;
        $this->examDate = $examDate;
        $this->examStart = $examStart;
        $this->examEnd = $examEnd;
    }
}

?>