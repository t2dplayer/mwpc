<?php
require_once 'sql-element.php';

class PrimaryKey extends SQLElement {
    function __construct($arg1 = array(),
                         $arg2 = array()) {
        parent::__construct($arg1, $arg2);
        $this->instruction = "PRIMARY KEY  (`". $this->attributes['name'] . "`)";
    }
    public function __toString() {
        return $this->instruction;
    }
}
