<?php

	require_once('includes/global.fn.php');
	require_once('includes/Session.class.php');
	
	$session->logout();
	redirect('do.login.php');

?>
