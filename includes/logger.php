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
        self::clear_log();
    }
    
    private static function clear_log() {
        //unset(self::$log_message);
        //check $ar= array_values($ar); if there are holes 
    }
    
}


MessageLogger::add_log("something");
MessageLogger::print_log();

?>