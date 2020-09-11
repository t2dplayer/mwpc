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
    public static function Select($options=array()) {
        $html = HTMLTemplates::_self()->get('select');
        $f = function($options) {
            $options_html = "";
            foreach($options['enum'] as $value=>$label) {
                $options_html .= HTMLTemplates::_self()->get('option', [
                    '%value'=>$value,
                    '%label'=>$label,
                    '%selected'=>($options['item'][$options['selected_key']] == $value) ? "selected" : "",
                ]);
            }
            $html = HTMLTemplates::_self()->get('select', [
                '%options'=>$options_html,
            ]);
            return $html;
        };
        return ['html'=>$html, 'options'=>$options, 'f'=>$f];
    }
    public static function TableSelect($options=array()) {
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
    public static function TableMultiSelectField($master, $detail) {
        $prefix = Settings::_self()->get_prefix();
        return [
            'sql_template'=>'select_join_all',
            'data'=>[
                '%detailtable'=>$prefix . $detail,
                '%mastertable'=>$prefix . $master,
                '%detailfield'=>$master,
                '%itemfield'=>$master,
            ]
        ];
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
                foreach($arr['fields'] as $field=>$type) {
                    $jsfields .= HTMLTemplates::_self()->get('js_field', [
                        '%label'=>MWPCLocale::get($field),
                        '%type'=>$type,
                        '%id'=>$field,
                    ]);
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
    public static function DetailTableMultiSelectField($master, $detail) {
        $prefix = Settings::_self()->get_prefix();
        return [
            'sql_template'=>'select_join_detail',
            'data'=> [
                '%detailtable'=>$prefix . $detail,
                '%mastertable'=>$prefix . $master,
                '%detailfield'=>$master,
            ]
        ];  
    }
    public static function DetailTableMultiSelect($options=array()) {
        $html = HTMLTemplates::_self()->get('detail_table_multiselect');
        $fields = implode(',', $options['fields']);
        $sql = SQLTemplates::_self()->get("select_fields_where", [
            '%fields'=>$fields,
            '%tablename'=>SQLTemplates::_self()->full_table_name($options['table_name']),
        ]);        
        $options['sql'] = $sql;
        $f = function($options) {
            global $wpdb;
            $fields = $options['fields'];
            $options['sql'] = TemplateUtils::t($options['sql'], [
                '%where'=>'user_id = %d AND '. $options['foreign_key'] . ' = %d'
            ]);
            $sql = $wpdb->prepare(
                $options['sql'],
                get_current_user_id(),
                isset($_REQUEST['id']) ? $_REQUEST['id'] : "0"
            );
            $get_results = $wpdb->get_results($sql, ARRAY_A);
            $columns = "";
            $rows = "";
            foreach($fields as $field) {
                $columns .= HTMLTemplates::_self()->get('th_column', [
                    '%label'=>MWPCLocale::get($field),
                ]);
            }
            $columns .= '<th style="width:5%" class="manage-column check-column" scope="col"></th>';
            foreach($get_results as $row) {
                $rows .= '<tr id="mwpc-detail-row-'. $options['checkbox_id'] . '-' . $row['id'] . '">';
                $str = "";
                $counter = 0;
                foreach ($row as $key=>$value) {
                    $str .= $key . ":" . $value;
                    if ($counter++ < sizeof($row) - 1) {
                        $str .= ";";
                    }
                }
                $rows .= HTMLTemplates::_self()->get('th_hidden', [
                    '%id'=>$options['checkbox_id'] . "[]",
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