<?php

class FormDetailUtils {
    private static function Data($key, $table_name, $closure) {
        return [
            $key=>[
                'table_name'=>$table_name,
                'closure'=>$closure,                
            ]
        ];        
    }
    public static function InsertData($table_name, $closure) {
        return FormDetailUtils::Data("insert_data", $table_name, $closure);
    }
    public static function DeleteData($table_name, $closure) {
        $result = FormDetailUtils::Data("delete_data", $table_name, $closure);
        $result['nonce'] = wp_create_nonce($table_name);
        return $result;
    }    
    public static function SelectData($data) {
        return [
            'select_data'=>$data
        ];
    }    
}
