<?php 
	session_start();
	if(isset($_SESSION['login_true']) && !empty($_SESSION['login_true']))
	{
		session_unset();
		session_destroy();
		header("Location: login_form.php");
	}
	else
	{
		header("Location: login_form.php");
	}
?>