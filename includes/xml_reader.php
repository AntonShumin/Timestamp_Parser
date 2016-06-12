<?php
require_once("config.php");
require_once("logger.php");

class XMLRead {
    
    private $xml_file;
    private $log = "";
    
    public function readXML($path) {
        //(future extra)Anti injection path check might be useful
        //(libxml_)might cause memory problems in a long runnng process. Clear internal error buffer
        libxml_use_internal_errors(true);  
        $this->xml_file = simplexml_load_file($path);
        if ($this->xml_file === false) {
            $this->add_log("XML LOAD FAILED. PATH " . $path);
            foreach(libxml_get_errors() as $error) {
                $this->add_log("<br/>" . $error->message);
            }
            $this->send_log();
            return false;
        } else {
            $this->add_log('XML FILE LOADED.');
            $check_array = $this->build_array($this->xml_file);
            return $check_array;   
        } 
    }
    
    private function build_array($xml) {
        $xml_array = [];
        $this->add_log("Building XML array, ");
        $this->add_log('Total records: ' . $xml->count() . '. ');
        $this->send_log();
        return true;
    }
    
    /***************************
    ********LOGGER*************
    ****************************/
    
    //build a single string line locally
    private function add_log($log) {
        $this->log .= " " . $log;
    }
    
    //add a single line to logger array
    private function send_log() {
        MessageLogger::add_log($this->log);
    }
}

/*
        try {
          $xml = simplexml_load_file(X_TESTFILE);
          echo 'XML FILE LOADED<hr/>';
        } catch(Exception $e) {
          echo 'Excep Message: ' .$e->getMessage();
        }
*/
?>