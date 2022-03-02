var global = this;

$(document).ready(function () {
    var t = global;
    setTimeout(function(){
	    $('.marquee').marquee({
		    //speed in milliseconds of the marquee
		    duration: 6000,
		    //gap in pixels between the tickers
		    gap: 400,
		    //time in milliseconds before the marquee will start animating
		    delayBeforeStart: 0,
		    //'left' or 'right'
		    direction: 'left',
		    //true or false - should the marquee be duplicated to show an effect of continues flow
		    duplicated: true
		});

    },1000);

});
