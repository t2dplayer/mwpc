<?php
require_once './core/wordpress/html-builder/check-box.php';

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
function mcwp_admin_notices() {
	echo '<div class="error"><p><strong>Error</strong>: this is my message</p></div>';
}

class TableBase extends WP_List_Table {
    public function __construct($array)
    {
        global $status, $page;
        add_action( 'mcwp_admin_notices', array( $this, 'output_errors' ) );
        parent::__construct($array);
    }
    public function column_default($item, $column_name)
    {
        return $item[$column_name];
    }
    public function column_cb($item)
    {
        return (new CheckBox("id[]"))
                ->value($item['id'])
                ->to_string();
    }
}