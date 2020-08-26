<?php
require_once WP_PLUGIN_DIR . '/mwpc/core/template-utils.php';
require_once WP_PLUGIN_DIR .'/mwpc/core/wordpress/table-base.php';
require_once WP_PLUGIN_DIR .'/mwpc/core/wordpress/page-handler.php';
require_once WP_PLUGIN_DIR .'/mwpc/core/wordpress/mwpc-locale.php';

class Student extends TableBase {
    function __construct($table_name) {
        parent::__construct(array(
            'singular' => 'student',
            'plural' => 'students',
        ), $table_name, Role::ADMIN);
        $this->configure('Lista de Discentes e Co-autores',
                         'Discentes, Egressos e Coautores');
    }
    public function make_sql() {
        $s = Settings::_self();
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
    public function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', 
            'user_id' => MWPCLocale::get('teacher'),
            'name' => MWPCLocale::get('name'),
            'cpf' => MWPCLocale::get('cpf'),
            'email' => MWPCLocale::get('email'),
            'type' => MWPCLocale::get('type'),
        );
        return $columns;
    }
};