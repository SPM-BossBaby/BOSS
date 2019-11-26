<?php

class CourseCompleted {
    // property declaration
    public $userid;
    public $code;
    
    public function __construct($userid='', $code='') {
        $this->userid = $userid;
        $this->code = $code;
    }
}

?>