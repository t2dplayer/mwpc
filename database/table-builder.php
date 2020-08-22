<?php 
require_once WP_PLUGIN_DIR .'/mwpc/core/vality-check.php';

class TableBuilder  {
    private $arguments = array();
    private $child = array();
    public function create($database_name, $table_name) {
        if (!is_valid_string($database_name)
            || !is_valid_string($table_name)) return $this;
        $this->arguments['database_name'] = $database_name;
        $this->arguments['table_name'] = $table_name;
        return $this;
    }
    public function engine($name) {
        if (!is_valid_string($name)) return $this;
        $this->arguments['engine'] = $name;
        return $this;
    }
    public function add_child($child) {
        if (!is_subclass_of($child, 'Field')) return $this;
        array_push($this->child, $child);
        return $this;
    }
    public function to_string() {
        $database_name = $this->arguments['database_name'];
        $table_name = $this->arguments['table_name'];
        $engine = $this->arguments['engine'];
        $engine_str = isset($engine) ? "Engine = $engine" : "";
        $child_str = "";
        foreach($this->child as &$value) {
            $child_str .= $value->to_string();
        }
        return "CREATE TABLE IF NOT EXISTS `$database_name`.`$table_name` ($child_str) $engine_str;";
    }
};