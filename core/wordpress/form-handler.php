<?php

function create_form_handler(&$table) {
    global $wpdb;
    $table_name = Settings::_self()->table_name($table->table_name);
    if (isset($_REQUEST['nonce']) 
        && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {    
        $item = shortcode_atts($default, $_REQUEST);
    }
}