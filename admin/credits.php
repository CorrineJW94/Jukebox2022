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

$settings = $db->getCreditSettings();

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $min_val = $_POST['min'];
    $per_val = $_POST['per'];
    $max_val = $_POST['max'];
    $credits = $_POST['credits'];

    $db->updateCreditSettings($min_val, $max_val, $per_val, $credits);
} else {
    $min_val = $settings['min'];
    $per_val = $settings['per'];
    $max_val = $settings['max'];
    $credits = $settings['credits'];
}

$user_playlist = $db->getUserPlaylist();
$user_playlist_length = count($user_playlist);

echo '<pre>';
//print_r($_SESSION);
echo '</pre>';
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
        var current_length = <?php echo $user_playlist_length; ?>;
        function calculateExample(changed) {
            if(changed) {
                $('.cred_changed').show();
            }
            var min = $('.cred_val_min')[0].value;
            var per = $('.cred_val_track')[0].value;
            var max = $('.cred_val_max')[0].value;

            var e20 = Math.ceil((20 * per) < min ? min : ((20 * per) > max ? max : (20 * per)));
            var e100 = Math.ceil((100 * per) < min ? min : ((100 * per) > max ? max : (100 * per)));
            var e500 = Math.ceil((500 * per) < min ? min : ((500 * per) > max ? max : (500 * per)));
            var ecur = Math.ceil((current_length * per) < min ? min : ((current_length * per) > max ? max : (current_length * per)));

            $('.ex_20').text(e20);
            $('.ex_100').text(e100);
            $('.ex_500').text(e500);
            $('.cred_cur').text(ecur);
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

        <div class="wrapper credits">
            <p class="cred_explain">
                This is where you can tweak the algorithm for how long playback credits take to timeout.
            </p>
            <div class="cred_setup">
                <form method="POST">
                    Minimum Credit Timeout (Minutes) <input type="number" value="<?php echo $min_val; ?>" class="cred_val_min"  name="min" step="1" min="1" onchange="calculateExample(true);" onmouseup="calculateExample()" onkeyup="calculateExample();"/>
                    Minutes Per Track Credit Timeout (Minutes) <input type="number" value="<?php echo $per_val; ?>" class="cred_val_track" name="per" step="0.25" min="0.25" onchange="calculateExample(true);" onmouseup="calculateExample()" onkeyup="calculateExample();"/>
                    Maximum Credit Timeout (Minutes) <input type="number" value="<?php echo $max_val; ?>" class="cred_val_max" step="1" name="max" min="5" onchange="calculateExample(true);" onmouseup="calculateExample()" onkeyup="calculateExample();"><br>
                    <br>
                    Maximum Number of Credits Per User <input type="number" value="<?php echo $credits; ?>" step="1" min="1" max="10" name="credits"><br>
                    <input class="button" type="submit" value="Save" name="save">
                </form>
            </div>
            <div class="credit_example">
                <h3>Examples:</h3>
                When Playlist Length is <strong>20</strong>, the Credit timeout is <span class="cred_ex ex_20">...</span> Minutes<br>
                When Playlist Length is <strong>100</strong>, the Credit timeout is <span class="cred_ex ex_100">...</span> Minutes<br>
                When Playlist Length is <strong>500</strong>, the Credit timeout is <span class="cred_ex ex_500">...</span> Minutes<br>
                <br>
                Current Playlist Length is <strong><?php echo $user_playlist_length; ?></strong>, the Credit timeout is <span class="cred_ex cred_cur">...</span> Minutes <span style="display: none;" class="cred_changed">(with new values)</span>
            </div>
        </div>
        <script>
            calculateExample();
        </script>
    </body>
</html>