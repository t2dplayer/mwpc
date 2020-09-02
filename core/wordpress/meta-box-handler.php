<?php

// It's creates the fields of add entry form
function create_meta_box_handler($table, $item) {
    CoreUtils::log($table);
    $labels = $table->get_columns();
    echo HTMLTemplates::_self()->get('table_header');
    foreach ($table->get_form_fields() as $key=>$value) {
        echo HTMLTemplates::_self()->get('table_tr', [
            '%for'=>$key,
            '%label'=>$labels[$key],
            '%content'=>$value,
            '%value'=>"",
            '%id'=>$key,
            '%selected'=>"",
        ]);
    }
    echo HTMLTemplates::_self()->get('table_footer');
}