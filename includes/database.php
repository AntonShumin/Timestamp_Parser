<?php 
require_once("config.php");
require_once("logger.php");

class MySQLDatabase {
    private $connection;
    
    
    public function construct_connection() {
        $this->open_connection();
        $check_connection = $this->connection ? true : false;
        return $check_connection;
    }
    
    public function open_connection() {
        $this->connection = mysqli_connect (DB_SERVER, DB_USER, DB_PASS, DB_NAME);
        if( mysqli_connect_errno() ) {
            MessageLogger::add_log("ERROR: DB connect failed, bekijk config.php voor correcte mySQL configuratie. " . 
            mysqli_connect_error() . " (" . mysqli_connect_errno() . ")"); 
        } else {
            MessageLogger::add_log("mySQL connection OK ");
        }   
    }
    
    public function close_connection() {
        if(isset($this->$connection)) {
            mysqli_close($this-connection);
            unset($this->connection);
        }
    }
    
    public function query($sql) {
        $result = mysqli_query($this->connection,$sql);
        $this->confirm_query($result);
        return $result;
    }
    
    public function confirm_query($result) {
        if(!$result) {
            echo "(database.php.query) DB Query failed " . $result;
        }
    }
    
    public function fetch_array($result_set) {
        return mysqli_fetch_array($result_set);
    }
}




    
?>