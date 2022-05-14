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


