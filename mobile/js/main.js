var global = this;
global.touchmove_poller = 0;
(function ($, global) {

    'use strict';
    /*
       
    */
    var ImagesLoaded = function ($images_to_load, trigger) {
        var t = this;

        t.$images_to_load = $images_to_load[0];
        t.trigger = trigger;

        for (var i = 1; i < $images_to_load.length; i++) {
            t.$images_to_load = t.$images_to_load.add($images_to_load[i]);

        }

        t.init();

        return t;
    };

    ImagesLoaded.prototype = {

        init: function () {

        var t = this;
        t.finishedCount = 0;
        t.imageCount = t.$images_to_load.length;


        t.$images_to_load.each(function(index, item) {

            $(item).waitForImages({
                finished: function () {
                    t.finishedCount++;
                    if ((300/(t.imageCount-t.finishedCount))<=0) {
                    $('.bar').css({'width':"0px"});

                    }else {
                    $('.bar').css({'width':(300/(t.imageCount-t.finishedCount)) + "px"});
                }

                    if (t.imageCount<=t.finishedCount) {
                        $(window).trigger(t.trigger);
                    }
                },
                waitForAll: true
            });

        });

            return t;
        }
    };

    // Exports
    global.ImagesLoaded = ImagesLoaded;

})(jQuery, this);

(function ($, global) {

    'use strict';
    /*
       
    */
    var PageManager = function (pages, trigger) {
        var t = this;

        t.pages = pages;
        t.$pages = $(pages);
        t.trigger = trigger;

        return t;
    };

    PageManager.prototype = {

        switch: function (section_name) {
            var t = this; 

             t.$pages.removeClass('active');
             $(t.pages+'.'+section_name).addClass('active');

            $(window).trigger(t.trigger);

            return t;
        }
    };

    // Exports
    global.PageManager = PageManager;

})(jQuery, this);



(function ($, global) {

    'use strict';
    /*
       
    */
    var FormInput = function () {
        var t = this;

        t.$inputArray = $('.field [data-default!=""]');

        t.init();

        return t;
    };

    FormInput.prototype = {

        init: function () {
            var t = this; 

            t.$inputArray.each(function(index, item) {
                $(item).on('focus', function() {

                    if ($(item).val()==$(item).data('default')) {
                        $(item).val('');
                    }
                });

                $(item).on('blur', function() {

                    if ($(item).val()=="") {
                        $(item).val($(item).data('default'));
                    }
                });

   

            });

            return t;
        }

    };

    // Exports
    global.FormInput = FormInput;

})(jQuery, this);

