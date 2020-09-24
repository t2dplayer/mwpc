<?php
require_once WP_PLUGIN_DIR .'/mwpc/core/template-utils.php';

class SQLTemplates {
    protected static $instance = null;
    public static function _self() {
        if (self::$instance == null) self::$instance = new self;
        return self::$instance;
    }
    protected static $sql_map = [
        'bulk_delete'=>'DELETE FROM %tablename WHERE id IN(%ids);',
        'select_count'=>'SELECT COUNT(id) FROM %tablename;',
        'select_prepare_user'=>'SELECT * FROM %tablename WHERE user_id = %userid ORDER BY %orderby %order LIMIT %d OFFSET %d;',        
        'select_prepare_adm'=>'SELECT * FROM %tablename ORDER BY %orderby %order LIMIT %d OFFSET %d;',
        'select_where_id'=>'SELECT * FROM %tablename WHERE id = %d;',
        'select_all'=>'SELECT %fields FROM %tablename;',
        'full_table_name'=>'`%database`.`%table`',
        'select_join'=>'SELECT master.name FROM %detailtable as detail inner join %mastertable as master on detail.%detailfield_id = master.id where detail.%itemfield_id = %itemvalue;',
        'select_detail_join'=>'SELECT %fields FROM %detailtable as detail inner join %mastertable as master on detail.%detailfield_id = master.id where detail.%itemfield_id = %itemvalue;',
        'select_join_detail'=>'SELECT master.*, detail.* FROM %detailtable AS detail INNER JOIN %mastertable AS master ON detail.%detailfield_id = master.id WHERE detail.%detailfield_id = %itemvalue;', 
        'select_join_all'=>'SELECT master.*, detail.* FROM %detailtable as detail inner join %mastertable as master on detail.%detailfield_id = master.id where detail.%itemfield_id = %itemvalue;',
        'prepare_select_join_all'=>'SELECT master.*, detail.* FROM %detailtable as detail inner join %mastertable as master on detail.%detailfield_id = master.id where detail.%itemfield_id = %d;',
        'prepare_select_fields_join_all'=>'SELECT %fields FROM %detailtable as detail inner join %mastertable as master on detail.%detailfield_id = master.id where detail.%itemfield_id = %d;',
        'delete_where'=>'DELETE FROM %tablename WHERE %attr;',
        'select_fields_where'=>'SELECT %fields FROM %tablename WHERE %where;',
    ];
    public static function get($key, $data=array()) {
        if (!array_key_exists($key, SQLTemplates::$sql_map)) CoreUtils::log("Invalid key -> ". $key);
        else return TemplateUtils::t(SQLTemplates::$sql_map[$key], $data);
        return '';
    }
    public static function full_table_name($table_name) {
        return TemplateUtils::t(SQLTemplates::$sql_map['full_table_name'], [
            '%database'=>Settings::_self()->get_database_name(),
            '%table'=>Settings::_self()->get_prefix() . $table_name,
        ]);
    }
    public static function make_where_attr($arr, $key, $field) {
        if (!array_key_exists($key, $arr)) return "";
        $attr = "user_id = " . get_current_user_id() . " AND ";
        $counter = 0;
        $size = sizeof($arr[$key]);
        foreach($arr[$key] as $id) {
            $attr .= "$field = " . $id;
            if ($counter++ < $size - 1) $attr .= " OR ";
        }
        return $attr;
    }
}