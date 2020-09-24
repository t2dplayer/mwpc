<?php
require_once 'student.php';
require_once 'skill.php';
require_once 'research-line.php';
require_once 'project.php';
require_once 'project-has-researchline.php';
require_once 'project-has-student.php';
require_once 'publishing.php';

function mwpc_make_user_select_field() {
    if (current_user_can('administrator')) {
        return FormUtils::SelectFromTable([
            'sql'=>SQLTemplates::_self()->get('select_all', [
                '%fields'=>'id as value, display_name as label',
                '%tablename'=>'wp_users',
            ]),
            'selected_key'=>'id',
        ]);
    } else {
        global $current_user; 
        wp_get_current_user();
        return $current_user->display_name;
    }
}