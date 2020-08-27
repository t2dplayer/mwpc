<?php

class URLUtils {
    public static function URLPage($id) {
        $url = 'admin.php?page=' . $id;
        return get_admin_url(get_current_blog_id(), $url); 
    }
    public static function URLForm($id) {
        $url = 'admin.php?page=' . $id . "_form_id";
        return get_admin_url(get_current_blog_id(), $url); 
    }    
};