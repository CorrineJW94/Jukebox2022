<?php
//configuration
include_once '../config/config.php';
include_once BASEPATH.'jukebox/lib/CommonClass.php';
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
if(!$security->isLoggedIn()){
    header("Location:login.php");
    exit();
}

$db = new Common(DB_USER, DB_PASS, DB_NAME, DB_HOST);
if( ! $db->is_connected()){
    $db->connect();
}

$mpd = new Net_MPD('localhost', '6600');
$mpdCommon = $mpd->factory("Common");
$mpdDatabase = $mpd->factory("Database");

if( ! $mpdCommon->isConnected()){
    $mpdCommon->connect();
}

?>
<!DOCTYPE html>
<html lang="en">
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
        function deleteSugestion(id) {
          $.get('/jukebox/listener/suggestionListener.php', {action: 'removeSuggestion', id: id}, function (response){
            if(response.msg == 'success') {
              $('#'+id+'.user_suggestion').remove();
            }
          }, 'json');
        }
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
        
        <div class="wrapper feature_button_group">
        <div class="button">
                <a href="/jukebox/admin/index.php">
                    <div class="icon icon_arrow-left"></div>
                    <span class="text">Back</span>
                </a>
            </div>
        </div>

        <div class="wrapper suggestions">
            <table>
                <thead>
                <tr>
                    <th>Track</th>
                    <th>User</th>
                    <th></th>
                </tr>
                </thead>
            <?php foreach($db->listUserSuggestions() as $suggestion) { ?>
                <tr class="user_suggestion" id="<?php echo $suggestion['id']; ?>">
                    <td>
                        <div class="artist"><?php echo $suggestion['artist']; ?></div>
                        <div class="track"><?php echo $suggestion['track']; ?></div>
                    </td>
                    <td class="user"><?php echo $suggestion['user']; ?></td>
                    <td class="delete_col">
                        <div class="delete" onclick="deleteSugestion('<?php echo $suggestion['id']; ?>');">x</div>
                    </td>
                </tr>
            <?php } ?>
            </table>
        </div>
    </body>
</html>