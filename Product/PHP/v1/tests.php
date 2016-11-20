<?php

require_once 'TestClass.php';

$base_url="http://localhost/Ecommerce/Product/PHP/v1/index.php/";

 $t=new TestClass();
 $flag = true;

//--------------------------READING LOGIN TEST CASES ----------------------------------------------

echo "Testing login:------------------";
$myfile = file_get_contents("login_testcase.txt") or die("Unable to open file!");
$data = json_decode($myfile);
foreach ($data as $record){
	$var="login";
	if (assert($t->testLogin($base_url,$record,$var))) {
		echo "PASS ";
	}
	else {
		echo "FAIL ";
		$flag = false;
	}
}
echo "<br>";

//--------------------------READING ADD PRODUCT TEST CASES -------------------------------------------

echo "Testing add product:----------";
$myfile = file_get_contents("addproduct_testcase.txt") or die("Unable to open file!");
$data = json_decode($myfile);
foreach ($data as $record){
	$var="add";
	if (assert($t->testAdd($base_url,$record,$var))) {
		echo "PASS ";
	}
	else {
		echo "FAIL ";
		$flag = false;
	}
}
echo "<br>";

//---------------------- READING EDIT PRODUCT TEST CASES ------------------------------------------

echo "Testing edit product:----------";
$myfile = file_get_contents("editproduct_testcase.txt") or die("Unable to open file!");
$data = json_decode($myfile);
foreach ($data as $record){
	$var="edit";
	if (assert($t->testEdit($base_url,$record,$var))) {
		echo "PASS ";
	}
	else {
		echo "FAIL ";
		$flag = false;
	}
}
echo "<br>";


//--------------------------READING VIEW PRODUCT TEST CASES---------------------------------

echo "Testing view product:---------";
$myfile = file_get_contents("viewproduct_testcase.txt") or die("Unable to open file!");
$data = json_decode($myfile);
foreach ($data as $record){
	$var="view";
	if (assert($t->testView($base_url,$record,$var))) {
		echo "PASS ";
	}
	else {
		echo "FAIL ";
		$flag = false;
	}
}
echo "<br>";


//------------------------READING DELETE PRODUCT TEST CASES ----------------------------------------------

echo "Testing delete product:--------";
$myfile = file_get_contents("deleteproduct_testcase.txt") or die("Unable to open file!");
$data = json_decode($myfile);
foreach ($data as $record){
	$var="delete";
	if (assert($t->testDelete($base_url,$record,$var))) {
		echo "PASS ";
	}
	else {
		echo "FAIL ";
		$flag = false;
	}
}

echo "<br>";

if ($flag == true) {
	echo "<h4 style='color:green;'>Setup successful!</h4>";
}
else {
	echo "<h4 style='color:red;'>Setup failed.</h4>";
}
//------------------<--------------END-------------->------------------------

?>