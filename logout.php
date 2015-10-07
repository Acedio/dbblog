<?php
	session_start();
	session_unset(); // clear session so they have to log in again
	$_SESSION = array();
	header('Location: ' . $_SERVER['HTTP_REFERER']);
	exit;
?>
