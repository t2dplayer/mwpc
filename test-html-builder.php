<?php
require_once './core/wordpress/html-builder/header.php';

print((new Input("id[]", "checkbox"))
        ->set_value('42')
        ->to_string());

$div = (new Div())->set_class("error")->set_content("this is my message")->add_child((new P())->add_child((new Strong())->set_content("Error")));
print($div)->to_string();