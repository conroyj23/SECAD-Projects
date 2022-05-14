<?php
	require "session_auth.php";
	require "database.php";
	$username= $_SESSION["username"];
	$content= $_POST["content"];
	$nocsrftoken = $_POST["nocsrftoken"];
	if (!isset($nocsrftoken) or ($nocsrftoken!=$_SESSION['nocsrftoken'])) {
		echo "<script>alert('Cross-site request forgery is detected!');</script>";
		header("Refresh:0; url=logout.php");
		die();
	}
	echo "DEBUG:addnewpost.php->Got: username=$username;content=$content\n<br>";
	if (addnewpost($content, $username)) {
		echo "<h4>The post has been created.</h4>";
	}else{
		echo "<h4>Error: Cannot create post.</h4>";
	}
?>
<a href="index.php">Home</a> | <a href="logout.php">Logout</a>