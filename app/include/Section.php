<?php

class Section {
    // property declaration
    public $course;
    public $section;
    public $day;
    public $start;
    public $end;
    public $instructor;
    public $venue;
    public $size;
    public $minBid;
    
    public function __construct($course='', $section='', $day=0, $start='', $end='', $instructor='', $venue='', $size=0, $minBid=0) {
        $this->course = $course;
        $this->section = $section;
        $this->day = $day;
        $this->start = $start;
        $this->end = $end;
        $this->instructor = $instructor;
        $this->venue = $venue;
        $this->size = $size;
        $this->minBid = $minBid;
    }
}

?>