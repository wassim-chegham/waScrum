<?php
	
	require_once('includes/global.fn.php');
	require_once('includes/Session.class.php');

	if ( $session->ok() ) redirect( 'index.php' );

	$_SESSION['error'] = "";
	if ( isset($_POST['submit-login']) )
	{
		
		extract($_POST);
		
		if ( ! $session->login( $login, $password ) )
		{
			$_SESSION['error'] = "Wrong credentials!";
		}
		else 
		{
		  $ln = isset($_COOKIE['cookie_name']['lang']) ? $_COOKIE['cookie_name']['lang'] : false;	
		  $ln = $ln != false ? "?".$ln : '';
		  
		  redirect( 'index.php'.$ln ); // redirect with the correct i8ln
		}
		
	}

?>
<!DOCTYPE html> 
<html lang="en"> 
	<head> 
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
		<title>waScrum v0.1-alpha</title> 
		<link rel="stylesheet" type="text/css" href="assets/css/wascrum.css" media="screen" /> 
	</head> 
		<body>
			<form id="login" method="post" action=""> 
				<h1>Login to waScrum</h1> 
				<?php if ( $_SESSION['error'] != "" ) echo '<p>' . $_SESSION['error'] . '</p>'; ?>
				<p> 
					<label for="login">Login</label> 
					<input id="login" name="login" type="text" /> 
				</p> 
				<p> 
					<label for="password">Password</label> 
					<input id="password" name="password" type="password" /> 
				</p>
				<p>
					<input type="submit" name="submit-login" value="login" id="submit-login"  />
					<p>
            <b><u>Demo account</u> : </b><i>demo</i> / <i>demo</i>
					</p>
				</p>
		</form> 
	</body> 
	
</html>
