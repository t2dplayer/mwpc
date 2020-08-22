<?php
require_once WP_PLUGIN_DIR .'/mwpc/core/vality-check.php';

class HtmlElement {
    protected $arguments = array();
    protected $child = array();
    protected $tag  = "";
    protected $is_self_closing = false;
    protected $content = "";
    function __construct($id) {
        if (!is_valid_string($id)) return $this;
        $this->arguments['id'] = $id;
        $args = array('class', 'title', 'style');
        foreach ($args as &$a) {
            $this->arguments[$a] = "";
        }
    }
    public function set_class($arg) {
        if (!is_valid_string($arg)) return $this;
        $this->arguments['class'] = $arg;
        return $this;
    }
    public function set_title($arg) {
        if (!is_valid_string($arg)) return $this;
        $this->arguments['title'] = $arg;
        return $this;
    }
    public function set_style($arg) {
        if (!is_valid_string($arg)) return $this;
        $this->arguments['style'] = $arg;
        return $this;
    }    
    public function add_child($child) {
        if (!is_subclass_of($child, 'HtmlElement')) return $this;
        array_push($this->child, $child);
        return $this;
    }
    public function set_content($arg) {
        $this->content = $arg;
        return $this;
    }
    public function to_string() {
        $html = '<' . $this->tag;
        $i = 0;
        $keys = array_keys($this->arguments);
        if (sizeof($keys) > 0) $html .= " ";
        for ($i=0; $i < sizeof($this->arguments); $i++) {
            $html .= $keys[$i] . '="' . $this->arguments[$keys[$i]] . '"';
            if ($i < sizeof($keys) - 1) $html .= " ";
        }
        $html .= (($this->is_self_closing ? "/" : "")) . '>';
        if (!$this->is_self_closing) {
            foreach($this->child as&$c) {
                $html .= $c->to_string();
            }
            $html .= $this->content;
            $html .= '</' . $this->tag . '>';
        }
        return $html;
    }    
}
