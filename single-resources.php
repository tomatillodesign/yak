<?php

//* Remove the author box on single posts HTML5 Themes
remove_action( 'genesis_after_entry', 'genesis_do_author_box_single', 8 );

//* Remove the post content (requires HTML5 theme support)
// remove_action( 'genesis_entry_content', 'genesis_do_post_content' );

//* Remove the entry meta in the entry header (requires HTML5 theme support)
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );




// add custom functions, like a metabox or download button, here....




genesis();