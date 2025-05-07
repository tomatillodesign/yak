<?php


add_action('genesis_before', 'clb_front_page_setup');
function clb_front_page_setup() {

    // new custom slider background //////////////

    $front_page_1_slider = get_field('homepage_background_images');
    $randomize_gallery = get_field('randomize_gallery');
    if( $randomize_gallery ) { shuffle($front_page_1_slider); }

    if ( $front_page_1_slider ) {
        // enqueue scripts for backstretch
        wp_enqueue_script( 'backstretch', get_stylesheet_directory_uri() . '/js/backstretch.min.js', array( 'jquery' ), '2.1.18', true );
        wp_enqueue_script( 'backstretch-set-home', get_stylesheet_directory_uri() . '/js/backstretch-set-home.js' , array( 'backstretch' ), '1.0.0', true );

        // "display" Front Page 1 slider - i.e., get the array of URLs of slide images using the soliloquy_output filter used earlier and store it in a variable
        //$slide_image_urls = soliloquy( 'front-page-1', 'slug', array(), true  );

        // pass an array named "BackStretchImg" to the JS file loaded by "backstretch-set" handle i.e., to backstretch-set.js. We are setting "src" key of this array to the above array variable
        wp_localize_script( 'backstretch-set-home', 'BackStretchImg', $front_page_1_slider );
    }

    /////////////////////////////////////////////

}



genesis();