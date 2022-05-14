<?php
	$mysqli = new mysqli('localhost','conroyj4','password','secad');
	if($mysqli->connect_errorno){
		printf("Database connection failed: %s\n", $mysqli->connect_error);
			exit();
	}

	function changepassword($username, $newpassword) {
  		global $mysqli;
  		$prepared_sql = "UPDATE users SET password=password(?) WHERE username= ?;";
  		echo "DEBUG>prepared_sql= $prepared_sql\n";
  		if(!$stmt = $mysqli->prepare($prepared_sql)) return FALSE;
  		$stmt->bind_param("ss", $newpassword,$username);
  		if(!$stmt->execute()) return FALSE;
  		return TRUE;
  	}

  	//? - lECTURE 25 SLIDE 35
  	function addnewuser($username, $password) {
  		global $mysqli;
  		$prepared_sql = "INSERT INTO users (username, password) VALUES (?, password(?));";
  		echo "DEBUG>prepared_sql= $prepared_sql\n";
  		if(!$stmt = $mysqli->prepare($prepared_sql)) return FALSE;
  		$stmt->bind_param("ss", $username, $password);
  		if(!$stmt->execute()) return FALSE;
  		return TRUE;
  	}
?>
