<?php
require_once 'html-element.php';

class CheckBox extends HTMLElement {    
    function __construct($id, $value="") {
        parent::__construct($id);
        $this->arguments['value'] = $value;
    }
    public function value($arg) {
        if (!is_valid_string($arg)) return $this;
        $this->arguments['value'] = $arg;
        return $this;
    }
    public function to_string() {
        return sprintf(
            '<input type="checkbox" name="%s" value="%s" />',
            $this->arguments['id'], $this->arguments['value']
        );        
    }
};