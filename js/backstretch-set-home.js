// Thanks to Tonya. https://wpdevelopersclub.slack.com/archives/code-php/p1470103914000440

console.log("backstretch set home 318pm");

;(function ( $, window, document, undefined ) {
    'use strict'

    var $frontPageElement;

    function initImg() {
        if ( typeof BackStretchImg !== 'object' || BackStretchImg.src == '' ) {
            return;
        }

        //var images = $.parseJSON( BackStretchImg.src );
        var images = BackStretchImg;

        if ( ! $.isArray( images ) ) {
            return;
        }

        console.log( images );

        $frontPageElement.backstretch( images,{
            duration: 5000,
            fade: 1500,
        });
    }

    $( document ).ready( function () {
        $frontPageElement = $( ".clb-homepage-hero" );

        //console.log( BackStretchImg );

        if ( typeof $frontPageElement === "undefined" ) {
            return;
        }

        initImg();

    } );

}( jQuery, window, document ));
