<?php
require_once './core/wordpress/html-builder/header.php';

print((new CheckBox('id[]', '42'))
        ->to_string());
print((new CheckBox('id'))
        ->value('43')
        ->to_string());
