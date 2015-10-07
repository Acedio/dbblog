<?php
	session_start();

	include 'clean.php';

	$message = '';

	if(isset($_POST['title']) && $_POST['title'] != ''){ // did they write a title (all that's required)
		if($_SESSION['logged_in'] === true && $_SESSION['rights'] === 'admin'){
			include 'db_config.php';
			include 'db_connect.php';
			$query = sprintf('INSERT INTO post (title,subtitle,body,author) VALUES ("%s", "%s", "%s",
				(SELECT uid FROM user WHERE username = "%s"))',
				clean_input($_POST['title']), // even though their admins we want to clean input
				clean_input($_POST['subtitle']),
				clean_input($_POST['text']),
				clean_input($_SESSION['username']));
			if(!mysql_query($query,$dbconn))
			{
				die('Error: ' . mysql_error());
			} else {
				$message = 'Posted "' . $_POST['title'] . '".';
				header('Location: index.php');
			}
			include 'db_close.php';
		}
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
	if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true){
		echo '<p>Please <a href="#login">login</a> before posting.</p><br/>' . PHP_EOL;
	} else if(!isset($_SESSION['rights']) || $_SESSION['rights'] === 'user'){
		echo '<p>You do not have sufficient privileges to post.</p><br/>' . PHP_EOL;
	}

	if($message != ''){
		echo '<p>' . $message . '</p>';
	}
	if($_SESSION['rights'] === 'admin'){
		echo '<form action="newpost.php" method="post"><p>
Title:<br/><input type="text" name="title"/><br/>
Subtitle:<br/><input type="text" name="subtitle"/><br/>
Text:<br/><textarea cols="40" rows="10" name="text"></textarea><br/>
<div class="submit"><input type="submit" value="Post!"/></div>
</p></form>';
	}
?>
</div>
</div>
<div id="navi">
<?php include 'navi.php'; ?>
</div>
</div>
</body>
</html>
