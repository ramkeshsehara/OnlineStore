<?php

if(isset($_POST['name'])&&isset($_POST['password'])&&isset($_POST['host'])&&isset($_POST['dbname']))
{
		$username = $_POST['name'];
		$password = $_POST['password'];
		$host = $_POST['host'];
		$database = $_POST['dbname'];	

		$config = "<?php
		define('DB_USERNAME', '$username');
		define('DB_PASSWORD', '$password');
		define('DB_HOST', '$host');
		define('DB_NAME', '$database');
		define('USER_CREATED_SUCCESSFULLY', 0);
		define('USER_CREATE_FAILED', 1);
		define('USER_ALREADY_EXISTED', 2);
		?>
		";

		file_put_contents("../include/Config.php", $config);

		function connect() {
		    include_once '../include/Config.php';
		    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
		    if (mysqli_connect_errno()) {
		        echo "Failed to connect to MySQL: " . mysqli_connect_error();
		    }
		    return $conn;
		}

		$conn = connect();

		$sql = file_get_contents("../include/ecommerceonlinestore.sql");

		if (!$conn->multi_query($sql)) {
		    echo "Multi query failed: (" . $conn->errno . ") " . $conn->error;
		}

		header("location: tests.php");
}
else
{

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Wingify</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
  <center><h2>Please Enter Database information:</h2></center>
  <form class="form-horizontal" action="setup.php" method="post">
    <div class="form-group">
      <label class="control-label col-sm-2" for="email">Username:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="name" placeholder="Enter Username" required>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="pwd">Password:</label>
      <div class="col-sm-10">
        <input type="password" class="form-control" name="password" placeholder="Enter password">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="pwd">Database Name:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="dbname" placeholder="Enter Database Name" required>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="pwd">Host Url:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="host" placeholder="Enter Host Url" required>
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-default">Submit</button>
      </div>
    </div>
  </form>
</div>

</body>
</html>

<?php
} 
?>
