<?php
require_once 'field.php';

class Integer extends Field {
    function __construct($id) {
        if (!is_valid_string($id)) return $this;
        $this->arguments['id'] = $id;
        $this->arguments['auto_increment'] = "";
    }
    public function auto_increment() {
        $this->arguments['auto_increment'] = " AUTO_INCREMENT";
        return $this;
    }
    public function to_string() {
        $id = $this->arguments['id'];
        $not = $this->arguments['not'];
        $null = $this->arguments['null'];
        $auto_increment = $this->arguments['auto_increment'];
        $comma = $this->arguments['comma'];
        return "`$id` INT$not$null$auto_increment$comma";
    }
};