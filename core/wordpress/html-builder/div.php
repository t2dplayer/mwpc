<?php
require_once 'html-element.php';

class Div extends HtmlElement {
    function __construct($arg1 = array(),
                         $arg2 = array()) {
        $this->tag = "div";
        parent::__construct($arg1, $arg2);
    }    
}