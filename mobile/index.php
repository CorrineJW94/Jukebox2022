<?php
//configuration
include_once '../config/config.php';
include_once BASEPATH.'jukebox/lib/MobileClass.php';

//check domain (so cookie is set and read correctly)

//if($_SERVER['HTTP_HOST'] != UI_DOMAIN && !SJ_DEBUG) {
    //header('location: http://'.UI_DOMAIN.'/');
    //return;
//}

$db = new Mobile(DB_USER, DB_PASS, DB_NAME, DB_HOST);
if( ! $db->is_connected()){
    $db->connect();
}

$newUser = false;
if(isset($_COOKIE["jukebox_user_id"])) {
    $user = $db->getUser($_COOKIE["jukebox_user_id"]);
    $user_id = $user["id"];
    $user_name = $user["name"];
} else {
    $user_id = $db->registerUser();
    $user = array("id" => $user_id, "name" => "Guest");
    $user_name = "Guest";
    setcookie("jukebox_user_id", $user_id, time()+(60*60*24*365));
    $newUser = true;
}
$user["new"] = $newUser;

$cookieSet = true;
if(!isset($_COOKIE['cookie_explain'])) {
    $cookieSet = false;
    setcookie('cookie_explain', 'true', time()+(60*60*24*365));
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, user-scalable=no">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <script type="text/javascript" src="js/libs.js"></script>
    <script type="text/javascript" src="js/Dragger.js"></script>
    <script type="text/javascript" src="js/main.js"></script>
    <title>Shuffle Bar &amp; Jukebox</title>
</head>

<body  ontouchstart="">
    <input type="hidden" name="base_url" value="/" />
    <input type="hidden" name="selected_track" value="" />
    <input type="hidden" name="uid" value="<?php echo $user_id;?>" />
    
<div class="warning_rotation"> <img src="images/logo@2x.png" width="70%" style="max-width:70%; margin:0 auto; margin-top:20px;" /> <br>Please browse in portrait mode.</div>
    <!-- INTRO -->
    <div class="section intro <?php echo $user["new"] ? "active" : ""?>">
       <div class="content">
           <div class="inner_content">
                <img class="logo" src="images/logo@2x.png" width="90%" /> 
                <p>Welcome <?php echo $user["new"] ? "" : "back ";?>to the Shuffle Bar music system. We're putting the music in your hands</p>
                <p>This system uses cookies to ensure you get the best experience. By continuing you consent to the use of cookie storage on your device.</p>
                <div data-destination="artists" class="start button">play my song</div>
           </div>
       </div>
    </div>

    <div class="section cookies <?php echo !$user["new"] && !$cookieSet ? "active" : ""?>">
            <div class="content">
                <div class="inner_content">
                    <img  class="logo"  src="images/logo@2x.png" width="90%" />
                    <p>You've got 3 song credits!<br>Choose wisely &#9786;</p>
                    <p>Don't worry you will get some more in due course so keep checking back.</p>
                    <div data-destination="artists" class="start button">Got it!</div>
            </div>
        </div>
    </div>
    <!--//  INTRO -->

    <!-- SELECT AN ARTIST -->
    <div class="section artists <?php echo !$user["new"] && $cookieSet && (!isset($_COOKIE['tab']) || (isset($_COOKIE['tab']) && $_COOKIE['tab'] != "songs")) ?  "active" : ""; ?>">
        <div class="content">
            <div class="inner_content">
                <?php
                if($db->hasMenu()) {
                ?>
                <a class="menu" href="<?php echo $db->getMenu(); ?>">
                    <span class="icon icon_menu"></span>
                    <span class="name">Menu</span>
                </a>
                <?php } ?>
                <img  class="logo"  src="images/logo@2x.png" width="90%" />
                <div class="credits">
                    <span class="credit-count">0</span>
                    <span class="credit-sub">Credits</span>
                </div>
                <div class="userName">
                    <?php if($user_name == "Guest") { ?>
                        <span class="welcome_guest_display">Welcome, Click here to let us know who you are!</span>
                    <?php } else  {?>
                        Welcome Back <span class="user_name_display"><?php echo $user_name; ?></span>!
                    <?php } ?>
                </div>
                <div class="header">
                    <div class="tab" data-destination="artists" >
                        Artist
                    </div>
                    <div class="tab" data-destination="songs" >
                        Track
                    </div>
                </div>

                <div class="list_scroller tall artists_list">
                <div class="fade_top"></div>
                <div class="inner" data-scrollable="y">
                    <?php /*
                     //Notice: We do this in ajax now to improve loading speed
                    $artists = $db->getArtists();
                    if(count($artists) > 0 ){
                        foreach ($artists as $k => $v) { ?>
                    <div id="<?php echo $k; ?>_section" class="letter_section">
                            <div class="letter_margin"><p><?php echo $k; ?></p></div><div class="artist_list">
                                <?php foreach ($v as $k1 => $v1) { ?>
                                <a href="#" data-artist="<?php echo stripslashes($v1['artist']); ?>" data-artist_id="<?php echo $v1['artist_id']; ?>" class="artist"><?php echo stripslashes($v1['artist']); ?></a>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } ?>
                    <?php }else{ ?>
                    <p>Currently no artists are available.</p>
                    <?php } */?>
                </div>
                 <div class="fade_bottom"></div>
                </div>
                <div class="suggestion_footer suggestTrack">
                    Cant find what you're looking for?
                    click here to suggest a track
                </div>
            </div>

            <div class="alphabety_selecty">
                <div class="alphabet_wrapper">
                    <span data-href="#A_section" class="alphabet">A</span>
                    <span data-href="#B_section" class="alphabet">B</span>
                    <span data-href="#C_section" class="alphabet">C</span>
                    <span data-href="#D_section" class="alphabet">D</span>
                    <span data-href="#E_section" class="alphabet">E</span>
                    <span data-href="#F_section" class="alphabet">F</span>
                    <span data-href="#G_section" class="alphabet">G</span>
                    <span data-href="#H_section" class="alphabet">H</span>
                    <span data-href="#I_section" class="alphabet">I</span>
                    <span data-href="#J_section" class="alphabet">J</span>
                    <span data-href="#K_section" class="alphabet">K</span>
                    <span data-href="#L_section" class="alphabet">L</span>
                    <span data-href="#M_section" class="alphabet">M</span>
                    <span data-href="#N_section" class="alphabet">N</span>
                    <span data-href="#O_section" class="alphabet">O</span>
                    <span data-href="#P_section" class="alphabet">P</span>
                    <span data-href="#Q_section" class="alphabet">Q</span>
                    <span data-href="#R_section" class="alphabet">R</span>
                    <span data-href="#S_section" class="alphabet">S</span>
                    <span data-href="#T_section" class="alphabet">T</span>
                    <span data-href="#U_section" class="alphabet">U</span>
                    <span data-href="#V_section" class="alphabet">V</span>
                    <span data-href="#W_section" class="alphabet">W</span>
                    <span data-href="#X_section" class="alphabet">X</span>
                    <span data-href="#Y_section" class="alphabet">Y</span>
                    <span data-href="#Z_section" class="alphabet">Z</span>
                </div>
            </div>

        </div>
    </div>
    <!-- // SELECT AN ARTIST -->

    <!-- SELECT A TRACK -->
    <div class="section songs <?php echo !$user["new"] && isset($_COOKIE['tab']) && $_COOKIE['tab'] == "songs" ?  "active" : ""; ?>">
        <div class="content">
            <div class="inner_content">
                <?php
                if($db->hasMenu()) {
                    ?>
                    <a class="menu" href="<?php echo $db->getMenu(); ?>">
                        <span class="icon icon_menu"></span>
                        <span class="name">Menu</span>
                    </a>
                <?php } ?>
                <img  class="logo"  src="images/logo@2x.png" width="90%" />
                <div class="credits">
                    <span class="credit-count">0</span>
                    <span class="credit-sub">Credits</span>
                </div>
                <div class="userName">
                    <?php if($user_name == "Guest") { ?>
                        <span class="welcome_guest_display">Welcome, Click here to let us know who you are!</span>
                    <?php } else  {?>
                        Welcome Back <span class="user_name_display"><?php echo $user_name; ?></span>!
                    <?php } ?>
                </div>
                <div class="header">
                    <div class="tab" data-destination="artists" >
                        Artist
                    </div>
                    <div class="tab" data-destination="songs" >
                        Track
                    </div>
                </div>

                <div class="list_scroller tall track_list">
                    <div class="fade_top"></div>
                    <div class="inner" data-scrollable="y">
                        <?php /*
                        //Notice: We do this in ajax now to improve loading speed
                        $songs = $db->getAllSongs();
                        if(count($songs) > 0 ){
                            foreach ($songs as $k => $v) { ?>
                                <div id="<?php echo $k; ?>_section" class="letter_section">
                                    <div class="letter_margin"><p><?php echo $k; ?></p></div><div class="artist_list">
                                        <?php foreach ($v as $k1 => $v1) { ?>
                                            <a href="#" data-track="<?php echo stripslashes($v1['track']); ?>" data-artist="<?php echo stripslashes($v1['artist']); ?>" data-track_id="<?php echo $v1['track_id']; ?>" class="artist"><?php echo stripslashes($v1['track']) . " by " . stripslashes($v1['artist']); ?></a>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php }else{ ?>
                            <p>Currently no artists are available.</p>
                        <?php } */?>
                    </div>
                    <div class="fade_bottom"></div>
                </div>
                <div class="suggestion_footer suggestTrack">
                    Cant find what you're looking for?
                    click here to suggest a track
                </div>
            </div>

            <div class="alphabety_selecty">
                <div class="alphabet_wrapper">
                    <span data-href="#A_section" class="alphabet">A</span>
                    <span data-href="#B_section" class="alphabet">B</span>
                    <span data-href="#C_section" class="alphabet">C</span>
                    <span data-href="#D_section" class="alphabet">D</span>
                    <span data-href="#E_section" class="alphabet">E</span>
                    <span data-href="#F_section" class="alphabet">F</span>
                    <span data-href="#G_section" class="alphabet">G</span>
                    <span data-href="#H_section" class="alphabet">H</span>
                    <span data-href="#I_section" class="alphabet">I</span>
                    <span data-href="#J_section" class="alphabet">J</span>
                    <span data-href="#K_section" class="alphabet">K</span>
                    <span data-href="#L_section" class="alphabet">L</span>
                    <span data-href="#M_section" class="alphabet">M</span>
                    <span data-href="#N_section" class="alphabet">N</span>
                    <span data-href="#O_section" class="alphabet">O</span>
                    <span data-href="#P_section" class="alphabet">P</span>
                    <span data-href="#Q_section" class="alphabet">Q</span>
                    <span data-href="#R_section" class="alphabet">R</span>
                    <span data-href="#S_section" class="alphabet">S</span>
                    <span data-href="#T_section" class="alphabet">T</span>
                    <span data-href="#U_section" class="alphabet">U</span>
                    <span data-href="#V_section" class="alphabet">V</span>
                    <span data-href="#W_section" class="alphabet">W</span>
                    <span data-href="#X_section" class="alphabet">X</span>
                    <span data-href="#Y_section" class="alphabet">Y</span>
                    <span data-href="#Z_section" class="alphabet">Z</span>
                </div>
            </div>

        </div>
    </div>
    <!-- // SELECT A TRACK -->
       
    <!-- SELECT A TRACK -->
    <div class="section tracks">
       <div class="content">
           <div class="inner_content">
                <img  class="logo"  src="images/logo@2x.png" width="90%" /> 
                <div class="header">Select a Track</div>

                <div class="list_scroller tracks_list">
                <div class="fade_top"></div>
                <div class="inner tracks_container" data-scrollable="y">
                    </div>
                     <div class="fade_bottom"></div>
                </div>

                <div data-destination="artists" class="button"><div class="icon icon_arrow_left"></div>Artists</div>
           </div>
       </div>
    </div>
    <!-- //SELECT A TRACK -->


    <!--send_to_jukebox Screen -->
    <div class="section send_to_jukebox">
       <div class="content">
           <div class="inner_content">
                <img  class="logo"  src="images/logo@2x.png" width="90%" /> 

                <div class="copy">
                <div class="subtitle"></div>
                <div class="title"></div>

                <p>Please select your table number:</p>
                <div id="tables_dropdown" style="margin-top: 20px;"></div>
                <p id="tables_error" style="color: red; font-weight: bold; display:none;">Use the drop down above to select your table number</p>

                <div id="send_div" onClick="sendSong();" class="disabled send_button purple button">Send to Jukebox</div>
                    <p>your music will now be queued up to play</p>
                </div>

                <div data-destination="tracks" class="button back_to_tracks_button"><div class="icon icon_arrow_left"></div>Tracks</div>
           </div>
       </div>
    </div>
    <!-- //send_to_jukebox Screen -->



            <!--message Screen -->
    <div class="section message">
       <div class="content">
           <div class="inner_content">
                <img  class="logo"  src="images/logo@2x.png" width="90%" /> 

                <div class="copy">
                <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh</p>

                <div class="field"><input data-default="your name" type="text" name="message_1" value="your name" /></div>
                <div class="field"><textarea data-default="your message" name="message_2">your message</textarea></div>

                </div>

                <div data-destination="message_attached" id="message_button" class="half button disabled" onClick="attachMessage();"><div class="icon icon_arrow_right"></div>Send</div>
                <div onClick="sendSong();" class="half button"><div class="icon icon_arrow_right"></div>Skip</div>
           </div>
       </div>
    </div>
    <!-- //message Screen -->



    <!--message_attached Screen -->
    <div class="section message_attached">
       <div class="content">
           <div class="inner_content">
                <img  class="logo"  src="images/logo@2x.png" width="90%" /> 

                <div class="copy">
                <div class="subtitle"></div>
                <div class="title"></div>

                <p class="message_selected"></p>
                <p class="name"></p>
                </div>

                <div onClick="sendSong();" class="half button"><div class="icon icon_arrow_right"></div>Send</div>
                <div data-destination="artists" class="half button"><div class="icon icon_close"></div>Cancel</div>
           </div>
       </div>
    </div>
    <!-- //message_attached Screen -->



    <!--Error Screen -->
    <div class="section error">
       <div class="content">
           <div class="inner_content">
                <img  class="logo"  src="images/logo@2x.png" width="90%" /> 

                <div class="copy">
                    <div class="icon icon_cross"></div>
                    <div class="title">Sorry</div>
                    <p>Track is already on tonight's playlist. Choose another or buy a drink at the bar.</p>
                </div>

                <div data-destination="tracks" class="artist_button button"><div class="icon icon_arrow_left"></div>New Track</div>
           </div>
       </div>
    </div>
    <!-- //Error Screen -->

    <!--Credit Explain Screen -->
    <div class="section credit_explain">
        <div class="content">
            <div class="inner_content">
                <img  class="logo"  src="images/logo@2x.png" width="90%" />

                <div class="copy">
                    <div class="icon icon_credits"></div>
                    <div class="title">Credits</div>
                    <p>Credits will be given to you periodically, after using all your credits you simply have to wait to get more.</p>
                    <p>You currently have <span class="credit_explain_count">0</span> credits</p>
                </div>

                <div data-destination="artists" class="artist_button button"><div class="icon icon_arrow_left"></div>Got It</div>
            </div>
        </div>
    </div>
    <!-- //Error Screen -->

        <!--Success Screen -->
    <div class="section success">
       <div class="content">
           <div class="inner_content">
                <img  class="logo"  src="images/logo@2x.png" width="90%" /> 

                <div class="copy">
                    <div class="icon icon_tick"></div>
                    <div class="title">Thank You</div>
                    <p>You have added a track to the playlist. If you want to add another song just tap the &quot;New Track&quot; button.</p>
                </div>

                <div data-destination="reload-page" class="artist_button button"><div class="icon icon_arrow_left"></div>New Track</div>
           </div>
       </div>
    </div>
    <!-- //Success Screen -->

    <!--No Credit Screen -->
    <div class="section no_credit">
        <div class="content">
            <div class="inner_content">
                <img  class="logo"  src="images/logo@2x.png" width="90%" />

                <div class="copy">
                    <div class="icon icon_credits"></div>
                    <div class="title">No Available Credit</div>
                    <p>You have used up your credit for now. Don't worry! you'll get more soon, so why not grab another drink whilst you wait.</p>
                </div>

                <div data-destination="heijm" class="artist_button button"><div class="icon icon_arrow_left"></div>Back</div>
            </div>
        </div>
    </div>
    <!-- //No Credit Screen -->

    <!--Set Name Screen -->
    <div class="section username">
        <div class="content">
            <div class="inner_content">
                <img  class="logo"  src="images/logo@2x.png" width="90%" />

                <div class="copy">
                    <div class="icon icon_user"></div>
                    <div class="title">Take a bow</div>
                    <p>Set your name, so that you can take credit for a great song choice!</p>
                    <div class="field"><input data-default="your name" type="text" name="user_name" value="your name" /></div>
                </div>

                <div data-destination="artists" class="half button"><div class="icon icon_arrow_left"></div>Cancel</div>
                <div onClick="updateUser();" data-destination="heijm" class="half button"><div class="icon icon_arrow_right"></div>Save</div>
            </div>
        </div>
    </div>
    <!-- //Set Name Screen -->

    <!--Set Name Screen -->
    <div class="section suggestions">
        <div class="content">
            <div class="inner_content">
                <img  class="logo"  src="images/logo@2x.png" width="90%" />

                <div class="copy">
                    <div class="icon icon_suggest"></div>
                    <div class="title">Suggest A Track</div>
                    <p>Let us know what you want to see on the jukebox, and well see what we can do to get it ready for your next visit</p>
                    <div class="field"><input data-default="Track Artist" type="text" name="suggest_artist" value="Track Artist" /></div>
                    <div class="field"><input data-default="Track Title" type="text" name="suggest_track" value="Track Title" /></div>
                </div>

                <div data-destination="artists" class="half button"><div class="icon icon_arrow_left"></div>Cancel</div>
                <div onClick="trackSuggest();" data-destination="heijm" class="half button"><div class="icon icon_arrow_right"></div>Send</div>
            </div>
        </div>
    </div>
    <!-- //Set Name Screen -->


    <!--<div class="popup song_not_available">
        <div class="content">
            <div class="inner_content">
                <div class="copy">
                    <p>This song has already been selected.</p>
                    <p>It will be available again soon.</p>

                    <div class="close_popup_button"><div class="icon icon_close"></div>Close</div>
                </div>
            </div>
        </div>

    </div>-->

    <div class="popup suggest_thanks">
        <div class="content">
            <div class="inner_content">
                <div class="copy">
                    <p>Thanks for your suggestion!</p>
                    <p>Look for your track next time you visit us</p>

                    <div class="close_popup_button"><div class="icon icon_close"></div>Close</div>
                </div>
            </div>
        </div>

    </div>

    <div class="ajax-loader">
        <img src="images/loader.gif" class="ajax-image">
        <span class="ajax-text">Loading</span>
    </div>

</body>
</html>