(function ($, global) {

    'use strict';
    /*
       
    */
    var DataHandler = function (base_url) {
        var t = this;
        t.base_url = base_url;
        //t.$pages = $pages;
        //t.init();

        return t;
    };

    DataHandler.prototype = {

        getArtists: function () {
            var t = this;
            return;
        },

        getTracks: function (artist_id) {
            var t = this;     
            var $songs = $('div.tracks_container');
            var song_html ='';
            
            $songs.html('');
            
            $.get(t.base_url+'jukebox/listener/mobileGetSongsListener.php', {artist_id: artist_id}, function (response){
                $.each(response, function(index, obj){
                    var el = document.createElement('div');
                    //if(obj.available == 0){
                        //song_html = '<div class="number_margin"><p>'+(index+1)+'</p></div><div class="name"><a href="#" class="song_unavailable" onclick="song_not_available();" style="color: #ccc;">'+stripslashes(obj.title)+'</a></div><div class="duration" style="color: #ccc;">'+obj.duration+'</div>';
                    //}else{
                        song_html = '<div class="number_margin"><p>'+(index+1)+'</p></div><div class="name"><a href="#" class="artist_track" data-track_id="'+obj.song_id+'" >'+stripslashes(obj.title)+'</a></div><div class="duration">'+obj.duration+'</div>';
                    //}
                    $(el).addClass('track').html(song_html);
                    $songs.append(el);
                });
                var clicked = false;
                var init = null;
                $songs.find(".artist_track").on('touchstart', function(e) {
                    clicked = true;
                    init = {
                      x: e.originalEvent.touches[0].pageX,
                      y: e.originalEvent.touches[0].pageY
                    };
                    setTimeout(function() {
                        clicked = false;
                    }, 200);
                });
                $songs.find(".artist_track").on('touchend', function(e) {
                  console.log(e.originalEvent.changedTouches[0]);
                  if(clicked === true && e.originalEvent.changedTouches[0] &&
                    init.x - 10 < e.originalEvent.changedTouches[0].pageX &&
                    init.x + 10 > e.originalEvent.changedTouches[0].pageX &&
                    init.y - 10 < e.originalEvent.changedTouches[0].pageY &&
                    init.y + 10 > e.originalEvent.changedTouches[0].pageY) {
                    getTables();
                    selectTrack($(this).data('track_id'), 'send_to_jukebox');
                  }
                  clicked = false;
                });
                $songs.find(".artist_track").on('click', function(e) {
                    e.preventDefault();
                    getTables();
                    selectTrack($(this).data('track_id'), 'send_to_jukebox');
                });
                $(window).trigger('section_callback');
            }, 'json');
            return t;
        },

        sendData: function (song_id) {
            var t = this;     

            return data;
        }

    };

    // Exports
    global.DataHandler = DataHandler;

})(jQuery, this);


function song_not_available(){
  showPopup('song_not_available');
}

