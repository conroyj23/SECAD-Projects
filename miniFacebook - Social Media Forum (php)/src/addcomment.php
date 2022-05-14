<?php
	require "session_auth.php";
	require "database.php";
	$username= $_SESSION["username"];
	$content= $_POST["commentcontent"];
	$pid= $_POST["commentPostID"];
	$nocsrftoken = $_POST["nocsrftoken"];
	if (!isset($nocsrftoken) or ($nocsrftoken!=$_SESSION['nocsrftoken'])) {
		echo "<script>alert('Cross-site request forgery is detected!');</script>";
		header("Refresh:0; url=logout.php");
		die();
	}
	echo "DEBUG:addnewcomment.php->Got: username=$username;content=$content;pid=$pid\n<br>";
	if (addnewcomment($content, $username, $pid)) {
		echo "<h4>The comment has been created.</h4>";
	}else{
		echo "<h4>Error: Cannot create comment.</h4>";
	}
?>
<a href="index.php">Home</a> | <a href="logout.php">Logout</a>
