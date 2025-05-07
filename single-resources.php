<?php

//* Remove the author box on single posts HTML5 Themes
remove_action( 'genesis_after_entry', 'genesis_do_author_box_single', 8 );

//* Remove the post content (requires HTML5 theme support)
// remove_action( 'genesis_entry_content', 'genesis_do_post_content' );

//* Remove the entry meta in the entry header (requires HTML5 theme support)
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );



add_action('genesis_entry_content', 'clb_publish_book_cover_section', 6);
function clb_publish_book_cover_section() {

    $book_section_to_publish = null;
    $button_area = null;

    $featured_image = get_the_post_thumbnail();
    $default_book_purchase_link = get_field('default_book_purchase_link');

    if( !$featured_image ) { return; }

    $book_section_to_publish .= '<div class="clb-book-info-wrapper">';

    if( $default_book_purchase_link ) {
        $book_section_to_publish .=  '<div class="book-card" data-tilt data-tilt-max="12" data-tilt-speed="400" data-tilt-glare data-tilt-max-glare="0.5" data-tilt-reset="false">
                                            <a href="' . $default_book_purchase_link . '">
                                                ' . $featured_image . '
                                            </a>
                                    </div>';
    } else {
        $book_section_to_publish .=  '<div class="book-card" data-tilt data-tilt-max="12" data-tilt-speed="400" data-tilt-glare data-tilt-max-glare="0.5" data-tilt-reset="false">
                                                ' . $featured_image . '
                                    </div>';
    }

    if( have_rows('book_links') ) {

        while ( have_rows('book_links') ) {
        
            the_row();

                $button = null;
            
                $button_text = get_sub_field('button_text');
                $button_icon = get_sub_field('button_icon');
                $button_url = get_sub_field('button_url');

                if( $button_text && $button_url ) {
                    $button = '<div class="clb-button-wrapper"><a href="' . $button_url . '" target="_blank" class="button">' . $button_icon . $button_text . '</a></div>';
                    $button_area .= $button;
                }
            
        }
        
    }

    if( $button_area ) { $button_area = '<div class="clb-button-area-wrapper">' . $button_area . '</div>'; }

    $book_section_to_publish .= $button_area . '</div>';

    echo $book_section_to_publish;

}




genesis();