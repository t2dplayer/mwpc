<?php

class CoreUtils {
    public static function log_error($obj) {
        $var = print_r($obj, true);
        trigger_error($var, E_USER_ERROR);
    }
}