var data, pageManager, t, songsLoaded = false, artistsLoaded = false;
$(document).ready(function () {
    t = global;
    t.scrollers= new Array();

        var opts = { lines: 13, length: 20, width: 10, radius: 30, corners: 1, rotate: 0, direction: 1, color: '#fff', speed: 1, trail: 60, 
          shadow: false, hwaccel: false, className: 'spinner', zIndex: 2e9, top: '350px', left: 'auto'  
        };
    var target = document.getElementById('spinner');
    var spinner = new Spinner(opts).spin(target);


// PAGE LOADED
    new ImagesLoaded([$('body'),$('.logo') ],'images_loaded');


    /*var elements = document.querySelectorAll('[data-scrollable],[data-zoomable]'), element;
    for (var i = 0; i < elements.length; i++) {
        element = elements[i];
        var scrollerObj = new EasyScroller(element, {
            scrollingX: false,
            scrollingY: true,
            scrollingComplete: function(){
                
                for (var i in t.scrollers) {
                    if($(t.scrollers[i].container).hasClass('artists_list') && $('.section.artists').hasClass('active')) {
                        $.cookie('scroll_pos', t.scrollers[i].scroller.__scrollTop+"");
                    }
                }

            }

        });
        t.scrollers.push(scrollerObj);

        for (var i in t.scrollers) {
            if($(t.scrollers[i].container).hasClass('artists_list')) {
                var loadPos = parseFloat($.cookie('scroll_pos')) || 0;
                if(loadPos<0){
                    loadPos=0;
                }
                t.scrollers[i].scroller.__scrollTop=loadPos;
            }
        }


    };*/

    //Handles switching pages
    pageManager = new PageManager('.section','section_callback');

   new FormInput();

    $('.artist').data('destination','tracks');

    data = new DataHandler($('[name=base_url]').val());
    
   $('.button').on('click', function(e) {
        e.preventDefault();
        
        if($(this).data('destination') === 'reload-page'){
            window.location.href = $('[name=base_url]').val()+'jukebox/mobile/';
            return false;
        }
        
        if( ! $(this).hasClass('disabled')){
            //close pages, open page labeled "destination" from button
            var dest = $(this).data('destination');
            if(dest == 'heijm') {
                dest = getCookie('tab') == 'songs' ? 'songs' : 'artists';
            }

            console.log("no disabled class");

            pageManager.switch(dest);
        }
   });

    $('.credits').on('click', showCreditExplain);
    $('.userName').on('click', showNameSet);
    $('.suggestTrack').on('click', showSuggestion);

    /*$('.tracks').on('click', function(e) {
        e.preventDefault();
        pageManager.switch($(this).data('destination'));
    });*/

    $('.tab').on('click', function(e) {
        e.preventDefault();
        //close pages, open page labeled "destination" from artist
        if ($(this).data('destination') === "songs") {
            $('.section.send_to_jukebox .button').data('destination', 'songs');
            if (!songsLoaded) {
                songsLoaded = true;
                getTrackList();
            }
            document.cookie = "tab=songs";
        } else {
            $('.section.send_to_jukebox .button').data('destination', 'tracks');
            if (!artistsLoaded) {
                artistsLoaded = true;
                getArtistList();
            }
            document.cookie = "tab=artists";
        }
        pageManager.switch($(this).data('destination'));
    });
   
   $('[name=message_1]').keyup(function(e) {
        var message_body = $('[name=message_2]').val();
        var message_title = $('[name=message_1]').val();
        
        if(message_title === 'your name' || message_title === '' || message_body === 'your message' || message_body === ''){
            $('#message_button').addClass('disabled');
        }else{
            $('#message_button').removeClass('disabled');
        }
   });
   
   $('[name=message_2]').keyup(function(e) {
        var message_body = $('[name=message_2]').val();
        var message_title = $('[name=message_1]').val();
        
        if(message_title === 'your name' || message_title === '' || message_body === 'your message' || message_body === ''){
            $('#message_button').addClass('disabled');
        }else{
            $('#message_button').removeClass('disabled');
        }
   });


// IMAGES LOADED
    $(window).on('images_loaded', function(e) {
        if (Modernizr.csstransitions) {
            $('.loader').css({'top':'-520%'});
        }else {
           $('.loader').animate({'top':'-520%'});
        }
    }); 


//Listener to reflow lists when contents changes, height of lists change. Must be done when list is display:block
    $(window).on('section_callback', function(e) {

        for (var i in t.scrollers) {

            if($(t.scrollers[i].container).hasClass('artists_list')) {
                var loadPos = parseFloat($.cookie('scroll_pos')) || 0;
                if(loadPos<0){
                    loadPos=0;
                }
                t.scrollers[i].scroller.__scrollTop=loadPos;
            }

            if($(t.scrollers[i].container).hasClass('tracks_list')) {
                var loadPos = parseFloat($.cookie('scroll_pos_track')) || 0;
                if(loadPos<0){
                    loadPos=0;
                }
                t.scrollers[i].scroller.__scrollTop=loadPos;
            }
            
            t.scrollers[i].reflow();  

        }

    });


    if (navigator.userAgent.match(/(iPad|iPhone|iPod touch);.*CPU.*OS 7_\d/i)) {
       // alert('yes: '+window.innerHeight)
       // $('.section, body').height(window.innerHeight);
       // window.scrollTo(0, 0);
    }

    setupScrolling();
    //check user available credit
    getAvailableCredits();
    //get artist list
    if(getCookie('tab') === 'songs') {
        $('.section.send_to_jukebox .button').data('destination', 'songs');
        getTrackList();
        songsLoaded = true;
    } else {
        getArtistList();
        $('.section.send_to_jukebox .button').data('destination', 'tracks');
        artistsLoaded = true;
    }
});

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) === 0) {
            return c.substring(name.length,c.length);
        }
    }
    return "";
}

