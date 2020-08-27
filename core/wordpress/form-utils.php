<?php

class FormUtils {
    public static function Input($type, $placeholder, $size=50) {
        $html = HTMLTemplates::_self()->get('input', [
            '%type'=>$type,
            '%placeholder'=>$placeholder,
            '%size'=>$size,
        ]);
        return $html;
    }
};