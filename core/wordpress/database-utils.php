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
        /*
        SELECT master.name FROM %detailtable as detail inner join %mastertable 
        as master on detail.%detailfield_id = master.id 
        where detail.%itemfield_id = %itemvalue;
        */
        global $wpdb;
        $sql = SQLTemplates::_self()->get('select_join', $item);        
        $results = $wpdb->get_results($sql);
        $str = "";
        foreach ($results as $r) {
            $str .= '&#9642;<em>'. esc_attr($r->name) .'</em></br>';
        }
        return $str;
    }
    public static function inner_detail_join($item, $form_id=null, $fields="detail.name") {
        /*
        SELECT %fields FROM %detailtable as detail inner join %mastertable 
        as master on detail.%detailfield_id = master.id 
        where detail.%itemfield_id = %itemvalue;
        */
        global $wpdb;        
        $url = null;
        if (isset($form_id)) {
            $url = HTMLTemplates::_self()->get('edit_link_row', [
                '%formid'=>$form_id,                
            ]);
        }
        $item['%fields'] = $fields;
        $sql = SQLTemplates::_self()->get('select_detail_join', $item);        
        $results = $wpdb->get_results($sql);
        $str = "";
        foreach ($results as $r) {
            if (isset($url)) {
                $str .= '&#9642;';
                $str .= TemplateUtils::t($url, [
                    '%itemid'=>$r->id, 
                    '%content'=>esc_attr($r->name)
                ]);
                $str .= '</br>';
            } else {
                $str .= '&#9642;<em>'. esc_attr($r->name) .'</em></br>';
            }
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
            $str .= '&#9642;<em>'. esc_attr($r->$output_field) .'</em></br>';
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