function setupScrolling() {
    var elements = document.querySelectorAll('[data-scrollable],[data-zoomable]'), element;
    for (var i = 0; i < elements.length; i++) {
        element = elements[i];
        var scrollerObj = new EasyScroller(element, {
            scrollingX: false,
            scrollingY: true,
            scrollingComplete: function(){

                for (var i in t.scrollers) {
                    if($(t.scrollers[i].container).hasClass('artists_list') && $('.section.artists').hasClass('active')) {
                        $.cookie('scroll_pos', t.scrollers[i].scroller.__scrollTop+"");
                    }
                    if($(t.scrollers[i].container).hasClass('track_list') && $('.section.songs').hasClass('active')) {
                        $.cookie('scroll_pos_track', t.scrollers[i].scroller.__scrollTop+"");
                    }
                }

            }

        });
        t.scrollers.push(scrollerObj);

        for (var i in t.scrollers) {
            if($(t.scrollers[i].container).hasClass('artists_list')) {
                var loadPos = parseFloat($.cookie('scroll_pos')) || 0;
                if(loadPos<0){
                    loadPos=0;
                }
                t.scrollers[i].scroller.__scrollTop=loadPos;
            }
            if($(t.scrollers[i].container).hasClass('track_list')) {
                var loadPos = parseFloat($.cookie('scroll_pos_track')) || 0;
                if(loadPos<0){
                    loadPos=0;
                }
                t.scrollers[i].scroller.__scrollTop=loadPos;
            }
        }

        t.dragger = new Dragger({$container:$('.section.artists .alphabety_selecty .alphabet_wrapper'),
            onmove_callback: updateArtistScrollPosition,
            onend_callback: updateArtistEndTouch});

        t.dragger2 = new Dragger({$container:$('.section.songs .alphabety_selecty .alphabet_wrapper'),
            onmove_callback: updateTrackScrollPosition,
            onend_callback: updateTrackEndTouch});
    };
}

function updateScrollers() {
    for (var i in t.scrollers) {

        if($(t.scrollers[i].container).hasClass('artists_list')) {
            var loadPos = parseFloat($.cookie('scroll_pos')) || 0;
            if(loadPos<0){
                loadPos=0;
            }
            t.scrollers[i].scroller.__scrollTop=loadPos;
        }
        if($(t.scrollers[i].container).hasClass('track_list')) {
            var loadPos = parseFloat($.cookie('scroll_pos_track')) || 0;
            if(loadPos<0){
                loadPos=0;
            }
            t.scrollers[i].scroller.__scrollTop=loadPos;
        }

        t.scrollers[i].reflow();

    }
}

function getArtistList() {
    showLoader();
    $.get($('[name=base_url]').val()+'jukebox/listener/mobileGetArtistsListener.php', {}, function (response){
        for(var k in response) {
            var v = response[k];
            var section = document.createElement('div');
            section.id = k+"_section";
            section.className = "letter_section";

            var margin = document.createElement('div');
            margin.className = "letter_margin";

            var letterp = document.createElement("p");
            $(letterp).text(k);

            margin.appendChild(letterp);

            var list = document.createElement("div");
            list.className = "artist_list";

            for(var k1 in v) {
                var v1 = v[k1];
                var artist = document.createElement('a');
                artist.href = "#";
                artist.className = "artist";

                $(artist).data('artist', v1.artist)
                    .data('artist_id', v1['artist_id'])
                    .data('destination', 'tracks')
                    .text(stripslashes(v1.artist));
                list.appendChild(artist);
            }
            section.appendChild(margin);
            section.appendChild(list);
            $(".section.artists .inner").append(section);
        }
        var clicked = false;
        var init = null;
        var artistsSection = $('.section.artists .artist');
        artistsSection.on('touchstart', function(e) {
          clicked = true;
          init = {
            x: e.originalEvent.touches[0].pageX,
            y: e.originalEvent.touches[0].pageY
          };
          setTimeout(function() {
              clicked = false;
          }, 200);
        });
        artistsSection.on('touchend', function(e) {
          console.log(e.originalEvent.changedTouches[0]);
          if(clicked === true && e.originalEvent.changedTouches[0] &&
            init.x - 10 < e.originalEvent.changedTouches[0].pageX &&
            init.x + 10 > e.originalEvent.changedTouches[0].pageX &&
            init.y - 10 < e.originalEvent.changedTouches[0].pageY &&
            init.y + 10 > e.originalEvent.changedTouches[0].pageY) {
            data.getTracks($(this).data('artist_id'));
            pageManager.switch($(this).data('destination'));
          }
          clicked = false;
        });
        artistsSection.on('click', function(e) {
            e.preventDefault();
            data.getTracks($(this).data('artist_id'));

            //close pages, open page labeled "destination" from artist
            pageManager.switch($(this).data('destination'));
        });

        updateScrollers();
        hideLoader();
    }, 'json');
}

