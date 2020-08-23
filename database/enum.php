<?php
require_once 'sql-element.php';

class Enum extends SQLElement {
    function __construct($arg1 = array(),
                         $arg2 = array()) {
        $this->instruction = "ENUM(";
        parent::__construct($arg1, $arg2);
        for($i = 0; $i < sizeof($this->attributes['enum']); $i++) {
            $this->instruction .= "'". $this->attributes['enum'][$i] ."'";
            if ($i < sizeof($this->attributes['enum']) - 1) {
                $this->instruction .= ", ";
            }
        }
        $this->instruction .= ")";
    }
};
