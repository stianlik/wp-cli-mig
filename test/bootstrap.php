<?php

require_once __DIR__ . '/../vendor/autoload.php';

class WP_CLI {
    
    public static $testLog = array();
    
    public static function success()
    {
        self::$testLog['success'] = func_get_args();
    }
    
    public static function error($message)
    {
        self::$testLog['error'] = func_get_args();
        throw new WP_CLI_ErrorTriggeredException($message);
    }
    
    public static function reset()
    {
        self::$testLog = array();
    }
}

class WP_CLI_ErrorTriggeredException extends Exception {
    
}