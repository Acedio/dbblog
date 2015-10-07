<?php
	session_start();
	include 'clean.php';
	include 'db_config.php';
	include 'db_connect.php';
	if(isset($_POST['text']) && $_SESSION['logged_in'] === true){ // if a comment has been posted and we are logged in, post it
		$query = sprintf('INSERT INTO comment (parent_post,body,author) VALUES ("%s", "%s",
			(SELECT uid FROM user WHERE username = "%s"))',
			clean_input(htmlspecialchars($_GET['post'])), // no html allowed!
			clean_input(htmlspecialchars($_POST['text'])),
			clean_input($_SESSION['username']));
		if(!mysql_query($query,$dbconn))
		{
			die('Error: ' . mysql_error());
		}
		header("Location: #comments"); // go to the top of comments
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
<?php
	$query = 'SELECT pid,title,subtitle,body,posted_at,display_name FROM post, user WHERE post.author = user.uid AND post.pid = ' . clean_input($_GET['post']);
	$not_found = false;
	if(isset($_GET['post'])){
		if($result = mysql_query($query)){ // if post exists display it
			if($row = mysql_fetch_array($result, MYSQL_ASSOC)){
				echo '<div class="post">' . PHP_EOL;
				echo '<h2><a title="' . $row['subtitle'] . '" href="post.php?post=' . $row['pid'] . '">' . $row['title'] . '</a></h2>' . PHP_EOL;
				echo '<h4>Posted by <b>' . $row['display_name'] . '</b> on ' . date('l, F jS Y', strtotime($row['posted_at'])) . ' at ' . date('g:i A', strtotime($row['posted_at'])) . '.';
				if($_SESSION['rights'] === 'admin'){
					echo ' <a href="editpost.php?post=' . $row['pid'] . '">Edit</a>';
				}
				echo '</h4>' . PHP_EOL;
				echo '<p>' . nl2br($row['body']) . '</p>' . PHP_EOL;
				echo '</div>' . PHP_EOL;
			} else {
				$not_found = true;
			}
		} else {
			$not_found = true;
		}
	} else {
		$not_found = true;
	}
	if($not_found){
		echo '<div class="post">' . PHP_EOL;
		echo '<h2>404\'d</h2>' . PHP_EOL;
		echo '<p>Oooh, you really screwed it up now. There\'s no post with that ID!' . PHP_EOL;
		echo '</div>' . PHP_EOL;
	} else { // display comments and commend authors
		$query = 'SELECT U.display_name, C.date, C.body FROM comment AS C, user AS U WHERE C.author = U.uid AND C.parent_post = ' . clean_input($_GET['post']);
		$result = mysql_query($query);
		echo '<div id="comments">';
		echo '<h2>Comments</h2>' . PHP_EOL;
		echo '<a name="comments"></a>' . PHP_EOL;
		if(mysql_num_rows($result) > 0){
			while($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				echo '<div class="comment">' . PHP_EOL;
				echo '<h4>' . $row['display_name'] . ' said at ' . date('l, F jS g:i A', strtotime($row['date'])) . '</h4>' . PHP_EOL;
				echo '<p>' . nl2br($row['body']) . '</p>' . PHP_EOL;
				echo '</div>' . PHP_EOL;
			}
		} else {
			echo '<div class="comment"><p>No comments yet!</p></div>' . PHP_EOL;
		}
		echo '<div class="comment">' . PHP_EOL;
		if($_SESSION['logged_in'] === true){
			echo '<p>Leave a comment:</p>';
			echo '<form action="post.php?post=' . $_GET['post'] . '" method="post"><p>'. PHP_EOL;
			echo '<textarea cols="40" rows="10" name="text"></textarea>' . PHP_EOL;
			echo '<div class="submit"><input type="submit" value="Post!"/></div>' . PHP_EOL;
			echo '</p></form>' . PHP_EOL;
		} else {
			echo '<p>Please <a href="#login">login</a> to comment.</p>' . PHP_EOL;
		}
		echo '</div>';
		echo '</div>';
	}
	include 'db_close.php';
?>
</div>
<div id="navi">
<?php include 'navi.php'; ?>
</div>
</div>
</body>
</html>
