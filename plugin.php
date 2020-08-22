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

function wordpress_loaded() {
    /* you must put your project header here **/
    require_once 'pgquim/header.php';
    mwpc_table_install();
}
register_activation_hook(__FILE__, 'wordpress_loaded');