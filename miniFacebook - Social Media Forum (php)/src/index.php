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


