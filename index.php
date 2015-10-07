<?php
	// db_blog
	// Josh Simmons
	// index.php: start page, shows posts by date
	session_start();
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
	include 'db_config.php';
	include 'db_connect.php';

	$postsPerPage = 7;

	$query = 'SELECT COUNT(pid) AS num_rows FROM post'; // determine how many posts/pages we have
	$result = mysql_query($query);
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$num_posts = $row['num_rows'];
	$num_pages = ceil($num_posts/$postsPerPage);

	if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 && $_GET['page'] < $num_pages){ // make sure users are asking for pages that exist
		$page = $_GET['page'];
	} else {
		$page = 0;
	}
	// get all posts, author display names, and comment counts for current page
	$query = 'SELECT pid, title, subtitle, body, posted_at, display_name, comment_count
FROM post LEFT OUTER JOIN 
( SELECT parent_post, COUNT( cid ) AS comment_count FROM comment GROUP BY parent_post) AS comment_counts 
ON pid = parent_post,user 
WHERE post.author = user.uid 
ORDER BY posted_at 
DESC LIMIT ' . $postsPerPage * $page . ',' . $postsPerPage;
	$result = mysql_query($query);

	// main post loop
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		echo '<div class="post">' . PHP_EOL;
		echo '<h2><a title="' . $row['subtitle'] . '" href="post.php?post=' . $row['pid'] . '">' . 
			$row['title'] . '</a></h2>' . PHP_EOL;
		echo '<h4>Posted by <b>' . $row['display_name'] . '</b> on ' . date('l, F jS Y', strtotime($row['posted_at'])) . 
			' at ' . date('g:i A', strtotime($row['posted_at'])) . '.';
		if($_SESSION['rights'] === 'admin'){
			echo ' <a href="editpost.php?post=' . $row['pid'] . '">Edit</a>';
		}
		echo '</h4>' . PHP_EOL;
		echo '<p>' . nl2br($row['body']) . '</p>' . PHP_EOL;
		$ccount = $row['comment_count'];
		echo '<a href="post.php?post=' . $row['pid'] . 
		'#comments"><h4 class="ccount">Comments (' . ($ccount?$ccount:0) . ')</h4></a>' . PHP_EOL;
		echo '</div>' . PHP_EOL;
	}
	include 'db_close.php';
?>
<div id="paging">
<?php
	if($page > 0){
		echo '<a href="index.php?page=' . ($page - 1) . '">&laquo; Prev</a>' . PHP_EOL;
	}
	if($page < $num_pages - 1){
		echo '<a href="index.php?page=' . ($page + 1) . '">Next &raquo;</a>' . PHP_EOL;
	}
?>
</div>
</div>
<div id="navi">
<?php include 'navi.php'; ?>
</div>
<br clear="all"/>
</div>
</body>
</html>
