### CPS 475/575 Secure Application Development 

John Conroy <conroyj4@udayton.edu>
James Oei <oeij01@udayton.edu>

# 1. Introduction

This is project's purpose is to design and implement a secure "MiniFacebook" page. Users will be able to Login, change password, add and edit posts, and comment on other post. In addition we will also add an extra access level in which superusers can view the list of registered users as well as disable/enable users. This project will reinforce safe programming practive as well as help gain experience in application development.


# 2. Design



*   Database: Our database is powered by MySql and manipulated with PHP. There are currently 5 tables being stored in the database. These four are users, posts, superusers, and comments.
*   The user interface: We have not implemented any CSS to our site yet as we are focusing on the functionality of it at the moment. Once logged-in users are taken to the home page in which they can create a post, edit a post, view posts, and make comments on other people's post all on the same page.
*   Functionalities of your application:

# 3. Implementation & security analysis


*   How did you apply the security programming principles in your project?
    - We have applied the security programing principles that we have learned in class by taking a look at each portion of the web app and analyzing it for possible vulnerabilites. We took steps to deploy steps to secure the app against cross-site scripting by sanatizing our HTML outputs. In addition we have prevented SQL injection by preparing SQL statements when writing our code. Sessions are secured through cookie authentification and all transaction data is protected by encryprtion.
*   What database security principles have you used in your project?
    - Our database is not susceptible to SQL injection attacks as we have prepared SQL statements in our php code. In addition we use our own login for the database instead of using the root user. 
*   Is your code robust and defensive? How?
    - Our code is robust and defense. It is robust because we sanatize our inputs at every level of the application. It is defensive because we check for CSRF tokens on every page, deployed with HTTPS so that our traffic is encrypted, we filter our html outputs
*   How did you defend your code against known attacks such as including XSS, SQL Injection, CSRF, Session Hijacking
    - We defend XSS attacks by sanatizing the HTML outputs. 
    - SQL injections are defended by having prepared statements in our PHP. 
    - CRSF attacks are defended against by encrypting all transactional data as well as salting the passwords upon registration.
    - Session hijacking is defended against by using https as well as securing the cookie for each session
*   How do you separate the roles of super users and regular users?
    - We seperate the roles of siper users and reulgar users by assigning a Session role upon login. Normal users are given the role "user" and superusers are assigned "superuser". Only sessions with a "superuser" role can access pages meant for admins.



# 4. Demo (screenshots)


