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

//echo '<pre>';
//print_r($mpdDatabase->getAllInfo());

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
    
    <div class="wrapper feature_button_group">
    <div class="button">
            <a href="/jukebox/admin/index.php">
                <div class="icon icon_arrow-left"></div>
                <span class="text">Back</span>
            </a>
        </div>

<?php
    if(isset($_GET['status']) && $_GET['status'] == 'updateMPD'){
        $mpdCommon->runCommand('update');
        echo '<div class="wrapper"><div class="status">Synchronised Music Folders with MPD <div class="icon icon_checkmark"></div></div></div>';
    }

    if(isset($_GET['status']) && $_GET['status'] == 'update'){
        //set time limit to infinite for mysql exec
        set_time_limit(0);
        
        $mpd_stored_data = $mpdDatabase->getAllInfo();
        foreach ($mpd_stored_data['file'] as $k => $v) {
            $insert = array();
            if(isset($v['Title'])){
                $title = $v['Title'];
            }else{
                $info = pathinfo($v['file']);
                $title = $info['filename'];
            }

            $artist = (isset($v['Artist'])) ? $v['Artist'] : 'Undefined';
            $album = (isset($v['Album'])) ? $v['Album'] : 'Undefined';
            $genre = (isset($v['Genre'])) ? $v['Genre'] : 'Undefined';
            $date = (isset($v['Date'])) ? $v['Date'] : 'Undefined';

            $data['title'] = addslashes(($title));
            $data['artist'] = addslashes(($artist));
            $data['album'] = addslashes(($album));
            $data['genre'] = addslashes(($genre));
            $data['file'] = addslashes(($v['file']));
            $data['year'] = addslashes(($date));
            $data['track_time'] = $v['Time'];
            
            $db->databaseUpdateSongsData($data);
        }
        $db->sanitiseSongsList();
        
        echo '<div class="wrapper"><div class="status">Database updated <div class="icon icon_checkmark"></div></div></div>';
    }

    if(! isset($_GET['status']) ) {
        echo '<div class="wrapper"><div class="status"><p>Follow the steps to update the jukebox with new songs</p>
        Step 3 allows the editing of song/artist/album names</div></div>'; 
    }
?>

        <div class="feature_button">
            <a href="/jukebox/admin/database.php?status=updateMPD">
                <div class="icon icon_spinner"></div>
                <span class="text"><p>step 1</p>Syncronise Music Folders</span>
            </a>
        </div><div class="feature_button">
            <a href="/jukebox/admin/database.php?status=update">
                <div class="icon icon_arrow-up"></div>
                <span class="text"><p>step 2</p>Update Music Database</span>
            </a>
        </div><div class="feature_button">
            <a href="/jukebox/admin/manageDatabase.php">
                <div class="icon icon_music"></div>
                <span class="text"><p>step 3 (optional)</p>Manage Music Database</span>
            </a>
        </div>
    </div>

</body>