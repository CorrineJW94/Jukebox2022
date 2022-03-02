var global = this;

(function ($, Modernizr, global) {

    'use strict';
    /*
       
    */
    var Example = function ($container) {
        var t = this;

        t.$container = $container;

        t.init();

        return t;
    };

    Example.prototype = {

        init: function () {
            var t = this;
            
            return t;
        }
    };

    // Exports
    global.Example = Example;

})(jQuery, Modernizr, this);




$(document).ready(function () {
    var t = global;

});
