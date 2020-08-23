<?php
require_once "./database/header.php";

$sql = new CreateTable(SQLUtils::Make_Table('wordpress', 'student'), [
    new Integer(SQLUtils::Make_PK('id')),
    new Integer(SQLUtils::Make_NotNull('user_id')),
    new VarChar(SQLUtils::Make_SizedNotNull('name', 255)),
    new VarChar(SQLUtils::Make_SizedNotNull('cpf', 15)),
    new VarChar(SQLUtils::Make_SizedNotNull('email', 255)),
    new Enum(SQLUtils::Make_Enum('type', ['egress', 'coautor', 'graduate', 'mastering', 'phd'])),
    new PrimaryKey(SQLUtils::Id('id')),
]);
print($sql . "\n");