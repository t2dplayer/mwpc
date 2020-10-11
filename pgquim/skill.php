<?php
$files = [
    'template-utils.php',
    'wordpress/table-base.php',
    'wordpress/page-handler.php',
    'wordpress/mwpc-locale.php',
    'wordpress/form-utils.php',
];
foreach($files as $f) {
    require_once WP_PLUGIN_DIR . '/mwpc/core/' . $f;
}

class Skill extends TableBase {    
    function __construct($table_name) {
        parent::__construct(array(
            'singular' => 'skill',
            'plural' => 'skills',
        ), $table_name, Role::ADMIN);
        $this->configure('Lista de Habilidade',
                         'Habilidades');
        $this->fields = ['id', 'user_id', 'name'];
        $this->defaults = CoreUtils::merge($this->fields, [
            0, 
            get_current_user_id(), 
            '',
        ]);
        global $wpdb;
        $this->fields_types = CoreUtils::merge($this->fields, [
            '',
            FormUtils::SelectFromTable([
                'sql'=>SQLTemplates::_self()->get('select_all', [
                    '%fields'=>'id as value, display_name as label',
                    '%tablename'=>$wpdb->prefix . 'users',
                ]),
                'selected_key'=>'id',
            ]),
            FormUtils::Input('text', 'Digite o nome da habilidade aqui'),
        ]);
        $this->labels = CoreUtils::merge($this->fields, [
            MWPCLocale::get('id'),
            MWPCLocale::get('teacher'),
            MWPCLocale::get('name'),
        ]);
        $this->is_sortable = CoreUtils::merge($this->fields,[
            false, false, true
        ]);
    }
    public function make_sql() {
        $sql_string = "CREATE TABLE IF NOT EXISTS %table (
            `id` INT NOT NULL AUTO_INCREMENT,
            `user_id` INT NOT NULL,
            `name` VARCHAR(255) NOT NULL,
            PRIMARY KEY  (`id`))
          ENGINE = InnoDB;";
        $this->sql =  TemplateUtils::t($sql_string, [
            '%table'=>SQLTemplates::full_table_name($this->table_name),
        ]);
    }
};