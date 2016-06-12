<?php
require_once("config.php");

class XMLRead {
    
    private $xml_file;
    
    public function readXML($path) {
        //Anti injection path check might be useful
        libxml_use_internal_errors(true); //might cause memory problems in a long runnng process. Clear internal error buffer
        $this->xml_file = simplexml_load_file($path);
        if ($this->xml_file === false) {
            echo "Failed loading XML\n";
            foreach(libxml_get_errors() as $error) {
                echo "\t<hr/>", $error->message;
            }
        } else {
            echo 'XML FILE LOADED<hr/>';
            $this->build_array($this->xml_file);
           
        } 
    }
    
    private function build_array($xml) {
        echo "Building XML array<hr/>";
        echo 'Total records: ' . $xml->count() . '<hr>';
    }
}

$xml_reader = new XMLRead();
$xml_reader->readXML(X_TESTFILE);
/*
        try {
          $xml = simplexml_load_file(X_TESTFILE);
          echo 'XML FILE LOADED<hr/>';
        } catch(Exception $e) {
          echo 'Excep Message: ' .$e->getMessage();
        }
*/
?>