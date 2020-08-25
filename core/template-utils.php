<?php
require_once WP_PLUGIN_DIR .'/mwpc/core/wordpress/settings.php';

class TemplateUtils {
    public static function t($string, $data) {
        return str_replace(array_keys($data), array_values($data), $string);
    }
    public static function full($table_name) {
        return [
            '%database_name'=>Settings::get_instance()->get_database_name(),
            '%table_name'=>Settings::get_instance()->get_prefix() . $table_name,
        ];
    }
};