<?php
require_once './core/vality-check.php';
interface ToString {
    public function to_string();
}

class HtmlElement implements ToString {
    protected $arguments = array();
    function __construct($id) {
        if (!is_valid_string($id)) return $this;
        $this->arguments['id'] = $id;
    }
    public function to_string() {
        return "";
    }
}
