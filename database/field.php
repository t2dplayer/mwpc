<?php
require_once WP_PLUGIN_DIR .'/mwpc/core/vality-check.php';

class Field {
    protected $arguments = [
        "not" => "",
        "null" => "",
        "comma" => "",
    ];
    public function not() {
        $this->arguments['not'] = " NOT"; 
        return $this;
    }
    public function null() {
        $this->arguments['null'] = " NULL"; 
        return $this;
    }
    public function comma() {
        $this->arguments['comma'] = ","; 
        return $this;
    }
};
