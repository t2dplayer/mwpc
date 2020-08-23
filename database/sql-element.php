<?php

class SQLElement {
    protected $attributes = array();
    protected $children = array();
    protected $instruction = "";
    function __construct($arg1 = array(),
                         $arg2 = array()) {
        if (sizeof($arg1) > 0) {
            if (gettype($arg1[array_keys($arg1)[0]]) == "string") {
                $this->attributes = $arg1;
                $this->content_before = true;
            } else if (gettype($arg1[array_keys($arg1)[0]]) == "object") {
                $this->children = $arg1;
            }
        }
        if (sizeof($arg2) > 0) {
            if (gettype($arg2[array_keys($arg2)[0]]) == "string") {
                $this->attributes = $arg2;
            } else if (gettype($arg2[array_keys($arg2)[0]]) == "object") {
                $this->children = $arg2;
            }
        }                     
    }
    public function __toString() {
        $sql = "`". $this->attributes['name'] . "`"
            . " " . $this->instruction;
        if (sizeof($this->attributes['attributes']) > 0) {
            $sql .= " ";
            $i = 0;
            foreach($this->attributes['attributes'] as &$attr) {
                $sql .= $attr;
                if ($i < sizeof($this->attributes['attributes']) - 1) $sql .= " ";
                $i++;
            }
        }
        return $sql;
    }
};

class SQLUtils {
    public static function Id($id, $arr = array()) {
        return array_merge(['name'=>$id], $arr);
    }
    public static function Size($size, $arr = array()) {
        return array_merge(['size'=>$size], $arr);
    }
    public static function Attr($arr = array()) {
        return ['attributes'=>$arr];
    }
    public static function Not($arr = array()) {
        return array_merge(['NOT'], $arr);
    }
    public static function Null($arr = array()) {
        return array_merge(['NULL'], $arr);
    }
    public static function AutoIncrement($arr = array()) {
        return array_merge(['AUTO_INCREMENT'], $arr);
    }
    public static function Enum($arr = array(), $arr2 = array()) {
        return array_merge(['enum'=>$arr], $arr2);
    }
    public static function Make_PK($id) {
        return SQLUtils::Id($id, SQLUtils::Attr(SQLUtils::Not(SQLUtils::Null(SQLUtils::AutoIncrement()))));
    }
    public static function Make_NotNull($id) {
        return SQLUtils::Id($id, SQLUtils::Attr(SQLUtils::Not(SQLUtils::Null())));
    }
    public static function Make_SizedNotNull($id, $size) {
        return SQLUtils::Id($id, SQLUtils::Size($size, SQLUtils::Attr(SQLUtils::Not(SQLUtils::Null()))));
    }    
    public static function Make_Null($id) {
        return ['name'=>$id, 'attributes'=>['NULL']];
    }
    public static function Make_Enum($id, $arr = array()) {
        return SQLUtils::Id($id, SQLUtils::Enum($arr, SQLUtils::Attr(SQLUtils::Not(SqlUtils::Null()))));
    }
    public static function Make_Table($database, $name, $engine = "InnoDB") {
        return ['database'=>$database, 'name'=>$name, 'engine'=> $engine];
    }
};