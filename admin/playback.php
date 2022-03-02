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

$mpd = new Net_MPD('127.0.0.1', '3306');
$mpdCommon = $mpd->factory("Common");
$mpdDatabase = $mpd->factory("Database");

if( ! $mpdCommon->isConnected()){
    $mpdCommon->connect();
}

$mpdPlaylist = $mpd->factory("Playlist");
$mpdPlayback = $mpd->factory("Playback");
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
                        response.current_playlist = 'Background Music';
                    }
                   $('.playerManagerCurrentSong span').html(response.song_title+' ('+response.current_playlist+' playlist)'); 
                }, 'json');
            },1000);
            
            var gbPlaylistAction = setInterval(function(){
                $.get('/jukebox/listener/userPlaylistListener.php', function (response){
                   var $this = $('#user_sortable');
                   $this.find('li').remove();
                   
                   $.each(response,function(index, obj){
                       var el = document.createElement('li');
                       var html = '<span class="counter"></span><div class="song_title">'+stripslashes(obj.artist)+' <br> '+stripslashes(obj.title)+'</div> <a style="right:275px;" class="button icon icon_play" href="javascript:playSongUser('+obj.up_id+');">Play</a>  <a style="right:124px;" class="button purple icon icon_top" href="javascript:moveToTopOfPlaylist('+obj.up_id+');">Move to top</a>  <a class="button red icon icon_remove" href="javascript:removeFromUserPlaylist('+obj.up_id+');">Remove</a><p style="margin-top: 5px;"><span style="font-weight:bold;">Table(s):&nbsp;</span><span>'+obj.table_numbers+'</span>&nbsp;&nbsp;&nbsp;<span style="font-weight: bold;">Requests:&nbsp;</span><span>'+obj.requests+'</span></p>';
                       $(el).addClass('ui-state-default').attr('playlist_song_id', obj.up_id).html(html);
                       $this.append(el);
                   });
                   userPlaylistOrder($this);
                }, 'json');
            },5000);
        });
        
        $(function() {
            $( "#admin_sortable" ).sortable({
                placeholder: "ui-state-highlight",
                stop: function( event, ui ) {
                    var $this = $('#admin_sortable');
                    var order_array = adminPlaylistOrder($this);
                    $.get('/jukebox/listener/playlistOrderListener.php', {playlist_type: 'admin', order_array: order_array});
                }
            });
            $( "#sortable" ).disableSelection();
        });
        
        $(function() {
            $( "#user_sortable" ).sortable({
                placeholder: "ui-state-highlight",
                stop: function( event, ui ) {
                    var $this = $('#user_sortable');
                    var order_array = userPlaylistOrder($this);
                    $.get('/jukebox/listener/playlistOrderListener.php', {playlist_type: 'user', order_array: order_array});
                }
            });
            $( "#sortable" ).disableSelection();
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
                    var html ='<div class="song_title">'+stripslashes(obj.artist)+' <br> ' + stripslashes(obj.title)+'</div><a style="right:144px;" class="button icon icon_plus" href="javascript:addToUserPlaylist('+obj.id+');">Add to User Playlist</a> <a class="button purple icon icon_top" href="javascript:moveToTopOfUserPlaylist('+obj.id+');">Add to top</a>';
                    $(el).html(html);
                    $this.append(el);
                });
            }, 'json');
            return false;
        }
        
        function addToAdminPlaylist(song_id){
            $.get('/jukebox/listener/playlistListener.php', {action: 'addSongToAdminPlaylist', song_id: song_id}, function (response){
                var $this = $('#admin_sortable');
                var el = document.createElement('li');
                var html = '<span class="counter"></span><div class="song_title">'+stripslashes(response.artist)+'<br>'+stripslashes(response.title)+' </div> <a class="button icon icon_play" href="javascript:playSongAdmin('+response.ap_id+');">Play</a> <a class="button red icon icon_remove" href="javascript:removeFromAdminPlaylist('+response.ap_id+');">Remove</a>';
                $(el).addClass('ui-state-default').attr('playlist_song_id', response.ap_id).html(html);
                $this.append(el);
                
                adminPlaylistOrder($this);
            }, 'json');
        }
        
        function removeFromAdminPlaylist(playlist_song_id){
            var $this = $('#admin_sortable');
            $.get('/jukebox/listener/playlistListener.php', {action: 'removeSongFromAdminPlaylist', playlist_song_id: playlist_song_id}, function (){
                $.each($this.find('li'), function(index, obj){
                    if(parseInt($(obj).attr('playlist_song_id')) == playlist_song_id){
                        $(this).remove();
                    }
                });
                
                adminPlaylistOrder($this);
            });
        }
        
        function adminPlaylistOrder($element){
            var list_number = 1;
            var order_array = new Array;

            $.each($element.find('li'),function(index, obj){
                $(this).find('span.counter').html(list_number+'. ');
                order_array.push(parseInt($(this).attr('playlist_song_id')));
                list_number++;
            });
            return order_array;
        }
        
        function playSongAdmin(playlist_song_id){
            $.get('/jukebox/listener/playbackListener.php', {side: 'admin', status: 'play', playlist_song_id: playlist_song_id});
        }
                
        function moveToTopOfUserPlaylist(song_id){
            $.get('/jukebox/listener/playlistListener.php', {action: 'moveToTopOfUserPlaylist', song_id: song_id}, function (response){
                var $this = $('#user_sortable');
                var el = document.createElement('li');
                var html = '<span class="counter"></span><div class="song_title">'+stripslashes(response.artist)+'<br>'+stripslashes(response.title)+'</div> <a class="button icon icon_play" href="javascript:playSongUser('+response.up_id+');">Play</a> <a class="button red icon icon_remove" href="javascript:removeFromUserPlaylist('+response.up_id+');">Remove</a>';
                $(el).addClass('ui-state-default').attr('playlist_song_id', response.up_id).html(html);
                $this.prepend(el);
                
                userPlaylistOrder($this);
            }, 'json');
        }
        
        function addToUserPlaylist(song_id){
            $.get('/jukebox/listener/playlistListener.php', {action: 'addSongToUserPlaylist', song_id: song_id}, function (response){
                var $this = $('#user_sortable');
                var el = document.createElement('li');
                var html = '<span class="counter"></span><div class="song_title">'+stripslashes(response.artist)+'<br>'+stripslashes(response.title)+'</div> <a class="button icon icon_play" href="javascript:playSongUser('+response.up_id+');">Play</a> <a class="button red icon icon_remove" href="javascript:removeFromUserPlaylist('+response.up_id+');">Remove</a>';
                $(el).addClass('ui-state-default').attr('playlist_song_id', response.up_id).html(html);
                $this.append(el);
                
                userPlaylistOrder($this);
            }, 'json');
        }
        
        function removeFromUserPlaylist(playlist_song_id){
            var $this = $('#user_sortable');
            $.get('/jukebox/listener/playlistListener.php', {action: 'removeSongFromUserPlaylist', playlist_song_id: playlist_song_id}, function (){
                $.each($this.find('li'), function(index, obj){
                    if(parseInt($(obj).attr('playlist_song_id')) == playlist_song_id){
                        $(this).remove();
                    }
                });
                
                userPlaylistOrder($this);
            });
        }
        
        function moveToTopOfPlaylist(playlist_song_id){
            var $this = $('#user_sortable');
            $.get('/jukebox/listener/playlistListener.php', {action: 'moveToTopOfPlaylist', playlist_song_id: playlist_song_id}, function (){
                $.each($this.find('li'), function(index, obj){
                    if(parseInt($(obj).attr('playlist_song_id')) == playlist_song_id){
                        $(this).parent().prepend(this);
                    }
                });
                
                userPlaylistOrder($this);
            });
        }       

        function userPlaylistOrder($element){
            var list_number = 1;
            var order_array = new Array;

            $.each($element.find('li'),function(index, obj){
                $(this).find('span.counter').html(list_number+'. ');
                order_array.push(parseInt($(this).attr('playlist_song_id')));
                list_number++;
            });
            return order_array;
        }
        
        function playSongUser(playlist_song_id){
            $.get('/jukebox/listener/playbackListener.php', {side: 'user', status: 'play', playlist_song_id: playlist_song_id});
        }
        
        
        
        function stopSong(){
            $.get('/jukebox/listener/playbackListener.php', {side: '', status: 'stop'});
        }
        
        function pauseResume(){
            $.get('/jukebox/listener/playbackListener.php', {side: '', status: 'pauseResume'});
        }
        
        function playNext(){
            $.get('/jukebox/listener/playbackListener.php', {side: '', status: 'next'});
        }
        
        function playPrev(){
            $.get('/jukebox/listener/playbackListener.php', {side: '', status: 'prev'});
        }

        function volumeChange() {
          $.get('/jukebox/listener/playbackListener.php', {side: '', volume: $('.slider.slider_volume')[0].value, status: 'volume'});
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
                <p>This is the current state of the background and user playlists</p>
                You can drag tracks in the list to re-order them
            </div>
        </div>

        <div class="wrapper fields_wrapper">
        <p class="title">Player manager</p>
        
            <div class="player-manager-container">
                <p class="subtitle playerManagerCurrentSong">Now playing - <span></span></p>
                <p>
                    <a class="button icon icon_backward" href="javascript:playPrev();">Previous</a>
                    <a class="button" href="javascript:pauseResume();"><span class="icon icon_pause"></span>Pause / <span class="icon icon_play"></span>Resume</a>
                    <a class="button icon icon_stop" href="javascript:stopSong();">Stop</a>
                    <a class="button icon icon_forward" href="javascript:playNext();">Next</a><br>
                    Volume: <input type="range" min="0" max="100" value="100" class="slider slider_volume" onchange="volumeChange()" step="1">
                </p>
            </div>
            

            

        <div class="playlist_creator">
            <div style="width:40%;" class="playlist alt" id="songs_content">
                <div class="admin-playlist-container">
                    <p class="subtitle">Current Background Music</p>
                    <ul id="admin_sortable" class="sortable">
                    <?php $admin_playlist = $db->getAdminPlaylist();
                    if(count($admin_playlist) > 0){
                        $i = 1;
                            foreach ($admin_playlist as $k => $v) { ?>
                    <li class="ui-state-default" playlist_song_id="<?php echo $v['ap_id']; ?>">
                        <span class="counter"><?php echo $i; ?>.</span><div class="song_title"> <?php echo $v['artist'].'<br>'.stripslashes($v['title']); ?> </div><a class="button icon icon_play" href="javascript:playSongAdmin(<?php echo $v['ap_id']; ?>);">Play</a> <a class="button red icon icon_remove" href="javascript:removeFromAdminPlaylist(<?php echo $v['ap_id']; ?>);">Remove</a>
                    </li>
                           <?php $i++; } ?>
                    <?php } ?>
                    </ul>
                </div>
            </div><div style="width:60%;" class="playlist alt" id="playlist_content">
                            <div class="user-playlist-container">
                <p class="subtitle">Current User Playlist</p>
                <ul id="user_sortable" class="sortable">
                <?php $user_playlist = $db->getUserPlaylist();
                if(count($user_playlist) > 0){ 
                    $i = 1;
                        foreach ($user_playlist as $k => $v) { ?>
                <li class="ui-state-default" playlist_song_id="<?php echo $v['up_id']; ?>">
                    <span class="counter"><?php echo $i; ?>.</span><div class="song_title"> <?php echo $v['artist'].'<br>'.stripslashes($v['title']); ?> </div><a style="right:275px;" class="button icon icon_play" href="javascript:playSongUser(<?php echo $v['up_id']; ?>);">Play</a> <a style="right:124px;" class="button purple icon icon_top" href="javascript:moveToTopOfPlaylist(<?php echo $v['up_id']; ?>);">Move to top</a> <a class="button red icon icon_remove" href="javascript:removeFromUserPlaylist(<?php echo $v['up_id']; ?>);">Remove</a><p style="margin-top: 5px;"><span style="font-weight:bold;">Table(s):&nbsp;</span><span><?php echo $v['table_numbers']; ?></span>&nbsp;&nbsp;&nbsp;<span style="font-weight: bold;">Requests:&nbsp;</span><span><?php echo $v['requests']; ?></span></p>
                </li>
                       <?php $i++; } ?>
                <?php } ?>
                </ul>
            </div>
            </div>
            <div class="divider" style="width:60%;"></div>
        </div>

            <br>
            <div class="music-container">
                <p class="subtitle">Add Song from Library</p>
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
                            <option value="<?php echo $v['id']; ?>"><?php echo $v['artist']; ?></option> 
                            <?php } ?>
                            <option value="0">All Artists</option>
                        </select>

                        <input type="text" class="text" name="playlist_title" value="" placeholder="(optional) text search"/>

                        <input class="button" type="submit" name="search" value="Show Song Library" />

                    </form>
                </div>
                <div class="playlist_creator playlist_search_results">
                    <div class="playlist alt wide no_drag" id="songs_content">
                        <ul></ul>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>