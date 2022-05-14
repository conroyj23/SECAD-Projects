<?php
	$lifetime = 15 * 60; //15 Minutes
	$path = "/"; //For team project: $path="/";
	$domain = "secad-s22-team14-conroyj4-oeij01.minifacebook.com"; //For team project: $domain="*.minifacebook.com";
	$secure = TRUE;
	$httponly = TRUE;
	session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);	
	session_start();

	//Check the session
	if (!isset($_SESSION["logged"] ) or $_SESSION["logged"] != TRUE) {
	//The session is not authenticated
		echo "<script>alert('You have to login first!');</script>";
		session_destroy();
		header("Refresh:0; url=form.php");
		die();
	}
	//I think this goes here...
	if ($_SESSION["browser"] != $_SERVER["HTTP_USER_AGENT"]) {
		echo "<script>alert('Session hijacking is detected!');</script>";
		header("Refresh:0; url=form.php");
		die();
	}
?>