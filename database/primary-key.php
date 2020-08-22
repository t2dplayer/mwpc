<?php
require_once 'field.php';

class PrimaryKey extends Field {
    function __construct($id) {
        if (!is_valid_string($id)) return $this;
        $this->arguments['id'] = $id;
    }
    public function to_string() {
        $id = $this->arguments['id'];
        $comma = $this->arguments['comma'];
        return " PRIMARY KEY  (`$id`)$comma";
    }    
}
