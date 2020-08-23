<?php
require_once './core/wordpress/html-builder/header.php';

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