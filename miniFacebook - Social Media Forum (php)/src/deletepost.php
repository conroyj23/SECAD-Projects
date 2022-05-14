<?php
	require "session_auth.php";
	require "database.php";
	$username= $_SESSION["username"];
	$postID= $_POST["deletePostID"];
	$nocsrftoken = $_POST["nocsrftoken"];
	if (!isset($nocsrftoken) or ($nocsrftoken!=$_SESSION['nocsrftoken'])) {
		echo "<script>alert('Cross-site request forgery is detected!');</script>";
		header("Refresh:0; url=logout.php");
		die();
	}
	echo "DEBUG:deletepost.php->Got: username=$username;postID=$postID\n<br>";
	if (deletepost($postID, $username)) {
		deletecomments($postID);
		echo "<h4>The post has been deleted.</h4>";
	}else{
		echo "<h4>Error: Failed to delete post.</h4>";
	}
?>
<a href="index.php">Home</a> | <a href="logout.php">Logout</a>