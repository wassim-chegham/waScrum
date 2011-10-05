<?php 

$config = array();
$config['title'] = 'waScrum';
$config['available_lang'] = array('en'=>'english', 'fr'=>'fran&ccedil;ais', 'es'=>'español', 'de'=>'deutsch');
$config['default_lang'] = 'en';
$config['cookie_expiration'] = strtotime('now + 6 months');
$config['cookie_name'] = "wascrum";
$config['users_file'] = 'users/users.txt';


//
$GLOBALS['config'] = $config;

?>