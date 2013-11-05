<?php

/**
 * Description of User
 *
 * @author DavhanaM
 */
require_once ('DatabaseConnection.php');

class User {
    
    private $db;
    
    public $user_id;
    public $unique_id;
    public $user_email;
    public $user_password;
    public $user_type;
    
    protected static $table_name = "user_login_tbl";
    protected static $db_fields = array('user_id', 'user_email', 'user_password', 
        'salt', 'user_type', 'created_at');
    
    public function __construct() {
        $this->db = new DatabaseConnection();
        
    }
    
    public function authenticate_user($email="", $password=""){
        global $database;
        $email = $database->escape_value($email);
        $password = $database->escape_value($password);   
        
        $sql = "SELECT * FROM user_login_tbl ";
        $sql .= "WHERE user_email = '{$email}' ";
        
        $num_of_rows = $database->num_rows($database->query($sql));
        if($num_of_rows > 0){
            $result = mysql_fetch_array($database->query($sql));
            $salt = $result['salt'];
            $encrypted_password = $result['user_password'];
            $hash = $this->check_hash_sha($salt, $password);
            
            if($encrypted_password == $hash){
                return $result;
            }else{
                return false;
            }
        }
    }
    
    public function is_user_registered($email){
        global $database;
        $sql = "SELECT user_email FROM user_login_tbl WHERE user_email = '$email'";
        $num_of_rows = $database->num_rows($database->query($sql));
        
        if($num_of_rows > 0){
            return true;
        }else{
            return false;
        }
    }

    public function __call($method_name, $arguments) {
        
        if($method_name == "create_user"){
            
            $count = count($arguments);
            switch ($count) {
                case "0":
                    global $database;
                    $sql = "INSERT INTO ".self::$table_name."(";
                    $attributes = $this->sanitized_attributes();

                    $sql = "INSERT INTO ".self::$table_name." (";
                    $sql .= join(", ", array_keys($attributes));
                    $sql .= ") VALUES ('";
                    $sql .= join("', '", array_values($attributes));
                    $sql .= "')";        

                    if($database->query($sql)){
                        $this->user_id = $database->insert_id();
                        return true;
                    }else{
                        return false;
                    }                    
                    break;
                case "3":
                    global $database;
                    echo $arguments[2]."<br/>";
                    $hash = $this->hash_sha($arguments[1]);
                    $encrypted_password = $hash["encrypted"];
                    $salt = $hash["salt"];
                    
                    $sql = "INSERT INTO ";
                    $sql .= self::$table_name."(user_email, user_password, salt, user_type) ";
                    $sql .= "VALUES('$arguments[0]', '$encrypted_password', '$salt', '$arguments[2]')";
                    
                    if($database->query($sql)){
                        $this->user_id = $database->insert_id();
                        return true;
                    }else{
                        return false;
                    }
                    break;

                default:
                    throw new Exception("Bad argument");
                    break;
            }
        }
    }
    
    public function hash_sha($password){
        
        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrpted = base64_encode(sha1($password.$salt, true).$salt);
        $hash = array("salt" => $salt, "encrypted" => $encrpted);
        return $hash;
    }
    
    public function check_hash_sha($salt, $password){
        return base64_encode(sha1($password . $salt, true) . $salt);
    }
    
    protected function sanitized_attributes() {
      global $database;
      $clean_attributes = array();
      
      foreach($this->attributes() as $key => $value){
        $clean_attributes[$key] = $database->escape_value($value);
      }
      return $clean_attributes;
    }    
    
    private static function instantiate($record) {
        $object = new self;

        foreach($record as $attribute=>$value){
          if($object->has_attribute($attribute)) {
            $object->$attribute = $value;
          }
        }
        return $object;
    }    
    
    protected function atributes(){
        $attributes = array();
        
        foreach(self::$db_fields as $field){
            if(property_exists($this, $field)){
                $attributes[$field] = $this->$field;
            }
        }
        return $attributes;
    }
    
    private function has_attribute($attribute) {
        return array_key_exists($attribute, $this->attributes());
    }    
    
    public function find_by_sql($sql=""){
        
        global $database;
        $result_set = $database->query($sql);
        
        $object_array = array();
        
        while($row = $database->fetch_array($result_set)){
            $object_array[] = self::instantiate($row);
        }
        return $object_array;
    }
    
    public function find_by_uuid($id){
        $qry = "SELECT * FROM ".self::$table_name." WHERE unique_id={$id} LIMIT 1";
        $result_array = self::find_by_sql($qry);
        return !empty($result_array) ? array_shift($result_array) : false;
    }
}

?>
