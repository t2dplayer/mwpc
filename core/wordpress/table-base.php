<?php
// require_once WP_PLUGIN_DIR .'/mwpc/core/template-utils.php';
// require_once WP_PLUGIN_DIR .'/mwpc/core/wordpress/database-utils.php';
// require_once WP_PLUGIN_DIR .'/mwpc/core/core-utils.php';
// require_once WP_PLUGIN_DIR .'/mwpc/core/wordpress/settings.php';
require_once WP_PLUGIN_DIR .'/mwpc/core/wordpress/sql-templates.php';
require_once 'admin-settings.php'; 

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

abstract class Role {
    const ADMIN = 0;// Show in admin and user wordpress account
    const USER = 1;// Show only in user wordpress account
}

class TableBase extends WP_List_Table {
    protected $sql = "";
    protected $table_name = "";
    public $role = 1;
    public $project_settings = [];  
    protected $fields = [];
    protected $labels = [];
    protected $defaults = [];
    protected $is_sortable = [];
    protected $fields_types = [];
    public function __construct($array, $table_name = "", $role = Role::USER) {
        global $status, $page;
        $this->project_settings = ProjectSettings::Make_Settings();
        $this->table_name = $table_name;
        $this->project_settings['id'] = $this->table_name;
        $this->project_settings['form_id'] = $table_name . "_form_id";
        $this->project_settings['page_handler'] = $table_name . "_page_handler";
        $this->project_settings['meta_box_id'] = $table_name . "_meta_box_id";
        $this->project_settings['meta_box_handler'] = $table_name . "_meta_box_handler";
        $this->role = $role;
        parent::__construct($array);
        $this->make_sql();
        $this->make_handlers();
    }
    private function make_handlers() {
        $_str = '
        function %id%name(%item) {
            $obj = Settings::_self()->get_object("%id");
            %lambda($obj%comma);
        }';
        foreach([
            ['_page_handler', 'create_page_handler', ''], 
            ['_form_handler', 'create_form_handler', ''],
            ['_meta_box_handler', 'create_meta_box_handler', '$item'],
        ] as $arr) {
            $function = TemplateUtils::t($_str, [
                '%id'=>$this->table_name,
                '%name'=>$arr[0],
                '%lambda'=>$arr[1],
                '%item'=>$arr[2],
                '%comma'=>($arr[2] != '') ? ", $arr[2]" : "",
            ]);
            eval($function);
        }
    }
    private function get_items($per_page, $sortable) {
        if (!function_exists('array_key_first')) {
            function array_key_first(array $arr)
            {
                foreach ($arr as $key => $unused) {
                    return $key;
                }
                return null;
            }
        }        
        global $wpdb;
        $paged = isset($_REQUEST['paged']) ? ($per_page * max(0, intval($_REQUEST['paged']) - 1)) : 0;
        $sql_select = "";
        $data = [
            '%tablename'=> Settings::_self()->get_prefix() . $this->table_name,
            '%orderby'=>(isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : array_key_first($sortable),
            '%order'=>(isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc'
        ];
        if (!is_admin()) {
            $sql_select = SQLTemplates::get('select_prepare_user', $data);
        } else {
            $user = wp_get_current_user();
            $data['%userid']=$user->ID;
            $sql_select = SQLTemplates::get('select_prepare_adm', $data);
        }
        $prepared_sql = $wpdb->prepare($sql_select, $per_page, $paged);
        return $wpdb->get_results($prepared_sql, ARRAY_A);
    }
    // Required Methods
    public function column_name($item)
    {
        $actions = array(
            'edit'=>HTMLTemplates::_self()->get('edit_link', [
                '%formid'=>$this->project_settings['form_id'], 
                '%itemid'=>$item['id'], 
                '%content'=>Settings::L('Alterar')
            ]),
            'delete'=>HTMLTemplates::_self()->get('delete_link', [
                '%page'=>$_REQUEST['page'], 
                '%itemid'=>$item['id'], 
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
        $html = HTMLTemplates::_self()->get('input_checkbox', [
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
        $total_items = DatabaseUtils::count_items($this->table_name);
        $this->items = $this->get_items($per_page, $sortable);
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page), // calculate pages count
        ));
    }
    public function get_columns()
    {
        if (sizeof($this->fields) != sizeof($this->labels)) {
            throw new Exception("Invalid size of fields and labels");
        }        
        $columns = array_combine($this->fields, $this->labels);
        $columns = array('cb'=>'<input type="checkbox" />') + $columns;
        return $columns;
    } 
    public function get_sortable_columns()
    {
        if (sizeof($this->fields) != sizeof($this->is_sortable)) {
            throw new Exception("Invalid size of fields and is_sortable");
        }
        $columns = [];
        for($i = 0; $i < sizeof($this->fields); $i++) {
            $f = &$this->fields[$i];
            $columns[$f] = array($f, $this->is_sortable[$i]);
        }
        return $columns;
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
        $s = Settings::_self();
        $table_name = Settings::_self()->table_name($this->table_name);
        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) {
                $ids = implode(',', $ids);
            }
            if (!empty($ids)) {
                $sql_delete = SQLTemplates::get('bulk_delete', [
                    '%tablename'=>$table_name, 
                    '%ids'=>$ids
                ]);
                $wpdb->query($sql_delete);
            }
        }
    }
    public function column_user_id($item) {
        global $wpdb;
        $sql = SQLTemplates::_self()->get('select_where_id', [
            '%tablename'=> 'wp_users',
        ]);
        $items = $wpdb->get_row($wpdb->prepare($sql, $item['user_id']), ARRAY_A);
        return '<em>' . $items['display_name'] . '</em>';
    }      
    // Not related with wordpress
    public function get_create_sql() {
        return $this->sql;
    }
    public function make_sql() {
        // you must implement this in TableBase subclass
    }
    public function configure($title, $menu_title) {
        $this->project_settings['title'] = $title;
        $this->project_settings['menu_title'] = $menu_title;
    }
    public function get_fields() {
        return $this->fields;
    }
    public function get_form_fields() {
        if (sizeof($this->fields) != sizeof($this->fields_types)) {
            throw new Exception("Invalid size of fields and fields_types");
        }
        $columns = [];
        for($i = 0; $i < sizeof($this->fields); $i++) {
            if(empty($this->fields_types[$i])) continue;
            $f = &$this->fields[$i];
            $columns[$f] = $this->fields_types[$i];
        }
        return $columns;
    }
    public function get_defaults() {
        if (sizeof($this->fields) != sizeof($this->defaults)) {
            throw new Exception("Invalid size of fields and defaults");
        }
        $columns = [];
        for($i = 0; $i < sizeof($this->fields); $i++) {
            $f = &$this->fields[$i];
            $columns[$f] = $this->defaults[$i];
        }
        return $columns;
    }
    public function validate($item) {
        return true;
    }
}