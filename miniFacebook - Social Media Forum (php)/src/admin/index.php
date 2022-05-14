<?php
	$lifetime = 15 * 60; //15 Minutes
	$path = "/admin/"; //For team project: $path="/";
	$domain = "secad-s22-team14-conroyj4-oeij01.minifacebook.com"; //For team project: $domain="*.minifacebook.com";
	$secure = TRUE;
	$httponly = TRUE;
	session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);	
	session_start();    
	$mysqli = new mysqli('localhost','team14','password','secad_team14');
	if($mysqli->connect_errorno){
		printf("Database connection failed: %s\n", $mysqli->connect_error);
			exit();
	}
	if (isset($_POST["username"]) and isset($_POST["password"]) ){
		if (securechecklogin($_POST["username"],$_POST["password"])) {
			$_SESSION["logged"] = TRUE;
			$_SESSION["username"] = $_POST["username"];
			$_SESSION["browser"] = $_SERVER["HTTP_USER_AGENT"];
			$_SESSION["role"] = "superuser"; //admin role
			
		}else{
			echo "<script>alert('Invalid username/password');</script>";
			session_destroy();
			header("Refresh:0; url=form.php");
			die();
		}
	}

	if(strcmp($_SESSION["role"], "superuser") !==0 ){
		echo "<script>alert(You are not authoried to access this!);</scipt>";
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

	<h2> Registered users below:</h2>
	<br><br> 

<?php
	// show all the regular users
	global $mysqli;
	$prepared_sql = "SELECT username FROM users;";
	if (!$stmt = $mysqli->prepare($prepared_sql))
		return FALSE;
	if (!$stmt->execute()) { 
		echo "Stuck!";
		return FALSE;
	}
	$username = NULL;
	if(!$stmt->bind_result($username)) echo "Binding failed";
	while($stmt->fetch()){
		echo "username: '" . htmlentities($username) . "'<br>";
	}
	echo "<br><br>";

	// show all the superusers
	$prepared_sql = "SELECT user FROM superusers;";
	if (!$stmt = $mysqli->prepare($prepared_sql))
		return FALSE;
	if (!$stmt->execute()) { 
		echo "Stuck!";
		return FALSE;
}
$username = NULL;
	if(!$stmt->bind_result($username)) echo "Binding failed";
	while($stmt->fetch()){
		echo "Superuser with username '" . htmlentities($username) . "'<br>";
	}
?>

<?php
  	function securechecklogin($username, $password) {
  		global $mysqli;
  		$prepared_sql = "SELECT * FROM superusers WHERE user=? AND password=password(?);";
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
<?php
	require 'database.php';
	require 'session_auth.php';
	$rand= bin2hex(openssl_random_pseudo_bytes(16));
	$_SESSION["nocsrftoken"] = $rand;

?>