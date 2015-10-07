<?php // connection string
$dbconn = mysql_connect($dbhost,$dbuser,$dbpass) or die("Connection error: " . mysql_error());
$dbname = 'dbblog';
mysql_select_db($dbname);
?>
