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

class Paper extends TableBase {
    function __construct($table_name) {
        parent::__construct(array(
            'singular' => 'paper',
            'plural' => 'papers',
        ), $table_name, Role::ONLY_TABLE);
    }
    public function make_sql() {
        $sql_string = "CREATE TABLE IF NOT EXISTS %table (
            `id` INT NOT NULL AUTO_INCREMENT,
            `user_id` INT NOT NULL,
            `student_id` INT NOT NULL,
            `name` VARCHAR(255) NOT NULL,
            `year` INT NOT NULL,
            KEY(`student_id`), 
            PRIMARY KEY(`id`),
            INDEX `fk_student_idx` (`student_id` ASC),
            CONSTRAINT `fk_student`
                FOREIGN KEY (`student_id`)
                REFERENCES %student (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            ) ENGINE = InnoDB;";
        $this->sql =  TemplateUtils::t($sql_string, [
            '%table'=>SQLTemplates::full_table_name($this->table_name),
            '%student'=>SQLTemplates::full_table_name('student'),
            '%skill'=>SQLTemplates::full_table_name('skill'),
        ]);
    }
};