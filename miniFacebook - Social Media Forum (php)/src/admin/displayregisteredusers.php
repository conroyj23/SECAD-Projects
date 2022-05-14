<?php
	require 'database.php';
	//require 'session_auth.php';
	// DO WE NEED THESE TWO ABOVE?

	$mysqli = new mysqli('localhost',
			     'team14',
			     'password',
			     'secad_team14');
	if ($mysqli->connect_errno) {
		printf("Database connection failed: %s\n", $mysqli->connect_errno);
		exit();
	}   

	if(strcmp($_SESSION["role"], "superuser") !== 0) {
		echo "<script>alert(You are not authorized to access this!);</script>";
		session_destroy();
		header("Refresh:0; url=form.php");
		die();
	}


?>
	<h2> Please view the registered users below:</h2>
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
	<br>
	<a href="index.php">Go back to the main page</a> | <a href="logout.php">Logout</a>
