<?php

class Settings {
    private static $instance = null;
    private function __construct(){}
    private function __clone(){}
    private function __wakeup(){}
    private $objects = array();
    private $database_name = "wordpress";
    private $prefix = "mwpc_";
    public static function get_instance() {
        if (self::$instance == null) self::$instance = new self;
        return self::$instance;
    }
    public function get_objects() {
        return $this->objects;
    }
    public function add_objects($array) {        
        $this->objects = array_merge($this->objects, $array);
    }
    public function get_database_name() {
        return $this->database_name;
    }
    public function get_prefix() {
        return $this->prefix;
    }
    public static function L($text) {
        return __($text, 'MWPC_DOMAIN');
    }
}