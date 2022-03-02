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

if(isset($_GET['word']) && isset($_GET['add'])){
    $response = $db->addSwearWord($_POST['word']);
    if($response['status'] == 'success'){
        unset($_POST);
    }
    $word_create_msg = $response['msg'];
}

if(isset($_GET['remove'])){
    $db->query("DELETE FROM `swear_filter_words` WHERE id = ".$_GET['remove']);
    $word_remove_msg = 'Selected word has been removed.';
}

if(isset($_GET['word']) && isset($_GET['search'])){
    $swear_words = $db->getSwearWords($_POST['search_word']);
}else{
    $swear_words = $db->getSwearWords();
}


if(isset($_GET['replacement']) && isset($_GET['add'])){
    $response = $db->addSwearRepalcement($_POST['replacement']);
    if($response['status'] == 'success'){
        unset($_POST);
    }
    $replacement_create_msg = $response['msg'];
}

if(isset($_GET['removeReplacement'])){
    $db->query("DELETE FROM `swear_filter_replacement` WHERE id = ".$_GET['removeReplacement']);
    $replacement_remove_msg = 'Selected word has been removed.';
}

if(isset($_GET['replacement']) && isset($_GET['search'])){
    $swear_replacements = $db->getSwearReplacements($_POST['search_replacement']);
}else{
    $swear_replacements = $db->getSwearReplacements();
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
    </div>
    
    <div class="wrapper">
        <div class="status">
            <p>Add words that you want to filter out from the TV display</p>
            Add replacement words that the system can use as a replacement for unwanted words
        </div>
    </div>

    <div class="wrapper fields_wrapper">

        <div class="playlist_creator">
            <div class="playlist no_drag" id="songs_content">
                <p class="subtitle">Unwanted Words</p>
                    <p>Add new word:</p>
                    <div>
                        <form action="/jukebox/admin/swearFilter.php?word&add" method="POST">
                            <input type="text"  class="text" name="word" value="<?php echo (isset($_POST['word'])) ? $_POST['word'] : ''; ?>" placeholder="Add new unwanted word" /> <input class="button" type="submit" value="Create" name="add_word" />
                        </form>
                        <?php if(isset($word_create_msg)){ ?>
                        <p><?php echo $word_create_msg; ?></p>
                        <?php } ?>
                    </div>
                    <p>Search for words:</p>
                    <div>
                        <form action="/jukebox/admin/swearFilter.php?word&search" method="POST">
                            <input type="text" class="text" name="search_word" value="<?php echo (isset($_POST['search_word'])) ? $_POST['search_word'] : ''; ?>" placeholder="filter unwanted word" /> <input class="button" type="submit" value="Search" name="search_word_submit" />
                        </form>
                        <?php if(isset($word_search_msg)){ ?>
                        <p><?php echo $word_search_msg; ?></p>
                        <?php } ?>
                    </div>

                <?php if(isset($word_remove_msg)){ ?>
                <p><?php echo $word_remove_msg; ?></p>
                <?php } ?>
                <br>
                <?php if(count($swear_words) > 0 ){ ?>
                <ul>
                    <?php $i = 1; foreach ($swear_words as $k => $v) { ?>
                    <li>
                        <div class="song_title"><?php echo stripslashes($v['word']); ?></div>
                        <a class="button red icon icon_remove" href="/jukebox/admin/swearFilter.php?remove=<?php echo $v['id']; ?>">Remove</a>
                    </li>
                    <?php $i++; } ?>
                </ul>
                <?php }else{ ?>
                <p>No words found.</p>
                <?php } ?>
            </div><div class="playlist no_drag" id="playlist_content">
            <p class="subtitle">Replacement Words</p>
                <p>Add New Replacement:</p>
                <div>
                    <form action="/jukebox/admin/swearFilter.php?replacement&add" method="POST">
                        <input type="text" class="text" name="replacement" value="<?php echo (isset($_POST['replacement'])) ? $_POST['replacement'] : ''; ?>" placeholder="Add new replacement" /> <input class="button" type="submit" value="Create" name="add_replecement" />
                    </form>
                    <?php if(isset($replacement_create_msg)){ ?>
                    <p><?php echo $replacement_create_msg; ?></p>
                    <?php } ?>
                </div>
                
                <p>Filter Replacement Words:</p>
                <div>
                    <form action="/jukebox/admin/swearFilter.php?replacement&search" method="POST">
                        <input type="text" class="text" name="search_replacement" value="<?php echo (isset($_POST['search_replacement'])) ? $_POST['search_replacement'] : ''; ?>" placeholder="filter replacement words" /> <input class="button" type="submit" value="Search" name="search_replacement_submit" />
                    </form>
                    <?php if(isset($repalcement_search_msg)){ ?>
                    <p><?php echo $repalcement_search_msg; ?></p>
                    <?php } ?>
                </div>
                
                <?php if(isset($replacement_remove_msg)){ ?>
                <p><?php echo $replacement_remove_msg; ?></p>
                <?php } ?>
                <br>
                <?php if(count($swear_replacements) > 0 ){ ?>
                <ul>
                    <?php $i = 1; foreach ($swear_replacements as $k => $v) { ?>
                        <li><div class="song_title"><?php echo stripslashes($v['replacement']); ?></div>
                        <a class="button red icon icon_remove" href="/jukebox/admin/swearFilter.php?removeReplacement=<?php echo $v['id']; ?>">Remove</a>
                        </li>
                    <?php $i++; } ?>
                    </ul>
                <?php }else{ ?>
                <p>No replacements found.</p>
                <?php } ?>
            </div>
            <div class="divider"></div>
        </div>
    </div>
</body>
</html>