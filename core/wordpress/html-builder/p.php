<?php
require_once 'html-element.php';

class P extends HtmlElement {
    function __construct($arg1 = array(),
                         $arg2 = array()) {
        $this->tag = "p";
        parent::__construct($arg1, $arg2);
    }    
}