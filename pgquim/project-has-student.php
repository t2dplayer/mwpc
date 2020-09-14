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

class ProjectHasStudent extends TableBase {
    function __construct($table_name) {
        parent::__construct(array(
            'singular' => 'project_has_student',
            'plural' => 'project_has_student',
        ), $table_name, Role::ONLY_TABLE);
    }
    public function make_sql() {
        $sql_string = "CREATE TABLE IF NOT EXISTS %table (
            `id` INT NOT NULL AUTO_INCREMENT,
            `user_id` INT NOT NULL,
            `project_id` INT NOT NULL,            
            `student_id` INT NOT NULL,
            KEY(`id`), 
            PRIMARY KEY(`project_id`, `student_id`),
            INDEX `fk_project_has_student_project_idx` (`project_id` ASC),
            INDEX `fk_project_has_student_student_idx` (`student_id` ASC),
            CONSTRAINT `fk_project_has_student_project`
                FOREIGN KEY (`project_id`)
                REFERENCES %project (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
            CONSTRAINT `fk_project_has_student_student`
                FOREIGN KEY (`student_id`)
                REFERENCES %student (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            ) ENGINE = InnoDB;";
        $this->sql =  TemplateUtils::t($sql_string, [
            '%table'=>SQLTemplates::full_table_name($this->table_name),
            '%project'=>SQLTemplates::full_table_name('project'),
            '%student'=>SQLTemplates::full_table_name('student'),
        ]);
    }
};