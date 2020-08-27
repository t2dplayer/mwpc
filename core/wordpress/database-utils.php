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
    public static function count_items($table_name) {
        global $wpdb;
        $sql_count = SQLTemplates::get('select_count', [
            '%tablename'=>Settings::_self()->table_name($table_name)
        ]);
        return $wpdb->get_var($sql_count);
    }
}


