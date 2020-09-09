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
        //CoreUtils::log($options);
        return $result;
    }
    public static function MultiSelectClass($options=array()) {
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
};