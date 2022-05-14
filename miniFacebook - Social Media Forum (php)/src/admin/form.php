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

