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
        if($handle = fopen(LOG_FILE,'wt')){
            //generate output and write to file
            $write = fwrite($handle,join(PHP_EOL,self::$log_message));
            fclose($handle);
            $write ? self::add_log('Creating an entry in log.txt ('.$write." bytes)") : self::add_log('Could not write to log.txt'); 
        } else {
            self::add_log('Unable to open log.txt. Check permissions');     
        }
        
    }
    
}



?>