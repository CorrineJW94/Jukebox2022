<?php
//configuration
include_once '../config/config.php';

$context = ContextStorage::getInstance();

$context->delete('transition_song_id'); // reset transition when user reopen browser

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
}else {
    header('Location: /mobile/');
    exit;
}*/


?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta property="og:url" content="http://www.theshufflebar.co.uk/" />
<meta property="og:site_name" content="Shuffle Bar & Kitchen" />
<meta property="og:title" content="Shuffle Bar & Kitchen" />
<meta property="og:description" content="Shuffle, a unique new cocktail bar and kitchen concept which literally puts music in the hands of its guests" />
<meta property="og:type" content="website" />
<meta property="og:image" content="http://www.theshufflebar.co.uk/images/logo.png" id="ogImage" />
<meta name="description" content="Shuffle, a unique new cocktail bar and kitchen concept which literally puts music in the hands of its guests">
<meta name="keywords" content="Shuffle Bar and Kitichen, Cocktails, Brighton Cocktail Bar, Organic Hotdogs, going out Brighton, Hen Party Brighton, Stag Party Brighton, Gourmet burgers, Night out Brigton, Brighton, Shuffle Bar" >
<meta name="viewport" content="width=device-width, user-scalable=no">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <title>Shuffle Bar &amp; Jukebox</title>
    
    
    <script type="text/javascript" src="/jukebox/tv/js/libs.js"></script>
    <script type="text/javascript" src="/jukebox/tv/js/main.js"></script>

    <script>
        var gbPlaylistAction;
        $(window).load(function(){
            gbPlaylistAction = setInterval(function(){
                tv();
            },1000);            
        });
        
        function tv(){
            $.get('/jukebox/listener/frontendListener.php', function (response){
                /* //Awesome track queue count - commented out :(
                var tracks_in_queue = "Tracks queued: ";                       
                if (!response.user_playlist){
                    tracks_in_queue="";
                }else{
                    tracks_in_queue =tracks_in_queue+ (response.user_playlist.length);
                }
                $('p.custom_text_1').html(tracks_in_queue);


                
                var wait_time= "A track selected now plays in: ";
                var mins = 0;
                var secs = 0;
                if (!response.user_playlist){
                    wait_time= '';

                }else{
                    var tmp_total_time=0;
                    for(var w=0;w<response.user_playlist.length;w++){
                        tmp_total_time= parseInt(tmp_total_time) + parseInt(response.user_playlist[w].track_time);

                        if(response.user_playlist[0].id != response.id) {
                            tmp_total_time= tmp_total_time+ parseInt(response.track_time)
                        }
                    }
                    tmp_total_time = tmp_total_time - response.current_song_time;
                    mins = Math.floor(tmp_total_time / 60);
                    secs = Math.floor(tmp_total_time % 60);
                wait_time= wait_time +'<b>'+ mins+'mins '+secs+'s</b>';

                }

                $('p.custom_text_2').html(wait_time);

                */ //END Awesome track queue count 

/*
                var next_track= "Next Chosen Track: ";
                if (!response.user_playlist){
                    next_track = "";
                }else{
                    console.log(response.user_playlist[0]);
                    next_track += response.user_playlist[0][8] + ' - '+response.user_playlist[0][11];
                }

                $('p.custom_text_2').html(next_track);
*/

                
                if(response.transition == 'yes'){
                    $('h2.artist').html('');
                    $('h1.song_title').html('');
                    $('p.custom_text_1').html('');
                    $('p.custom_text_2').html('');
                    
                    clearInterval(gbPlaylistAction);
                    $('.active').slideUp(200, function(){
                        setTimeout(function(){
                            $('.active').slideDown(200, function(){
                                gbPlaylistAction = setInterval(function(){
                                    tv();
                                },1000);
                                $('h2.artist').html(stripslashes(response.artist));
                                $('h1.song_title').html(stripslashes(response.title));
                                if(response.user != undefined && response.user != "Guest") {
                                    $('p.custom_text_1').html("Played By: "+stripslashes(response.user));
                                }

                                
                                //$('p.custom_text_2').html(response.custom_text_2);
                            });
                        },700);
                    });
                }
            }, 'json');
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

<body >
    <!-- INTRO -->
    <div class="section active">
       <div class="content">
           <div class="inner_content">
                <img class="logo" src="images/logo@2x.png" width="100%" /> 
                <div class="impact">
                    <div class="titles">
                        <h2 class="artist"></h2>
                        <h1 class="song_title"></h1>
                    </div>
                </div>
                    <p class="custom_text_2"></p>
                    <p class="name custom_text_1"></p>
                    <p class="marquee">To choose a track simply use the bar Wifi: Shuffle Jukebox. Then go to shuffle.com and the music is in your hands</p>
           </div>
       </div>
    </div>
    <!--//  INTRO -->

</body>


</html>
