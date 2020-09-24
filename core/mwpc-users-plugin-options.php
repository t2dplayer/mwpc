<?php

function mwpc_activate_plugin_name()
{
    $role = get_role('subscriber');
    $role->add_cap('activate_plugins'); // capability
}

// Register our activation hook
register_activation_hook(__FILE__, 'mwpc_activate_plugin_name');

function mwpc_deactivate_plugin_name()
{
    $role = get_role('subscriber');
    $role->remove_cap('activate_plugins'); // capability
}
// Register our de-activation hook
register_deactivation_hook(__FILE__, 'mwpc_deactivate_plugin_name');


function mwpc_user_contactmethods($methods, $user)
{
    $methods['CPF'] = 'CPF';
    $methods['ORCID'] = 'ORCID';
    return $methods;
}
add_filter('user_contactmethods', 'mwpc_user_contactmethods', 10, 2);

function mwpc_modify_user_table($column)
{
    $column['CPF'] = 'CPF';
    $column['ORCID'] = 'ORCID';
    return $column;
}
add_filter('manage_users_columns', 'mwpc_modify_user_table');

function mwpc_modify_user_table_row($val, $column_name, $user_id)
{
    switch ($column_name) {
        case 'CPF':
            return get_the_author_meta('CPF', $user_id);
        case 'ORCID':
            return get_the_author_meta('ORCID', $user_id);
        default:
    }
    return $val;
}
add_filter('manage_users_custom_column', 'mwpc_modify_user_table_row', 10, 3);

function mwpc_admin_menu_rename()
{
    $user = wp_get_current_user();
    if ($user->roles[0] == 'administrator') {
        global $menu;
        global $submenu;
        $menu[70][0] = 'Docentes';
        $submenu['users.php'][5][0] = 'Lista de docentes';
    }
}
add_action('admin_menu', 'mwpc_admin_menu_rename');

function mwpc_getPostField($key) {
    return (!empty($_POST[$key])) ? sanitize_text_field($_POST[$key]) : '';
}

//1. Add a new form element...
add_action('register_form', 'mwpc_register_form');
function mwpc_register_form()
{
    echo HTMLTemplates::_self()->get('input_form_field', [
        '%label'=>"CPF",
        '%value'=>esc_attr(mwpc_getPostField('CPF')),
        '%size'=>15
    ]);
    echo HTMLTemplates::_self()->get('input_form_field', [
        '%label'=>"ORCID",
        '%value'=>esc_attr(mwpc_getPostField('ORCID')),
        '%size'=>15
    ]);
}

//2. Add validation. In this case, we make sure first_name is required.
function mwpc_check_register_input(&$errors, $value, $label) {
    if (empty($value) 
        || !empty($value) 
        && trim($value) == '') {
        $errors->add(
            $label . '_error', 
            sprintf(
                '<strong>%s</strong>: %s', 
                __('Erro', 'MWPC_DOWMAIN'), 
                __('O ' . $label . ' é obrigatório.', 'MWPC_DOWMAIN')
            )
        );
    }
}

add_filter('registration_errors', 'mwpc_registration_errors', 10, 3);
function mwpc_registration_errors($errors, $sanitized_user_login, $user_email)
{
    mwpc_check_register_input($errors, $_POST['CPF'], 'CPF');
    if (!CoreUtils::validate_cpf($_POST['CPF'])) {
        $errors->add(
            'CPF_error', 
            sprintf(
                '<strong>%s</strong>: %s', 
                __('Erro', 'MWPC_DOWMAIN'), 
                __('O número do CPF é inválido.', 'MWPC_DOWMAIN')
            )
        );        
    }
    mwpc_check_register_input($errors, $_POST['ORCID'], 'ORCID');
    return $errors;
}

//3. Finally, save our extra registration user meta.
function mwpc_register_meta($user_id, $key) {
    if (!empty($_POST[$key])) {
        update_user_meta(
            $user_id, 
            $key, 
            sanitize_text_field($_POST[$key])
        );
    }
}

add_action('user_register', 'mwpc_user_register');
function mwpc_user_register($user_id)
{
    mwpc_register_meta($user_id, 'CPF');
    mwpc_register_meta($user_id, 'ORCID');
}
