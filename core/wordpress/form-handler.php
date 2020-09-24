<?php

function delete_all(&$table_name, $item) {
    global $wpdb;
    $attr = "";
    $counter = 0;
    foreach($item as $key=>$value) {
        $attr .= "$key = \"$value\"";
        if ($counter++ < sizeof($item) - 1) {
            $attr .= " AND ";            
        }
    }
    $sql = SQLTemplates::_self()->get('delete_where', [
        "%tablename"=>$table_name,
        "%attr"=>$attr,
    ]);
    // CoreUtils::log($sql, "[delete_all - $table_name - sql]");
    // CoreUtils::log($item, "[delete_all - $table_name - item]");
    $wpdb->query($sql);
}

function insert_item(&$table_name, &$data) {
    global $wpdb;
    // CoreUtils::log($data, "INSERT_ITEM [$table_name] -> ");
    $result = $wpdb->insert($table_name, $data);
    $data['id'] = $wpdb->insert_id;
    if ($wpdb->last_error !== '') {
        CoreUtils::log($wpdb->last_error, "[INSERT ITEM] ");
        CoreUtils::log($table_name, "[TABLE NAME] ");
        CoreUtils::log($data, "[DATA] ");
    }    
    return $data;
}

function update_item(&$table, &$table_name, &$item) {
    global $wpdb;
    // Remove all detail table values and update only the master table
    foreach ($table->get_detail_fields() as $value=>$data) {
        if (array_key_exists($value, $item)) {
            unset($item[$value]);
        }
    }
    $wpdb->update($table_name, $item, array('id' => $item['id']));
    return $item;
}

abstract class Status {
    const None = 0;
    const Success = 1;
    const Error = 2;
    const NotFound = 4;
    const Invalid = 8;
};

abstract class SQLCommand {
    const None = 0;
    const Insert = 1;
    const Update = 2;
    const Select = 4;
    const Delete = 8;
};

class Result {
    public $sql_command = null;
    public $status = null;
    public $result = null;
    function __construct($sql_command, $status, $result) {
        $this->sql_command = $sql_command;
        $this->status = $status;
        $this->result = $result;
    }
};

function notice(&$sql_command, &$sql_status, $validate_result = array()) {
    if ($sql_status == Status::Success
        || $sql_status == Status::None) return '';
    $result = "Ocorreu um erro ao tentar ";
    if ($sql_command == SQLCommand::Insert) {
        $result .= "salvar ";
    } else if ($sql_command == SQLCommand::Update) {
        $result .= "atualizar ";
    } else if ($sql_command == SQLCommand::Delete) {
        $result .= "apagar ";
    } else if ($sql_command == SQLCommand::Select) {
        $result .= "localizar ";
    }
    $result .= "o registro.<br />";
    if (is_array($validate_result)) {
        foreach ($validate_result as $arr) {
            if ($arr['result']) continue;
            $result .= $arr['error_message'] . '<br />';
        }
    }
    return HTMLTemplates::_self()->get('div_error', [
        '%content'=>$result,
    ]);
}

function message(&$sql_command, &$sql_status) {
    if ($sql_status == Status::Error
        || $sql_status == Status::None
        || $sql_status == Status::Invalid
        || $sql_command == SQLCommand::Select) {            
            return '';
    }
    $result = "Registrado com sucesso.";
    $html = HTMLTemplates::_self()->get('div_message', [
        '%content'=>$result,
    ]);
    return $html;
}

function get_detail_table(&$item) {
    $result = array();
    foreach($item as $key=>$value) {
        if (is_array($value)) {
            $result[$key] = $value;
            unset($item[$key]);
        } else if (strlen($value) == 0) {
            unset($item[$key]);
        }
    }
    return sizeof($result) > 0 ? $result : null;
}

function get_delete_command(&$request) {
    $result = array();
    foreach($request as $key=>$value) {
        if (preg_match('/delete_(.*)/', $key) == 1) {
            $result[$key] = $value;
        }
    }
    return sizeof($result) > 0 ? $result : null;
}

