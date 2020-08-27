<?php

function create_form_handler(&$table) {
    global $wpdb;
    $table_name = Settings::_self()->table_name($table->table_name);
    if (isset($_REQUEST['nonce']) 
        && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {    
        $item = shortcode_atts($table->get_defaults(), $_REQUEST);
        CoreUtils::log($item);
    }
    echo HTMLTemplates::_self()->get('form_handler_header', [
        '%title'=>$table->project_settings['title'], 
        '%link'=>URLUtils::URLPage($table->project_settings['id']),
        '%back'=>MWPCLocale::get('back'),
        '%notice'=>'',
        '%message'=>'',
        '%nonce'=>wp_create_nonce(basename(__FILE__)),
        '%id'=>'',
    ]);

    echo HTMLTemplates::_self()->get('form_handler_footer', [
        '%addnew'=>'',
        '%save'=>MWPCLocale::get('save'),
    ]);
}