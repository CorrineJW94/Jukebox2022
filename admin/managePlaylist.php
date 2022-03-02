<?php
//configuration
include_once '../config/config.php';
include_once BASEPATH.'jukebox/lib/CommonClass.php';
include_once BASEPATH.'jukebox/lib/SecurityClass.php';
include_once BASEPATH.'jukebox/lib/ContextStorageClass.php';
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
$context = ContextStorage::getInstance();

if(isset($_POST['create_playlist'])){
    $response = $db->addPlaylist(addslashes($_POST['playlist']));
    if($response['status'] == 'success'){
        unset($_POST);
    }
    $playlist_create_msg = $response['msg'];
}

if(isset($_POST['update_playlist'])){
    $response = $db->updatePlaylist($_GET['detailsId'], addslashes($_POST['playlist']));
    if($response['status'] == 'success'){
        unset($_POST);
    }
    $playlist_update_msg = $response['msg'];
}

if(isset($_GET['importLibrary'])){
    $db->playlistImportLibrary($_GET['detailsId']);
}

if(isset($_GET['importLibrary'])){
    $db->playlistImportLibrary($_GET['detailsId']);
}

if(isset($_POST['year_import']) && isset($_POST['playlist_year']) && $_POST['playlist_year'] !== '0') {
    $db->playlistAddAllForYear($_GET['detailsId'], $_POST['playlist_year']);
}

if(isset($_GET['shuffleTracks'])){
    $db->playlistShuffleTracks($_GET['detailsId']);
}

if(isset($_GET['removeId'])){
    $response = $db->removePlaylist($_GET['removeId']);
    $playlist_create_msg = $response['msg'];
}

