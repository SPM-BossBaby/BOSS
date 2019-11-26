<?php

class Student {
    // property declaration
    public $userid;
    public $password;
    public $name;
    public $school;
    public $edollar;
    
    public function __construct($userid='', $password='', $name='', $school='', $edollar=0) {
        $this->userid = $userid;
        $this->password = $password;
        $this->name = $name;
        $this->school = $school;
        $this->edollar = $edollar;
    }

    public function authenticate($enteredPwd) {
        // return password_verify ($enteredPwd, $this->password);
        return $enteredPwd === $this->password;
    }
}

?>