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
    // inner_join is used at creating column in listing table
    public static function inner_join($item) {
        global $wpdb;
        $sql = SQLTemplates::_self()->get('select_join', $item);        
        $results = $wpdb->get_results($sql);
        $str = "";
        foreach ($results as $r) {
            $str .= '<em>'. $r->name .'</em></br>';
        }
        return $str;
    }
    // inner_join_field is used at creating a column in table listing values
    public static function inner_join_field($item, $output_field) {
        global $wpdb;
        $sql = SQLTemplates::_self()->get('select_join_all', $item);
        $results = $wpdb->get_results($sql);
        $str = "";
        foreach ($results as $r) {
            $str .= '<em>'. $r->$output_field .'</em></br>';
        }
        return $str;
    }    
    public static function inner_join_all($item) {
        global $wpdb;
        $sql = SQLTemplates::_self()->get('select_join_all', $item);        
        $results = $wpdb->get_results($sql);
        return $results;
    }
    public static function select_fields_where($options) {
        global $wpdb;
        $sql = SQLTemplates::_self()->get('select_fields_where', $options);
        $results = $wpdb->get_results($sql);
        return $results;
    }
}


