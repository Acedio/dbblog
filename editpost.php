<?php
	session_start();

	include 'clean.php';

	$message = '';

	include 'db_config.php';
	include 'db_connect.php';

	$post = 0;
	if(is_numeric($_GET['post'])){ // make sure we aren't being bad people
		$post = $_GET['post'];
	} else {
		$message = 'That is not a valid post!';
	}

	if($post != 0 && isset($_POST['title']) && $_POST['title'] != ''){
		if($_SESSION['logged_in'] === true && $_SESSION['rights'] === 'admin'){
			if($_POST['delete'] === false){
				$query = sprintf('UPDATE post SET title="%s", subtitle="%s", body="%s" WHERE pid=%s',
					clean_input($_POST['title']),
					clean_input($_POST['subtitle']),
					clean_input($_POST['text']),
					$post);
				if(!mysql_query($query,$dbconn))
				{
					die('Error: ' . mysql_error());
				} else {
					$message = 'Edited "' . $_POST['title'] . '".'; // if you see this, the next line didn't work ;D
					header('Location: post.php?post=' . $post); // go back to the post
				}
			} else {
					$query = sprintf('DELETE FROM comment WHERE parent_post = %s', $post); // must delete comments first because of foreign key to post
					if(mysql_query($query, $dbconn)){
						$query = sprintf('DELETE FROM post WHERE pid = %s', $post);
						if(mysql_query($query, $dbconn)){
							header('Location: index.php');
						} else {
							$message = 'Error deleting post.';
						}
					} else {
						$message = 'Error deleting post comments.';
					}
			}
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
		echo '<p>Please <a href="#login">login</a> before editing.</p><br/>' . PHP_EOL;
	} else if(!isset($_SESSION['rights']) || $_SESSION['rights'] === 'user'){
		echo '<p>You do not have sufficient privileges edit a post.</p><br/>' . PHP_EOL;
	}

	if($message != ''){
		echo '<p>' . $message . '</p>' . PHP_EOL;
	}
	$query = 'SELECT title, subtitle, body FROM post WHERE pid = ' . $post;
	$result = mysql_query($query,$dbconn);
	if($_SESSION['rights'] === 'admin' && $post != 0 && mysql_num_rows($result) > 0){ // make sure that the post exists
		if($row = mysql_fetch_array($result,MYSQL_ASSOC)){
			$title = htmlspecialchars($row['title']);
			$subtitle = htmlspecialchars($row['subtitle']);
			$body = htmlspecialchars($row['body']);
			// fill in all the necessary data for editing
			echo '<form action="editpost.php?post=' . $post . '" method="post"><p>
Title:<br/><input type="text" name="title" value="' . $title . '"/><br/>
Subtitle:<br/><input type="text" name="subtitle" value="' . $subtitle . '"/><br/>
Text:<br/><textarea cols="40" rows="10" name="text">' . $body . '</textarea><br/>
<input type="checkbox" name="delete" id="checkbox"/>Delete?<br/>
<div class="submit"><input type="submit" value="Save!"/></div>
</p></form>' . PHP_EOL;
		}
	} else {
		echo '<h2>404\'d</h2>' . PHP_EOL;
		echo '<p>Oooh, you really screwed it up now. There\'s no post with that ID!' . PHP_EOL;
	}
	include 'db_close.php';
?>
</div>
</div>
<div id="navi">
<?php include 'navi.php'; ?>
</div>
</div>
</body>
</html>
