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

if(isset($_GET['artists']) && isset($_GET['add'])){
    $response = $db->addArtist($_POST['artist_name']);
    if($response['status'] == 'success'){
        unset($_POST);
    }
    $artist_create_msg = $response['msg'];
}

if(isset($_GET['artists']) && isset($_GET['edit'])){
    $update_artist_id = $_POST['artist_id'];
    $update_artist_name = $_POST['new_artist_name'];
    
    if($_POST['edit_artist_submit'] == 'Save'){
        $response = $db->updateArtist($update_artist_id, $update_artist_name);
    }else if($_POST['edit_artist_submit'] == 'Remove'){
        $response = $db->removeArtist($update_artist_id);
    }
    
    if($response['status'] == 'success'){
        unset($_POST);
    }
    $artist_update_msg = $response['msg'];
}



if(isset($_GET['albums']) && isset($_GET['add'])){
    $response = $db->addAlbum($_POST['album_name']);
    if($response['status'] == 'success'){
        unset($_POST);
    }
    $album_create_msg = $response['msg'];
}

if(isset($_GET['albums']) && isset($_GET['edit'])){
    $update_album_id = $_POST['album_id'];
    $update_album_name = $_POST['new_album_name'];
    
    if($_POST['edit_album_submit'] == 'Save'){
        $response = $db->updateAlbum($update_album_id, $update_album_name);
    }else if($_POST['edit_album_submit'] == 'Remove'){
        $response = $db->removeAlbum($update_album_id);
    }
    
    if($response['status'] == 'success'){
        unset($_POST);
    }
    $album_update_msg = $response['msg'];
}



if(isset($_GET['genres']) && isset($_GET['add'])){
    $response = $db->addGenre($_POST['genre_name']);
    if($response['status'] == 'success'){
        unset($_POST);
    }
    $genre_create_msg = $response['msg'];
}

if(isset($_GET['genres']) && isset($_GET['edit'])){
    $update_genre_id = $_POST['genre_id'];
    $update_genre_name = $_POST['new_genre_name'];
    
    if($_POST['edit_genre_submit'] == 'Save'){
        $response = $db->updateGenre($update_genre_id, $update_genre_name);
    }else if($_POST['edit_genre_submit'] == 'Remove'){
        $response = $db->removeGenre($update_genre_id);
    }
    
    if($response['status'] == 'success'){
        unset($_POST);
    }
    $genre_update_msg = $response['msg'];
}


if(isset($_GET['duplicates']) && isset($_GET['remove'])){
    $db->softRemoveSong($_GET['remove']);
    $duplicate_msg = 'Selected track has been deleted.';
}

if(isset($_GET['duplicates']) && isset($_GET['removeP'])){
    $db->hardRemoveSong($_GET['removeP']);
    $duplicate_msg = 'Selected track has been deleted.';
}

