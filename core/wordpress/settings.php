<?php

class Settings {
    private static $instance = null;
    private function __construct(){}
    private function __clone(){}
    private function __wakeup(){}
    private $objects = array();
    private $database_name = "wordpress";
    private $prefix = "mwpc_";
    public static function _self() {
        if (self::$instance == null) self::$instance = new self;
        return self::$instance;
    }
    public function get_object($key) {
        return $this->objects[$key];
    }
    public function get_objects() {
        return $this->objects;
    }    
    public function add_object($key, $object) {
        $this->objects[$key] = $object;
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
    public function table_name($table_name) {
        return "`$this->database_name`.`$this->prefix$table_name`";
    }
}