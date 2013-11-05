<?php

require_once('config.php');

class DatabaseConnection
{
    private $connection;
    public $last_query;
    private $magic_quotes_active;
    private $real_escape_string_exists;
        
    function __construct() {
        $this->connect();
    }
    
    public function connect(){
        
        $this -> connection = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
        
        if(!$this->connection){
            die("Database connection Failed: ".mysql_error());
        }else{
            $db_select = mysql_select_db(DB_NAME);
            if(!$db_select){
                die("Database selection failed: ".mysql_error());
            }
        }
    }
    
    public function query($sql){
        $this->last_query = $sql;
        $result = mysql_query($sql);
        $this->confirm_query($result);
        return $result;
    }
    
    public function escape_value($value){
        if($this->real_escape_string_exists){
            if($this->magic_quotes_active){
                $value = mysql_real_escape_string($value);
            }
        }
        return $value;
    }
    
    private function confirm_query($result){
        if(!$result){
            $output = "Database query failed: ".mysql_error();
            die($output);
        }
    }

    public function fetch_array($result_set) {
       return mysql_fetch_array($result_set);
     }

    public function num_rows($result_set) {
     return mysql_num_rows($result_set);
    }

    public function insert_id() {
      // get the last id inserted over the current db connection
      return mysql_insert_id($this->connection);
    }

    public function affected_rows() {
      return mysql_affected_rows($this->connection);
    }    
    
    public function close_connection(){
        if(isset($this->connection)){
            mysql_close($this->connection);
            unset($this->connection);
        }
    }
    
    public function __destruct() {
        
    }
}
$database = new DatabaseConnection();
$db =& $database;
?>
