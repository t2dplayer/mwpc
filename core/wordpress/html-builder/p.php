<?php
require_once 'html-element.php';

class P extends HtmlElement {
    function __construct($id="") {
        parent::__construct($id);
        $this->tag = "p";
    }
}