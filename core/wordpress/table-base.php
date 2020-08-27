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
    protected $is_sortable = [];
    public function __construct($array, $table_name = "", $role = Role::USER) {
        global $status, $page;
        $this->project_settings = ProjectSettings::Make_Settings();
        $this->table_name = $table_name;
        $this->project_settings['id'] = $this->table_name;
        $this->project_settings['pagehandler'] = $table_name."_page_handler";
        $this->role = $role;
        parent::__construct($array);
        $this->make_sql();
        $function = '
        function '.$this->table_name.'_page_handler() {
            $obj = Settings::_self()->get_object("'.$this->table_name.'");
            create_page_handler($obj);
        }';
        eval($function);
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
        $this->items = $wpdb->get_results($wpdb->prepare($sql_select, $per_page, $paged), ARRAY_A);
    }
    // Required Methods
    public function column_name($item)
    {
        $actions = array(
            'edit'=>HTMLTemplates::_self()->get('edit_link', [
                '%formid'=>$this->project_settings['formid'], 
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
                $sql_delete = SQLTemplates::get('bulkdelete', [
                    '%tablename'=>$table_name, 
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
    public function configure($title, $menu_title) {
        $this->project_settings['title'] = $title;
        $this->project_settings['menutitle'] = $menu_title;
    }
    public function get_fields() {
        return $this->fields;
    }
}