function explode_items($str) {
    $result = array();
    if (sizeof($str) == 0) return $result;
    $r = explode(";", $str);
    if (sizeof($r) > 1) {
        foreach($r as $item) {
            $arr = explode("#", $item);
            if (sizeof($arr) == 2) {
                $arr[0] = CoreUtils::clean($arr[0]);
                $arr[1] = CoreUtils::clean($arr[1]);
                $result[$arr[0]] = $arr[1];
            }
        }
    } else {
        array_push($result, $r[0]);
    }
    return $result;
}

function save_sub_item(&$table, &$table_name, &$detail, &$item) {
    foreach($table->get_detail_fields() as $key=>$detail_fields) {
        foreach($detail_fields as $fields) {
            if (!array_key_exists('insert_data', $fields)) {
                continue;
            }
            $detail_table_name = $fields['insert_data']['table_name'];
            $closure = $fields['insert_data']['closure'];
            if (!array_key_exists($key, $detail)) {
                continue;
            }
            foreach($detail[$key] as $str) {
                if (is_callable($closure)) {
                    $exploded = explode_items($str);
                    CoreUtils::log($exploded, " [exploded] ");
                    if (array_key_exists('id', $exploded)
                        && isset($exploded['id'])) continue;
                    CoreUtils::log($item, " [ item before ] ");
                    $data = $closure($exploded, $item);
                    $data['user_id'] = get_current_user_id();
                    CoreUtils::log($data, " [data] ");
                    $r_id = insert_item($detail_table_name, $data);
                    $item[$key."_id"] = $r_id['id'];
                    if (array_key_exists($key."_id_array", $item)) {
                        array_push($item[$key."_id_array"], $r_id['id']);
                    } else {
                        $item[$key."_id_array"] = [$r_id['id']];
                    }
                    CoreUtils::log($item, " [ item after ] ");
                }
            }
        }
    }
}

function delete($detail_fields, &$delete_items) {    
    global $wpdb;
    $delete_key = "delete_data";
    foreach($detail_fields as $key=>$value) {
        foreach($value as $arr) {            
            if (!array_key_exists($delete_key, $arr)) continue;
            $arr_opt = $arr[$delete_key];
            $closure = $arr_opt['closure'];
            $sql = $closure($delete_items, $_REQUEST['id']);
            if (strlen($sql) > 0) {
                $wpdb->query($sql);
                // if ($wpdb->last_error !== '') {
                //     CoreUtils::log($delete_items, "Error Del-->");
                //     CoreUtils::log($sql, "Error SQL-->");
                // } else {
                //     CoreUtils::log($sql, "SQL-->");
                // }
            }
        }
    }
}

function save_or_update(&$table, &$table_name, &$item, &$detail, &$delete_items) {
    $result = null;
    // saving new item
    if (!array_key_exists('id', $item) 
        || $item['id'] == 0) {
        $sql_command = SQLCommand::Insert;
        $result = insert_item($table_name, $item);
    } else { // updating item
        //if (isset($delete_items)) {
            delete($table->get_detail_fields(), $delete_items);
        //}
        $sql_command = SQLCommand::Update;
        $result = update_item($table, $table_name, $item);                
    }
    if (isset($detail) > 0) {
        // CoreUtils::log($detail, " [save_or_update] ");
        save_sub_item($table, $table_name, $detail, $item);
    }        
    return $result;
}

