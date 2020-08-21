<?php
require_once './db-manager.php';

function table_install()
{
    global $wpdb;
    $objects = [
    ];
    foreach ($objects as $obj) {
        $obj->create_table();
    }
}
register_activation_hook(__FILE__, 'table_install');