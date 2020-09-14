<?php

class FormUtils {
    public static function Input($type, $placeholder, $required='required', $size=50) {
        $html = HTMLTemplates::_self()->get('input', [
            '%type'=>$type,
            '%placeholder'=>$placeholder,
            '%size'=>$size,
            '%required'=>$required,
        ]);
        return ['html'=>$html, 'f'=>null];
    }
    public static function SelectFromArray($options=array()) {
        $html = HTMLTemplates::_self()->get('select');
        $f = function($options) {
            $options_html = "";
            CoreUtils::log($options, "[SELECTARRAY]");
            foreach($options['enum'] as $value=>$label) {
                CoreUtils::log($options['item'][$options['selected_key']], "[SELECTEDKEY]");
                CoreUtils::log($value, "[VALUE]");
                $is_selected = ($options['item'][$options['selected_key']] == $value);
                $options_html .= HTMLTemplates::_self()->get('option', [
                    '%value'=>$value,
                    '%label'=>$label,
                    '%selected'=> ($is_selected == true) ? "selected" : "",
                ]);
            }
            $html = HTMLTemplates::_self()->get('select', [
                '%options'=>$options_html,
            ]);
            return $html;
        };
        return ['html'=>$html, 'options'=>$options, 'f'=>$f];
    }
    public static function SelectFromTable($options=array()) {
        $html = HTMLTemplates::_self()->get('select');
        $f = function($options) {
            global $wpdb;
            $options_html = "";
            $rows = $wpdb->get_results($options['sql']);
            foreach($rows as $row) {
                $options_html .= HTMLTemplates::_self()->get('option', [
                    '%value'=>$row->value,
                    '%label'=>$row->label,
                    '%selected'=>($options['item'][$options['selected_key']] == $row->value) ? "selected" : "",
                ]);
            }
            $html = HTMLTemplates::_self()->get('select', [
                '%options'=>$options_html,
            ]);
            return $html;
        };
        return ['html'=>$html, 'options'=>$options, 'f'=>$f];
    }
    private static function GetDetailIDs($options) {
        $result = array();
        if (key_exists('item', $options)
            && isset($options['item'])) {
            if (key_exists($options['table_name'], $options['item'])) {            
                $rows = $options['item'][$options['table_name']];
                if (is_array($rows)) {
                    foreach($rows as $r) {
                        $field_name = $options['table_name']."_id";
                        array_push($result, $r->$field_name);
                    }
                }
            }
        }
        return $result;
    }
    public static function DataSQL($sql_template, $master, $detail) {
        $prefix = Settings::_self()->get_prefix();
        return [
            'sql_template'=>$sql_template,
            'data'=> [
                '%detailtable'=>$prefix . $detail,
                '%mastertable'=>$prefix . $master,
                '%detailfield'=>$master,
            ]
        ];  

    }
    // used to show stored values on edit entry form
    public static function DataSQLJoinAll($master, $detail) {
        /*
        'SELECT master.*, detail.* FROM %detailtable as detail 
        inner join %mastertable as master on detail.%detailfield_id = master.id 
        where detail.%itemfield_id = %itemvalue;',
        */
        $data_sql = FormUtils::DataSQL('select_join_all', $master, $detail);
        $data_sql['data']['%itemfield'] = $master;
        return $data_sql;
    }
    // used to show stored values on edit entry form
    public static function DataSQLJoinDetail($master, $detail, $detailfield) {
        /*
        SELECT master.*, detail.* FROM %detailtable AS detail 
        INNER JOIN %mastertable AS master ON detail.%detailfield_id = master.id 
        WHERE detail.%detailfield_id = %itemvalue;
        */
        $data_sql = FormUtils::DataSQL('select_join_detail', $master, $detail);
        $data_sql['data']['%detailfield'] = $detailfield;
        return $data_sql;
    }
    public static function DataSQLPrepareJoinDetail($master, $detail, $itemfield) {
        /*
        SELECT master.*, detail.* FROM %detailtable as detail inner join %mastertable 
        as master on detail.%detailfield_id = master.id 
        where detail.%itemfield_id = %d;
        */
        $data_sql = FormUtils::DataSQL('prepare_select_join_all', $master, $detail);
        $data_sql['data']['%itemfield'] = $itemfield;
        return $data_sql;
    }
    public static function DataSQLPrepareJoinDetailFields($master, $detail, $itemfield, $fields = array()) {
        /*
        SELECT %fields FROM %detailtable as detail inner join %mastertable 
        as master on detail.%detailfield_id = master.id 
        where detail.%itemfield_id = %d;
        */
        $data_sql = FormUtils::DataSQL('prepare_select_fields_join_all', $master, $detail);
        $data_sql['data']['%itemfield'] = $itemfield;
        $str_fields = "";
        $counter = 0;
        foreach($fields as $f) {
            $str_fields .= "master." . $f;
            if ($counter++ < sizeof($fields) - 1) {
                $str_fields .= ", ";
            }
        }
        $data_sql['data']['%fields'] = $str_fields;
        return $data_sql;
    }
    public static function TableMultiSelect($options=array()) {
        $html = HTMLTemplates::_self()->get('table_multiselect');
        $sql = SQLTemplates::_self()->get("select_all", [
            '%fields'=>'id as value, name as label',
            '%tablename'=>SQLTemplates::_self()->full_table_name($options['table_name'])
        ]);
        $options['sql'] = $sql;
        $f = function($options) {
            global $wpdb;
            $get_results = $wpdb->get_results($options['sql']);
            $columns = "";
            $rows = "";
            foreach($options['fields'] as $field) {
                $columns .= HTMLTemplates::_self()->get('th_column', [
                    '%label'=>MWPCLocale::get($field),
                ]);
            }
            $selected_ids = FormUtils::GetDetailIDs($options);
            foreach($get_results as $row) {
                $rows .= "<tr>";
                $rows .= HTMLTemplates::_self()->get('th_scope', [
                    '%id'=>$options['table_name'] . "[]",
                    '%value'=>$row->value,
                    '%checked'=>in_array($row->value, $selected_ids) ? "checked" : "",
                ]);                
                foreach ($row as $r) {
                    $rows .= HTMLTemplates::_self()->get('td', [
                        '%value'=>$r,
                    ]);
                }
                $rows .= "<tr />";
            }
            $html = HTMLTemplates::_self()->get('table_multiselect', [
                '%searchlabel'=>MWPCLocale::get("search"),
                '%searchplaceholder'=>MWPCLocale::get("search"),
                '%columns'=>$columns,
                '%rows'=>$rows,
            ]);
            return $html;
        };
        return ['html'=>$html, 'options'=>$options, 'f'=>$f];
    }
    private static function CreateCombobox(&$html, &$options) {
        if (key_exists('combobox', $options)) {
            $opt = "";
            $ifelse = "";
            $table_values = "";
            $counter = 0;
            foreach ($options['combobox'] as $arr) {
                if (!key_exists('fields', $arr)) continue;
                $jsfields = "";
                $size = sizeof($arr['fields']);
                $counter = 0;
                $jsfields = "";
                foreach($arr['fields'] as $field=>$type) {
                    if (is_array($type)) {
                        $enum = "";
                        foreach($type['enum'] as $k=>$v) {
                            $enum .= HTMLTemplates::_self()->get('dynamic_option', [
                                '%value'=>$k,
                                '%label'=>$v
                            ]);
                        }
                        $jsfields .= HTMLTemplates::_self()->get('js_field_' . $type['type'], [
                            '%label'=>MWPCLocale::get($field),
                            '%options'=>$enum,
                            '%id'=>$field,
                        ]);                        
                    } else {
                        $jsfields .= HTMLTemplates::_self()->get('js_field', [
                            '%label'=>MWPCLocale::get($field),
                            '%type'=>$type,
                            '%id'=>$field,
                        ]);
                    }
                    if (++$counter % 3 == 0) {
                        $jsfields .= "</tr><tr>";
                    }
                }
                $ifelse .= "if(value==" . $arr['value'] . "){\n";
                $ifelse .= HTMLTemplates::_self()->get('js_table', [
                    '%jsfields'=>$jsfields,
                ]);
                $ifelse .= "}";
                if ($counter < sizeof($options['combobox']) - 1) {
                    $ifelse .= " else ";
                }
                $opt .= HTMLTemplates::_self()->get('dynamic_option', [
                    '%value'=>$arr['value'],
                    '%label'=>$arr['label'],
                ]);
                $counter++;
            }
            $html .= HTMLTemplates::_self()->get('dynamic_combobox', [
                '%options'=>$opt,
                '%ifelse'=>$ifelse,
                '%tablevalues'=>$table_values,
            ]);
        }
    }
    public static function ComboboxItem($label, $value, $fields) {
        return [
            'label'=>$label,
            'value'=>$value,
            'fields'=>$fields,
            'type'=>"text",
        ];
    }
    private static function MakeColumns($fields = array()) {
        $columns = "";
        foreach($fields as $field) {
            $columns .= HTMLTemplates::_self()->get('th_column', [
                '%label'=>MWPCLocale::get($field),
            ]);
        }
        $columns .= '<th style="width:5%" class="manage-column check-column" scope="col"></th>';
        return $columns;
    }
    private static function MakeRows(&$get_results, $checkboxid) {
        $rows = "";
        if (is_iterable($get_results)) {
            foreach($get_results as $row) {
                $rows .= '<tr id="mwpc-detail-row-'. $checkboxid . '-' . $row['id'] . '">';
                $str = "";
                $counter = 0;
                foreach ($row as $key=>$value) {
                    $str .= $key . ":" . $value;
                    if ($counter++ < sizeof($row) - 1) {
                        $str .= ";";
                    }
                }
                $rows .= HTMLTemplates::_self()->get('th_hidden', [
                    '%id'=>$checkboxid . "[]",
                    '%value'=>$str,
                ]);
                foreach ($row as $r) {
                    $rows .= HTMLTemplates::_self()->get('td', [
                        '%value'=>$r,
                    ]);
                }
                $rows .= HTMLTemplates::_self()->get('delete_button', [
                    '%label'=>MWPCLocale::get('delete'),
                    '%itemid'=>$row['id'],
                ]);
                $rows .= "<tr />";
            }
        } else {
            CoreUtils::log('Variable is not iterable!');
        }
        return $rows;
    }
    private static function GetResults(&$options) {
        global $wpdb;
        $get_results = $wpdb->get_results($options['sql'], ARRAY_A);
        return $get_results;
    }
    public static function DynamicTableMasterDetail($options=array()) {
        $html = HTMLTemplates::_self()->get('detail_table_multiselect');        
        $fields = "";
        if (key_exists('fields', $options)) {
            $fields = implode(',', $options['fields']);
        } else {
            CoreUtils::log("Error: key 'field' wasn't defined in options");
        }
        $table_name = "";
        if (key_exists('table_name', $options)) {
            $table_name = SQLTemplates::_self()->full_table_name($options['table_name']);
        } else {
            CoreUtils::log("Error: key 'table_name' wasn't defined in options");
        }
        $sql = SQLTemplates::_self()->get("select_fields_where", [
            '%fields'=>$fields,
            '%tablename'=>$table_name,
        ]);        
        $options['sql'] = $sql;
        $f = function($options) {
            global $wpdb;
            $fields = $options['fields'];            
            $columns = FormUtils::MakeColumns($fields);
            $options['sql'] = TemplateUtils::t($options['sql'], [
                '%where'=>'user_id = %d AND '. $options['foreign_key'] . ' = %d'
            ]);
            $options['sql'] = $wpdb->prepare(
                $options['sql'],
                get_current_user_id(),
                isset($_REQUEST['id']) ? $_REQUEST['id'] : "0"
            );
            $get_results = FormUtils::GetResults($options);
            $rows = Formutils::MakeRows($get_results, $options['checkbox_id']);
            $html = HTMLTemplates::_self()->get('detail_table_multiselect', [
                '%searchlabel'=>MWPCLocale::get("search"),
                '%searchplaceholder'=>MWPCLocale::get("search"),
                '%columns'=>$columns,
                '%rows'=>$rows,
            ]);
            FormUtils::CreateCombobox($html, $options);
            return $html;
        };
        return ['html'=>$html, 'options'=>$options, 'f'=>$f];
    }
    public static function DynamicTableMasterHasDetail($options=array()) {
        $html = HTMLTemplates::_self()->get('detail_table_multiselect');        
        /*
        SELECT master.*, detail.*
        FROM %detailtable as detail inner join %mastertable as master 
        on detail.%detailfield_id = master.id where detail.%itemfield_id = %d;        
        */
        $prefix = Settings::_self()->get_prefix();
        $options['sql'] = SQLTemplates::_self()->get("prepare_select_fields_join_all",
            $options['data_sql']['data']
        );        
        $f = function($options) {
            global $wpdb;            
            $columns = FormUtils::MakeColumns($options['fields']);
            $options['sql'] = $wpdb->prepare(
                $options['sql'],
                isset($_REQUEST['id']) ? $_REQUEST['id'] : "0"
            );                                    
            $get_results = FormUtils::GetResults($options);
            $rows = Formutils::MakeRows($get_results, $options['checkbox_id']);
            $html = HTMLTemplates::_self()->get('detail_table_multiselect', [
                '%searchlabel'=>MWPCLocale::get("search"),
                '%searchplaceholder'=>MWPCLocale::get("search"),
                '%columns'=>$columns,
                '%rows'=>$rows,
            ]);
            FormUtils::CreateCombobox($html, $options);
            return $html;
        };
        return ['html'=>$html, 'options'=>$options, 'f'=>$f];
    }        
};