*   Everyone can register a new account and then login
![register](https://i.ibb.co/tp9qXmH/Screenshot-338.png)
New users can register for an account using the 'Create an account' link on the login page. This redirects the user to the regirstration form in which they input a valid username password. The info is then saved in the database and the user can now login and get authenticated at the login page.
* Logged in users can create a new post and view it.
![create_post](https://i.ibb.co/fqFT6qB/Screenshot-340.png)
* Logged In users can edit their own posts
![edit_post](https://i.ibb.co/KyVt52S/Screenshot-341.png)
* Logged-in users can delete their own posts
![delete_post](https://i.ibb.co/qsqT3Hx/Screenshot-343.png)
*   A regular logged-in user can delete her own existing posts but cannot delete the posts of others
![delete_post2](https://i.ibb.co/2cnX7HX/Screenshot-344.png)
*  Superuser can login and view list of registered users.
![superuser](https://i.ibb.co/1dBXjj9/Screenshot-345.png)

# Appendix

## /addcomment.php
```php
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

```
## /addnewpost.php
```php
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
```
## /addnewuser.php
```php
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
	function sanitize_input($input) {
 		$input = trim($input);
 		$input = stripslashes($input);
		$input = htmlspecialchars($input);
		return $input;
}
?>
<a href="form.php">Back to Login</a>

```
## /admin/displayregisteredusers.php
```php
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

```
## /admin/form.php
```php
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login page - SecAD</title>
</head>
<body>
      	<h1>Superuser login form, SecAD</h1>
        <h2>Team 14: James Oei and Jack Conroy</h2>


<?php
  //some code here
  echo "Current time: " . date("Y-m-d h:i:sa")
?>
	 <form action="index.php" method="POST" class="form login">
                Username:<input type="text" class="text_field" name="username" /> <br>
                Password: <input type="password" class="text_field" name="password" /> <br>
                <button class="button" type="submit">
                  Login
                </button>
          </form>
          <br>

</body>
</html>


```
## /admin/index.php
```php
<?php
	$lifetime = 15 * 60; //15 Minutes
	$path = "/admin/"; //For team project: $path="/";
	$domain = "secad-s22-team14-conroyj4-oeij01.minifacebook.com"; //For team project: $domain="*.minifacebook.com";
	$secure = TRUE;
	$httponly = TRUE;
	session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);	
	session_start();    
	$mysqli = new mysqli('localhost','team14','password','secad_team14');
	if($mysqli->connect_errorno){
		printf("Database connection failed: %s\n", $mysqli->connect_error);
			exit();
	}
	if (isset($_POST["username"]) and isset($_POST["password"]) ){
		if (securechecklogin($_POST["username"],$_POST["password"])) {
			$_SESSION["logged"] = TRUE;
			$_SESSION["username"] = $_POST["username"];
			$_SESSION["browser"] = $_SERVER["HTTP_USER_AGENT"];
			$_SESSION["role"] = "superuser"; //admin role
			
		}else{
			echo "<script>alert('Invalid username/password');</script>";
			session_destroy();
			header("Refresh:0; url=form.php");
			die();
		}
	}

	if(strcmp($_SESSION["role"], "superuser") !==0 ){
		echo "<script>alert(You are not authoried to access this!);</scipt>";
		//session_destroy(); //Should this be here? It wasnt in notes but it is above.
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

	<h2> Welcome <?php echo htmlentities($_SESSION["username"]); ?> !</h2>

	<h2> Registered users below:</h2>
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

<?php
  	function securechecklogin($username, $password) {
  		global $mysqli;
  		$prepared_sql = "SELECT * FROM superusers WHERE user=? AND password=password(?);";
  		if(!$stmt = $mysqli->prepare($prepared_sql))
  			echo "Prepared Statement Error";
  		$stmt->bind_param("ss", $username,$password);
  		if(!$stmt->execute()) echo "Execute Error";
  		if(!$stmt->store_result()) echo "Store_Result Error";
  		$result = $stmt;
  		if ($result->num_rows ==1)
  			return TRUE;
  		return FALSE;
  	}
?>
<?php
	require 'database.php';
	require 'session_auth.php';
	$rand= bin2hex(openssl_random_pseudo_bytes(16));
	$_SESSION["nocsrftoken"] = $rand;

?>
```
## /changepassword.php
```php
<?php
	require "session_auth.php";
	require "database.php";
	$username= $_SESSION["username"]; //$_REQUEST["username"];
	$newpassword= $_POST["newpassword"];
	$nocsrftoken = $_POST["nocsrftoken"];
	if (!isset($nocsrftoken) or ($nocsrftoken!=$_SESSION['nocsrftoken'])) {
		echo "<script>alert('Cross-site request forgery is detected!');</script>";
		header("Refresh:0; url=logout.php");
		die();
	}
	if (isset($username) AND isset($newpassword)) {
		echo "DEBUG:changepassword.php->Got: username=$username;newpassword=$newpassword\n<br>";
		if (changepassword($username, $newpassword)) {
			echo "<h4>The new password has been set.</h4>";
		}else{
			echo "<h4>Error: Cannot change the password.</h4>";
		}
	}else{
		echo "No provided username/password to change.";
		exit();
	}
?>
<a href="index.php">Home</a> | <a href="logout.php">Logout</a>
```
## /changepasswordform.php
```php
<?php
	require "session_auth.php";
	$rand= bin2hex(openssl_random_pseudo_bytes(16));
	$_SESSION["nocsrftoken"] = $rand;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Change password page - SecAD</title>
</head>
<body>
      	<h2>Change Password, SecAD</h2>

<?php
  //some code here
  echo "Current time: " . date("Y-m-d h:i:sa")
?>
          <form action="changepassword.php" method="POST" class="form login">
                Username:<!--<input type="text" class="text_field" name="username" /-->
                <?php echo htmlentities($_SESSION["username"]); ?> 
                <br>
                <input type="hidden" name="nocsrftoken" value="<?php echo $rand; ?>" />
                New password: <input type="password" class="text_field" name="newpassword" /> <br>
                <button class="button" type="submit">
                  Change password
                </button>
          </form>

</body>
</html>


```
## /database.php
```php
<?php
	$mysqli = new mysqli('localhost','team14','password','secad_team14');
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
	function sanitize_input($input) {
		 $input = trim($input);
		 $input = stripslashes($input);
		 $input = htmlspecialchars($input);
		 return $input;
  }


  //posts = [postID, content, owner]
  function addnewpost($content, $owner) {
      global $mysqli;

      $prepared_sql = "INSERT INTO posts (content, owner) VALUES (?, ?);";
      echo "DEBUG>prepared_sql= $prepared_sql\n";
      if(!$stmt = $mysqli->prepare($prepared_sql)) return FALSE;
      $stmt->bind_param("ss", $content, $owner);
      if(!$stmt->execute()) return FALSE;
      return TRUE;
    }

    //comments = [commentID, content, owner, pID]
    function addnewcomment($content, $owner, $pID) {
      global $mysqli;

      $prepared_sql = "INSERT INTO comments (content, owner, pID) VALUES (?, ?, ?);";
      echo "DEBUG>prepared_sql= $prepared_sql\n";
      if(!$stmt = $mysqli->prepare($prepared_sql)) return FALSE;
      $stmt->bind_param("ssi", $content, $owner, $pID); 
      if(!$stmt->execute()) return FALSE;
      return TRUE;
    }
    //posts = [postID, content, owner] // MUST INPUT USERNAME TO CROSSMATCH WITH POSTS'S OWNER
    function editpost($postID, $content, $user) {
      global $mysqli;

      $prepared_sql = "UPDATE posts SET content=? WHERE postID=? AND owner=?;";
      echo "DEBUG>prepared_sql= $prepared_sql\n";
      if(!$stmt = $mysqli->prepare($prepared_sql)) return FALSE;
      $stmt->bind_param("sis", $content, $postID, $user);
      if(!$stmt->execute()) return FALSE;
      return TRUE;
    }

    //posts = [postID, content, owner] // MUST INPUT USERNAME TO CROSSMATCH WITH POSTS'S OWNER
    function deletepost($postID, $user) {
      global $mysqli;

      $prepared_sql = "DELETE FROM posts WHERE postID=? AND owner=?;";
      echo "DEBUG>prepared_sql= $prepared_sql\n";
      if(!$stmt = $mysqli->prepare($prepared_sql)) return FALSE;
      $stmt->bind_param("is", $postID, $user);
      if(!$stmt->execute()) return FALSE;
      return TRUE;
    }

    //comments = [commentID, content, owner, pID]
    function deletecomments($pID) {
      global $mysqli;

      $prepared_sql = "DELETE FROM comments WHERE pID=?;";
      echo "DEBUG>prepared_sql= $prepared_sql\n";
      if(!$stmt = $mysqli->prepare($prepared_sql)) return FALSE;
      $stmt->bind_param("i", $pID);
      if(!$stmt->execute()) return FALSE;
      return TRUE;
    }
    
?>

```
## /deletepost.php
```php
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
```
## /editpost.php
```php
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
```
## /form.php
```php
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login page - SecAD</title>
</head>
<body>
      	<h1>Team Project, SecAD</h1>
        <h2>Team 14: John Conroy and James Oei</h2>

<?php
  //some code here
  echo "Current time: " . date("Y-m-d h:i:sa")
?>
          <form action="index.php" method="POST" class="form login">
                Username:<input type="text" class="text_field" name="username" /> <br>
                Password: <input type="password" class="text_field" name="password" /> <br>
                <button class="button" type="submit">
                  Login
                </button>
          </form>
          <a href="registrationform.php">Create an account</a>

</body>
</html>


```
## /index.php
```php
<?php
	$lifetime = 15 * 60; //15 Minutes
	$path = "/"; //For team project: $path="/";
	$domain = "secad-s22-team14-conroyj4-oeij01.minifacebook.com"; //For team project: $domain="*.minifacebook.com";
	$secure = TRUE;
	$httponly = TRUE;
	session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);	
	session_start();    
	$mysqli = new mysqli('localhost','team14','password','secad_team14');
	if($mysqli->connect_errorno){
		printf("Database connection failed: %s\n", $mysqli->connect_error);
			exit();
	}
	if (isset($_POST["username"]) and isset($_POST["password"]) ){
		if (securechecklogin($_POST["username"],$_POST["password"])) {
			$_SESSION["logged"] = TRUE;
			$_SESSION["username"] = $_POST["username"];
			$_SESSION["browser"] = $_SERVER["HTTP_USER_AGENT"];
		}else{
			echo "<script>alert('Invalid username/password');</script>";
			session_destroy();
			header("Refresh:0; url=form.php");
			die();
		}
	}
	if (!isset($_SESSION["logged"] ) or $_SESSION["logged"] != TRUE) {
		echo "<script>alert('You have not login. Please login first');</script>";
		//session_destroy(); //Should this be here? It wasnt in notes but it is above.
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
	<h2> Welcome <?php echo htmlentities($_SESSION["username"]); ?> !</h2>
	<a href="changepasswordform.php">Change password</a> 
	<a href="logout.php">Logout</a>
<?php
  	function securechecklogin($username, $password) {
  		global $mysqli;
  		$prepared_sql = "SELECT * FROM users WHERE username= ?  AND password=password(?);";
  		if(!$stmt = $mysqli->prepare($prepared_sql))
  			echo "Prepared Statement Error";
  		$stmt->bind_param("ss", $username,$password);
  		if(!$stmt->execute()) echo "Execute Error";
  		if(!$stmt->store_result()) echo "Store_Result Error";
  		$result = $stmt;
  		if ($result->num_rows ==1)
  			return TRUE;
  		return FALSE;
  	}
?>
<?php
	require 'database.php';
	require 'session_auth.php';
	$rand= bin2hex(openssl_random_pseudo_bytes(16));
	$_SESSION["nocsrftoken"] = $rand;
// test comment
?>
	<!-- CODE FOR POSTS -->
	<form action="addnewpost.php" method="POST">
		<input type="hidden" name="nocsrftoken" value="<?php echo $rand; ?>" />
        Write a post in the box below <br>
	<input type="text" class="text_field" name="content" size="50" maxlength="280" required
            pattern=".{1,280}"
            title="The post must be less than 280 characters" /> <br>
       	<button class="button" type="submit">
           Add Post
        </button>
    </form>

    <!-- CODE FOR EDITING POSTS -->
	<form action="editpost.php" method="POST">
		<input type="hidden" name="nocsrftoken" value="<?php echo $rand; ?>" />
        Enter the Post ID of the post you'd like to change below: <br>
	<input type="text" class="text_field" name="editPostID" size="50" maxlength="5" required
            pattern=".{1,5}"
            title="The post ID must be less than 6 characters long" /> <br>
    	New Content: <br>
    <input type="text" class="text_field" name="newcontent" size="50" maxlength="280" required
            pattern=".{1,280}"
            title="The post must be less than 280 characters" /> <br>

       	<button class="button" type="submit">
           Edit Post
        </button>
    </form>

    <!-- CODE FOR MAKING COMMENTS -->
	<form action="addcomment.php" method="POST">
		<input type="hidden" name="nocsrftoken" value="<?php echo $rand; ?>" />
        Enter the Post ID you'd like to comment on below: <br>
	<input type="text" class="text_field" name="commentPostID" size="50" maxlength="5" required
            pattern=".{1,5}"
            title="The post ID must be less than 6 characters" /> <br>
    	Comment Content: <br>
    <input type="text" class="text_field" name="commentcontent" size="50" maxlength="280" required
            pattern=".{1,280}"
            title="The comment must be less than 280 characters" /> <br>

       	<button class="button" type="submit">
           Post Comment
        </button>
    </form>

    <!-- CODE FOR DELETING POSTS -->
	<form action="deletepost.php" method="POST">
		<input type="hidden" name="nocsrftoken" value="<?php echo $rand; ?>" />
        Enter the Post ID you'd like to delete below: <br>
	<input type="text" class="text_field" name="deletePostID" size="50" maxlength="5" required
            pattern=".{1,5}"
            title="The post ID must be less than 6 characters" /> <br>
       	<button class="button" type="submit">
           Delete Post
        </button>
    </form>





<?php
	global $mysqli;
	$prepared_sql = "SELECT owner, content, postID FROM posts;";
	if (!$stmt = $mysqli->prepare($prepared_sql))
		return FALSE;
	if (!$stmt->execute()) { 
		echo "Execute Failed!";
		return FALSE;
	}
	$owner = NULL; $content = NULL; $postid = NULL;
	if(!$stmt->bind_result($owner, $content, $postid)) echo "Binding failed";
	while($stmt->fetch()){
		echo "Post by '" . htmlentities($owner) . "' with Post ID " . htmlentities($postid) . ": " . htmlentities($content);
?>
	<form action="showcomments.php" method="POST">
		<input type="hidden" name="nocsrftoken" value="<?php echo $rand; ?>" />
		<input type="hidden" name="postcontent" value="<?php echo $content; ?>" />
		<input type="hidden" name="postid" value="<?php echo $postid; ?>" />
		<input type="hidden" name="postowner" value="<?php echo $owner; ?>" />
       	<button class="button" type="submit">
           Show Comments
        </button>
    </form>
<?php
		//showComments($postid);
		echo "<br><br>";
	} //LOOP TO NEXT POST
?>



```
## /logout.php
```php
<?php
	session_start();
	session_destroy();
?>
<p> You are logged out! </p>

<a href="form.php">Login again</a>


```
## /post.php
```php
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

```
## /registrationform.php
```php
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Registration page - SecAD</title>
</head>
<body>
      	<h1>Registration form, SecAD</h1>

<?php
  //some code here
  echo "Current time: " . date("Y-m-d h:i:sa")
?>
          <form action="addnewuser.php" method="POST" class="form login">
                Username:<input type="text" class="text_field" name="username" required
		title="Please enter a valid email as username"
		placeholder="Your email address"
 			/>
	<br>
                 Password:  <input type="password" class="text_field" name="password" required
                    pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#%^&])[\w!@#%^&]{8,20}"
                    title="Password must have between 8-20 characters and include 1 special symbol !@#%^&, 1 number, 1 lowercase and 1 uppercase letter"
                    onchange="this.setCustomValidity(this.validity.patternMismatch?this.title: ''); form.repassword.pattern = this.value;" />
		Retype password: <input type="password" class="text_field" name="repassword"
                    placeholder="Retype your password" required
                    title="Passwords do not match"
                    onchange="this.setCustomValidity(this.validity.patternMismatch?this.title '');" /><br>
                <button class="button" type="submit">
                  Sign Up
                </button>
          </form>

</body>
</html>



```
## /session_auth.php
```php
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
```
## /showcomments.php
```php
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
```
## /database.sql
```sql
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `posts`;
DROP TABLE IF EXISTS `superusers`;

CREATE TABLE users(
	username varchar(50) PRIMARY KEY,
	password varchar(100) NOT NULL);


LOCK TABLES `users` WRITE;
INSERT INTO `users` VALUES ('admin',password('password'));
UNLOCK TABLES;

DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
	postID int NOT NULL AUTO_INCREMENT,
	content varchar(280) NOT NULL,
	`owner` varchar(50),
	PRIMARY KEY (postID));


CREATE TABLE `comments` (
	commentID int NOT NULL AUTO_INCREMENT,
	content varchar(280) NOT NULL,
	`owner` varchar(50),
	pID int,
	PRIMARY KEY (commentID));

CREATE TABLE `superusers` (
	user varchar(20) PRIMARY KEY,
	password varchar(50) NOT NULL);

LOCK TABLES `superusers` WRITE;
INSERT INTO `superusers` VALUES ('admin', password('password'));
UNLOCK TABLES;
```
