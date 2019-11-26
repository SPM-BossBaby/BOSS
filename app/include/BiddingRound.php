<?php

class BiddingRound{
    // property declaration
    public $roundNo;
    public $active;
    public $start;
    public $end;

    public function __construct($roundNo='', $active=1, $start=NULL, $end=NULL)
    {
        $this->roundNo = $roundNo;
        $this->active = $active;
        $this->start = $start;
        $this->end = $end;
    }
}

?>