<?php
require_once("config.php");
require_once("logger.php");
require_once("central_logic.php");

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
        $this->add_log("Building XML array, ");
        $this->add_log('Records found: ' . $xml->count() . '. ');
        $this->send_log();
        //Build key names
         $array_keys = [];
        //echo $xml->child()->getName();
        foreach($xml->RECORD->children() as $record) {
            $array_keys[] = $record->getName();
        }
        $this->add_log("Retrieve field names from xml: (".count($array_keys).") ". join(", ", $array_keys) );
        $this->send_log();
        //Build full array. Each record is 1 $record_array. The collection of individual arrays in $xml_array
        $xml_array = [];
        $record_array = [];
        foreach($xml as $record) {
            $record_array = [];
            foreach($array_keys as $key ) {
                $record_array[(string)$key] = (string)$record->$key;
            }
            //$xml_array[$index] = $record_array;
            $xml_array[] = $record_array;
        }
        $this->add_log("Constructed xml array with ".count($xml_array)." entries, each holding associative array with ".count($record_array). " lines");
        $this->send_log();
        return $xml_array;
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
        $this->log = "";
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