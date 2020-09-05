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
        $f = function($html, $selected, $options) {
            $options_html = "";
            foreach($options as $value=>$label) {
                $options_html .= HTMLTemplates::_self()->get('option', [
                    '%value'=>$value,
                    '%label'=>$label,
                    '%selected'=>($selected == $value) ? "selected" : "",
                ]);
            }
            $html = HTMLTemplates::_self()->get('select', [
                '%options'=>$options_html,
            ]);
            return $html;
        };
        return ['html'=>$html, 'options'=>$options, 'f'=>$f];
    }    
};