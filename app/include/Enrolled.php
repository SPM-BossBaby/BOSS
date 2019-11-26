<?php

class Enrolled {
    // property declaration
    public $userid;
    public $code;
    public $section;
    public $amount;
    public $roundNo;
    
    public function __construct($userid='', $code='', $section='', $amount='', $roundNo=0) {
        $this->userid = $userid;
        $this->code = $code;
        $this->section = $section;
        $this->amount = $amount;
        $this->roundNo = $roundNo;
    }
}

?>