<?php

class Admin {
    // property declaration
    public $userid;
    public $password;
    public $name;
    
    public function __construct($userid='', $password='', $name='') {
        $this->userid = $userid;
        $this->password = $password;
        $this->name = $name;
    }
    
    public function authenticate($enteredPwd) {
        return password_verify ($enteredPwd, $this->password);
    }
}

?>