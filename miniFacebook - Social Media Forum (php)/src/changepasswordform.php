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

