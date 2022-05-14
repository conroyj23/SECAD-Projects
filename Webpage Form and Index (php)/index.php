<?php
	$lifetime = 15 * 60; //15 Minutes
	$path = "/lab6"; //For team project: $path="/";
	$domain = "192.168.56.101"; //Your IP Address ////For team project: $domain="*.minifacebook.com";
	$secure = TRUE;
	$httponly = TRUE;
	session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);	
	session_start();    
	$mysqli = new mysqli('localhost','conroyj4','password','secad');
	if($mysqli->connect_errorno){
		printf("Database connection failed: %s\n", $mysqli->connect_error);
			exit();
	}
	if (isset($_POST["username"]) and isset($_POST["password"]) ){
		if (securechecklogin($_POST["username"],$_POST["password"])) {
			$_SESSION["logged"] = TRUE;
			$_SESSION["username"] = $_POST["username"];
			$_SESSION["browser"] = $_SERVER["HTTP_USER_AGENT"];
		}else{
			echo "<script>alert('Invalid username/password');</script>";
			session_destroy();
			header("Refresh:0; url=form.php");
			die();
		}
	}
	if (!isset($_SESSION["logged"] ) or $_SESSION["logged"] != TRUE) {
		echo "<script>alert('You have not login. Please login first');</script>";
		//session_destroy(); //Should this be here? It wasnt in notes but it is above.
		header("Refresh:0; url=form.php");
		die();
	}
	//I think this goes here...
	if ($_SESSION["browser"] != $_SERVER["HTTP_USER_AGENT"]) {
		echo "<script>alert('Session hijacking is detected!');</script>";
		header("Refresh:0; url=form.php");
		die();
	}
?>
	<h2> Welcome <?php echo htmlentities($_SESSION["username"]); ?> !</h2>
	<a href="changepasswordform.php">Change password</a> 
	<a href="logout.php">Logout</a>
<?php
  	function securechecklogin($username, $password) {
  		global $mysqli;
  		$prepared_sql = "SELECT * FROM users WHERE username= ?  AND password=password(?);";
  		if(!$stmt = $mysqli->prepare($prepared_sql))
  			echo "Prepared Statement Error";
  		$stmt->bind_param("ss", $username,$password);
  		if(!$stmt->execute()) echo "Execute Error";
  		if(!$stmt->store_result()) echo "Store_Result Error";
  		$result = $stmt;
  		if ($result->num_rows ==1)
  			return TRUE;
  		return FALSE;
  	}
?>
