<?php 
  
  require_once('config/config.php');
  require_once('includes/global.fn.php');
  require_once('includes/Session.class.php');

  $ln = $config['default_lang'];
  
  if ( !empty($_GET) )
  {
	  
	  foreach( array_keys($config['available_lang']) as $l ) 
	  {
	    if ( isset($_GET[ $l ]) )
	    {
	      $ln = $l;
	      break;
	    }
	      
	  }

  }

  // set the right i8ln
  require_once('lang/'.$ln.'_lang.php');
  setcookie($config['cookie_name'].'[lang]', $ln, $config['cookie_expiration']);
  

?>