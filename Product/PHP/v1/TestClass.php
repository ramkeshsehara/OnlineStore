<?php

// Test case testing class

class TestClass
{   



//--------------------------TESTING OF ADD PRODUCT API ----------------------------------------------

        // add product test case . input=> url, productlist,'/add', api key for add product

		public function testAdd($base_url, $arr, $var)
		{
			$url = $base_url . $var;

					$post_data = array(
							"name" => $arr->name,
							"description" => $arr->description,
							"supplier_name" =>$arr->supplier_name				
					);

					$string="Authorization:".$arr->api_key;

					$header = array($string);
					
					$response = $this->httpPost($url, $post_data, $header);
					
					$decoded = json_decode($response);

					 if (isset($decoded->error))
			        {
			        
						if ($decoded->error == $arr->error)
						{
							if ($decoded->error == true)
							{ 
								
								return $decoded->message == $arr->message;
							}
							return true;
						}
				      return false;
			        }
					if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
						die('error occured: ' . $decoded->response->errormessage);
					}
					return false; 
		}


//--------------------------TESTING OF EDIT PRODUCT API ------------------------------------------

        // update product . input=> url, productlist,'/edit', api key for update product

		public function testEdit($base_url, $arr, $var)
		{    
                      
		
					$url = $base_url . $var ."/". $arr->name;
					 

					$post_data = array(
							"name" => $arr->updatedname,
							"description" => $arr->description,
							"supplier_name" =>$arr->supplier_name				
					);

					$string="Authorization:".$arr->api_key;

					$header = array($string);
					
					$response = $this->httpPost($url, $post_data, $header);

					$decoded = json_decode($response);

					 if (isset($decoded->error))
			        {
						if ($decoded->error == $arr->error)
						{
							if ($decoded->error == true)
							{
								return $decoded->message == $arr->message;
							}
							return true;
						}
				      return false;
			        }
					if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
						die('error occured: ' . $decoded->response->errormessage);
					}
					return false;

		}
       

//--------------------------TESTING OF LOGIN API ----------------------------------------------

       // user login. input=> url, credential,'/login'

        public function testLogin($base_url, $arr, $var)
        {

			$url = $base_url . $var;

			$post_data = array(
					"email" => $arr->email,
					"password_hash" => $arr->password				
			);
           
			$response = $this->httpPostLogin($url, $post_data);
			
			$decoded = json_decode($response);
            
			if (isset($decoded->error))
			{
				if ($decoded->error == $arr->error)
				{
					if ($decoded->error == true)
					{
						
						return $decoded->message == $arr->message;
					}
					return true;
				}
				return false;
			}
			if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
				die('error occured: ' . $decoded->response->errormessage);
			}
			return false;
        }


//--------------------------TESTING OF VIEW PRODUCT API ----------------------------------------------
   
       // view product list . input=> url, productlist,'/view',api key for access api

        public function testView($base_url,$arr,$var)
        {
        	        $url = $base_url . $var;
       
					$string="Authorization:".$arr->api_key;

					$header = array($string);

					$response = $this->httpGet($url,$header);
					
					$decoded = json_decode($response);

					if (isset($decoded->error))
					{
						if ($decoded->error == $arr->error)
						{
							if ($decoded->error == true)
							{
								
								return $decoded->message == $arr->message;
							}
							return true;
						}
						return false;
					}
					if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
						die('error occured: ' . $decoded->response->errormessage);
					}
					return false;				


        }
       
//--------------------------TESTING OF DELETE API ----------------------------------------------

       // test delete. input=> url, id of product,'/delete',api key for access api

        public function testDelete($base_url,$arr,$var)
        {
        	
					$url = $base_url . $var."/".$arr->name;

					$string="Authorization:".$arr->api_key;

					$header = array($string);

					$response = $this->httpDelete($url,$header);
					
					$decoded = json_decode($response);

					//var_dump($decoded);

					if (isset($decoded->error))
					{
						if ($decoded->error == $arr->error)
						{
							if ($decoded->error == true)
							{
								return $decoded->message == $arr->message;
							}
							return true;
						}
						
						return false;
					}
					if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
						die('error occured: ' . $decoded->response->errormessage);
					}
					return false;					
        	
        }

        // sending request to api for get method 

        public function httpGet($url, $header)
        {

        	$curl = curl_init($url);
		    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		    $response = curl_exec($curl);
		    curl_close($curl);
		    return $response;

        }

        // sending request to api for delete method
       
       public function httpDelete($url, $header)
       {

       	    $curl = curl_init($url);
		    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		    $response = curl_exec($curl);
		    curl_close($curl);
		    return $response;

       }

		// sending request to api for delete method

		public function httpPost($url, $data, $header)
		{

		    $curl = curl_init($url);
		    curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
		    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		    $response = curl_exec($curl);
		    curl_close($curl);
		    return $response;
		}
        
        //sending request to api for login method

		public function httpPostLogin($url,$data)
		{
			$curl = curl_init($url);
		    curl_setopt($curl, CURLOPT_POST, true);
		    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
		    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		    $response = curl_exec($curl);
		    curl_close($curl);
		    return $response;

		} 
		
}

?>