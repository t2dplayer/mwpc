<?php
/*
Plugin Name: Model Wordpress Controller
Author: Sérgio Vieira
Description: Development framework to create and manage custom database in Wordpress
License: Private
Version: 0.1
 */

require_once 'core/mwpc-users-plugin-options.php';
require_once 'core/wordpress/settings.php';
require_once 'core/wordpress/install-plugin.php';
require_once 'core/wordpress/page-handler.php';
require_once 'core/wordpress/form-handler.php';
require_once 'core/wordpress/meta-box-handler.php';


/* you must put your project header here */
function your_project_objects() {
    require_once 'pgquim/header.php';
    Settings::_self()->add_objects([
        'student'=>new Student('student'),
        'researchline'=>new ResearchLine('researchline'),
        'project'=>new Project('project'),
        'project_has_publishing'=>new Publishing('project_has_publishing'),
        'project_has_researchline'=>new ProjectHasResearchLine('project_has_researchline'),
        'project_has_student'=>new ProjectHasStudent('project_has_student'),
    ]);
}

// function get_display_name($user_id) {
//     if (!$user = get_userdata($user_id))
//         return false;
//     return $user->data->display_name;
// }

/* Now we need to create the menu items */
function mwpc_plugin_admin_menu_install()
{
    your_project_objects();
    if (current_user_can('administrator')) {
        mwpc_make_admin_menu_items(Settings::_self()->get_objects());
    } else {
        mwpc_remove_admin_menu();
    }
    mwpc_make_menu_items(Settings::_self()->get_objects());
}
add_action('admin_menu', 'mwpc_plugin_admin_menu_install');

/* It's time to create all tables */
function mwpc_plugin_table_install() {
    your_project_objects();
    mwpc_table_install(Settings::_self()->get_objects());
}
register_activation_hook(__FILE__, 'mwpc_plugin_table_install');
