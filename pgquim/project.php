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

class Project extends TableBase {
    protected $types = array(
        'remain'=>'Permanece',
        'finished'=>'Concluído',
        'new'=>'Novo',
        'disabled'=>'Desativado',
    );

    protected function make_researchline() {
        $closure = function($arr, &$item) {
            $result['project_id']=$item['id'];
            $result['researchline_id']=$arr[0];
            return $result;
        };
        $this->push_detail_field('researchline', 
            FormDetailUtils::InsertData('mwpc_project_has_researchline', 
                $closure)
        );
        $this->push_detail_field('researchline', 
            FormDetailUtils::SelectData(FormUtils::DataSQLJoinAll(
                'project', 
                'project_has_researchline'
            ))
        );
        $this->push_detail_field('researchline', 
            FormDetailUtils::DeleteData('mwpc_project_has_researchline', 
                $closure)
        );
    }
    protected function make_student() {
        $this->push_detail_field('student', 
            FormDetailUtils::InsertData('mwpc_student',
                function($arr, &$item){
                    $result['name']=$arr['name'];
                    $result['cpf']=$arr['cpf'];
                    $result['email']=$arr['email'];
                    $result['thesis_name']=$arr['thesis_name'];
                    $result['type']=$arr['type'];
                    return $result;
                }
            )
        );
        $this->push_detail_field('student', 
            FormDetailUtils::InsertData('mwpc_project_has_student',
                function($arr, &$item){
                    $result['project_id']=$item['id'];
                    $result['student_id']=$item['student_id'];
                    return $result;
                }
            )
        );
        $this->push_detail_field('student', 
            FormDetailUtils::SelectData(FormUtils::DataSQLJoinDetail(
                'project', 
                'project_has_student',
                'project'
            ))
        );
        $this->push_detail_field('student', 
            FormDetailUtils::DeleteData('mwpc_student',
                function($arr, &$item){
                    $result['id']=$item['id'];
                    return $result;
                }
            )
        );
        $this->push_detail_field('student', 
            FormDetailUtils::DeleteData('mwpc_project_has_student',
                function($arr, &$item){
                    $result['project_id']=$item['id'];
                    return $result;
                }
            )
        );
    }
    function __construct($table_name) {
        parent::__construct(array(
            'singular' => 'student',
            'plural' => 'students',
        ), $table_name, Role::ADMIN);
        $this->configure('Lista de Projetos',
                         'Projetos');
        $this->fields = [
            'id', 
            'user_id', 
            'name', 
            'status', 
            'researchline',
            'student',
        ];
        $this->make_researchline();
        $this->make_student();
        $this->defaults = CoreUtils::merge($this->fields, [
            0, 
            get_current_user_id(), 
            '', 
            'remain', 
            '',
            '',
        ]);
        $this->fields_types = CoreUtils::merge($this->fields, [
            '',//id
            FormUtils::SelectFromTable([//user_id
                'sql'=>SQLTemplates::_self()->get('select_all', [
                    '%fields'=>'id as value, display_name as label',
                    '%tablename'=>'wp_users',
                ]),
                'selected_key'=>'id',
            ]),
            FormUtils::Input('text', 'Digite o título do projeto aqui'),//name
            FormUtils::SelectFromArray(['enum'=>$this->types, 'selected_key'=>'status']),//status
            FormUtils::TableMultiSelect([
                'table_name'=>'researchline',
                'fields'=>['id', 'name'],
            ]),
            FormUtils::DynamicTableMasterHasDetail([
                'data_sql'=>FormUtils::DataSQLPrepareJoinDetailFields('student', 
                    'project_has_student', 
                    'project',
                    ['id', 'name', 'email',  'cpf', 'thesis_name', 'type']
                ),
                'foreign_key'=>'project_id',
                'table_name'=>'project_has_student',
                'checkbox_id'=>'student',
                'fields'=>['id', 'name', 'email',  'cpf', 'thesis_name', 'type'],
                'combobox'=>[
                    FormUtils::ComboboxItem(
                        'Discente', 
                        0, 
                        ['name'=>"text", 'email'=>'email', 'cpf'=>'text', 'thesis_name'=>'text', 'type'=>'text']
                    ),
                    FormUtils::ComboboxItem(
                        'Colaborador', 
                        1, 
                        ['name'=>"text", 'email'=>'email']
                    ),
                ],
            ]),   
        ]);
        $this->labels = CoreUtils::merge($this->fields, [
            MWPCLocale::get('id'),
            "Professor Responsável",
            "Nome do Projeto",
            "Status",
            "Linhas de Pesquisa",
            "Equipe",
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
            `name` VARCHAR(255) NOT NULL,
            `status` ENUM('remain', 'finished', 'new', 'disabled') NOT NULL,
            PRIMARY KEY  (`id`))
          ENGINE = InnoDB;";
        $this->sql =  TemplateUtils::t($sql_string, [
            '%table'=>SQLTemplates::full_table_name($this->table_name),
        ]);
    }
    public function column_status($item) {
        return $this->types[$item['status']];
    }
    public function column_researchline($item) {
        return DatabaseUtils::inner_join([
            '%detailtable'=>'mwpc_project_has_researchline',
            '%mastertable'=>'mwpc_researchline',
            '%detailfield'=>'researchline',
            '%itemfield'=>'project',
            '%itemvalue'=>$item['id'],
        ]);
    }
    public function column_student($item) {
        return DatabaseUtils::inner_join([
            '%detailtable'=>'mwpc_project_has_student',
            '%mastertable'=>'mwpc_student',
            '%detailfield'=>'student',
            '%itemfield'=>'project',
            '%itemvalue'=>$item['id'],
        ]);
    }

};