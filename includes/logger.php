<?php

class MessageLogger {
    
    private static $log_message = [];
    
    public static function add_log($message) {
        self::$log_message[] = $message;
        
    }
    
    public static function print_log() {
        //check if ran from command line
        $line_break = PHP_SAPI != "cli" ? "<hr/>" : PHP_EOL; //If not cmd. EOL is a cross platform new line
        //generate a string and output
        foreach (self::$log_message as &$log) {
            $log = "* ".$log;
            echo $log . $line_break;
        }
        //Create header with timestamp
        self::construct_header();
        array_unshift(self::$log_message,PHP_EOL);
        //Write and display
        self::write_to_file();
        self::clear_log();
    }
    
    public static function clear_log() {
        self::$log_message = [];
    }
    
    public static function write_to_file() {
        //enable file writing
        chmod(LOG_FILE,0777);
        //open log.txt. check config.php for path
        if($handle = fopen(LOG_FILE,"a+")){
            //generate output and write to file  
            $write = fwrite($handle,$write = join(PHP_EOL,self::$log_message));
            echo $write ? PHP_EOL.'Creating an entry in log.txt ('.$write." bytes)".PHP_EOL : PHP_EOL.'Could not write to log.txt. '; 
            fclose($handle);
        } else {
            self::add_log('Unable to open log.txt. Check permissions');     
        }
    }
    
    public static function construct_header(){
        $header = [];
        $header[] = "**********************************************************";
        $header[] = "      Log Entry: ".date('g:ia, l, F j, Y');
        $header[] = "**********************************************************";
        foreach ($header as $item) {
            array_unshift(self::$log_message,$item);
        }
    }
    
}



?>