<?php
require_once 'html-element.php';

class Div extends HtmlElement {
    function __construct($id="") {
        parent::__construct($id);
        $this->tag = "div";
    }
}