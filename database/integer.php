<?php
require_once 'sql-element.php';

class Integer extends SQLElement {
    function __construct($arg1 = array(),
                         $arg2 = array()) {
        $this->instruction = "INT";
        parent::__construct($arg1, $arg2);
    }
};