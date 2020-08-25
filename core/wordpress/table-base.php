<?php
// require_once WP_PLUGIN_DIR .'/mwpc/core/wordpress/html-builder/input.php';
require_once WP_PLUGIN_DIR .'/mwpc/core/template-utils.php';
require_once WP_PLUGIN_DIR .'/mwpc/core/wordpress/database-utils.php';
require_once WP_PLUGIN_DIR .'/mwpc/core/core-utils.php';
require_once WP_PLUGIN_DIR .'/mwpc/core/wordpress/settings.php';


if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

interface TableInterface {
    public function make_sql();
}

class TableBase extends WP_List_Table implements TableInterface {
    protected $sql = "";
    protected $table_name = "";
    protected $sql_map = [
        'bulk_delete'=>'DELETE FROM %table_name WHERE id IN(%ids);',
        'select_count'=>'SELECT COUNT(id) FROM %table_name;',
        'select_prepare_user'=>'SELECT * FROM %table_name ORDER BY %order_by %order LIMIT %d OFFSET %d;',
        'select_prepare_adm'=>'SELECT * FROM %table_name WHERE user_id = %user_id ORDER BY %order_by %order LIMIT %d OFFSET %d;',
    ];
    protected $html_map = [
        'url_edit'=>'<a href="?page=%form_id&id=%item_id">%content</a>',
        'url_delete'=>'<a href="?page=%page&action=delete&id=%item_id">%content</a>',
        'input_checkbox'=>'<input type="checkbox" name="%id" value="%value" />',
    ];
    public function __construct($array, $table_name = "") {
        global $status, $page;
        $this->table_name = $table_name;
        parent::__construct($array);
        $this->make_sql();
    }
    private function get_items($per_page, $sortable) {
        global $wpdb;
        $paged = isset($_REQUEST['paged']) ? ($per_page * max(0, intval($_REQUEST['paged']) - 1)) : 0;
        $sql_select = "";
        $data = [
            '%table_name'=>$this->table_name,
            '%order_by'=>(isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : array_key_first($sortable),
            '%order'=>(isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc'
        ];
        if (!is_admin()) {
            $sql_select = TemplateUtils::t($this->sql_map['select_prepare_user'], $data);
        } else {
            $user = wp_get_current_user();
            array_merge($data, ['%user_id'=>$user->ID]);
            $sql_select = TemplateUtils::t($this->sql_map['select_prepare_adm'], $data);
        }
        $this->items = $wpdb->get_results($wpdb->prepare($sql_select, $per_page, $paged), ARRAY_A);
    }
    // Required Methods
    public function column_name($item)
    {
        $actions = array(
            'edit'=>TemplateUtils::t($this->html_map['url_edit'], [
                '%form_id'=>'form_id', 
                '%item_id'=>$item['id'], 
                '%content'=>Settings::L('Alterar')
            ]),
            'delete'=>TemplateUtils::t($this->html_map['url_delete'], [
                '%page'=>$_REQUEST['page'], 
                '%item_id'=>$item['id'], 
                '%content'=>Settings::L('Apagar')
            ]), 
        );
        return sprintf('%s %s',
            $item['name'],
            $this->row_actions($actions)
        );
    }    
    public function column_default($item, $column_name) {
        return $item[$column_name];
    }
    public function column_cb($item) {
        $html = TemplateUtils::t($this->html_map['input_checkbox'], [
            '%id'=>'id[]',
            '%value'=>$item['id'],
        ]);        
        return $html;
    }
    public function prepare_items()
    {
        $per_page = 5;
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array(
            $this->get_columns(), 
            array(), 
            $sortable,
        );
        $this->process_bulk_action();
        $total_items = DatabaseUtils::count_items($this->sql_map['select_count'], $this->table_name);
        $this->items = $this->get_items($per_page, $sortable);
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page), // calculate pages count
        ));
    }
    // Bulk Actions
    public function get_bulk_actions()
    {
        $actions = array(
            'delete' => Settings::L('Apagar'),
        );
        return $actions;
    }
    public function process_bulk_action()
    {
        global $wpdb;
        $s = Settings::get_instance();
        $table_name = $s->prefix . $this->table_name;
        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) {
                $ids = implode(',', $ids);
            }
            if (!empty($ids)) {
                $sql_delete = TemplateUtils::t($this->sql_map['bulk_delete'], [
                    '%table_name'=>$table_name, 
                    '%ids'=>$ids
                ]);
                $wpdb->query($sql_delete);
            }
        }
    }
    // Not related with wordpress
    public function get_create_sql() {
        return $this->sql;
    }
    public function make_sql() {
        // you must implement this in TableBase subclass
    }
}