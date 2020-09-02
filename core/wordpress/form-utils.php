<?php

class FormUtils {
    public static function Input($type, $placeholder, $required='required', $size=50) {
        $html = HTMLTemplates::_self()->get('input', [
            '%type'=>$type,
            '%placeholder'=>$placeholder,
            '%size'=>$size,
            '%required'=>$required,
        ]);
        return $html;
    }
    public static function Select($options=array()) {
        $options_html = "";
        foreach($options as $value=>$label) {
            $options_html .= HTMLTemplates::_self()->get('option', [
                '%value'=>$value,
                '%label'=>$label,
            ]);
        }
        $html = HTMLTemplates::_self()->get('select', [
            '%options'=>$options_html,
        ]);
        return $html;
    }    
};