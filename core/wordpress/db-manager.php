<?php

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

function mwpc_create_table($sql) {
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    global $wpdb;
    dbDelta($sql);    
}

