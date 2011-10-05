<?php
	
	require_once('includes/Session.class.php');
	require_once('includes/global.fn.php');
	
	header('Content-Type: text/javascript');
	
	if( ! $session->ok() )
	{
		$json['response'] = false;
		$json['message'] = 'Board NOT saved! (Your session has timed out! You need to login again!)';
	}
	else {
		
		$board = isset($_POST['board']) && !empty($_POST['board']) ? $_POST['board'] : false;
		
		if ( $board != false )
		{
			$board = json_encode(json_decode(stripslashes($_POST['board']), true));
			
			if ( $session->save($board) != false )
			{
				$json['response'] = true;
			}
			else {
				$json['response'] = false;
			}
		}
	
	}
	
	echo json_encode($json);
	exit();
	
?>