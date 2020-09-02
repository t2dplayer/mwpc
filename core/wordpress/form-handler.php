<?php

function create_form_handler(&$table) {
    global $wpdb;
    $table_name = Settings::_self()->table_name($table->table_name);
    $item = [];
    if (isset($_REQUEST['nonce']) 
        && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {    
        $item = shortcode_atts($table->get_defaults(), $_REQUEST);
    }
    CoreUtils::log($table->project_settings);
    add_meta_box(
        $table->project_settings['meta_box_id'],
        $table->project_settings['title'],
        $table->project_settings['meta_box_handler'],
        $table->project_settings['id'],
        'normal',
        'default'
    );    
    echo HTMLTemplates::_self()->get('form_handler_header', [
        '%title'=>$table->project_settings['title'], 
        '%link'=>URLUtils::URLPage($table->project_settings['id']),
        '%back'=>MWPCLocale::get('back'),
        '%notice'=>'',
        '%message'=>'',
        '%nonce'=>wp_create_nonce(basename(__FILE__)),
        '%id'=>'',
    ]);
    do_meta_boxes($table->project_settings['id'], 'normal', $item);
    echo HTMLTemplates::_self()->get('form_handler_footer', [
        '%addnew'=>'',
        '%save'=>MWPCLocale::get('save'),
    ]);  
}