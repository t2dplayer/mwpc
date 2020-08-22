<?php
require_once 'html-element.php';

class Strong extends HtmlElement {
    function __construct($arg1 = array(),
                         $arg2 = array()) {
        $this->tag = "strong";
        parent::__construct($arg1, $arg2);
    }
}