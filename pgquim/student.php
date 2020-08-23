<?php
require_once WP_PLUGIN_DIR .'/mwpc/core/wordpress/table-base.php';
require_once WP_PLUGIN_DIR .'/mwpc/database/header.php';

class Student extends TableBase {
    function __construct() {
        parent::__construct(array(
            'singular' => 'student',
            'plural' => 'students',
        ));
        $s = Settings::get_instance();
        $this->sql = new CreateTable(SQLUtils::Make_Table('wordpress', $s->get_prefix(). 'student'), [
            new Integer(SQLUtils::Make_PK('id')),
            new Integer(SQLUtils::Make_NotNull('user_id')),
            new VarChar(SQLUtils::Make_SizedNotNull('name', 255)),
            new VarChar(SQLUtils::Make_SizedNotNull('cpf', 15)),
            new VarChar(SQLUtils::Make_SizedNotNull('email', 255)),
            new Enum(SQLUtils::Make_Enum('type', ['egress', 'coautor', 'graduate', 'mastering', 'phd'])),
            new PrimaryKey(SQLUtils::Id('id')),
        ]);
    }
};