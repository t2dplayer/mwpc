<?php
require_once 'field.php';

class Enum extends Field {
    function __construct($id, $enums = array()) {
        if (!is_valid_string($id)
            || !is_array($enums)) return $this;
        $this->arguments['id'] = $id;
        $this->arguments['enums'] = $enums;        
    }
    public function to_string() {
        $id = $this->arguments['id'];
        $enum_str = "";
        $size = sizeof($this->arguments['enums']);
        for ($i=0; $i<$size; $i++) {
            $enum_str .= "'".$this->arguments['enums'][$i]."'";
            if ($i < $size - 1) $enum_str .= ", ";
        }
        $not = $this->arguments['not'];
        $null = $this->arguments['null'];
        $comma = $this->arguments['comma'];        
        return "`$id` ENUM($enum_str)$not$null$comma";
    }
};
