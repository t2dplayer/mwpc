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

function pgquim_make_project_select_field($table_name) {
    if (current_user_can('administrator')) {
        return FormUtils::SelectFromTable([
            'sql'=>SQLTemplates::_self()->get('select_all', [
                '%fields'=>'id as value, name as label',
                '%tablename'=>SQLTemplates::full_table_name($table_name),
            ]),
            'selected_key'=>'project_id',
            ], true);
    } else {
        //'SELECT %fields FROM %tablename WHERE %where;'
        return FormUtils::SelectFromTable([
            'sql'=>SQLTemplates::_self()->get('select_fields_where', [
                '%fields'=>'id as value, name as label',
                '%tablename'=>SQLTemplates::full_table_name($table_name),
                '%where'=>'user_id = ' . get_current_user_id(),
            ]),
            'selected_key'=>'project_id',
            ]);
    }
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
        ), $table_name);
        $this->configure('Lista de Publicações/Patentes',
                         'Publicações e Patentes');
        $this->fields = [
            'id', 
            'user_id',
            'project_id', 
            'doi',
            'name',
            'year', 
            'project_type', 
            'publishing_type',
            'note', 
        ];
        $this->defaults = CoreUtils::merge($this->fields, [
            0, 
            get_current_user_id(),
            '', 
            '', 
            '',
            '',
            'project',
            'paper',
            '',
        ]);
        $this->fields_types = CoreUtils::merge($this->fields, [
            '',
            mwpc_make_user_select_field(),
            pgquim_make_project_select_field('project'),            
            FormUtils::Input('text', 'Digite o DOI aqui', '', 512),
            FormUtils::Input('text', 'Digite o título do trabalho aqui', 'required', 512),
            FormUtils::Input('number', 'Digite o ano da publicação do trabalho aqui', 'required'),
            FormUtils::SelectFromArray(['enum'=>Publishing::$project_type, 'selected_key'=>'project_type']),
            FormUtils::SelectFromArray(['enum'=>Publishing::$publishing_type, 'selected_key'=>'publishing_type']),
            FormUtils::TextArea('<em>NOME</em> do(s) discente(s) ou egresso(s), caso tenha nesta publicação, (considerar egressos dos últimos 5 anos).'),
        ]);
        $this->labels = CoreUtils::merge($this->fields, [
            MWPCLocale::get('id'),
            MWPCLocale::get('teacher'),
            "Projeto",
            "DOI",
            "Título",
            "Ano",
            "Feito em",
            "Tipo",
            "Autores",
        ]);
        $this->is_sortable = CoreUtils::merge($this->fields,[
            false, false, true, false, false, false, false, false, false
        ]);
    }
    public function make_sql() {
        $s = Settings::_self();
        $sql_string = "CREATE TABLE IF NOT EXISTS %table (
            `id` INT NOT NULL AUTO_INCREMENT,
            `user_id` INT NOT NULL,
            `project_id` INT DEFAULT 0,
            `doi` VARCHAR(512) NULL,
            `name` VARCHAR(512) NOT NULL,
            `year` YEAR NULL,
            `project_type` ENUM('project', 'collaboration') DEFAULT 'project',
            `publishing_type` ENUM('paper', 'patent') DEFAULT 'paper',
            `note` TEXT NULL,
            PRIMARY KEY  (`id`)
            ) ENGINE = InnoDB;";
        $this->sql =  TemplateUtils::t($sql_string, [
            '%table'=>SQLTemplates::full_table_name($this->table_name),
        ]);
    }
    public function column_project_id($item) {
        if (!array_key_exists('project_id', $item)
            || !isset($item['project_id'])) return "-";
        $result = DatabaseUtils::select_fields_where([
            "%fields"=>"name, id",
            "%tablename"=>SQLTemplates::full_table_name('project'),
            '%where'=>"id = " . $item['project_id'],
        ]);
        if (sizeof($result) == 0) return "";
        $url = HTMLTemplates::_self()->get('edit_link_row', [
            '%formid'=>'project_form_id',
            '%itemid'=>$result[0]->id,
            '%content'=>esc_attr($result[0]->name)
        ]);
        return $url;
    }
    public function column_project_type($item) {
        if (!array_key_exists('project_type', $item)
            || !isset($item['project_type'])) return "-";
        return Publishing::$project_type[$item['project_type']];
    }
    public function column_publishing_type($item) {
        if (!array_key_exists('publishing_type', $item)
            || !isset($item['publishing_type'])) return "-";
        return Publishing::$publishing_type[$item['publishing_type']];
    }
    public function column_note($item) {
        return esc_attr($item['note']);
    }
};