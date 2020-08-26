<?php

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class DatabaseUtils {
    public static function create_table($sql) {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        global $wpdb;
        dbDelta($sql);    
    }
    public static function count_items($sql_string, $table_name) {
        global $wpdb;        
        $sql_count = TemplateUtils::t($sql_string, [
            '%tablename'=>Settings::_self()->get_prefix() . $table_name
        ]);
        return $wpdb->get_var($sql_count);        
    }
}


