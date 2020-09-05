<?php

class CoreUtils {
    public static function log($obj) {
        $var = print_r($obj, true);
        trigger_error("log(".$var.")", E_USER_NOTICE);
    }
}
