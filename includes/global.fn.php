<?php

if( ! function_exists('redirect') )
{
	function redirect( $str )
	{
		header( 'location: ' . $str );
	}
}

if( ! function_exists('fibonacci') )
{
	function fibonacci($n=5)
	{
		for( $l = array(1,2), $i = 2, $x = 0; $i < $n; $i++ )
		{	
		    $l[] = $l[$x++] + $l[$x];
		}              
		return $l;
	}	
}

?>
