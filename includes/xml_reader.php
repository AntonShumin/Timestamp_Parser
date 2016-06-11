<?php
require_once("config.php");
//$xml=simplexml_load_file('../public/testXML.xml');

class XMLRead {
    function __construct() {
        //test local xml
        //$xml = simplexml_load_file(X_TESTFILE) or die("Can't connect to url");
        /*
        try {
          $xml = simplexml_load_file(X_TESTFILE);
          //If the exception is thrown, this text will not be shown
          echo 'XML FILE LOADED<hr/>';
        } catch(Exception $e) {
          echo 'Excep Message: ' .$e->getMessage();
        }
        */
        libxml_use_internal_errors(true);
        $xml = simplexml_load_file(X_TESTFILE);
        if ($xml === false) {
            
            echo "Failed loading XML\n";
            foreach(libxml_get_errors() as $error) {
                echo "\t<hr/>", $error->message;
            }
        } else {
            echo 'XML FILE LOADED<hr/>';
        }
        
    }
}

$xml_reader = new XMLRead();
?>