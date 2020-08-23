<?php
require_once 'sql-element.php';

class VarChar extends SQLElement {
    function __construct($arg1 = array(),
                         $arg2 = array()) {
        parent::__construct($arg1, $arg2);
        $this->instruction = "VARCHAR(";
        if (key_exists('size', $this->attributes)) {
            $this->instruction .= $this->attributes['size'];
        } else {
            $this->instruction .= '255';
        }
        $this->instruction .= ")";
    }
}
