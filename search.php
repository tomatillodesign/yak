<?php
/*
* CLB Custom Genesis search results page template
*
*/

// Use with SearchWP and also SearchWP Term Highlight plugins

add_action('genesis_before_content', 'clb_add_search_form', 14);
function clb_add_search_form() {

	echo '<div class="yak-search-results-page-header">';

	?>
		<h1 class="search-title">
			<?php _e( 'Search results for', 'locale' ); ?>: "<?php the_search_query(); ?>"
		</h1>


	<?php

	get_search_form();

	echo '</div>';

}



// Remove Genesis defaults on search pages
add_action('genesis_before_loop', function () {
	remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
		remove_action( 'genesis_entry_content', 'genesis_do_post_content' );
		remove_action( 'genesis_entry_content', 'genesis_do_post_image' );
		remove_action( 'genesis_entry_content', 'genesis_do_post_content_nav' );
		remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
		remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );
});


add_action('genesis_entry_content', 'clb_searchwp_custom_excerpt');


//* Remove the post content (requires HTML5 theme support)
remove_action( 'genesis_entry_content', 'genesis_do_post_content' );

//* Remove the entry meta in the entry footer (requires HTML5 theme support)
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

genesis();
