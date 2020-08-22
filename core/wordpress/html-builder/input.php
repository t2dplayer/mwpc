<?php
require_once 'html-element.php';

class Input extends HtmlElement {
    function __construct($id, $type, $value="") {
        parent::__construct($id);
        $args = array('type', 'value');
        foreach ($args as &$a) {
            $this->arguments[$a] = "";
        }
        $this->arguments['name'] = $id;
        $this->tag = "input";
        $this->is_self_closing = true;
    }
    public function set_type($arg) {
        if (!is_valid_string($arg)) return $this;
        $this->arguments['type'] = $arg;
        return $this;
    } 
    public function set_value($arg) {
        if (!is_valid_string($arg)) return $this;
        $this->arguments['value'] = $arg;
        return $this;
    }  
}