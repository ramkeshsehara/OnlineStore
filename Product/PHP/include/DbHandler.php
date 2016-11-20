<?php

require_once '../include/DbConnect.php';
require_once '../include/PassHash.php';
class DbHandler {

    private $conn;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

  //--------------------------CREATE USER ----------------------------------------------

    public function createUser($name, $email, $password) {
        require_once 'PassHash.php';
        $response = array();

        if (!$this->isUserExists($email)) {
          
            $password_hash = PassHash::hash($password);

            $api_key = $this->generateApiKey();

            $stmt = $this->conn->prepare("INSERT INTO users(name, email, password_hash, api_key) values(?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $password_hash, $api_key);

            $result = $stmt->execute();

            $stmt->close();

            if ($result) {
                return USER_CREATED_SUCCESSFULLY;
            } else {
                
                return USER_CREATE_FAILED;
            }
        } else {
            return USER_ALREADY_EXISTED;
        }

        return $response;
    }

  //--------------------------CHECK LOGIN CREDENTIALS----------------------------------------------
 
    public function checkLogin($email, $password) {
	
        $stmt = $this->conn->prepare("SELECT password_hash FROM users WHERE email = ?");

        $stmt->bind_param("s", $email);

        $stmt->execute();
        
        $stmt->bind_result($password_hash);

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
           
            $stmt->fetch();

            $stmt->close();
            
            if (PassHash::check_password($password_hash, $password)) {
                return TRUE;
            } else {
				
              
                return FALSE;
            }
        } else {
            $stmt->close();
			
            return FALSE;
        }
    }

//--------------------------USER EXIST WITH EMAIL ID ----------------------------------------------

    private function isUserExists($email) {
        $stmt = $this->conn->prepare("SELECT id from users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    
//--------------------------USER DETAIL BY EMAIL ID ----------------------------------------------

    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT name, email, api_key, created_at FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            $stmt->bind_result($name, $email, $api_key, $created_at);
            $stmt->fetch();
            $user = array();
            $user["name"] = $name;
            $user["email"] = $email;
            $user["api_key"] = $api_key;
            $user["created_at"] = $created_at;
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }

    
//--------------------------GET API KEY BY USER ID ----------------------------------------------

    public function getApiKeyById($user_id) {
        $stmt = $this->conn->prepare("SELECT api_key FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $stmt->bind_result($api_key);
            $stmt->close();
            return $api_key;
        } else {
            return NULL;
        }
    }

//---------------CHECK PRODUCT NAME EXIST IN DATABASE ----------------------------------------------

    public function productIdExist($product_id) {
        $stmt = $this->conn->prepare("SELECT * FROM product WHERE name = ?");
        $stmt->bind_param("s", ($product_id));
        if ($stmt->execute()) {
            $stmt->store_result();
            $num_rows = $stmt->num_rows;
            $stmt->close();
            if ($num_rows > 0){
                return true;
            }
            else{
                return false;
            }
        } else {
            return false;
        }
    }


    
   //----------------------GET USER ID BY API KEY ----------------------------------------

    public function getUserId($api_key) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        if ($stmt->execute()) {
            $stmt->bind_result($user_id);
            $stmt->fetch();
            $stmt->close();
            return $user_id;
        } else {
            return NULL;
        }
    }

//--------------------------CHECK VALID API KEY -----------------------------------------

    public function isValidApiKey($api_key) {
        $stmt = $this->conn->prepare("SELECT id from users WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }


//----------Generating random Unique MD5 String for user Api key ----------------------------------
	
    private function generateApiKey() {
        return md5(uniqid(rand(), true));
    }


    /* ------------- `Product` table method ------------------ */
	
//--------------------------ADD PRODUCT IN DATABASE -----------------------------------------

    public function addProduct($name, $decription,$supplier_name) {
        $stmt = $this->conn->prepare("INSERT INTO product (name,description,supplier_name) VALUES(?,?,?)");
        $stmt->bind_param("sss",$name,$decription,$supplier_name);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
			$status ="Success";
            return $status;
        } else {
           
            return NULL;
        }
    }

    
//--------------------------RETRIEVE PRODUCTS FROM DATABASE  -----------------------------------------

    public function getAllProduct() {
        $stmt = $this->conn->prepare("SELECT * FROM product");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

//--------------------------UPDATE PRODUCT IN DATABASE -----------------------------------------

    public function updateProduct($id, $values) {
		if(count($values) > 0)
		{		
	          $var="s";
              foreach($values as $value)
			  {
				  $var.="s";
			  }				  
			$names= implode(' = ? , ',array_keys($values))."= ?";
			$stmt = $this->conn->prepare("UPDATE product set ".$names." WHERE name = ?");
			$params = array_merge([$var],array_values($values),[$id]);
			call_user_func_array(array($stmt,'bind_param'),$this->refValues($params));
			$stmt->execute();
			$num_affected_rows = $stmt->affected_rows;
			$stmt->close();
			return $num_affected_rows >=0;
		}
		return false;
    }

//--------------------------DELETE PRODUCT FROM DATABASE -----------------------------------------

     
    public function deleteProduct($id) {
		
        $stmt = $this->conn->prepare("DELETE FROM product WHERE name = ? ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        
        return $num_affected_rows > 0;
    }
	
	// helper function for updateProduct
	
	function refValues($arr){
		if (strnatcmp(phpversion(),'5.3') >= 0) 
		{
			$refs = array();
			foreach($arr as $key => $value)
				$refs[$key] = &$arr[$key];
			return $refs;
		}
		return $arr;
	}
}
?>