<?php
$files = [
    'template-utils.php',
    'wordpress/table-base.php',
    'wordpress/page-handler.php',
    'wordpress/mwpc-locale.php',
    'wordpress/form-utils.php',
    'wordpress/form-detail-utils.php',
];
foreach($files as $f) {
    require_once WP_PLUGIN_DIR . '/mwpc/core/' . $f;
}

class Publishing extends TableBase {
    static public $project_type = array(
        'project'=>'Projeto', 
        'collaboration'=>'Colaboração', 
    );
    static public $publishing_type = array(
        'paper'=>'Artigo', 
        'patent'=>'Patente', 
    );
    function __construct($table_name) {
        parent::__construct(array(
            'singular' => 'publishing',
            'plural' => 'publishings',
        ), $table_name, Role::ADMIN);
        $this->configure('Lista de Publicações/Patentes',
                         'Publicações e Patentes');
        $this->fields = [
            'id', 
            'user_id', 
            'DOI',
            'name', 
            'project_type', 
            'publishing_type', 
        ];
        $this->defaults = CoreUtils::merge($this->fields, [
            0, 
            get_current_user_id(), 
            '', 
            '', 
            'project',
            'paper',
        ]);
        $this->fields_types = CoreUtils::merge($this->fields, [
            '',
            FormUtils::SelectFromTable([
                'sql'=>SQLTemplates::_self()->get('select_all', [
                    '%fields'=>'id as value, display_name as label',
                    '%tablename'=>'wp_users',
                ]),
                'selected_key'=>'id',
            ]),
            FormUtils::Input('text', 'Digite o DOI aqui', '', 512),
            FormUtils::Input('text', 'Digite o título do trabalho aqui', 'required', 512),
            FormUtils::SelectFromArray(['enum'=>Publishing::$project_type, 'selected_key'=>'project_type']),
            FormUtils::SelectFromArray(['enum'=>Publishing::$publishing_type, 'selected_key'=>'publishing_type']),
        ]);
        $this->labels = CoreUtils::merge($this->fields, [
            MWPCLocale::get('id'),
            MWPCLocale::get('teacher'),
            "DOI",
            "Título",
            "Feito em",
            "Tipo",
        ]);
        $this->is_sortable = CoreUtils::merge($this->fields,[
            false, false, true, false, false, false
        ]);
    }
    public function make_sql() {
        $s = Settings::_self();
        $sql_string = "CREATE TABLE IF NOT EXISTS %table (
            `id` INT NOT NULL AUTO_INCREMENT,
            `user_id` INT NOT NULL,
            `DOI` VARCHAR(512) NULL,
            `name` VARCHAR(512) NOT NULL,
            `project_type` ENUM('project', 'collaboration') DEFAULT 'project',
            `publishing_type` ENUM('paper', 'patent') DEFAULT 'paper',
            PRIMARY KEY  (`id`))
          ENGINE = InnoDB;";
        $this->sql =  TemplateUtils::t($sql_string, [
            '%table'=>SQLTemplates::full_table_name($this->table_name),
        ]);
    }
    public function column_project_type($item) {
        return Publishing::$project_type[$item['project_type']];
    }
    public function column_publishing_type($item) {
        return Publishing::$publishing_type[$item['publishing_type']];
    }
};