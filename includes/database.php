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
            die("DB connect failed, check database.php.openconnection " . 
            mysqli_connect_error() . " (" . mysqli_connect_errno() . ")"); 
            } else {
                echo "connection ok";
            }   
        }
    }

    $database = new MySQLDatabase();

    
        
?>