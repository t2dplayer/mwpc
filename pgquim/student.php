<?php
require_once WP_PLUGIN_DIR . '/mwpc/core/template-utils.php';
require_once WP_PLUGIN_DIR .'/mwpc/core/wordpress/table-base.php';

class Student extends TableBase {
    function __construct($table_name) {
        parent::__construct(array(
            'singular' => 'student',
            'plural' => 'students',
        ), $table_name);
    }
    public function make_sql() {
        $s = Settings::get_instance();
        $sql_string = "CREATE TABLE IF NOT EXISTS `%database_name`.`%table_name` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `user_id` INT NOT NULL,
            `name` VARCHAR(255) NOT NULL,
            `cpf` VARCHAR(255) NULL,
            `email` VARCHAR(255) NULL,
            `type` ENUM('egress', 'coautor', 'graduate', 'mastering', 'phd') NOT NULL,
            PRIMARY KEY  (`id`))
          ENGINE = InnoDB;";
        $this->sql = TemplateUtils::t($sql_string, TemplateUtils::full('student'));
    }
};