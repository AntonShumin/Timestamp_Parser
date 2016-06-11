<?php 
require_once("config.php");

class MySQLDatabase {
    private $connection;
    
    function __construct() {
        $this->open_connection();
    }
    
    public function open_connection() {
        $this->connection = mysqli_connect (DB_SERVER, DB_USER, DB_PASS, DB_NAME);
        if( mysqli_connect_errno() ) {
            die("<h2>ERROR: DB connect failed, bekijk config.php voor correcte mySQL configuratie. " . 
            mysqli_connect_error() . " (" . mysqli_connect_errno() . ") </h2> <hr/>"); 
        } else {
            echo "mySQL connection OK <hr/>";
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

$database = new MySQLDatabase();


    
?>