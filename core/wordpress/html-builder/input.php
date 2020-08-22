<?php
require_once 'html-element.php';

class Input extends HtmlElement {
    function __construct($arg1 = array(),
                         $arg2 = array()) {
        $this->tag = "input";
        parent::__construct($arg1, $arg2);
    }    
}