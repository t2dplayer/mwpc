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

class Student extends TableBase {
    static public $types = array(
        'egress'=>'Egresso', 
        'coautor'=>'Co-Autor', 
        'graduate'=>'Graduação', 
        'mastering'=>'Mestrado', 
        'phd'=>'Doutorado',
        'external'=>'Colaborador Externo',
    );
    function __construct($table_name) {
        parent::__construct(array(
            'singular' => 'student',
            'plural' => 'students',
        ), $table_name);
        $this->configure('Lista de Colaboradores',
                         'Colaboradores');
        $this->fields = [
            'id', 
            'user_id', 
            'name', 
            'cpf', 
            'email', 
            'type',
            'thesis_name', 
        ];
        $this->defaults = CoreUtils::merge($this->fields, [
            0, 
            get_current_user_id(), 
            '', 
            '', 
            '', 
            'graduate',
            '',
        ]);


        $this->fields_types = CoreUtils::merge($this->fields, [
            '',
            mwpc_make_user_select_field(),
            FormUtils::Input('text', 'Digite o nome aqui'),
            FormUtils::Input('text', 'Digite um CPF válido aqui', '', 14),
            FormUtils::Input('email', 'Digite um E-mail válido aqui', ''),
            FormUtils::SelectFromArray(['enum'=>Student::$types, 'selected_key'=>'type']),
            FormUtils::Input('text', 'Digite o nome da tese/dissertação aqui', '', 512),
        ]);
        $this->labels = CoreUtils::merge($this->fields, [
            MWPCLocale::get('id'),
            MWPCLocale::get('teacher'),
            MWPCLocale::get('name'),
            MWPCLocale::get('cpf'),
            MWPCLocale::get('email'),
            MWPCLocale::get('type'),
            "Título da Tese/Dissertação",
        ]);
        $this->is_sortable = CoreUtils::merge($this->fields,[
            false, false, true, false, false, true, false
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
            `thesis_name` VARCHAR(512) NULL,
            `type` ENUM('egress', 'coautor', 'graduate', 'mastering', 'phd', 'external') DEFAULT 'external',
            KEY(`cpf`),
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
        return Student::$types[$item['type']];
    }
    public function column_cpf($item) {
        if (!array_key_exists('cpf', $item)) return '-';
        $cpf = $item['cpf'];
        if (strlen($item['cpf']) == 0) return '-';
        if (strlen($cpf) != 11) {
            $cpf = str_replace([".", "-", ",", ";"], '', $cpf);
        }
        return CoreUtils::mask("###.###.###-##", $cpf);
    }
};