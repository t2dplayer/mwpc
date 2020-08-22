<?php
require_once WP_PLUGIN_DIR .'/mwpc/core/wordpress/table-base.php';
require_once WP_PLUGIN_DIR .'/mwpc/database/header.php';

class Student extends TableBase {
    function __construct() {
        parent::__construct(array(
            'singular' => 'student',
            'plural' => 'students',
        ));
        $builder = new TableBuilder();
        $s = Settings::get_instance();
        $this->sql = $builder->create($s->get_database_name(), $s->get_prefix() . "student")
                       ->add_child((new Integer('id'))->not()->null()->auto_increment()->comma())
                       ->add_child((new Integer('user_id'))->not()->null()->comma())
                       ->add_child((new VarChar('name'))->not()->null()->comma())
                       ->add_child((new VarChar('cpf', 15))->not()->null()->comma())
                       ->add_child((new VarChar('email'))->not()->null()->comma())
                       ->add_child((new Enum('type', array('egress', 'coautor', 'graduate', 'mastering', 'phd')))->not()->null()->comma())
                       ->add_child(new PrimaryKey('id'))
                       ->engine("InnoDB")
                       ->to_string();
        // $this->prepare_items();
        // $this->display();   
    }
};