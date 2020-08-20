<?php
require_once './core/vality-check.php';
require_once 'field.php';

class VarChar extends Field {
    function __construct($id, $size = 255) {
        if (!is_valid_string($id)) return $this;
        $this->arguments['id'] = $id;
        $this->arguments['size'] = $size;
    }
    public function to_string() {
        $id = $this->arguments['id'];
        $size = $this->arguments['size'];
        $not = $this->arguments['not'];
        $null = $this->arguments['null'];
        $comma = $this->arguments['comma'];
        return "`$id` VARCHAR($size)$not$null$comma";
    }    
}
