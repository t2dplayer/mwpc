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
        $keys = [
            'add_new',
            'delete_link',
            'div_message',
            'edit_link',
            'form_handler_footer',
            'form_handler_header',
            'input_checkbox',
            'message',
            'notice',
            'page_footer',
            'page_header',
        ];
        foreach($keys as $k) {
            $filename = str_replace("_", "-", $k) . ".php";
            $this->html_map[$k] = file_get_contents($path . $filename); 
        }
        foreach([
            'input'=>'form-fields/input',
        ] as $key=>$value) {
            $this->html_map[$key] = file_get_contents($path . $value . ".php"); 
        }
    }
    public function get($key, $data=array()) {
        if (!array_key_exists($key, $this->html_map)) CoreUtils::log("Invalid key -> ". $key);
        else return TemplateUtils::t(HTMLTemplates::_self()->html_map[$key], $data);
        return '';
    }
}