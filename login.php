<?php // authentication
	session_start();
	include 'clean.php';
	$errorMessage = '';
	if(isset($_POST['username'])){
		include 'db_config.php';
		include 'db_connect.php';
		$query= sprintf('SELECT * FROM user WHERE username = "%s" AND password = "%s"', 
			clean_input($_POST['username']), // no injection!
			clean_input($_POST['password']));
		if($result = mysql_query($query,$dbconn)){
			if($row = mysql_fetch_array($result, MYSQL_ASSOC)){ // if we get a row back, it's legit
				$_SESSION['logged_in'] = true;
				$_SESSION['username'] = $row['username'];
				$_SESSION['rights'] = $row['rights'];
				header('Location: ' . $_SERVER['HTTP_REFERER']);
				exit;
			} else {
				$errorMessage = 'Wrong username or password!';
			}
		} else {
			die('Error: ' . mysql_error());
		}
		include 'db_close.php';
	}
?>
<html>
<head>
<title>dbblog</title>
</head>
<body>
<?php
	if($errorMessage != ''){
		echo $errorMessage . '<br>';
	}
?>
<form action="login.php" method="post">
Username:<br/><input type="text" name="username"/><br/>
Password:<br/><input type="password" name="password"/><br/>
<input type="submit" value="Login"/>
</form>
</body>
</html>
