<?php
require_once './core/vality-check.php';
require_once 'field.php';

class Field {
    protected $arguments = [
        "not" => "",
        "null" => "",
        "comma" => "",
    ];
    public function not() {
        $this->arguments['not'] = " NOT"; 
        return $this;
    }
    public function null() {
        $this->arguments['null'] = " NULL"; 
        return $this;
    }
    public function comma() {
        $this->arguments['comma'] = ","; 
        return $this;
    }
};
