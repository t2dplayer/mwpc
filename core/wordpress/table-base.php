<?php
require_once WP_PLUGIN_DIR .'/mwpc/core/wordpress/html-builder/input.php';

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

// function mwpc_admin_notices() {
//     echo (new Div())->set_class("error")->set_content("this is my message")->add_child((new P())->add_child((new Strong())->set_content("Error")))->to_string();
// }

class TableBase extends WP_List_Table {
    protected $sql = "";
    public function __construct($array) {
        global $status, $page;
        // add_action( 'mwpc_admin_notices', array( $this, 'output_errors' ) );
        parent::__construct($array);
    }
    public function column_default($item, $column_name) {
        return $item[$column_name];
    }
    public function column_cb($item) {
        return (new Input("id[]", "checkbox"))
                ->value($item['id'])
                ->to_string();
    }
    public function create_sql() {
        return $this->sql;
    }
}