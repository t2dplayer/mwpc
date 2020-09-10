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

class StudentHasSkill extends TableBase {
    function __construct($table_name) {
        parent::__construct(array(
            'singular' => 'student_skill',
            'plural' => 'student_skills',
        ), $table_name, Role::ONLY_TABLE);
    }
    public function make_sql() {
        $sql_string = "CREATE TABLE IF NOT EXISTS %table (
            `id` INT NOT NULL AUTO_INCREMENT,
            `user_id` INT NOT NULL,
            `student_id` INT NOT NULL,
            `skill_id` INT NOT NULL,
            KEY(`id`),
            PRIMARY KEY (`student_id`, `skill_id`),
            INDEX `fk_student_has_skill_student_idx` (`student_id` ASC),
            INDEX `fk_student_has_skill_skill_idx` (`skill_id` ASC),
            CONSTRAINT `fk_student_has_skill_student`
                FOREIGN KEY (`student_id`)
                REFERENCES %student (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
            CONSTRAINT `fk_student_has_skill_skill`
                FOREIGN KEY (`skill_id`)
                REFERENCES %skill (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE)
            ENGINE = InnoDB;";
        $this->sql =  TemplateUtils::t($sql_string, [
            '%table'=>SQLTemplates::full_table_name($this->table_name),
            '%student'=>SQLTemplates::full_table_name('student'),
            '%skill'=>SQLTemplates::full_table_name('skill'),
        ]);
    }
};