if(isset($_GET['setActive'])){
    $db->setPlaylistActive($_GET['setActive']);
    
    $mpd = new Net_MPD('127.0.0.1', '3306');
    $mpdCommon = $mpd->factory("Common");
    $mpdDatabase = $mpd->factory("Database");

    if( ! $mpdCommon->isConnected()){
        $mpdCommon->connect();
    }

    $mpdPlaylist = $mpd->factory("Playlist");
    $mpdPlayback = $mpd->factory("Playback");
    
    //check if there are any unplayed songs on user playlist
    if($db->checkUnplayedUserSongs()){
        $song_details = $db->getFirstUnplayedUserSong();
        $context->setContext('current_playlist_on', 'user');
    }else{
        $song_details = $db->getFirstUnplayedAdminSong();
        $context->setContext('current_playlist_on', 'admin');
    }
    
    if(empty($song_details)){
        $song_details = $db->getLastPlayedAdminSong();
    }
    
    //clear mpd playlist
    $mpdPlaylist->clear();

    //add song to mpd playlist
    $mpdPlaylist->addSong($song_details['file']);

    //play
    $mpdPlayback->play();

    //update database to set last time song was played
    $db->update(array('last_played_on' => time()), 'songs', array('id' => $song_details['id']));

    //save to session information of sond id currently playing
    if($context->getContext('current_playlist_on') == 'admin'){
        $context->setContext('song', array(
            'current_song_id'           => $song_details['id'],
            'current_song_order'        => $song_details['ap_order'],
            'current_song_title'        => $song_details['title'],
            'current_playlist_song_id'  => $song_details['ap_id'],
            'current_playlist_id'       => $song_details['ap_playlist_id']));

        //set all previous songs played to N
        $db->query("UPDATE `admin_playlist` SET played = 'N' WHERE `order` > ".$song_details['ap_order']." AND playlist_id = ".$song_details['ap_playlist_id']);
        //everything else set played to Y
        $db->query("UPDATE `admin_playlist` SET played = 'Y' WHERE `order` <= ".$song_details['ap_order']." AND playlist_id = ".$song_details['ap_playlist_id']);

    }else if($context->getContext('current_playlist_on') == 'user'){
        $context->setContext('song', array(
            'current_song_id'           => $song_details['id'],
            'current_song_order'        => $song_details['up_order'],
            'current_song_title'        => $song_details['title'],
            'current_playlist_song_id'  => $song_details['up_id'],
            'current_playlist_id'       => 0));

        //set all previous songs played to N
        //$db->query("UPDATE `user_playlist` SET played = 'N' WHERE `order` > ".$song_details['up_order']);
        //everything else set played to Y
        $db->query("UPDATE `user_playlist` SET played = 'Y' WHERE `order` = ".$song_details['up_order']);
    }

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
                   $('.playerManagerCurrentSong span').html(response.song_order+' - '+stripslashes(response.song_title)+' ('+response.current_playlist+' playlist)'); 
                }, 'json');
            },1000);
        });
    </script>
    <script>
        $(function() {
            $( "#sortable" ).sortable({
                placeholder: "ui-state-highlight",
                stop: function( event, ui ) {
                    var order_array = playlist_order();
                    $.get('/jukebox/listener/playlistOrderListener.php', {order_array: order_array});
                }
            });
            $( "#sortable" ).disableSelection();
            
            $('.import-library').on('click', function(){
                window.location.href = $(this).attr('href');
            })
        });
        
        function searchSongs(){
            var genre = $('[name=playlist_genre]').val();
            var artist = $('[name=playlist_artist]').val();
            var album = $('[name=playlist_album]').val();
            var title = $('[name=playlist_title]').val();
            
            $.get('/jukebox/listener/playlistListener.php', {action: 'searchSongs', genre: "", artist: artist, album: "", title: title}, function (response){
                var $this = $('.playlist_search_results').find('ul');
                $this.find('li').remove();
                $.each(response,function(index, obj){
                    var el = document.createElement('li');
                    var html ='<div class="song_title">'+ stripslashes(obj.artist) + '<br>'+stripslashes(obj.title)+'</div> <a class="button" href="javascript:addToPlaylist('+obj.id+');">Add to playlist</a>';
                    $(el).html(html);
                    $this.append(el);
                });
            }, 'json');
            return false;
        }
        
        function addToPlaylist(song_id){
            $.get('/jukebox/listener/playlistListener.php', {action: 'addSongToPlaylist', song_id: song_id, playlist_id: <?php echo $_GET['detailsId']; ?>}, function (response){
                var $this = $('#playlist_content').find('ul#sortable');
                var el = document.createElement('li');
                var html = '<span class="counter"></span><div class="song_title">'+stripslashes(response.artist)+' <br> '+stripslashes(response.title)+'</div> <a class="button red" href="javascript:removeFromPlaylist('+response.ps_id+');">Remove</a>';
                $(el).addClass('ui-state-default').attr('playlist_song_id', response.ps_id).html(html);
                $this.append(el);
                
                playlist_order();
            }, 'json');
        }
        
        function removeFromPlaylist(playlist_song_id){
            $.get('/jukebox/listener/playlistListener.php', {action: 'removeSongFromPlaylist', playlist_song_id: playlist_song_id, playlist_id: <?php echo $_GET['detailsId']; ?>}, function (){
                var $this = $('#playlist_content').find('ul#sortable');
                $.each($this.find('li'), function(index, obj){
                    if(parseInt($(obj).attr('playlist_song_id')) == playlist_song_id){
                        $(this).remove();
                    }
                });
                
                playlist_order();
            });
        }
        
        function playlist_order(){
            var list_number = 1;
            var order_array = new Array;

            $.each($('#sortable li'),function(index, obj){
                $(this).find('span.counter').html(list_number+'.');
                order_array.push(parseInt($(this).attr('playlist_song_id')));
                list_number++;
            });
            return order_array;
        }
        
        function stripslashes(str) {
            return (str + '')
              .replace(/\\(.?)/g, function (s, n1) {
                switch (n1) {
                case '\\':
                  return '\\';
                case '0':
                  return '\u0000';
                case '':
                  return '';
                default:
                  return n1;
                }
              });
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

    <div class="wrapper">
        <div class="status">
            <p>Create playlists that will play while no users have selected a track. Set the playlist you would like to be background music to "active". Click "manage" to add tracks to a playlist.</p>
            You can drag tracks in the list to re-order them
        </div>
    </div>


    <div class="wrapper fields_wrapper">
        <p class="title">Playlists management</p>
        <?php if(!isset($_GET['detailsId'])) { ?>
        <div>
            <form action="/jukebox/admin/managePlaylist.php" method="POST">
                <input class="text" type="text" name="playlist" value="<?php echo (isset($_POST['playlist'])) ? $_POST['playlist'] : ''; ?>" placeholder="Create a new playlist" /> <input class="button" type="submit" value="Create" name="create_playlist" />
            </form>
            <?php if(isset($playlist_create_msg)){ ?>
            <p><?php echo $playlist_create_msg; ?></p>
            <?php } ?>
        </div>
        <?php
        $playlists = $db->getAllPlaylists();
        if(count($playlists) > 0){ $i = 1;?>
        <br>
        <p class="title">Music Playlists</p>
        <table>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Tracks</th>
                <th>Total time</th>
                <th></th>
            </tr>
            <?php foreach ($playlists as $k => $v) { 
                $total_time = $db->secondsToTime($v['total_time']);?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $v['name']; ?></td>
                <td><?php echo $v['tracks']; ?></td>
                <td><?php echo str_pad($total_time['hours'], 2, "0", STR_PAD_LEFT).':'.str_pad($total_time['minutes'], 2, "0", STR_PAD_LEFT).':'.str_pad($total_time['seconds'], 2, "0", STR_PAD_LEFT); ?></td>
                <td>
                    <?php if(($v['active'] == 'N' || $v['active'] == NULL) && $v['tracks'] > 0){ ?>
                    <a class="icon icon_plus" href="/jukebox/admin/managePlaylist.php?setActive=<?php echo $v['id']; ?>">Set Active</a>
                    <?php } ?>
                    <a class="icon icon_cog" href="/jukebox/admin/managePlaylist.php?detailsId=<?php echo $v['id']; ?>">Manage</a> <a class="icon icon_remove" href="/jukebox/admin/managePlaylist.php?removeId=<?php echo $v['id']; ?>">Remove</a></td>
            </tr>
            <?php $i++; } ?>
        </table>
        <?php } ?>
        <?php } ?>
        
        <?php if(isset($_GET['detailsId'])) { 
            $playlist_details = $db->getPlaylist(array('id' => $_GET['detailsId']));
            $playlist_songs = $db->getSongsFromPlaylist($_GET['detailsId']);
        ?>
        <div>
            <form action="/jukebox/admin/managePlaylist.php?detailsId=<?php echo $_GET['detailsId']; ?>" method="POST">
                <input class="text" type="text" name="playlist" value="<?php echo (isset($_POST['playlist'])) ? $_POST['playlist'] : $playlist_details[0]['name']; ?>" placeholder="Playlist name" /> <input class="button" type="submit" value="Save name" name="update_playlist" />
            </form>

            <?php if(isset($playlist_update_msg)){ ?>
            <p><?php echo $playlist_update_msg; ?></p>
            <?php } ?>
        </div><br>
        <input class="button import-library" type="button" value="Import library" href="/jukebox/admin/managePlaylist.php?detailsId=<?php echo $_GET['detailsId']; ?>&importLibrary" /> <input class="button import-library" type="button" value="Shuffle tracks" href="/jukebox/admin/managePlaylist.php?detailsId=<?php echo $_GET['detailsId']; ?>&shuffleTracks" />
        <br><br>
        <form action="/jukebox/admin/managePlaylist.php?detailsId=<?php echo $_GET['detailsId']; ?>" method="POST">
            <select name="playlist_year">
                <option value="0">-- Select Year --</option>
                <?php
                $years = $db->getYears();
                foreach ($years as $k => $v) { ?>
                    <option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['year']); ?></option>
                <?php } ?>

                <input class="button import-library" type="submit" name="year_import" value="Import all tracks by year"/>
            </select>
        </form>
        </br></br>
            <?php
                $artists_arr = $db->getArtist(array('all' => 'all'));
                //$albums_arr = $db->getAlbum(array('all' => 'all'));
                //$genres_arr = $db->getGenre(array('all' => 'all'));
                ?>
                <div id="playlist_search_form">
                    <form action="" method="" onSubmit="return searchSongs();">

                                    <select name="playlist_artist">
                                        <option value="0">-- Select artist --</option>
                                        <?php foreach ($artists_arr as $k => $v) { ?>
                                        <option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['artist']); ?></option> 
                                        <?php } ?>
                                        <option value="0">All Artists</option>
                                    </select>
                                
                                <?php /* <td>
                                    <select name="playlist_genre">
                                        <option value="0">-- Select genre --</option>
                                        <?php foreach ($genres_arr as $k => $v) { ?>
                                        <option value="<?php echo $v['id']; ?>"><?php echo $v['genre']; ?></option> 
                                        <?php } ?>
                                        <option value="0">All Genres</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="playlist_album">
                                        <option value="0">-- Select album --</option>
                                        <?php foreach ($albums_arr as $k => $v) { ?>
                                        <option value="<?php echo $v['id']; ?>"><?php echo $v['album']; ?></option> 
                                        <?php } ?>
                                        <option value="0">All Albums</option>
                                    </select>
                                </td> */ ?>
                               
                                    <input class="text" type="text" name="playlist_title" value="" placeholder="(optional) text search"/>
                                
                                    <input class="button" type="submit" name="search" value="Show Song Library" />

                    </form>
                </div><br>
        <div class="playlist_creator">
            <div class="playlist no_drag" id="songs_content">
                <p class="subtitle">Song Library</p>
                <div class="playlist_search_results">
                    <ul></ul>
                </div>
            </div><div class="playlist" id="playlist_content">
                <p class="subtitle">Playlist content</p>
                <ul id="sortable">
                    <?php if(count($playlist_songs) > 0){
                        $i = 1;
                        foreach ($playlist_songs as $k => $v) { ?>
                <li class="ui-state-default" playlist_song_id="<?php echo $v['ps_id']; ?>">
                    <span class="counter"><?php echo $i; ?>.</span> <div class="song_title"><?php echo stripslashes($v['artist']).' <br> '.stripslashes($v['title']); ?> </div> <a class="button red" href="javascript:removeFromPlaylist(<?php echo $v['ps_id']; ?>);">Remove</a>
                </li>
                       <?php $i++; }
                    } ?>
                </ul>
            </div>
            <div class="divider"></div>
        </div>
        
        <?php } ?>
        </div>
    </body>
</html>