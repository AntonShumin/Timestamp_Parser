<?php
    require_once('initialize.php');

    //DatabaseObject is verantwoordelijk voor mySQL queries
    class Record extends DatabaseObject {
        
        protected static $table_name="records";
        protected static $db_fields = [];
        public $fields_values = [];
    }    

?>