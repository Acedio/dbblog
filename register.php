<?php
	session_start();

	include 'clean.php';

	$message = '';

	// all fields required
	if(isset($_POST['username']) && $_POST['username'] != '' &&
		isset($_POST['password']) && $_POST['password'] != '' &&
		isset($_POST['password_check']) && $_POST['password_check'] != '' &&
		isset($_POST['display_name']) && $_POST['display_name'] != '' &&
		isset($_POST['email']) && $_POST['email'] != ''){
		if($_POST['password'] != $_POST['password_check']){
			$message = 'Entered passwords don\'t match!';
		} else {
			include 'db_config.php';
			include 'db_connect.php';
			$query= sprintf('INSERT INTO user (username,password,display_name,email,rights) VALUES ("%s", "%s", "%s", "%s", "user")',
				clean_input($_POST['username']),
				clean_input($_POST['password']),
				clean_input($_POST['display_name']),
				clean_input($_POST['email']));
			if(!($result = mysql_query($query,$dbconn)))
			{
				$errno = mysql_errno();
				switch($errno){
					case 1061:
					case 1062:
						$message = 'There is already someone with that username!' . PHP_EOL;
						break;
					default:
						$message = 'There was an error (And it\'s name was ' . $errno . '). Please contact Josh for a fix.' . PHP_EOL; // there are probably things that could go wrong her that I haven't encountered.
						break;
				}
			} else {
				$message = 'Posted "' . $_POST['title'] . '".';
				header('Location: index.php');
			}
			include 'db_close.php';
		}
	} else {
		$message = 'All fields are required!' . PHP_EOL;
	}
?>
<html>
<head>
<title>dbblog</title>
<link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
<div id="frame">
<div id="header">
<pre><a href="index.php">    __  __             __     __               
.--|  ||  |--.        |  |--.|  |.-----..-----.
|  _  ||  _  |        |  _  ||  ||  _  ||  _  |
|_____||_____| ______ |_____||__||_____||___  |
              |______|                  |_____|</a></pre>
</div>
<div id="content">
<div class="post">
<?php
	if($message != ''){
		echo '<p>' . $message . '</p>' . PHP_EOL;
	}
?>
<form action="register.php" method="post"><p>
Username:<br/><input type="text" name="username"/><br/>
Password:<br/><input type="password" name="password"/><br/>
Password (again):<br/><input type="password" name="password_check"/><br/>
Display Name:<br/><input type="text" name="display_name"/><br/>
Email:<br/><input type="text" name="email"/><br/>
<div class="submit"><input type="submit" value="Submit"/></div>
</p></form>
</div>
</div>
<div id="navi">
<?php include 'navi.php'; ?>
</div>
</div>
</body>
</html>
