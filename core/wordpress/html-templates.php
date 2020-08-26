<?php
require_once WP_PLUGIN_DIR .'/mwpc/core/template-utils.php';

class HTMLTemplates {
    protected static $instance = null;
    public static function _self() {
        if (self::$instance == null) self::$instance = new self;
        return self::$instance;
    }
    protected $html_map = [];
    function __construct() {
        $path = WP_PLUGIN_DIR .'/mwpc/core/wordpress/html-templates/';
        $this->html_map = [
            'div_message'=>file_get_contents($path . 'div-message.php'),
            'page_header'=>file_get_contents($path . 'page-header.php'),
            'page_footer'=>file_get_contents($path . 'page-footer.php'),
        ];
    }
    public function get($key, $data=array()) {
        if (!array_key_exists($key, $this->html_map)) CoreUtils::log("Invalid key -> ". $key);
        else return TemplateUtils::t(HTMLTemplates::_self()->html_map[$key], $data);
        return '';
    }
}