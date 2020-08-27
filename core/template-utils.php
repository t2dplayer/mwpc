<?php
require_once WP_PLUGIN_DIR .'/mwpc/core/wordpress/settings.php';

class TemplateUtils {
    public static function t($string, $data) {
        return str_replace(array_keys($data), array_values($data), $string);
    }
};