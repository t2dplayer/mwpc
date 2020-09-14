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

class ProjectHasPublishing extends TableBase {
    function __construct($table_name) {
        parent::__construct(array(
            'singular' => 'project_has_publishing',
            'plural' => 'project_has_publishings',
        ), $table_name, Role::ONLY_TABLE);
    }
    public function make_sql() {
        $sql_string = "CREATE TABLE IF NOT EXISTS %table (
            `id` INT NOT NULL AUTO_INCREMENT,
            `user_id` INT NOT NULL,
            `project_id` INT NULL,            
            `publishing_id` INT NOT NULL,
            KEY(`id`), 
            PRIMARY KEY(`publishing_id`),
            INDEX `fk_project_has_publishing_publishing_idx` (`publishing_id` ASC),
            CONSTRAINT `fk_project_has_publishing_publishing`
                FOREIGN KEY (`publishing_id`)
                REFERENCES %publishing (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            ) ENGINE = InnoDB;";
        $this->sql =  TemplateUtils::t($sql_string, [
            '%table'=>SQLTemplates::full_table_name($this->table_name),
            '%publishing'=>SQLTemplates::full_table_name('publishing'),
        ]);
    }
};