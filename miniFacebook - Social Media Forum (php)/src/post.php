<?php
require 'database.php';
$query = $_REQUEST["post"];
if(!isset($query)) exit;
$prepared_sql = "SELECT title, content, postDate FROM posts";  //might need to fix later
//ensure that the database.php file exists and the $mysqli variable is defined there
if(!$stmt = $mysqli->prepare($prepared_sql))
	echo "Prepared Statement Error";
$query = "%".$query."%";
$stmt->bind_param('s', $query);
if(!stmt->execute()) echo "Execute failed ";
$title = NULL;
$content = NULL;
$postDate = NULL;
if(!stmt->bind_result($title,$content,$postDate)) echo "Binding failed";
//this will bind each row with the variables
$num_rows = 0;
while($stmt->fetch()){
	echo htmlentities($title) . ", " . htmlentities($content) . ", " . htmlentities($postDate) . "<br>";
	$num_rows++;
}
if($num_rows == 0) echo "No matching";

?>
