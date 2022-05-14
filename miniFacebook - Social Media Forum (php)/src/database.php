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
