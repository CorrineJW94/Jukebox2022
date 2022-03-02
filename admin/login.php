<?php
//configuration
include_once '../config/config.php';
include_once BASEPATH.'jukebox/lib/SecurityClass.php';
$security = new Security(DB_USER, DB_PASS, DB_NAME, DB_HOST);

/********** */
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
/*if ($client_ip!='192.168.1.254') {header('Location: /mobile/'); exit; }*/
/********** */


if(isset($_GET['logout'])){
    $security->logout();
}

if($security->isLoggedIn()){
    header("Location:index.php");
    exit();
}
$error = '';
if(isset($_POST['login'])){
    $result = $security->loginUser($_POST['username'], $_POST['password']);
    if($result['status'] == 'error'){
        $error = '<p>'.$result['msg'].'</p>';
    }
    
    if($result['status'] == 'success'){
        header("Location:index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, user-scalable=no">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <script type="text/javascript" src="js/libs.js"></script>
    <script type="text/javascript" src="js/main.js"></script> 
    <title>ADMIN - Shufflebar</title>
</head>
<body>
	<div class="header">
		<div class="wrapper">
			<div class="logo"><img src="images/logo@2x.png" /></div>
		</div>
	</div>

	<div class="wrapper login fields_wrapper">
       
            <div class="login_box"> 
            <p class="title">Login</p>
            <?php echo $error; ?>
            <div style="text-align:left;" >
            <form action="" method="post" onsubmit="">
                        <input type="text" class="text" name="username" value="" placeholder="username"><br>

                        <input type="password" class="text" name="password" value="" placeholder="password">

                        <input class="button" type="submit" name="login" value="login">

                    </form>  
                </div>
            </div>
    </div>	

</body>