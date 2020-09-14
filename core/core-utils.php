<?php

if (!function_exists('array_key_first')) {
    function array_key_first(array $arr)
    {
        foreach ($arr as $key => $unused) {
            return $key;
        }
        return null;
    }
}

class CoreUtils {
    public static function log($obj, $tag="") {
        $var = $tag;
        $var .= print_r($obj, true);
        trigger_error("log(".$var.")", E_USER_NOTICE);
    }
    public static function validate_email($email) {
        return filter_var($email, FILTER_SANITIZE_EMAIL);        
    }
    public static function validate_cpf($cpf) {
        // Extrai somente os números
        $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            return false;
        }
        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }
    public static function merge($keys, $values) {
        if (sizeof($keys) != sizeof($values)) {
            throw new Exception("Invalid size of fields and fields_types");
        }
        $result = [];
        for($i = 0; $i < sizeof($keys); $i++) {
            //if(empty($values[$i])) continue;
            $result[$keys[$i]] = $values[$i];
        }
        return $result;
    }
    public static function mask($mask, $string) {
        $string = str_replace(" ","", $string);
        for($i = 0; $i < strlen($string); ++$i) {
            $mask[strpos($mask,"#")] = $string[$i];
        }
        return $mask;
    }
}
