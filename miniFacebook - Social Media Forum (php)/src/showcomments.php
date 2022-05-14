<?php
	require "session_auth.php";
	require "database.php";

	$content= $_POST["postcontent"];
	$postid= $_POST["postid"];
	$nocsrftoken = $_POST["nocsrftoken"];
	$owner = $_POST["postowner"];

	if (!isset($nocsrftoken) or ($nocsrftoken!=$_SESSION['nocsrftoken'])) {
		echo "<script>alert('Cross-site request forgery is detected!');</script>";
		header("Refresh:0; url=logout.php");
		die();
	}
	echo "DEBUG:showcomments.php->Got: content=$content;postid=$postid;owner=$owner\n<br>";
	echo "<h4>Post by '" . htmlentities($owner) . "' with Post ID " . htmlentities($postid) . ": " . htmlentities($content) . "</h4><br><br>";

	showComments($postid);
?>
<a href="index.php">Home</a> | <a href="logout.php">Logout</a>

<?php
function showComments($postid){
		global $mysqli;
		$prepared_sql = "SELECT owner, content, commentID FROM comments WHERE pID=?;";
		if (!$stmt = $mysqli->prepare($prepared_sql)) echo "Failed to prepare";
		$stmt->bind_param('i', $postid);
		if (!$stmt->execute()) echo "Failed to execute";
		$owner = NULL; $content = NULL; $commentID = NULL;
		if(!$stmt->bind_result($owner, $content, $commentID)) echo "Binding failed";
		while($stmt->fetch()){
			echo htmlentities($owner) . " with comment ID " . htmlentities($commentID) . ": " . htmlentities($content) . "<br>";
		}
	}

?>