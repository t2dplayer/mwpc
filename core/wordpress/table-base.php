<?php
// require_once WP_PLUGIN_DIR .'/mwpc/core/template-utils.php';
// require_once WP_PLUGIN_DIR .'/mwpc/core/wordpress/database-utils.php';
// require_once WP_PLUGIN_DIR .'/mwpc/core/core-utils.php';
// require_once WP_PLUGIN_DIR .'/mwpc/core/wordpress/settings.php';
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
    protected $sql_map = [
        'bulkdelete'=>'DELETE FROM %tablename WHERE id IN(%ids);',
        'selectcount'=>'SELECT COUNT(id) FROM %tablename;',
        'selectprepareuser'=>'SELECT * FROM %tablename ORDER BY %orderby %order LIMIT %d OFFSET %d;',
        'selectprepareadm'=>'SELECT * FROM %tablename WHERE user_id = %userid ORDER BY %orderby %order LIMIT %d OFFSET %d;',
    ];
    protected $html_map = [
        'urledit'=>'<a href="?page=%formid&id=%itemid">%content</a>',
        'urldelete'=>'<a href="?page=%page&action=delete&id=%itemid">%content</a>',
        'inputcheckbox'=>'<input type="checkbox" name="%id" value="%value" />',
    ];
    public function __construct($array, $table_name = "", $role = Role::USER) {
        global $status, $page;
        $this->project_settings = ProjectSettings::Make_Settings();
        $this->table_name = $table_name;
        $this->project_settings['id'] = $this->table_name;
        $this->project_settings['pagehandler'] = $table_name."_page_handler";
        $this->role = $role;
        parent::__construct($array);
        $this->make_sql();
        eval('
        function '.$this->table_name.'_page_handler() {
            $obj = Settings::_self()->get_object("'.$this->table_name.'");
            create_page_handler($obj);
        }');        
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
            $sql_select = TemplateUtils::t($this->sql_map['selectprepareuser'], $data);
        } else {
            $user = wp_get_current_user();
            $data['%userid']=$user->ID;
            $sql_select = TemplateUtils::t($this->sql_map['selectprepareadm'], $data);
        }
        $this->items = $wpdb->get_results($wpdb->prepare($sql_select, $per_page, $paged), ARRAY_A);
    }
    // Required Methods
    public function column_name($item)
    {
        $actions = array(
            'edit'=>TemplateUtils::t($this->html_map['urledit'], [
                '%formid'=>$this->project_settings['formid'], 
                '%itemid'=>$item['id'], 
                '%content'=>Settings::L('Alterar')
            ]),
            'delete'=>TemplateUtils::t($this->html_map['urldelete'], [
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
        $html = TemplateUtils::t($this->html_map['inputcheckbox'], [
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
        $total_items = DatabaseUtils::count_items($this->sql_map['selectcount'], $this->table_name);
        $this->items = $this->get_items($per_page, $sortable);
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page), // calculate pages count
        ));
    }
    public function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', 
            'id' => 'id',
        );
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
        $table_name = Settings::_self()->get_prefix() . $this->table_name;
        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) {
                $ids = implode(',', $ids);
            }
            if (!empty($ids)) {
                $sql_delete = TemplateUtils::t($this->sql_map['bulkdelete'], [
                    '%tablename'=>$table_name, 
                    '%ids'=>$ids
                ]);
                $wpdb->query($sql_delete);
            }
        }
    }
    public function get_sortable_columns() {
        return [
            'id'=>'id'
        ];
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
}