function getTrackList() {
    showLoader();
    $.get($('[name=base_url]').val()+'jukebox/listener/mobileGetTracksListener.php', {}, function (response){
        for(var k in response) {
            var v = response[k];
            var section = document.createElement('div');
            section.id = k+"_section";
            section.className = "letter_section";

            var margin = document.createElement('div');
            margin.className = "letter_margin";

            var letterp = document.createElement("p");
            $(letterp).text(k);

            margin.appendChild(letterp);

            var list = document.createElement("div");
            list.className = "track_list";

            for(var k1 in v) {
                var v1 = v[k1];
                var track = document.createElement('a');
                track.href = "#";
                track.className = "song";

                $(track).data('artist', v1.artist)
                    .data('artist_id', v1['artist_id'])
                    .data('track', v1.track)
                    .data('track_id', v1['track_id'])
                    .text(stripslashes(v1.track)+" by "+stripslashes(v1.artist));
                list.appendChild(track);
            }
            section.appendChild(margin);
            section.appendChild(list);
            $(".section.songs .inner").append(section);
        }
        var clicked = false;
        var init = null;
        var songsSection = $('.section.songs .song');
        songsSection.on('touchstart', function(e) {
            clicked = true;
            init = {
                x: e.originalEvent.touches[0].pageX,
                y: e.originalEvent.touches[0].pageY
            };
            setTimeout(function() {
                clicked = false;
            }, 250);
        });
        songsSection.on('touchend', function(e) {
            console.log(e.originalEvent.changedTouches[0]);
            if(clicked === true && e.originalEvent.changedTouches[0] &&
              init.x - 10 < e.originalEvent.changedTouches[0].pageX &&
              init.x + 10 > e.originalEvent.changedTouches[0].pageX &&
              init.y - 10 < e.originalEvent.changedTouches[0].pageY &&
              init.y + 10 > e.originalEvent.changedTouches[0].pageY) {
              getTables();  
              selectTrack($(this).data('track_id'), 'send_to_jukebox');
            }
            clicked = false;
        });
        songsSection.on('click', function(e) {
            e.preventDefault();
            getTables();
            selectTrack($(this).data('track_id'), 'send_to_jukebox');
        });

        updateScrollers();
        hideLoader();
    }, 'json');
}

function getAvailableCredits() {
    var uid = $('[name=uid]').val();
    $.get($('[name=base_url]').val()+'jukebox/listener/mobileGetCreditListener.php', {uid: uid}, function (response){
        $('.credit-count, .credit_explain_count').text(response.available);
        //check for credits every 5 minutes
        setTimeout(getAvailableCredits, 300000);
    }, 'json');
}

function showPopup(name) {
  $('.popup.'+name).addClass('active');
  var close = function(e) {
    e.preventDefault();
    $('.popup.'+name).removeClass('active');
    $('.popup.' + name + ' .close_popup_button').off('click', close);
  };
  $('.popup.' + name + ' .close_popup_button').on('click', close);
}

function showCreditExplain() {
    pageManager.switch('credit_explain');
}

function showNameSet() {
  pageManager.switch('username');
}

function showSuggestion() {
  pageManager.switch('suggestions');
}

