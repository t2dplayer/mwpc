<?php
require_once 'database-utils.php';
require_once 'settings.php';
require_once WP_PLUGIN_DIR .'/mwpc/core/core-utils.php';

function mwpc_table_install()
{
    global $wpdb;
    $objects = Settings::get_instance()->get_objects();
    foreach ($objects as $obj) {      
        $create_sql = $obj->get_create_sql();
        DatabaseUtils::create_table($create_sql);
    }
}
