<?php

// It's creates the fields of add entry form
function create_meta_box_handler($table, $item) {
    $labels = $table->get_columns();
    $html = "";
    $html .= HTMLTemplates::_self()->get('table_header');
    $is_selected = "";
    foreach ($table->get_form_fields() as $key=>$value) {
        if ($key == 'id') continue;
        $content = $value;
        if (is_array($value)) {
            if (isset($value['f'])) {
                $f = $value['f'];
                $value['options']['item'] = $item;
                $content = $f($value['options']);
            } else {
                $content = $value['html'];
            }
        }
        $html .= HTMLTemplates::_self()->get('table_tr', [
            '%for'=>$key,
            '%label'=>$labels[$key],
            '%content'=>$content,
            '%value'=>(isset($item) && key_exists($key, $item) && !is_array($item[$key])) ? $item[$key] : "",
            '%id'=>$key,
        ]);
    }
    $html .= HTMLTemplates::_self()->get('table_footer');
    echo $html;    
}