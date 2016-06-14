<?php

class MessageLogger {
    
    private static $log_message = [];
    
    public static function add_log($message) {
        self::$log_message[] = $message;
        
    }
    
    public static function print_log() {
        foreach (self::$log_message as $log) {
            echo $log . "<hr/>";
        }
        //self::clear_log();
    }
    
    public static function clear_log() {
        self::$log_message = [];
    }
    
}



?>