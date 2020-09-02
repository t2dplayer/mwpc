<?php
require_once 'database-utils.php';
require_once 'settings.php';
require_once WP_PLUGIN_DIR .'/mwpc/core/core-utils.php';

function mwpc_table_install($objects)
{
    global $wpdb;
    foreach ($objects as $obj) {      
        $create_sql = $obj->get_create_sql();
        DatabaseUtils::create_table($create_sql);
    }
}

function mwpc_make_menu($settings) {
    $capability = "activate_plugins";
    add_menu_page(
        $settings['title'],
        $settings['menu_title'],
        $capability,
        $settings['id'],
        $settings['page_handler']
    );
    $add_new = MWPCLocale::get('add_new');
    add_submenu_page(
        $settings['id'],
        $add_new,
        $add_new,
        $capability,
        $settings['id'] . '_form_id',
        $settings['id'] . '_form_handler'
    );
}

function mwpc_make_admin_menu_items($objects) {
    foreach ($objects as $key=>$obj) {    
        if ($obj->role != Role::ADMIN) continue;
        mwpc_make_menu($obj->project_settings);
    }
}

function mwpc_make_menu_items($objects) {
    foreach ($objects as $key=>$obj) {
        if ($obj->role == Role::ADMIN) continue;
        mwpc_make_menu($obj->project_settings);
    }  
}

function mwpc_remove_admin_menu() {
    add_filter( 'plugin_action_links', function ( $actions, $plugin_file ) {
        if ( plugin_basename( __FILE__ ) === $plugin_file ) {
            unset( $actions['deactivate'] );
        }
        return $actions;
    }, 10, 2 );
    remove_menu_page( 'plugins.php' );    
}

