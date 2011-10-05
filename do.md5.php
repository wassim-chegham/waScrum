<?php
	
	$s = isset($_GET['s']) && !empty($_GET['s']) ? $_GET['s'] : false;
	
	if ( $s != false )
	{
		header('Content-Type: text/javascript');
		echo json_encode(array('string'=>$s, 'md5'=>md5($s)));
	}

?>