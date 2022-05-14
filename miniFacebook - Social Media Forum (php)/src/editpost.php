<?php
	require "session_auth.php";
	require "database.php";
	$username= $_SESSION["username"];
	$content= $_POST["newcontent"];
	$postID= $_POST["editPostID"];

	$nocsrftoken = $_POST["nocsrftoken"];
	if (!isset($nocsrftoken) or ($nocsrftoken!=$_SESSION['nocsrftoken'])) {
		echo "<script>alert('Cross-site request forgery is detected!');</script>";
		header("Refresh:0; url=logout.php");
		die();
	}
	echo "DEBUG:editpost.php->Got: username=$username;content=$content;postID=$postID\n<br>";
	if (editpost($postID, $content, $username)) {
		echo "<h4>The post has been edited.</h4>";
	}else{
		echo "<h4>Error: Failed to edit post.</h4>";
	}
?>
<a href="index.php">Home</a> | <a href="logout.php">Logout</a>