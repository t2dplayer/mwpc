<?php
require_once "./database/header.php";

// use mvwp;

$builder = new TableBuilder();
$sql = $builder->create("wordpress", "user")
               ->add_child((new Integer('id'))->not()->null()->auto_increment()->comma())
               ->add_child((new Integer('user_id'))->not()->null()->comma())
               ->add_child((new VarChar('name'))->not()->null()->comma())
               ->add_child((new VarChar('cpf', 15))->not()->null()->comma())
               ->add_child((new VarChar('email'))->not()->null()->comma())
               ->add_child((new Enum('type', array('egress', 'coautor', 'graduate', 'mastering', 'phd')))->not()->null()->comma())
               ->add_child(new PrimaryKey('id'))
               ->engine("InnoDB")
               ->to_string();
print($sql);