function updateUser() {
    var uid = $('[name=uid]').val();
    var name = $('[name=user_name]').val();
    var nameEL = $('.userName .user_name_display');

    $.get($('[name=base_url]').val()+'jukebox/listener/mobileSetNameListener.php', {uid: uid, name: name}, function (response){
        if(response.msg == 'success'){
            console.log(response);
            var name = response.name;
            if(nameEL.length < 1) {
                $('.userName .welcome_guest_display').parent().html('Welcome <span class="user_name_display">' + name + '</span>!');
            } else {
                nameEL.text(name);
            }
        } else if(response.msg == 'error'){
            pageManager.switch('error');
        }
    });
}

function trackSuggest() {
  var uid = $('[name=uid]').val();
  var track = $('[name=suggest_track]').val();
  var artist = $('[name=suggest_artist]').val();

  $.get($('[name=base_url]').val()+'jukebox/listener/mobileSuggestTrack.php', {uid: uid, track: track, artist: artist}, function (response){
    if(response.msg == 'success'){
      showPopup('suggest_thanks');
    } else if(response.msg == 'error'){
      pageManager.switch('error');
    }
  });

}

function selectTrack(song_id, destination) {
    //get track info
    $.get($('[name=base_url]').val()+'jukebox/listener/mobileGetSongDetailsListener.php', {song_id: song_id}, function (response){
      var sendToJB = $('.send_to_jukebox')
      sendToJB.find('.copy .subtitle').html(stripslashes(response.artist));
      sendToJB.find('.copy .title').html(stripslashes(response.title));

      sendToJB.find('.copy .subtitle').html(stripslashes(response.artist));
      sendToJB.find('.copy .title').html(stripslashes(response.title));
        
        $('[name=selected_track]').val(song_id);
    }, 'json');
    
    //close pages, open page labeled "destination" from track
    pageManager.switch(destination);
}

function attachMessage(){
    var message_body = $('[name=message_2]').val();
    var message_title = $('[name=message_1]').val();
    
    if(message_title == 'your name' || message_title == '' || message_body == 'your message' || message_body == ''){
        message_body = '';
        message_title = '';
    }
    var attachMessage = $('.message_attached');
    attachMessage.find('.message_selected').html(message_body);
    attachMessage.find('.name').html(message_title);
}

function getTables(){
    var t = this;   
    var $tables = $('#tables_dropdown');
    var table_html ='';
    $tables.html('');

    var el = document.createElement('select');
    el.setAttribute('id', 'tables');
    el.setAttribute('name', 'tables');
    el.setAttribute('onchange', 'toggleSendButton();');
    el.setAttribute('style', 'display: inline-block; background-color: #DBDBDB;');

    var defaultOptionEl = document.createElement('option');
    defaultOptionEl.setAttribute('value', 0);
    defaultOptionEl.innerText = "...";
    el.appendChild(defaultOptionEl);

    $.get($('[name=base_url]').val()+'jukebox/listener/mobileGetTablesListener.php', null, function (response){
         $.each(response, function(index, obj){
            var optionEl = document.createElement('option');
            optionEl.setAttribute('value', obj.number);
            optionEl.innerText = obj.number;
            el.appendChild(optionEl);
        });
    }, 'json');

    $tables.append(el);
    return t;
}

function toggleSendButton(){
    var tableid = document.getElementById('tables').value;
    var sendDiv = document.getElementById('send_div');

    if (tableid == 0){
        sendDiv.classList.add('disabled');
    } else {
        sendDiv.classList.remove('disabled');
    }
}

