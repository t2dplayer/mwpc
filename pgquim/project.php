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
require_once 'student.php';
require_once 'publishing.php';

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
                function($arr, $item_id) {
                    return "DELETE FROM mwpc_project_has_researchline WHERE project_id = $item_id;" ;
                }
            , true)
        );
    }
    protected function make_student() {
        $this->push_detail_field('student', 
            FormDetailUtils::InsertData('mwpc_student',
                function($arr, &$item){
                    $result['name']=esc_attr($arr['name']);
                    $result['cpf']=esc_attr($arr['cpf']);
                    $result['email']=esc_attr($arr['email']);
                    $result['thesis_name']=esc_attr($arr['thesis_name']);
                    $result['type']=esc_attr($arr['type']);
                    return $result;
                }
            )
        );
        $this->push_detail_field('student', 
            FormDetailUtils::InsertData('mwpc_project_has_student',
                function($arr, &$item){
                    $result['project_id']=esc_attr($item['id']);
                    $result['student_id']=esc_attr($item['student_id']);
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
                function($arr, $item_id) {
                    // delete_where'=>'DELETE FROM %tablename WHERE %attr;',
                    if (sizeof($arr) == 0
                    || !array_key_exists("delete_student", $arr)) return "";
                    $attr = SQLTemplates::make_where_attr(
                        $arr, 
                        "delete_student", 
                        "id"
                    );
                    $sql = SQLTemplates::_self()->get('delete_where', [
                        '%tablename'=>"mwpc_student",
                        '%attr'=>$attr,
                    ]);
                    return $sql;
                }
            )
        );
        $this->push_detail_field('student', 
            FormDetailUtils::DeleteData('mwpc_project_has_student',
                function($arr, $item_id) {
                    if (sizeof($arr) == 0
                        || !array_key_exists("delete_student", $arr)) return "";
                    // delete_where'=>'DELETE FROM %tablename WHERE %attr;',
                    $attr = SQLTemplates::make_where_attr(
                        $arr, 
                        "delete_student", 
                        "project_id"
                    );
                    $sql = SQLTemplates::_self()->get('delete_where', [
                        '%tablename'=>"mwpc_project_has_student",
                        '%attr'=>$attr,
                    ]);
                    return $sql;
                }
            )
        );
    }
    protected function make_publishing() {
        $this->push_detail_field('publishing', 
            FormDetailUtils::InsertData('mwpc_project_has_publishing',
                function($arr, &$item){                    
                    $result['doi']=esc_attr($arr['doi']);
                    $result['name']=esc_attr($arr['name']);
                    $result['year']=esc_attr($arr['year']);
                    if (array_key_exists('project_type', $arr)) {
                        $result['project_type']=esc_attr($arr['project_type']);
                        if ($result['project_type'] != 'collaboration') {
                            $result['project_id']=esc_attr($item['id']);
                        }
                    }
                    $result['publishing_type']=esc_attr($arr['publishing_type']);
                    $result['note']=esc_attr($arr['note']);
                    return $result;
                }
            )
        );
        $this->push_detail_field('publishing', 
            FormDetailUtils::SelectData(FormUtils::DataSQLJoinDetail(
                'project', // master table
                'project_has_publishing', // detail table
                'project' // foreign key in detail table
            ))
        );
        $this->push_detail_field('publising', 
            FormDetailUtils::DeleteData('mwpc_project_has_publishing', 
                function($arr, $item_id) {
                    if (sizeof($arr) == 0
                    || !array_key_exists("delete_publishing", $arr)) return "";
                    // delete_where'=>'DELETE FROM %tablename WHERE %attr;',
                    $attr = SQLTemplates::make_where_attr(
                        $arr, 
                        "delete_publishing", 
                        "id"
                    );
                    $sql = SQLTemplates::_self()->get('delete_where', [
                        '%tablename'=>"mwpc_project_has_publishing",
                        '%attr'=>$attr,
                    ]);
                    return $sql;
                })
        );
    }
    function __construct($table_name) {
        parent::__construct(array(
            'singular' => 'student',
            'plural' => 'students',
        ), $table_name);
        $this->configure('Lista de Projetos',
                         'Projetos');
        $this->fields = [
            'id', 
            'user_id', 
            'name', 
            'status', 
            'researchline',
            'student',
            'publishing',
        ];
        $this->make_researchline();
        $this->make_student();
        $this->make_publishing();
        $this->defaults = CoreUtils::merge($this->fields, [
            0, 
            get_current_user_id(), 
            '', 
            'remain', 
            '',
            '',
            '',
        ]);
        $this->fields_types = CoreUtils::merge($this->fields, [
            '',//id
            mwpc_make_user_select_field(),
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
                        [
                            'name'=>"text",
                            'email'=>'email',
                            'cpf'=>'text',
                            'thesis_name'=>'text',
                            'type'=>[
                                'type'=>'select',
                                'enum'=>Student::$types
                            ]
                        ]
                    ),
                    FormUtils::ComboboxItem(
                        'Colaborador', 
                        1, 
                        ['name'=>"text", 'email'=>'email']
                    ),
                ],
            ]),
            FormUtils::DynamicTableMasterDetail([
                'foreign_key'=>'project_id',
                'table_name'=>'project_has_publishing',
                'checkbox_id'=>'publishing',
                'fields'=>['id', 'doi', 'name', 'year', 'publishing_type', 'note'],
                'combobox'=>[
                    FormUtils::ComboboxItem(
                        'do Projeto', 
                        0, 
                        [
                            'doi'=>"text",
                            'name'=>"text",
                            'year'=>'number', 
                            'publishing_type'=>[
                                'type'=>'select',
                                'enum'=>Publishing::$publishing_type
                            ],
                            'note'=>[
                                'type'=>'textarea'
                            ],
                            'project_type'=>[
                                'type'=>'hidden',
                                'value'=>'project'
                            ]
                        ]
                    ),
                    FormUtils::ComboboxItem(
                        'de Colaboração', 
                        1, 
                        [
                            'doi'=>"text",
                            'name'=>"text",
                            'year'=>'number', 
                            'publishing_type'=>[
                                'type'=>'select',
                                'enum'=>Publishing::$publishing_type
                            ],
                            'note'=>[
                                'type'=>'textarea'
                            ],
                            'project_type'=>[
                                'type'=>'hidden',
                                'value'=>'collaboration'
                            ]
                        ]
                    ),
                ],
            ]),
        ]);
        $this->labels = CoreUtils::merge($this->fields, [
            MWPCLocale::get('id'),
            "Docente Responsável",
            "Nome do Projeto",
            "Status",
            "Linhas de Pesquisa",
            "Equipe",
            "Artigos ou Patentes"
        ]);
        $this->is_sortable = CoreUtils::merge($this->fields,[
            false, false, true, false, false, false, false
        ]);
    }
    public function make_sql() {
        $s = Settings::_self();
        $sql_string = "CREATE TABLE IF NOT EXISTS %table (
            `id` INT NOT NULL AUTO_INCREMENT,
            `user_id` INT NOT NULL,
            `name` VARCHAR(255) NOT NULL,
            `status` ENUM('remain', 'finished', 'new', 'disabled') DEFAULT 'remain',
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
        return DatabaseUtils::inner_detail_join([
            '%detailtable'=>'mwpc_project_has_student',
            '%mastertable'=>'mwpc_student',
            '%detailfield'=>'student',
            '%itemfield'=>'project',
            '%itemvalue'=>$item['id'],
        ], 'student_form_id', "master.id, master.name");
    }
    public function column_publishing($item) {        
        $html = DatabaseUtils::inner_detail_join([
            '%detailtable'=>SQLTemplates::full_table_name('project_has_publishing'),
            '%mastertable'=>SQLTemplates::full_table_name('project'),
            '%detailfield'=>'project',
            '%itemfield'=>'project',
            '%itemvalue'=>$item['id'],
        ], 'project_has_publishing_form_id', "detail.id, detail.name");         
        return $html;
    }
};