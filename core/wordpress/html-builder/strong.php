<?php
require_once 'html-element.php';

class Strong extends HtmlElement {
    function __construct($id="") {
        parent::__construct($id);
        $this->tag = "strong";
    }
}