function post(&$table, &$table_name, &$item) {
    $status = Status::None;
    $result = null;
    $sql_command = SQLCommand::None;    
    $item = shortcode_atts($table->get_defaults(), $_REQUEST);
    $detail = get_detail_table($item);
    $delete_items = get_delete_command($_REQUEST);
    $validate_result = $table->validate($item);
    $success = true;
    if (sizeof($validate_result) > 0) {
        foreach ($validate_result as $arr) {
            $success &= $arr['result'];
        }
    }
    $success = true;
    if ($success) {
        global $wpdb;
        $result = array();
        $wpdb->query("START TRANSACTION");
        $result = save_or_update($table, $table_name, $item, $detail, $delete_items);
        if ($wpdb->last_error !== '') {
            $wpdb->query("ROLLBACK");
        } else {
            $wpdb->query("COMMIT");
        }
    } else {
        if (sizeof($validate_result) > 0) {
            foreach ($validate_result as $arr) {
                if ($arr['result'] == false) {
                    return new Result(SQLCommand::None, Status::Invalid, $validate_result);
                }
            }
        }        
    }
    if ($wpdb->last_error !== '') {
        $status = Status::Error;
    } else { 
        $status = Status::Success;
    }
    return new Result($sql_command, $status, $result);
}

function load(&$table, &$table_name) {
    global $wpdb;
    if (isset($_REQUEST['id'])) {
        $sql_where = SQLTemplates::get('select_where_id', [
            '%tablename'=>Settings::_self()->table_name($table_name)
        ]);
        $result = $wpdb->get_row($wpdb->prepare($sql_where, $_REQUEST['id']), ARRAY_A);
        $status = Status::Success;
        if (!$result) {
            $result = $table->get_defaults();
            $status = Status::NotFound;
        }
        return new Result(SQLCommand::Select, $status, $result);
    }
    return new Result(SQLCommand::None, null, null);
}

function create_form_handler(&$table) {
    global $wpdb;
    $table_name = Settings::_self()->get_prefix() . $table->project_settings['id'];
    $item = [];
    if (isset($_REQUEST['nonce']) 
    && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {  
        $result = post($table, $table_name, $item);
    } else {
        $result = load($table, $table->project_settings['id']);
    }
    if (isset($result->result['id'])) {
        $prefix = Settings::_self()->get_prefix();
        $detail_results = [];
        $select_key = "select_data";
        foreach($table->get_detail_fields() as $key=>$detail_fields) {
            foreach($detail_fields as $fields) {
                if (!array_key_exists($select_key, $fields)) {
                    continue;
                }
                $data =$fields[$select_key]['data'];
                $data['%itemvalue'] = $result->result['id'];                
                $sql = SQLTemplates::_self()->get($fields[$select_key]['sql_template'], $data);
                $detail_results += $wpdb->get_results($sql);
                $result->result[$key] = $detail_results;                
            }
        }        
    }
    $item = $result->result;
    add_meta_box(
        $table->project_settings['meta_box_id'],
        $table->project_settings['title'],
        $table->project_settings['meta_box_handler'],
        $table->project_settings['id'],
        'normal',
        'default'
    );    
    echo HTMLTemplates::_self()->get('form_handler_header', [
        '%title'=>$table->project_settings['title'], 
        '%link'=>URLUtils::URLPage($table->project_settings['id']),
        '%back'=>MWPCLocale::get('back'),
        '%notice'=>notice($result->sql_command, $result->status, $result->result),
        '%message'=>message($result->sql_command, $result->status),
        '%nonce'=>wp_create_nonce(basename(__FILE__)),
        '%id'=>isset($_REQUEST['id']) ? $_REQUEST['id'] : '',
    ]);
    do_meta_boxes($table->project_settings['id'], 'normal', $item);
    $save_update = MWPCLocale::get('save');
    $add_new = "";
    if (isset($item['id']) && $item['id'] != 0) {
        $add_new = HTMLTemplates::_self()->get('add_new', [
            '%link'=>get_admin_url(get_current_blog_id(), 'admin.php?page=' . $table->project_settings['form_id']),
            '%addnew'=>MWPCLocale::get('add_new'),
        ]);
        $save_update = MWPCLocale::get('save_changes');
    }
    echo HTMLTemplates::_self()->get('form_handler_footer', [
        '%addnew'=>$add_new,
        '%save'=>$save_update,
    ]);
}