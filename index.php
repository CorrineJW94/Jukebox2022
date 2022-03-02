<?php
//configuration
include_once 'jukebox/config/config.php';
include_once BASEPATH.'jukebox/lib/CommonClass.php';

$db = new Common(DB_USER, DB_PASS, DB_NAME, DB_HOST);
if( ! $db->is_connected()){
    $db->connect();
}

function getIP() { 
	$ip; 
	if (getenv("HTTP_CLIENT_IP")) 
	$ip = getenv("HTTP_CLIENT_IP"); 
	else if(getenv("HTTP_X_FORWARDED_FOR")) 
	$ip = getenv("HTTP_X_FORWARDED_FOR"); 
	else if(getenv("REMOTE_ADDR")) 
	$ip = getenv("REMOTE_ADDR"); 
	else 
	$ip = "UNKNOWN";
	return $ip; 
} 

$client_ip=getIP();

/*if ($client_ip=='192.168.1.254') {
	header('Location: /admin/');
	exit;
}else {*/
    if(SJ_DEBUG)
        header('Location: /jukebox/mobile/');
    else
        header('Location: http://'.UI_DOMAIN.'/jukebox/mobile/');
	exit;
//}

?>

