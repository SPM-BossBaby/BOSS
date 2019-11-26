<?php

class Bid {
    // property declaration
    public $userid;
    public $amount;
    public $code;
    public $section;
    public $status;
    public $roundNo;
    
    public function __construct($userid='', $amount='', $code='', $section='', $status='', $roundNo=1) {
        $this->userid = $userid;
        $this->amount = $amount;
        $this->code = $code;
        $this->section = $section;
        $this->status = $status;
        $this->roundNo = $roundNo;
    }
}

?>