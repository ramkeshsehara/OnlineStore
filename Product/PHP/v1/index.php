<?php

require_once '../include/DbHandler.php';
require_once '../include/PassHash.php';

$flag=400;         // default http status code  

// fetching current url of broswer 

function getCurrentUri()
	{
		$basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
		$uri = substr(urldecode($_SERVER['REQUEST_URI']), strlen($basepath));
		if (strstr($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));
		$uri = '/' . trim($uri, '/');
		return $uri;
	}
	
	
	// authenticate user using apiKey if user header contains apikey then result true else unauthorize 
	
    function authenticate() {
	$headers = apache_request_headers();
    if (isset($headers['Authorization'])) {
        $db = new DbHandler();
        $api_key = $headers['Authorization'];
		
	    // validating api key form dbHandler 

        if ($db->isValidApiKey($api_key)) {
            return true;
        } else {
            return false;
        }
    } else {
		return false;
    }
}

   /*verifyParameters for updateProduct, addProduct which paramter is missing
   $list has list of given paramter and $names contains which paramter we need. function output will be missing paramter */

	function verifyParameters(array $list, $names)
	{
		$missing = array();
		if(!is_array($names))
		{
			if(!isset($list[$names]) || $list[$names]=="")
			{
				$missing[] = $names;
			}
		}
		else
		{
			foreach ($names as $name)
			{
				if($list[$name]=="")
				{
					$missing[] = $name;
				}
			}
		}
		return $missing;
	}

  // fectching url and triming base url  
  
	$base_url = getCurrentUri();
	$allRoutes = array();
	$routes = explode('/', $base_url);
	foreach($routes as $route)
	{
		if(trim($route) != '')
			array_push($allRoutes, $route);
	}
	$routes = $allRoutes;
 

 /** -- METHODS WITHOUT AUTHENTICATION -- **/
 

	if($routes[1] == "register")            // user register function  if url contains /register in url
	{					   			

			$missing = verifyParameters($_POST, ['name', 'email', 'password_hash']);  

            if(count($missing)==0)
			{
					$response = array();
					
					$name = $_POST['name'];
					$email = $_POST['email'];
					$password = $_POST['password_hash'];

					if(filter_var($email,FILTER_VALIDATE_EMAIL)!==FALSE)     // email validation 
					{
							$db = new DbHandler();
							$res = $db->createUser($name, $email, $password);

							if ($res == USER_CREATED_SUCCESSFULLY) {
								$flag=200;
								$response["error"] = false;
								$response["message"] = "You are successfully registered";
							} else if ($res == USER_CREATE_FAILED) {
								$response["error"] = true;
								$flag=400;
								$response["message"] = "Required field(s) name, email, password is missing or empty";
							} else if ($res == USER_ALREADY_EXISTED){
								$flag=202;
								$response["error"] = true;
								$response["message"] = "Sorry, this email already existed";
							}							
			        }
					else { $flag=400; $response["error"] = true; $response["message"] = "Sorry, invalid email";}
			}
			else
			{
				$response = array();
				$response['error'] = true;
				$response['message'] = "Missing parameters: " . implode(", ", $missing) . ".";
				
			}
			echoRespnse($flag, $response);
		
	}
	else if($routes[1]=="login")                 // user login authentication function 
	{
		$flag=400;
		 $response = array();
		if(isset($_POST['email'])&&isset($_POST['password_hash']))
		{
            $email = $_POST['email'];
            $password = $_POST['password_hash'];
                $db = new DbHandler();
                 if(filter_var($email,FILTER_VALIDATE_EMAIL)!==FALSE)
					{ 			
                        // check for correct email and password
						if ($db->checkLogin($email, $password)) {
							
							// get the user by email
							$user = $db->getUserByEmail($email);

							if ($user != NULL) {
								$response["error"] = false;
								$response['name'] = $user['name'];
								$response['email'] = $user['email'];
								$response['apiKey'] = $user['api_key'];
								$response['createdAt'] = $user['created_at'];
								$flag=200;
								
							} else {
								// unknown error occurred
								$flag=500;
								$response['error'] = true;
								$response['message'] = "An error occurred. Please try again";
							}
						} else {
							// user credentials are wrong
							$response['error'] = true;
							$response['message'] = 'Login failed. Incorrect credentials';
						}
					}
					else 
					{
						$response['error'] = true;
						$response['message'] = 'Invalid Email';
						
					}
                
		}
		echoRespnse($flag, $response);
	}
	else if($routes[1]=="add")                  // add product in the database if it is valid user 
	{
		 /** -- METHODS WITH AUTHENTICATION -- **/

		    $flag=400;
		   	$response = array();
			if(authenticate())
			{
				$db = new DbHandler();
                
				// addProduct
				$missing = verifyParameters($_POST, ['name', 'description', 'supplier_name']);
				
				if(count($missing)==0)
				{
					$task_id = $db->addProduct($_POST['name'],$_POST['description'],$_POST['supplier_name']);

					if ($task_id != NULL) {
						$response["error"] = false;
						$response["message"] = "Product added successfully";
						$flag=201;
					} else {
						$response["error"] = true;
						$response["message"] = "Failed to add product. Please try again";
						
					}	
					
				} else {

					     $flag=400;
					     $response["error"] = true;
						 $response["message"] = "Missing product field";
						}
			
			}
			else
			{ 
			    $flag=400;
			    $response["error"] = true;
				$response["message"] = "Unauthorized";	
				//unauthorize($flag,$response);
			}
			
			echoRespnse($flag, $response);

	}
	else if($routes[1]=="view")              //  output product list if user has valid api key  
	{ 
		    $response = array();    
			if(authenticate())
			{
				global $user_id;
				
				$db = new DbHandler();

				// fetching product list
				$result = $db->getAllProduct();

				$response["error"] = false;
				$response["products"] = array();

				// looping through result and preparing product list
				while ($task = $result->fetch_assoc()) {
					$tmp = array();
					$tmp["id"] = $task["id"];
					$tmp["name"] = $task["name"];
					$tmp["desciption"] = $task["description"];
					$tmp["supplier_name"] = $task["supplier_name"];
					$tmp["created_at"] = $task["created_at"];
					$tmp["updated_at"] = $task["updated_at"];
					array_push($response["products"], $tmp);
				}
                 $flag=200;
				
			}
			else 
			{ 
		        $flag=400;
			    $response["error"] = true;
				$response["message"] = "Unauthorized";
			}
			echoRespnse($flag, $response);
	}
	else if($routes[1]=="edit")                          // update product detail   
	{	    
			if(authenticate())              // Authentication of user using api key
			{
				            
				$db = new DbHandler();
				$response = array();
				
				$required = ['name', 'description', 'supplier_name'];
				$missing = verifyParameters($_POST, $required);
				
				$available = array_diff($required, $missing);
				
				$values = array();
				foreach ($available as $key)
				{
					$values[$key] = $_POST[$key];
				}
				

				// check product name exist in database 

				$k=$db->productIdExist($routes[2]);
				
				if ($k === false){
					$response["error"] = true;
					$response["message"] = "Product failed to update. Please try again!";
				}
				else{
					$result = $db->updateProduct($routes[2], $values);
					
					if ($result){
						$response["error"] = false;
						$response["message"] = "Product updated successfully";
					} else {
						$response["error"] = true;
						$response["message"] = "Product failed to update. Please try again!";
					}
				}
			}
			else 
			{
				$flag=401;
	            $response["error"] = true;
	            $response["message"] = "Unauthorized";	
			}
			
			echoRespnse($flag,$response);
	}
	else if($routes[1]=="delete")
	{
		   
			if(authenticate())
			{
				$db = new DbHandler();
				$response = array();
                
                // check this product name exist in the database 

    //             $k=$db->productIdExist($routes[2]);
				
				// if ($k === false){
				// 	$response["error"] = true;
				// 	$response["message"] = "Product failed to delete. Please try again!";
				// }
				// else{
					    
					    $result = $db->deleteProduct($routes[2]);
						if ($result) {
							
							// Product deleted successfully
							$response["error"] = false;
							$response["message"] = "Product deleted succesfully";
		                    $flag=200;					
						} else {							     				
							$response["error"] = false;
							$response["message"] = "Product failed to delete. Please try again!";
					}	
				//}
						
			}
			else {

				$flag=401;
	            $response["error"] = true;
	            $response["message"] = "Unauthorized";

			}
			echoRespnse($flag, $response);
	}
	

function unauthorize(&$flag, $response)
{
	$flag=401;
	$response["error"] = true;
	$response["message"] = "Unauthorized";
}

function echoRespnse($status_code, $response) {
	http_response_code($status_code);
    echo json_encode($response);
} 

?>