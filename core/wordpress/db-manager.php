<?php

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

function create_table($sql) {
    if (!is_valid_string($sql)) throw new Exception('Invalid SQL.');
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    global $wpdb;
    dbDelta($sql);    
}

