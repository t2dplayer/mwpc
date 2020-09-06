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

class Student extends TableBase {
    protected $types = array(
        'egress'=>'Egresso', 
        'coautor'=>'Co-Autor', 
        'graduate'=>'Graduação', 
        'mastering'=>'Mestrado', 
        'phd'=>'Doutorado',
    );
    function __construct($table_name) {
        parent::__construct(array(
            'singular' => 'student',
            'plural' => 'students',
        ), $table_name, Role::ADMIN);
        $this->configure('Lista de Discentes e Co-autores',
                         'Discentes, Egressos e Coautores');
        $this->fields = ['id', 'user_id', 'name', 'cpf', 'email', 'type'];
        $this->defaults = CoreUtils::merge($this->fields, [
            0, 
            get_current_user_id(), 
            '', 
            '', 
            '', 
            'graduate'
        ]);
        $this->fields_types = CoreUtils::merge($this->fields, [
            '',
            FormUtils::TableSelect(SQLTemplates::_self()->get('select_all', [
                '%fields'=>'id as value, display_name as label',
                '%tablename'=>'wp_users',
            ])),
            FormUtils::Input('text', 'Digite o nome aqui'),
            FormUtils::Input('text', 'Digite um CPF válido aqui'),
            FormUtils::Input('email', 'Digite um E-mail válido aqui'),
            FormUtils::Select($this->types),
        ]);
        $this->labels = CoreUtils::merge($this->fields, [
            MWPCLocale::get('id'),
            MWPCLocale::get('teacher'),
            MWPCLocale::get('name'),
            MWPCLocale::get('cpf'),
            MWPCLocale::get('email'),
            MWPCLocale::get('type'),
        ]);
        $this->is_sortable = CoreUtils::merge($this->fields,[
            false, false, true, false, false, true
        ]);
    }
    public function make_sql() {
        $s = Settings::_self();
        $sql_string = "CREATE TABLE IF NOT EXISTS %table (
            `id` INT NOT NULL AUTO_INCREMENT,
            `user_id` INT NOT NULL,
            `name` VARCHAR(255) NOT NULL,
            `cpf` VARCHAR(255) NULL,
            `email` VARCHAR(255) NULL,
            `type` ENUM('egress', 'coautor', 'graduate', 'mastering', 'phd') NOT NULL,
            PRIMARY KEY  (`id`))
          ENGINE = InnoDB;";
        $this->sql =  TemplateUtils::t($sql_string, [
            '%table'=>SQLTemplates::full_table_name($this->table_name),
        ]);
    }
    public function validate($item) {
        return [
            [
                'result'=>CoreUtils::validate_cpf($item['cpf']),
                'error_message'=>MWPCLocale::get('invalid_cpf'),
            ],
            [
                'result'=>CoreUtils::validate_email($item['email']),
                'error_message'=>MWPCLocale::get('invalid_email'),
            ],            
        ];
    }
    public function column_type($item) {
        CoreUtils::log($this->types);
        return $this->types[$item['type']];
    }
};