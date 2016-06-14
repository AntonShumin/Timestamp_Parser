<?php

class MessageLogger {
    
    private static $log_message = [];
    
    public static function add_log($message) {
        self::$log_message[] = $message;
        
    }
    
    public static function print_log() {
        self::write_to_file();
        foreach (self::$log_message as $log) {
            echo $log . "<hr/>";
        }
        
        self::clear_log();
    }
    
    public static function clear_log() {
        self::$log_message = [];
    }
    
    public static function write_to_file() {
        if($handle = fopen(LOG_FILE,'wt')){
            $write = fwrite($handle,join("\r\n",self::$log_message));
            fclose($handle);
            self::add_log('Creating an entry in log.txt ('.$write.")");    
        } else {
            self::add_log('Unable to write to log.txt. Check permissions');     
        }
        
    }
    
}



?>