<?php

// It's creates the fields of add entry form
function create_meta_box_handler($table, $item) {
    CoreUtils::log($item);
    $labels = $table->get_columns();
    $html = "";
    $html .= HTMLTemplates::_self()->get('table_header');
    $is_selected = "";
    CoreUtils::log($table->get_form_fields());    
    foreach ($table->get_form_fields() as $key=>$value) {
        $content = $value;
        if (is_array($value)) {
            if (isset($value['f'])) {
                $f = $value['f'];            
                $content = $f($value['html'], $item['type'], $value['options']);    
            } else {
                $content = $value['html'];
            }
        }
        $html .= HTMLTemplates::_self()->get('table_tr', [
            '%for'=>$key,
            '%label'=>$labels[$key],
            '%content'=>$content,
            '%value'=>$item[$key],
            '%id'=>$key,
        ]);        
    }
    $html .= HTMLTemplates::_self()->get('table_footer');
    echo $html;    
}