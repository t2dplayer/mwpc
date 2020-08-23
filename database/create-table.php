<?php
require_once 'sql-element.php';

class CreateTable extends SQLElement {
    function __construct($arg1 = array(),
                         $arg2 = array()) {
        $this->instruction = "CREATE TABLE IF NOT EXISTS";
        parent::__construct($arg1, $arg2);
    }
    public function __toString() {
        $sql = "$this->instruction `"
            . $this->attributes['database'] 
            . "`.`" 
            . $this->attributes['name'] 
            . "` (";
        $i = 0;
        foreach ($this->children as &$c) {
            $sql .= $c;
            if ($i < sizeof($this->children) - 1) $sql .= ", ";
            $i++;
        }
        $sql .= ")";
        if (key_exists('engine', $this->attributes)) {
            $sql .= " Engine = ". $this->attributes['engine'];
        }
        $sql .= ";";
        return $sql;
    }
};
