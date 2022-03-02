
(function ($, global) {

    'use strict';
    var Dragger = function (config) {
        var t = this;
        
        t.config = config;
        t.$container = t.config.$container;
        t.height = t.$container.outerHeight();

        t.$children = t.$container.find('span');

        t.onstart_callback = t.config.onstart_callback || $.noop;
        t.onmove_callback = t.config.onmove_callback || $.noop;
        t.onend_callback = t.config.onend_callback || $.noop;

        t.pos = 0;
        t.real_pos = 0;

        t.$touched_element = null;

        t.init();

        return t;
    };

    Dragger.prototype = {

        init: function () {
            var t = this;

            t.$container.on('touchstart', function(e) {
                e.preventDefault && e.preventDefault();
                var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
                t.count = 0;
                t.touching = true;
                
                t.check_touch(touch);

                try{t.onstart_callback(t);} catch(e){console.log(e);};

            });
                     
            t.$container.on('touchmove', function(e) {
                e.preventDefault && e.preventDefault();
                var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
                t.check_touch(touch);

                try{t.onmove_callback(t);} catch(e){console.log(e);};

            });

            t.$container.on('touchend', function(e) {
                e.preventDefault && e.preventDefault();

                if(t.touching==true){
                    t.touching = false;
                }
                try{t.onend_callback(t);} catch(e){console.log(e);};

            });

            return t;
        },

        check_touch: function(touch) {
            var t = this;
            t.count = touch.pageY - t.$container.position().top;

            t.pos = t.count/t.height *100;

            t.real_pos = t.pos;

            if(t.pos>95) {
                t.pos = 95 ;
            }

            if(t.real_pos>100) { t.real_pos =100;}

            if(t.pos<0) {
                t.pos = 0;
                t.real_pos = 0;
            }


            t.$children.each(function(index, item){   
                if($(item).position().top<t.count) {
                    t.$touched_element = $(item);
                }
            });

            return t.pos;
        }
    }

    // Exports
    global.Dragger = Dragger;

})(jQuery, this);
