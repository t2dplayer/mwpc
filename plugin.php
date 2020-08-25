<?php
/*
Plugin Name: Model Wordpress Controller
Author: Sérgio Vieira
Description: Development framework to create and manage custom database in Wordpress
License: Private
Version: 0.1
 */
require_once 'core/wordpress/settings.php';
require_once 'core/wordpress/install-plugin.php';

/* you must put your project header here */
function your_project_objects() {
    require_once 'pgquim/header.php';
    Settings::get_instance()->add_objects(array(
        new Student('student'),
    ));  
}

/* Now we need to create the menu items */
function admin_menu_install()
{
    your_project_objects();
    if (is_admin()) {
        mwpc_make_admin_menu_items(Settings::get_instance()->get_objects());
    } else {
        mwpc_remove_admin_menu();
    }
    mwpc_make_menu_items(Settings::get_instance()->get_objects());
}
add_action('admin_menu', 'admin_menu_install');

/* It's time to create all tables */
function table_install() {    
    mwpc_table_install(Settings::get_instance()->get_objects());
}
register_activation_hook(__FILE__, 'table_install');
