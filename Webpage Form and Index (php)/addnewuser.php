 <?php
	require "database.php";
	$username= $_POST["username"]; //$_REQUEST["username"];
	$password= $_POST["password"];
	if (isset($username) AND isset($password)) {
		echo "DEBUG:addnewuser.php->Got: username=$username;password=$password\n<br>";
		if (addnewuser($username, $password)) {
			echo "<h4>Your account has been created!</h4>";
		}else{
			echo "<h4>Error: Cannot create your account.</h4>";
		}
	}else{
		echo "No provided username/password to create account.";
		exit();
	}
?>
<a href="form.php">Back to Login</a>