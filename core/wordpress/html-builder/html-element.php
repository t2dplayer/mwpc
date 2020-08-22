<?php

class HtmlElement {
    protected $attributes = array();
    protected $children = array();
    protected $tag  = "";
    protected $is_self_closing = false;
    protected $content = "";
    protected $content_before = false;
    function __construct($arg1 = array(),
                         $arg2 = array()) {
        if (sizeof($arg1) > 0) {
            if (gettype($arg1[array_keys($arg1)[0]]) == "string") {
                $this->attributes = $arg1;
                $this->content_before = true;
            } else if (gettype($arg1[array_keys($arg1)[0]]) == "object") {
                $this->children = $arg1;
            }
        }
        if (sizeof($arg2) > 0) {
            if (gettype($arg2[array_keys($arg2)[0]]) == "string") {
                $this->attributes = $arg2;
            } else if (gettype($arg2[array_keys($arg2)[0]]) == "object") {
                $this->children = $arg2;
            }
        }        
        if (array_key_exists('content', $this->attributes)) {
            $this->content = $this->attributes['content'];
        }
    }
    public function __toString() {
        $html = '<' . $this->tag;
        $i = 0;
        $keys = array_keys($this->attributes);
        $j = 0;
        if (array_key_exists('content', $this->attributes)) {
            $j = 1;
        }
        if (sizeof($keys) > $j) $html .= " ";
        for ($i=0; $i < sizeof($this->attributes); $i++) {
            if ($keys[$i] == "content") continue;
            $html .= $keys[$i] . '="' . $this->attributes[$keys[$i]] . '"';
            if ($i < sizeof($keys) - ($j + 1)
                && $keys[$i] != "content") $html .= " ";
        }
        $html .= (($this->is_self_closing ? "/" : "")) . '>';
        if (!$this->is_self_closing) {
            if ($this->content_before) {
                $html .= $this->content;    
            }
            foreach($this->children as &$c) {
                $html .= $c;
            }
            if (!$this->content_before) {
                $html .= $this->content;    
            }
            $html .= '</' . $this->tag . '>';
        }
        return $html;
    }    
}
