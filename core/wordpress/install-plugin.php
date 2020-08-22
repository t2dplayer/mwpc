<?php
require_once 'db-manager.php';
require_once 'settings.php';

function mwpc_table_install()
{
    global $wpdb;
    $objects = Settings::get_instance()->get_objects();
    foreach ($objects as $obj) {
        mwpc_create_table($obj->create_sql());
    }
}
