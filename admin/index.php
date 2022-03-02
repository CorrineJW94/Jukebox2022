<?php
//configuration
include_once '../config/config.php';
include_once BASEPATH.'jukebox/Net/MPD.php';
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
/*$client_ip=getIP();
if ($client_ip!='192.168.1.254') {header('Location: /mobile/'); exit; }*/
/********** */

if(!$security->isLoggedIn()){
    header("Location:login.php");
    exit();
}

$mpd = new Net_MPD('localhost', '3306');
$mpdCommon = $mpd->factory("Common");
$mpdCommon->connect();

$mpdDatabase = $mpd->factory("Database");

$mpdPlaylist = $mpd->factory("Playlist");

$mpdPlayback = $mpd->factory("Playback");
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
    <script>
        $(window).load(function(){
            var gbPlaylistAction = setInterval(function(){
                $.get('/jukebox/listener/playbackListener.php', function (response){
                    if(response.current_playlist == 'admin'){
                        response.current_playlist = 'general';
                    }
                   $('.playerManagerCurrentSong span').html(response.song_order+' - '+response.song_title+' ('+response.current_playlist+' playlist)'); 
                }, 'json');
            },1000);
        });
    </script>
</head>
<body>
    <div class="header">
        <div class="wrapper">
            <div class="logo"><img src="images/logo@2x.png" /></div>
            <ul class="">
                <?php if($_SESSION['loggedAs'] == 'master'){ ?>
                    <li><a href="/jukebox/admin/database.php"><div class="icon icon_music"></div><div class="text">Music Database</div></a></li>
                    <li><a href="/jukebox/admin/managePlaylist.php"><div class="icon icon_cog"></div><div class="text">Bar Music</div></a></li>
                    <li><a href="/jukebox/admin/availableSongs.php"><div class="icon icon_cog"></div><div class="text">Customer Music</div></a></li>
                    <li><a href="/jukebox/admin/playback.php"><div class="icon icon_play"></div><div class="text">Track Status</div></a></li>
                    <li><a href="/jukebox/admin/suggestions.php"><div class="icon icon_user"></div><div class="text">Suggestions</div></a></li>
                    <li><a href="/jukebox/admin/credits.php"><div class="icon icon_cd"></div><div class="text">Credits</div></a></li>
                    <li><a href="/jukebox/admin/menu.php"><div class="icon icon_menu"></div><div class="text">Menu</div></a></li>
                    <li><a href="/jukebox/admin/swearFilter.php"><div class="icon icon_tag"></div><div class="text">Swear Filter</div></a></li>
                    <li class="last"><a href="/jukebox/admin/login.php?logout"><div class="icon icon_eject"></div><div class="text">Logout</div></a></li>
                <?php } ?>

                <?php if($_SESSION['loggedAs'] == 'admin'){ ?>
                    <li><a href="/jukebox/admin/managePlaylist.php"><div class="icon icon_cog"></div><div class="text">Bar Music</div></a></li>
                    <li><a href="/jukebox/admin/availableSongs.php"><div class="icon icon_cog"></div><div class="text">Customer Music</div></a></li>
                    <li><a href="/jukebox/admin/suggestions.php"><div class="icon icon_user"></div><div class="text">Suggestions</div></a></li>
                    <li class="last"><a href="/jukebox/admin/login.php?logout"><div class="icon icon_eject"></div><div class="text">Logout</div></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>

	<div class="wrapper">
        <div class="welcome_box">
        	<h1>Welcome</h1>
        	<p>Please select a menu item</p>
        	<span class="icon icon_arrow-up"></span>
        </div>
</div>	

</body>