if(isset($_GET['songs']) && isset($_GET['removeP'])){
    $db->hardRemoveSong($_GET['removeP']);
    $songs_msg = 'Selected track has been deleted.';
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
    <script type="text/javascript">
        $(document).ready(function(){
            $('.my_list li').on('click', function(){
                $.each($(".my_list li"), function(index,obj){
                    $(obj).find('.hidden').hide();
                    $(obj).find('.artist_name').show();
                    $(obj).find('.album_name').show();
                    $(obj).find('.genre_name').show();
                    $(obj).find('input[type=text]').prop('disabled', true);
                    $(obj).find('.artist_edit_error').remove();
                    $(obj).find('.album_edit_error').remove();
                    $(obj).find('.genre_edit_error').remove();
                });
                
                $(this).find('.hidden').show();
                $(this).find('.artist_name').hide();
                $(this).find('.album_name').hide();
                $(this).find('.genre_name').hide();
                $(this).find('input[type=text]').prop('disabled', false);
            });
            
            <?php if(isset($update_artist_id)){ ?>
            $('.my_list li#artist_<?php echo $update_artist_id; ?>').trigger('click');
            $('.my_list li#artist_<?php echo $update_artist_id; ?>').append('<p class="artist_edit_error"><?php echo $artist_update_msg ?></p>');
            $('.my_list li#artist_<?php echo $update_artist_id; ?>').find('.artist_edit_error').fadeOut(5000);
            $('html, body').animate({
                scrollTop: $("#artist_<?php echo $update_artist_id; ?>").offset().top
            }, 1000);
            <?php } ?>
            
            <?php if(isset($update_album_id)){ ?>
            $('.my_list li#album_<?php echo $update_album_id; ?>').trigger('click');
            $('.my_list li#album_<?php echo $update_album_id; ?>').append('<p class="album_edit_error"><?php echo $album_update_msg ?></p>');
            $('.my_list li#album_<?php echo $update_album_id; ?>').find('.album_edit_error').fadeOut(5000);
            $('html, body').animate({
                scrollTop: $("#album_<?php echo $update_album_id; ?>").offset().top
            }, 1000);
            <?php } ?>
            
            <?php if(isset($update_genre_id)){ ?>
            $('.my_list li#genre_<?php echo $update_genre_id; ?>').trigger('click');
            $('.my_list li#genre_<?php echo $update_genre_id; ?>').append('<p class="genre_edit_error"><?php echo $genre_update_msg ?></p>');
            $('.my_list li#genre_<?php echo $update_genre_id; ?>').find('.genre_edit_error').fadeOut(5000);
            $('html, body').animate({
                scrollTop: $("#genre_<?php echo $update_genre_id; ?>").offset().top
            }, 1000);
            <?php } ?>
        });
        
        function updateSong(update_type, song_id, new_value){
            
            $.get('/jukebox/listener/songUpdateListener.php', {update_type: update_type, song_id: song_id, new_value: new_value}, function (){
                $('td.id_'+song_id).html('<span>Saved</span>');
                $('td.id_'+song_id+' span').fadeOut(3000);
            });
        }
        
        function softRemoveSong(song_id){
            var x = confirm('Are you sure you want to delete this song ?');
            if(x){
                window.location.href='/jukebox/admin/manageDatabase.php?duplicates&remove='+song_id;
            }
        }
        
        function hardRemoveSong(song_id){
            var x = confirm('Are you sure you want to delete this song pernamently ?');
            if(x){
                window.location.href='/jukebox/admin/manageDatabase.php?<?php echo $_SERVER['QUERY_STRING']; ?>&removeP='+song_id;
            }
        }
    </script>
    <style>
        div.hidden{display:none;}
    </style>
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
            <a href="/jukebox/admin/database.php">
                <div class="icon icon_arrow-left"></div>
                <span class="text">Back</span>
            </a>
        </div>
    </div>

    <div class="wrapper">
        <div class="status">
        <p>Select a category to manage.</p>
            Edit or Add new fields
        </div>
    </div>

    <div class="wrapper feature_button_group">        

        <div class="feature_button <?php if(isset($_GET['artists'])){ ?>active<?php }?>">
            <a  href="/jukebox/admin/manageDatabase.php?artists">
                <div class="icon icon_music"></div>
                <span class="text"><p>Manage</p>Artists</span>
            </a>
        </div><div class="feature_button  <?php if(isset($_GET['songs'])){ ?>active<?php }?>">
            <a href="/jukebox/admin/manageDatabase.php?songs">
                <div class="icon icon_music"></div>
                <span class="text"><p>Manage</p>Songs</span>
            </a>
        </div><div class="feature_button  <?php if(isset($_GET['duplicates'])){ ?>active<?php }?>">
            <a href="/jukebox/admin/manageDatabase.php?duplicates">
                <div class="icon icon_music"></div>
                <span class="text"><p>Manage</p>Duplicates</span>
            </a>
        </div>
    </div>


    <div class="wrapper fields_wrapper">
        <?php if(isset($_GET['artists'])){ ?>
        <p class="title">Artists management:</p>
        <div>
        <p class="subtitle">Add Artist</p>
            <form action="/jukebox/admin/manageDatabase.php?artists&add" method="POST">
                <input class="text"  type="text" name="artist_name" value="<?php echo (isset($_POST['artist_name'])) ? $_POST['artist_name'] : ''; ?>" placeholder="Add new artist" /> <input class="button" type="submit" value="Create" name="add_artist_submit" /><br><br><br>
            </form>
            <?php if(isset($artist_create_msg)){ ?>
            <p><?php echo $artist_create_msg; ?></p>
            <?php } ?>
        </div>
            <?php $artists = $db->getArtist(array('all' => 'all'));
            if(count($artists) > 0){ ?>
            <p class="subtitle">Edit Artists</p>
        <form action="/jukebox/admin/manageDatabase.php?artists&edit" method="POST">
        <ul class="my_list">
            <?php foreach ($artists as $k => $v) { ?>
            <li id="artist_<?php echo $v['id']; ?>">
                <span class="artist_name"><?php echo stripslashes($v['artist']); ?></span>
                <div class="hidden"><input class="text" type="text" name="new_artist_name" value="<?php echo stripslashes($v['artist']); ?>" /> <input type="text" name="artist_id" value="<?php echo ($v['id']); ?>" style="display: none;" /> <input class="button" type="submit" name="edit_artist_submit" value="Save" /> <input class="button red" type="submit" name="edit_artist_submit" value="Remove" /></div>
            </li>
            <?php } ?>
        </ul>
        </form>
            <?php } ?>
        <?php } ?>
        
        <?php /* if(isset($_GET['albums'])){ ?>
        <p class="title">Album management:</p>
        <div>
            <form action="/jukebox/admin/manageDatabase.php?albums&add" method="POST">
                <input type="text" name="album_name" value="<?php echo (isset($_POST['album_name'])) ? $_POST['album_name'] : ''; ?>" placeholder="Add new album" /> <input type="submit" value="Create" name="add_album_submit" />
            </form>
            <?php if(isset($album_create_msg)){ ?>
            <p><?php echo $album_create_msg; ?></p>
            <?php } ?>
        </div>
            <?php $albums = $db->getAlbum(array('all' => 'all'));
            if(count($albums) > 0){ ?>
        <form action="/jukebox/admin/manageDatabase.php?albums&edit" method="POST">
        <ul class="my_list">
            <?php foreach ($albums as $k => $v) { ?>
            <li id="album_<?php echo $v['id']; ?>">
                <span class="album_name"><?php echo stripslashes($v['album']); ?></span>
                <div class="hidden"><input type="text" name="new_album_name" value="<?php echo stripslashes($v['album']); ?>" /><input type="text" name="album_id" value="<?php echo ($v['id']); ?>" style="display: none;" /><input type="submit" name="edit_album_submit" value="Save" /><input type="submit" name="edit_album_submit" value="Remove" /></div>
            </li>
            <?php } ?>
        </ul>
        </form>
            <?php } ?>
        <?php } ?>
        
        <?php if(isset($_GET['genres'])){ ?>
        <p class="title">Genres management:</p>
        <div>
            <form action="/jukebox/admin/manageDatabase.php?genres&add" method="POST">
                <input type="text" name="genre_name" value="<?php echo (isset($_POST['genre_name'])) ? $_POST['genre_name'] : ''; ?>" placeholder="Add new genre" /> <input type="submit" value="Create" name="add_genre_submit" />
            </form>
            <?php if(isset($genre_create_msg)){ ?>
            <p><?php echo $genre_create_msg; ?></p>
            <?php } ?>
        </div>
            <?php $genres = $db->getGenre(array('all' => 'all'));
            if(count($genres) > 0){ ?>
        <form action="/jukebox/admin/manageDatabase.php?genres&edit" method="POST">
        <ul class="my_list">
            <?php foreach ($genres as $k => $v) { ?>
            <li id="genre_<?php echo $v['id']; ?>">
                <span class="genre_name"><?php echo stripslashes($v['genre']); ?></span>
                <div class="hidden"><input type="text" name="new_genre_name" value="<?php echo stripslashes($v['genre']); ?>" /><input type="text" name="genre_id" value="<?php echo ($v['id']); ?>" style="display: none;" /><input type="submit" name="edit_genre_submit" value="Save" /><input type="submit" name="edit_genre_submit" value="Remove" /></div>
            </li>
            <?php } ?>
        </ul>
        </form>
            <?php } ?>
        <?php } */?>
        
        <?php if(isset($_GET['songs'])){ 
            $artists_arr = $db->getArtist(array('all' => 'all'));
            //$albums_arr = $db->getAlbum(array('all' => 'all'));
            //$genres_arr = $db->getGenre(array('all' => 'all'));
            ?>
            
            <?php if(isset($songs_msg)){ ?>
            <p><?php echo $songs_msg; ?></p>
            <?php } ?>
        <p>Song filter</p>
        <div>
            <form action="/jukebox/admin/manageDatabase.php" method="GET">
                <input type="hidden" name="songs" />

                            <select name="artist">
                                <option value="0">-- Select artist --</option>
                                <?php foreach ($artists_arr as $k => $v) { ?>
                                <option value="<?php echo $v['id']; ?>"<?php echo (isset($_GET['artist']) && $_GET['artist'] == $v['id']) ? "selected" : ''; ?>><?php echo $v['artist']; ?></option> 
                                <?php } ?>
                                <option value="0" <?php echo (isset($_GET['artist']) && $_GET['artist'] == 0) ? "selected" : ''; ?>>All Artists</option>
                            </select>
                        
                        <?php /*<td>
                            <select name="genre">
                                <option value="0">-- Select genre --</option>
                                <?php foreach ($genres_arr as $k => $v) { ?>
                                <option value="<?php echo $v['id']; ?>"<?php echo (isset($_GET['genre']) && $_GET['genre'] == $v['id']) ? "selected" : ''; ?>><?php echo $v['genre']; ?></option> 
                                <?php } ?>
                                <option value="0" <?php echo (isset($_GET['genre']) && $_GET['genre'] == 0) ? "selected" : ''; ?>>All Genres</option>
                            </select>
                        </td>
                        <td>
                            <select name="album">
                                <option value="0">-- Select album --</option>
                                <?php foreach ($albums_arr as $k => $v) { ?>
                                <option value="<?php echo $v['id']; ?>"<?php echo (isset($_GET['album']) && $_GET['album'] == $v['id']) ? "selected" : ''; ?>><?php echo $v['album']; ?></option> 
                                <?php } ?>
                                <option value="0" <?php echo (isset($_GET['album']) && $_GET['album'] == 0) ? "selected" : ''; ?>>All Albums</option>
                            </select>
                        </td> */ ?>
                        
                            <input class="text" type="text" name="title" value="<?php echo (isset($_GET['title']) && strlen($_GET['title']) > 0) ? $_GET['title'] : ''; ?>" placeholder="(optional) Search"/>
                            <input class="button" type="submit" name="search" value="Search" />

            </form>
        </div>
        <?php 
        if(isset($_GET['songs']) && isset($_GET['search'])){
            $params = array(
                'artist' => $_GET['artist'],
               // 'album' => $_GET['album'],
               // 'genre' => $_GET['genre'],
                'title' => $_GET['title']
            );
            $songs = $db->searchSongs($params);
            if(count($songs) > 0){ $i = 1;?>
        <form action="/jukebox/admin/manageDatabase.php?artists&edit" method="POST">
        <table class="plain">
            <tr>
                <th>#</th>
                <!--<th>Genre</th>-->
                <th>Artist</th>
                <!--<th>Album</th>-->
                <th>Title</th>
                <th></th>
                <th></th>
            </tr>
            <?php foreach ($songs as $k => $v) { ?>
            <tr>
                <td><?php echo $i; ?></td>
                <?php /*<td>
                    <select name="" onChange="updateSong('genre', <?php echo $v['id']; ?>, this.value);">
                        <?php foreach ($genres_arr as $k1 => $v1) { ?>
                        <option value="<?php echo $v1['id']; ?>"<?php echo ($v['genre_id'] == $v1['id']) ? "selected" : ''; ?>><?php echo $v1['genre']; ?></option> 
                        <?php } ?>
                    </select>
                </td>*/ ?>
                <td>
                    <select name="" onChange="updateSong('artist', <?php echo $v['id']; ?>, this.value);">
                        <?php foreach ($artists_arr as $k1 => $v1) { ?>
                        <option value="<?php echo $v1['id']; ?>"<?php echo ($v['artist_id'] == $v1['id']) ? "selected" : ''; ?>><?php echo $v1['artist']; ?></option> 
                        <?php } ?>
                    </select>
                </td>
                <?php /*<td>
                    <select name="" onChange="updateSong('album', <?php echo $v['id']; ?>, this.value);">
                        <?php foreach ($albums_arr as $k1 => $v1) { ?>
                        <option value="<?php echo $v1['id']; ?>"<?php echo ($v['album_id'] == $v1['id']) ? "selected" : ''; ?>><?php echo $v1['album']; ?></option> 
                        <?php } ?>
                    </select>
                </td>*/ ?>
                <td><input class="text" type="text" name="" onBlur="updateSong('title', <?php echo $v['id']; ?>, this.value);" value="<?php echo stripslashes($v['title']); ?>" /></td>
                <td class="update_status id_<?php echo $v['id']; ?>"></td>
                <td><input class="button red" type="button" onClick="hardRemoveSong(<?php echo $v['id']; ?>);" value="Remove" /></td>
            </tr>
            <?php $i++; } ?>
        </table>
        </form>
            <?php }else{ ?>
            <p>No songs found.</p>
            <?php } ?>
        <?php }
        }?>
            
        <?php if(isset($_GET['duplicates'])){ 
            $duplicates = $db->getDuplicatedSongs(); ?>
            
            <?php if(isset($duplicate_msg)){ ?>
            <p><?php echo $duplicate_msg; ?></p>
            <?php } ?>
            
            <?php if(count($duplicates) > 0){ $i = 1;?>
            <table class="plain">
                <tr>
                    <th>#</th>
                    <!--<th>Genre</th>-->
                    <th>Artist</th>
                    <th>Album</th>
                    <th>Title</th>
                    <th></th>
                </tr>
                <?php foreach ($duplicates as $k => $v) { ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <!--<td><?php echo $v['genre']; ?></td>-->
                    <td><?php echo $v['artist']; ?></td>
                    <td><?php echo $v['album']; ?></td>
                    <td><?php echo $v['title']; ?></td>
                    <td><input class="button red" type="button" onClick="hardRemoveSong(<?php echo $v['id']; ?>);" value="Remove" /></td>
                </tr>
                <?php $i++; } ?>
            </table>
            <?php }else{ ?>
            <p>There are no duplicates in database.</p>
            <?php } ?>
        <?php } ?>

        </div>
    </body>
</html>