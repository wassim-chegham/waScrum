<?php

	require_once('includes/Session.class.php');
	require_once('includes/global.fn.php');

	header('Content-Type: text/javascript');

	if( ! $session->ok() )
	{
		$json['response'] = false;
		$json['message'] = 'Board NOT restored! (Your session has timed out! You need to login again!)';
	}
	else {
		$board = $session->restore(true);
		if ( $board != false )
		{
			$json['response'] = true;
			$json['board'] = json_decode($board);
		}
		else {
			$json['response'] = false;
		}
	}
	
	echo json_encode($json);
	exit();
	
?>
