<?php
/*
* CLB Custom Genesis search results page template
*
*/

// Use with SearchWP and also SearchWP Term Highlight plugins

add_action('genesis_before_content', 'clb_search_results');
function clb_search_results() {
	?>
		<h1 class="search-title">
			<?php _e( 'Search results for', 'locale' ); ?>: "<?php the_search_query(); ?>"
		</h1>


	<?php
}

add_action('genesis_before_loop', 'clb_add_search_form');
function clb_add_search_form() {

	get_search_form();

}


// Careful, this code only works with SearchWP ver 3, NOT 4

// add_action('genesis_entry_content', 'clb_searchwp_custom_excerpt');
// function clb_searchwp_custom_excerpt() {
//
// 	$ugly_permalink = get_permalink();
//
// 	if( is_ssl() ) {
// 		$cut = 8;
// 	} else {
// 		$cut = 7;
// 	}
//
// 	$permalink = substr($ugly_permalink, $cut, -1);
// 	echo '<div class="search-results-permalink"><a href="' . $ugly_permalink . '">' . $permalink . '</a></div>';
//
// 	// echo the excerpt (designed to be used IN PLACE OF the_excerpt
// 	if( function_exists( 'searchwp_term_highlight_the_excerpt_global' ) ) {
// 		echo '<div class="searchwp-excerpt">';
// 		searchwp_term_highlight_the_excerpt_global();
// 		echo '...</div>';
// 	}
//
// }


// New code updated to work with both SearchWP ver 3 AND 4
add_action('genesis_entry_content', 'clb_searchwp_custom_excerpt');

function clb_searchwp_custom_excerpt() {
	global $post;

	$ugly_permalink = get_permalink();
	$permalink = $rest = substr($ugly_permalink, 8, -1);
	echo '<div class="search-results-permalink"><a href="' . $ugly_permalink . '">' . $permalink . '</a></div>';

	// echo the excerpt (designed to be used IN PLACE OF the_excerpt
	if( function_exists( 'searchwp_term_highlight_the_excerpt_global' ) ) {
		echo '<div class="searchwp-excerpt">';
			searchwp_term_highlight_the_excerpt_global();
		echo '...</div>';
	} else if ( class_exists( '\\SearchWP\\Entry' ) ) {
		// Get the excerpt.
		$source  = \SearchWP\Utils::get_post_type_source_name( $post->post_type );
		$entry   = new \SearchWP\Entry( $source, $post->ID, false, false );
		$excerpt = \SearchWP\Sources\Post::get_global_excerpt( $entry, get_search_query() );

		// Highlight the excerpt.
		$highlighter = new \SearchWP\Highlighter();
		$excerpt     = $highlighter::apply( $excerpt, explode( ' ', get_search_query() ) );

		echo "<div class=\"searchwp-excerpt\">{$excerpt}</div>";
	}

}


//* Remove the post content (requires HTML5 theme support)
remove_action( 'genesis_entry_content', 'genesis_do_post_content' );

//* Remove the entry meta in the entry footer (requires HTML5 theme support)
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

genesis();
