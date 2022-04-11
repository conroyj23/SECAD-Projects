<?php
	session_start();    
	$mysqli = new mysqli('localhost','conroyj4','password','secad');
	if($mysqli->connect_errorno){
		printf("Database connection failed: %s\n", $mysqli->connect_error);
			exit();
	}
	if (securechecklogin($_POST["username"],$_POST["password"])) {
?>
	<h2> Welcome <?php echo htmlentities($_POST['username']); ?> !</h2>
<?php		
	}else{
		echo "<script>alert('Invalid username/password');</script>";
		die();
	}
	function checklogin($username, $password) {
		$account = array("admin","1234");
		if (($username== $account[0]) and ($password == $account[1])) 
		  return TRUE;
		else return FALSE;
  	}
  	function checklogin_mysql($username, $password) {
		$mysqli = new mysqli('localhost',
							'conroyj4' /*Database username*/,
							'password'/*Database password*/,
							'secad' /*Database name*/);
		if ($mysqli->connect_errno){
			printf("Database connection failed: %s\n", $mysqli->connect_error);
			exit();
		}
		//for debug ONLY
		$sql = "SELECT * FROM users where username='$username' AND password = password('$password')";
		echo $sql;

		$result = $mysqli->query($sql);
		if ($result->num_rows == 1)
			return TRUE;
		return FALSE;
  	}
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
