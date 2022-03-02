

(function ($, global) {

    'use strict';
    /*
       
    */
    var LoadPages = function (data, $container) {
        var t = this;

        t.data = data;
        t.$container = $container;

        t.page_count = SITE_DATA.siteSections.length;

        t.load(0);

        return t;
    };

    LoadPages.prototype = {

        load: function (currentIndex) {

	        var t = this,
	        $page_section = $('<div class="page_section '+SITE_DATA.siteSections[currentIndex].id+'"></div>'),
			the_url = SITE_DATA.siteSections[currentIndex].url;

	        $.ajax({
	            url: the_url,
	            dataType: "html"
	        }).done(function(data) {
	            $page_section.html(data);

        		t.$container.append($page_section);

	            if (currentIndex<t.page_count-1) {
	                currentIndex++;
	                t.load(currentIndex);
	            }else {
	                $(window).trigger('pages_loaded');
	            }

	        }).fail(function(e){
	        	console.log(e);
	        });


            return t;
        }
    };

    // Exports
    global.LoadPages = LoadPages;

})(jQuery, this);

