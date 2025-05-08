<?php
/**
 * Custom blog index layout for Yak theme.
 */

add_action( 'genesis_loop', 'yak_blog_intro_content', 5 );
function yak_blog_intro_content() {
    if ( is_home() && $page_id = get_option( 'page_for_posts' ) ) {
        $content = get_post_field( 'post_content', $page_id );
        if ( ! empty( $content ) ) {
            echo '<div class="yak-blog-page-content entry-content">';
            echo apply_filters( 'the_content', $content );
            echo '</div>';
        }
    }
}






genesis();