function sendSong(){
    var pageManager = new PageManager('.section','section_callback');
    var song_id = $('[name=selected_track]').val();
    var name = $('[name=message_1]').val();
    var message = $('[name=message_2]').val();
    var uid = $('[name=uid]').val();
    var table_id = $('#tables').val();
    
    if(name == 'your name' || name == '' || message == 'your message' || message == ''){
        message = '';
        name = '';
    }

    if (table_id == 0) {
        return false;
    }

    $.get($('[name=base_url]').val()+'jukebox/listener/mobileAddSongListener.php', {table_id: table_id, song_id: song_id, message_1:name, message_2: message, uid: uid}, function (response){
        if(response.msg === 'success'){
            pageManager.switch('success');
        } else if(response.msg === 'error'){
            pageManager.switch('error');
        } else if(response.msg === 'no credit') {
            pageManager.switch('no_credit');
        }
    }, 'json');
    
    //clean up everything
    $('[name=selected_track]').val('');
    $('[name=message_1]').val('your name');
    $('[name=message_2]').val('your message');
    
    $('.send_to_jukebox').find('.copy .subtitle').html('');
    $('.send_to_jukebox').find('.copy .title').html('');
    $('.message_attached').find('.copy .subtitle').html('');
    $('.message_attached').find('.copy .title').html('');
}


function updateArtistScrollPosition(t){
        try{
            if(window.artist_letter_href!=t.$touched_element.data('href') ) {
                window.artist_letter_href = t.$touched_element.data('href');
                $('.section.artists .alphabet_wrapper span').removeClass('active');
                t.$touched_element.addClass('active');
                if($('.section.artists .list_scroller.artists_list .inner '+window.artist_letter_href).length>0) {
                    var top_pos = $('.section.artists .list_scroller.artists_list .inner '+window.artist_letter_href).offset().top;
                    for (var i in window.scrollers) {
                        if($(window.scrollers[i].container).hasClass('artists_list')) {
                            top_pos = top_pos +window.scrollers[i].scroller.__scrollTop - $(window.scrollers[i].container).offset().top;

                            if(top_pos<0){
                                top_pos=0;
                            }
                            var scroll_limit = window.scrollers[i].scroller.__contentHeight - window.scrollers[i].scroller.__clientHeight;

                            if(top_pos> scroll_limit) {
                                top_pos = scroll_limit;
                            }

                            window.scrollers[i].scroller.__scrollTop=top_pos;
                            window.scrollers[i].reflow();  
                        }
                    }
                }
            }
        }catch(e){console.log(e)}
}

function updateTrackScrollPosition(t){
    try{
        if(window.artist_letter_href!=t.$touched_element.data('href') ) {
            window.artist_letter_href = t.$touched_element.data('href');
            $('.section.songs .alphabet_wrapper span').removeClass('active');
            t.$touched_element.addClass('active');
            if($('.section.songs .list_scroller.track_list .inner '+window.artist_letter_href).length>0) {
                var top_pos = $('.section.songs .list_scroller.track_list .inner '+window.artist_letter_href).offset().top;
                for (var i in window.scrollers) {
                    if($(window.scrollers[i].container).hasClass('track_list')) {
                        top_pos = top_pos +window.scrollers[i].scroller.__scrollTop - $(window.scrollers[i].container).offset().top;

                        if(top_pos<0){
                            top_pos=0;
                        }
                        var scroll_limit = window.scrollers[i].scroller.__contentHeight - window.scrollers[i].scroller.__clientHeight;

                        if(top_pos> scroll_limit) {
                            top_pos = scroll_limit;
                        }

                        window.scrollers[i].scroller.__scrollTop=top_pos;
                        window.scrollers[i].reflow();
                    }
                }
            }
        }
    }catch(e){console.log(e)}
}

function updateArtistEndTouch(t){
    setTimeout(function(){
        $('.section.artists .alphabet_wrapper span').removeClass('active');
    },200);
}

function updateTrackEndTouch(t){
    setTimeout(function(){
        $('.section.songs .alphabet_wrapper span').removeClass('active');
    },200);
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

function showLoader() {
    //$(".ajax-loader").show();
}

function hideLoader() {
    //$(".ajax-loader").hide();
}