<?php
if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true){
	$logged_in = true;
	echo '<h4>Logged in as <b>' . $_SESSION[username] . '</b>.</h4><h4><a href="logout.php">Log off?</a></h4>' . PHP_EOL;
} else {
	echo '<a name="login"></a>';
	echo '<form action="login.php" method="post">
<h4>Username:</h4><input type="text" size="10" name="username"/><br/>
<h4>Password:</h4><input type="password" size="10" name="password"/><br/>
<div class="submit"><input type="submit" value="Login"/></div>
</form>' . PHP_EOL;
}
echo '<ul>
<li>Navi</li>
<li><ul>
<li><a href="index.php">Home</a></li>
</ul></li>' . PHP_EOL;
if($logged_in){ // must be logged in (and admin) to see new post option
	if($_SESSION['rights'] === 'admin'){
		echo '<li>Admin</li>
<li><ul>' . PHP_EOL;
		echo '<li><a href="newpost.php">New Post</a></li>' . PHP_EOL;
		echo '</ul></li>' . PHP_EOL;
	}
} else { // otherwise you may register
	echo '<li>Admin</li>
<li><ul>' . PHP_EOL;
	echo '<li><a href="register.php">Register</a></li>' . PHP_EOL;
	echo '</ul></li>' . PHP_EOL;
}
echo '</ul>' . PHP_EOL;
echo '' . PHP_EOL; 
?>
