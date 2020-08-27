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
        'select_prepare_user'=>'SELECT * FROM %tablename ORDER BY %orderby %order LIMIT %d OFFSET %d;',
        'select_prepare_adm'=>'SELECT * FROM %tablename WHERE user_id = %userid ORDER BY %orderby %order LIMIT %d OFFSET %d;',
        'full_table_name'=>'`%database`.`table`',
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
}