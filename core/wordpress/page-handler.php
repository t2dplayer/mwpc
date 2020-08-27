<?php
require_once 'html-templates.php';
require_once 'url-utils.php';

function create_page_handler(&$table)
{
    global $wpdb;
    $table->prepare_items();
    $message = "";
    if ('delete' === $table->current_action()) {
        $message = HTMLTemplates::_self()->get('div_message', [
            '%content'=>sprintf(MWPCLocale::get('deleted_records'), count($_REQUEST['id']))
        ]);   
    }
    echo HTMLTemplates::_self()->get('page_header', [
        '%id'=>$table->project_settings['id'],
        '%title'=>$table->project_settings['title'],
        '%link'=>URLUtils::URLPage($table->project_settings['id']),
        '%addnew'=>MWPCLocale::get('add_new'),
        '%message'=>$message,
        '%requestpage'=>$_REQUEST['page'],
    ]);
    $table->display();
    echo HTMLTemplates::_self()->get('page_footer');
}