<?php
require_once './core/wordpress/html-builder/header.php';

// print((new Input("id[]", "checkbox"))
//         ->set_value('42')
//         ->to_string());

// $div = (new Div())->set_class("error")->set_content("this is my message")
// ->add_child((new P())->add_child((new Strong())->set_content("Error")));
// print($div)->to_string();

/*
<div id="id" class="error">
    <p>
        <strong>this is my message</strong>
    </p>
    content
</div>
*/

// $div = new Div([
//     ['id' => 'div-id'],
//     new P([
//         new Strong([
//             ['content' => 'this is my message']
//         ])
//     ],
//     ['id' => 'div-p'])
// ]);
// $div = new Div([
//     'id' => 'title of div'
// ]);

$div = new Div(['id' => 'title of div'], [new P()]);
print($div . "\n\n");

$div = new Div([new P()], ['id' => 'title of div']);
print($div . "\n\n");

$div = new Div(['id' => 'title of div']);
print($div . "\n\n");

$div = new Div([new P()]);
print($div . "\n\n");

$div = new Div(['id'=>'title of div', 'content'=>'div message'], [
    new P([
        new Strong(['content'=>'strong text'])
    ])
]);
print($div . "\n\n");

$div = new Div([
    new P([
        new Strong(['content'=>'strong text'])
    ])
], ['id'=>'title of div', 'content'=>'div message']);
print($div . "\n\n");