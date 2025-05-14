<?php
/**
 * Yak custom child theme by Chris Liu-Beers, Tomatillo Design
 * Modified Genesis Sample.
 */


// Starts the engine.
require_once get_template_directory() . '/lib/init.php';

// CLB early customizations ///////

/// HARD BLOCK: Bail out early if ACF is not active
if (!function_exists('get_field')) {

	// Special override: if user clicked our custom "return to themes" button, force switch to fallback
	if (is_admin() && isset($_GET['yak_force_switch'])) {
		switch_theme('twentytwentyfive'); // fallback theme slug
		wp_safe_redirect(admin_url('themes.php?yak_switched_back=1'));
		exit;
	}

	if (is_admin()) {
		// Show blocking error page with working theme switch button
		wp_die(
			'<h1>Yak WP Theme Error</h1>
			 <p>This theme requires the Advanced Custom Fields plugin (free or PRO) to be installed and activated before it can be used.</p>
			 <p>Contact: Chris Liu-Beers, <a href="http://www.tomatillodesign.com/contact" target="_blank">Tomatillo Design</a></p>
			 <p><a href="' . esc_url(admin_url('themes.php?yak_force_switch=1')) . '" class="button button-primary">← Return to Themes</a></p>',
			'Missing Plugin: ACF',
			['response' => 500]
		);
	} else {
		wp_die(
			'<h2>Temporarily down for site maintenance and repairs.</h2><p>Please contact this website owner to notify them about this message.</p>',
			'Yak Theme Error',
			['response' => 500]
		);
	}
}

// // Prevent Genesis "Getting Started" redirect on theme activation (Genesis 3.6+)
// // Remove Genesis welcome redirect after Genesis loads
// add_action('after_setup_theme', function () {
// 	remove_action('admin_init', 'genesis_getting_started_redirect');
// }, 100); // Must be later than Genesis's default priority (10)


//////////////////////////////////////////////////////////

// Adds helper functions.
require_once get_stylesheet_directory() . '/lib/helper-functions.php';

// Adds image upload and color select to Customizer.
require_once get_stylesheet_directory() . '/lib/customize.php';

// Includes Customizer CSS.
require_once get_stylesheet_directory() . '/lib/output.php';

// Adds WooCommerce support.
require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-setup.php';

// Adds the required WooCommerce styles and Customizer CSS.
require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-output.php';

// Adds the Genesis Connect WooCommerce notice.
require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-notice.php';

// Removes header right widget area.
unregister_sidebar( 'header-right' );

// Removes secondary sidebar.
unregister_sidebar( 'sidebar-alt' );

// Removes site layouts.
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-content-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );

// Repositions primary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_header', 'genesis_do_nav', 12 );

// Repositions the secondary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_footer', 'genesis_do_subnav', 10 );

function register_additional_menu() {
    register_nav_menu( 'above-header' ,__( 'Above Header' ));
}

add_action( 'init', 'register_additional_menu' );
add_action( 'genesis_before_header', 'add_third_nav_genesis', 6 ); 
    
function add_third_nav_genesis() {
    
    if ( has_nav_menu( 'above-header' ) ) {
        wp_nav_menu( array( 'theme_location' => 'above-header', 'container_class' => 'genesis-nav-menu' ) );
    }
    
}

// Yak Theme: Enable essential theme supports for block editor + client-safe defaults
add_action('after_setup_theme', 'yak_theme_setup_features');
function yak_theme_setup_features() {

	// Modern block editor support
	add_theme_support( 'editor-styles' );                // Loads theme-defined CSS in the editor
	add_theme_support( 'wp-block-styles' );              // Enables default WP block styles
	add_theme_support( 'align-wide' );                   // Allows wide/full block alignments
	add_theme_support( 'responsive-embeds' );            // Makes embeds (e.g., YouTube) mobile-friendly

	// Media & markup
	add_theme_support( 'post-thumbnails' );              // Enables featured image support
	add_theme_support( 'html5', array(                   // Uses semantic HTML5 for core elements
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );
	add_theme_support( 'title-tag' );                    // Let WP manage the <title> tag
	add_theme_support( 'custom-logo' );                  // Allows logo upload in Customizer

	// Optional: Genesis accessibility if you're using Genesis
	add_theme_support( 'genesis-accessibility', array(
		'404-page',
		'drop-down-menu',
		'headings',
		'search-form',
		'skip-links',
	) );
}

// Yak Theme WP Dashboard Customizations and Improvements

// Yak Theme – Add branded dashboard widget with headshot
add_action( 'wp_dashboard_setup', 'yak_register_dashboard_widget' );

function yak_register_dashboard_widget() {
	add_meta_box(
		'yak_dashboard_widget',
		get_bloginfo( 'name' ), // Site title as heading
		'yak_render_dashboard_widget',
		'dashboard',
		'normal',
		'high'
	);
}

function yak_render_dashboard_widget() {
	$img_url = esc_url( 'https://www.tomatillodesign.com/wp-content/uploads/2024/02/clb-headshot-square-scaled.jpg' );
	$site_name = esc_html( get_bloginfo( 'name' ) );
	$contact_email = 'chris@tomatillodesign.com';
	$contact_link = 'https://www.tomatillodesign.com';

	echo '<div style="overflow:hidden;">';
	echo '<img src="' . $img_url . '" alt="Chris Liu-Beers" style="float:right; margin-left:1rem; width:90px; height:90px; border-radius:50%; object-fit:cover;" loading="lazy">';
	echo '<p><strong>Welcome to ' . $site_name . '</strong></p>';
	echo '<p>Congratulations on launching your new website!</p>';
	echo '<p>If you have any questions, please contact me:<br>';
	echo '<a href="mailto:' . antispambot( $contact_email ) . '">' . antispambot( $contact_email ) . '</a><br>';
	echo '919.576.0180<br>';
	echo '<a href="' . esc_url( $contact_link ) . '" target="_blank" rel="noopener">Tomatillo Design</a></p>';
	echo '</div>';
}

// clean up and remove default WP Dashboard widgets
add_action('wp_dashboard_setup', 'yak_remove_default_dashboard_widgets');
function yak_remove_default_dashboard_widgets() {
    // Removes Quick Draft
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');

    // Removes WordPress Events and News
    remove_meta_box('dashboard_primary', 'dashboard', 'side');

    // Optional removals
    // remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
    // remove_meta_box('dashboard_activity', 'dashboard', 'normal');
    // remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
}

// Add new credit line in the WP admin footer
add_filter('admin_footer_text', 'yak_custom_admin_footer_text');
function yak_custom_admin_footer_text($footer_text) {
    $footer_text .= ' | <span class="yak-admin-credit">A website by Chris Liu-Beers @ <a href="http://www.tomatillodesign.com" target="_blank" rel="noopener noreferrer">Tomatillo Design</a></span>';
    return $footer_text;
}

// Theme Options Page via ACF
// In functions.php or a theme setup file
add_action('acf/init', function () {
	if (function_exists('acf_add_options_page')) {

		acf_add_options_page([
			'page_title' => 'Theme Settings',
			'menu_title' => 'Theme Settings',
			'menu_slug'  => 'theme-settings',
            'icon_url' => 'dashicons-superhero',
			'capability' => 'manage_options',
			'redirect'   => false,
		]);

		acf_add_options_sub_page(
			[
			'page_title'  => 'Colors',
			'menu_title'  => 'Colors',
			'menu_slug'   => 'yak-options-colors', // ✅ this prevents the acf-options- prefix
			'parent_slug' => 'theme-settings',
		]);

		acf_add_options_sub_page([
			'page_title'  => 'Typography',
			'menu_title'  => 'Typography',
			'menu_slug'   => 'yak-options-typography', // ✅ this prevents the acf-options- prefix
			'parent_slug' => 'theme-settings',
		]);

		acf_add_options_sub_page([
			'page_title'  => 'Layouts',
			'menu_title'  => 'Layouts',
			'menu_slug'   => 'yak-options-layouts', // ✅ this prevents the acf-options- prefix
			'parent_slug' => 'theme-settings',
		]);

		acf_add_options_sub_page([
			'page_title'  => 'Login Screen',
			'menu_title'  => 'Login Screen',
			'menu_slug'   => 'yak-options-login', // ✅ this prevents the acf-options- prefix
			'parent_slug' => 'theme-settings', // or your actual main slug
		]);

	}
});

// Load YAK general theme settings
require_once get_stylesheet_directory() . '/inc/yak-theme-settings.php';

// Load YAK custom login functionality
require_once get_stylesheet_directory() . '/inc/yak-custom-login.php';

// Load YAK color palette system
require_once get_stylesheet_directory() . '/inc/yak-colors.php';

// Load YAK typography system
require_once get_stylesheet_directory() . '/inc/yak-typography.php';

// Load YAK layout settings
require_once get_stylesheet_directory() . '/inc/yak-layouts.php';

add_action('wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'yak-blocks',
		get_stylesheet_directory_uri() . '/css/yak-blocks.css',
		[],
		null
	);
}, 20); // Ensure it's dead last


// updated custom theme menu pages
// add_action( 'admin_menu', 'yak_register_theme_settings_menu' );

// function yak_register_theme_settings_menu() {
// 	add_menu_page(
// 		'Theme Settings',
// 		'Theme Settings',
// 		'manage_options',
// 		'theme-settings',
// 		'yak_render_theme_settings_page',
// 		'dashicons-superhero',
// 		88
// 	);

// 	add_submenu_page(
// 		'theme-settings',
// 		'Recommended Plugins',
// 		'Plugins',
// 		'manage_options',
// 		'yak-recommended-plugins',
// 		'yak_render_plugins_page'
// 	);
// }


// add_action( 'load-toplevel_page_theme-settings', function () {
// 	require_once get_stylesheet_directory() . '/inc/yak-theme-settings.php';
// } );

// function yak_render_plugins_page() {
// 	echo '<div class="wrap"><h1>Yak Recommended Plugins</h1>';
// 	// Your custom plugin list logic here
// }




// Update CSS within in Admin
function clb_custom_admin_styles() {

	wp_enqueue_style('custom-yak-admin-styles', get_stylesheet_directory_uri() . '/css/clb-custom-yak-admin-styles.css');

}
add_action('admin_enqueue_scripts', 'clb_custom_admin_styles');

// Enqueue custom scripts & styles
add_action( 'wp_enqueue_scripts', 'clb_enqueue_custom_scripts_styles', 100 );
function clb_enqueue_custom_scripts_styles() {

	// custom JS
    wp_enqueue_script( 'clb-custom-yak-scripts', get_stylesheet_directory_uri() . '/js/clb-custom-yak-scripts.js', array( 'jquery' ), '', true );
    wp_enqueue_script(
		'yak-mobile-menu',
		get_stylesheet_directory_uri() . '/js/yak-mobile-menu.js',
		[],
		filemtime(get_stylesheet_directory() . '/js/yak-mobile-menu.js'),
		true
	);

	// custom front-end CSS
	// wp_enqueue_style( 'clb-custom-yak-styles', get_stylesheet_directory_uri() . '/css/clb-custom-yak-styles.css', array(), '1.0.0', 'all');

}


// Yak custom BLOCK settings
add_action('enqueue_block_editor_assets', function () {
    wp_enqueue_script(
        'yak-block-enhancements',
        get_stylesheet_directory_uri() . '/js/block-enhancements.js',
        ['wp-blocks', 'wp-dom-ready', 'wp-edit-post', 'wp-hooks', 'wp-components', 'wp-element'],
        filemtime(get_stylesheet_directory() . '/js/block-enhancements.js'),
        true
    );
});

// remove Gutenberg injected styling and replace everything with my own custom styles (many copied directly, but better controlled)
add_action('wp_enqueue_scripts', function () {
	wp_dequeue_style('wp-block-library');
	wp_dequeue_style('wp-block-library-theme');
	wp_dequeue_style('global-styles'); // WP 5.9+ global styles
	wp_dequeue_style('classic-theme-styles');
}, 100);


// Remove Superfish scripts and styles
remove_action('wp_enqueue_scripts', 'genesis_load_superfish_scripts', 20);

// Load Dashicons on front end for all users
add_action('wp_enqueue_scripts', function () {
	wp_enqueue_style('dashicons');
});

add_filter('wp_nav_menu_args', function ($args) {
	if (!empty($args['theme_location']) && $args['theme_location'] === 'primary') {
		$args['menu_class'] = 'menu genesis-nav-menu yak-main-nav';
	}
	return $args;
});




add_action('widgets_init', function () {
    $count = (int) get_field('yak_number_of_footer_widgets', 'option');

    // Safety clamp
    $count = max(0, min($count, 4));

    for ($i = 1; $i <= $count; $i++) {
        register_sidebar([
            'name'          => "Footer Widget Area {$i}",
            'id'            => "yak-footer-widget-{$i}",
            'description'   => "Footer widget area #{$i}",
            'before_widget' => '<div id="%1$s" class="yak-footer-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="yak-footer-widget-title">',
            'after_title'   => '</h4>',
        ]);
    }
});

add_action( 'genesis_before_footer', 'yak_output_footer_widgets', 10 );
function yak_output_footer_widgets() {
	$count = (int) get_field( 'yak_number_of_footer_widgets', 'option' );
	$count = max( 0, min( $count, 4 ) );

	if ( $count === 0 ) {
		return;
	}

	// Check if at least one widget area is active
	$has_active = false;
	for ( $i = 1; $i <= $count; $i++ ) {
		if ( is_active_sidebar( "yak-footer-widget-{$i}" ) ) {
			$has_active = true;
			break;
		}
	}

	if ( ! $has_active ) {
		return;
	}

	echo '<div class="yak-footer-widgets-outer-wrapper">';
	echo '<div class="yak-footer-widgets yak-footer-widgets-' . esc_attr( $count ) . '">';

	for ( $i = 1; $i <= $count; $i++ ) {
		if ( is_active_sidebar( "yak-footer-widget-{$i}" ) ) {
			echo '<div class="yak-footer-widget-column yak-footer-widget-' . esc_attr( $i ) . '">';
			dynamic_sidebar( "yak-footer-widget-{$i}" );
			echo '</div>';
		}
	}

	echo '</div></div>';
}


add_action('widgets_init', 'yak_unregister_default_sidebars', 11);
function yak_unregister_default_sidebars() {
	unregister_sidebar('sidebar');
}











/**
 * Append user role to body class when logged in.
 *
 * Adds a class like `user-logged-in-editor` or `user-logged-in-administrator`.
 *
 * @param array $classes Existing body classes.
 * @return array Modified body classes.
 */
function yak_add_user_role_body_class( $classes ) {
    if ( is_user_logged_in() ) {
        $user = wp_get_current_user();
        if ( ! empty( $user->roles ) ) {
            $role_slug = sanitize_html_class( $user->roles[0] );
            $classes[] = 'user-logged-in-' . $role_slug;
        }
    }
    return $classes;
}
add_filter( 'body_class', 'yak_add_user_role_body_class' );




// Method 2: Setting.
function my_acf_init() {
    acf_update_setting('google_api_key', 'AIzaSyD-kMkqmuRLsPQe88VLRf6Xwoy_cCelJdQ');
}
add_action('acf/init', 'my_acf_init');






// Add Hello Bar
add_action('genesis_before_header', 'clb_ironwood_publish_hello_bar');
function clb_ironwood_publish_hello_bar() {

    $hello_bar_to_publish = null;

    $hello_bar_text = get_field('hello_bar_text', 'option');
    $current = strtotime("now");
    $hello_bar_start_date = get_field('hello_bar_start_date', 'option');
    $hello_bar_end_date = get_field('hello_bar_end_date', 'option');

    $hello_bar_link = get_field('hello_bar_link', 'option');
    $link_format = get_field('link_format', 'option');
    $hello_bar_link_target = get_field('link_target', 'option');
    $target = ' target="_self"';
    if( $hello_bar_link_target == 'New Tab' ) { $target = ' target="_blank"'; }

    if( !$hello_bar_link ) {
        $hello_bar_to_publish = $hello_bar_text;
    } elseif( $hello_bar_link && $link_format == 'No Button' ) {
        $hello_bar_to_publish = '<a href="' . $hello_bar_link . '"' . $target . '>' . $hello_bar_text . '</a>';
    } elseif( $hello_bar_link && $link_format == 'Button' ) {
        $button_text = get_field('button_text', 'option');
        if( !$button_text ) { $button_text = 'Learn More'; }
        $hello_bar_to_publish = '<span class="clb-hello-bar-text-wrapper">' . $hello_bar_text . '</span><span class="clb-hello-bar-button-wrapper"><a href="' . $hello_bar_link . '" class="button" ' . $target . '>' . $button_text . '</a></span>';
    }

    //echo strtotime($hello_bar_start_date);

    if( $hello_bar_start_date ) {
        if( strtotime($hello_bar_start_date) > $current ) { return; }
    }

    if( $hello_bar_end_date ) {
        if( strtotime($hello_bar_end_date) < $current ) { return; }
    }

    if( $hello_bar_text ) {
        echo '<div class="clb-hello-bar-wrapper">' . $hello_bar_to_publish . '</div>';
    }

}






if( function_exists('acf_add_local_field_group') ):

    acf_add_local_field_group(array(
        'key' => 'group_62ec1e6c24973',
        'title' => 'Featured Image Height',
        'fields' => array(
            array(
                'key' => 'field_62ec1e8026938',
                'label' => 'Featured Image Height',
                'name' => 'featured_image_height',
                'aria-label' => '',
                'type' => 'number',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => 400,
                'placeholder' => '',
                'prepend' => '',
                'append' => 'px',
                'min' => '',
                'max' => '',
                'step' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'side',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ));
    
    endif;		



// Remove Genesis Blocks Pro Portfolio Items CPT from Dashboard from Phil Johnston

function clb_ironwood_disable_gpb_portfolio_post_type() {
	remove_action( 'init', 'Genesis\PageBuilder\Portfolio\register_portfolio_post_type' );
}
add_action( 'init', 'clb_ironwood_disable_gpb_portfolio_post_type', 9 );



/** Remove the edit link */
add_filter ( 'genesis_edit_post_link' , '__return_false' );



/**
 * Add a body class if the current post has a featured image, or not.
 */
// function clb_featured_image_body_class( $classes ) {
//     if ( is_singular() && has_post_thumbnail() ) {
//         $classes[] = 'clb-has-featured-image';
//     } else {
//         $classes[] = 'clb-missing-featured-image';
//     }
//     return $classes;
// }
// add_filter( 'body_class', 'clb_featured_image_body_class' );







/**
 * Get the colors formatted for use with Iris, Automattic's color picker
 */
function output_the_colors() {

	// get the colors
    $color_palette = current( (array) get_theme_support( 'editor-color-palette' ) );

	// bail if there aren't any colors found
	if ( !$color_palette )
		return;

	// output begins
	ob_start();

	// output the names in a string
	echo '[';
		foreach ( $color_palette as $color ) {
			echo "'" . $color['color'] . "', ";
		}
	echo ']';

    return ob_get_clean();

}

/**
 * Add the colors into Iris
 */
add_action( 'acf/input/admin_footer', 'gutenberg_sections_register_acf_color_palette' );
function gutenberg_sections_register_acf_color_palette() {

    $color_palette = output_the_colors();
    if ( !$color_palette )
        return;

    ?>
    <script type="text/javascript">
        (function( $ ) {
            acf.add_filter( 'color_picker_args', function( args, $field ){

                // add the hexadecimal codes here for the colors you want to appear as swatches
                args.palettes = <?php echo $color_palette; ?>

                // return colors
                return args;

            });
        })(jQuery);
    </script>
    <?php

}





//* Customize search form input box text
add_filter( 'genesis_search_text', 'sp_search_text' );
function sp_search_text( $text ) {
	return esc_attr( 'Search this website...' );
}








// Show featured image inside <article> markup, above title
// add_action( 'genesis_entry_header', 'yak_add_featured_image_to_archives', 5 );
// function yak_add_featured_image_to_archives() {
// 	if ( is_singular() ) return;

// 	if ( has_post_thumbnail() ) {
// 		echo '<div class="yak-post-thumbnail">';
// 		the_post_thumbnail( 'large', ['loading' => 'lazy'] );
// 		echo '</div>';
// 	}
// }



add_action( 'genesis_loop', 'yak_open_archive_wrapper', 6 );
add_action( 'genesis_after_loop', 'yak_close_archive_wrapper', 6 );

/**
 * Open custom wrapper around the archive loop based on blog format setting.
 */
function yak_open_archive_wrapper() {
	if ( ! is_home() && ! is_archive() && ! is_search() ) return;

	$format = get_field( 'yak_blog_format', 'option' );
	$format_class = $format === 'cards' ? 'yak-blog-format-cards' : 'yak-blog-format-list';

	echo '<div class="entry-content"><div class="yak-archive-wrapper ' . esc_attr( $format_class ) . ' alignwide">';
}

/**
 * Close wrapper div after archive loop.
 */
function yak_close_archive_wrapper() {
	if ( ! is_home() && ! is_archive() && ! is_search() ) return;

	echo '</div></div>';
}


add_action( 'genesis_before', 'yak_clean_archive_entry_markup' );
function yak_clean_archive_entry_markup() {
	if ( is_home() || is_archive() || is_search() ) {

		// Remove header junk
		remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
		remove_action( 'genesis_entry_header', 'genesis_post_info', 8 );
		remove_action( 'genesis_entry_meta', 'genesis_post_info' ); // ✅ catch-all
		add_filter( 'genesis_post_info', '__return_false' );

		// Remove image and content
		remove_action( 'genesis_entry_content', 'genesis_do_post_content' );
		remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );

		// Remove footer junk
		remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
		remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
		remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );

		// Remove entry-header wrapper if your Genesis version uses it
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );

		// Inject clean layout
		add_action( 'genesis_entry_content', 'yak_custom_archive_entry_markup' );
	}
}


function yak_custom_archive_entry_markup() {

	if( !has_post_thumbnail() ) { $class = ' yak-missing-thumbnail'; }
	else { $class = ' yak-has-thumbnail'; }

	echo '<div class="yak-archive-card">';
	echo '<div class="yak-archive-entry' . $class . '">';

	// Image
	if ( has_post_thumbnail() ) {
		echo '<div class="yak-archive-image">';
		echo '<a href="' . esc_url( get_permalink() ) . '">';
		the_post_thumbnail( 'full', [ 'loading' => 'lazy' ] );
		echo '</a>';
		echo '</div>';
	}

	// Body: title, date, excerpt
	echo '<div class="yak-archive-body">';

		echo '<h2 class="yak-entry-title"><a href="' . esc_url( get_permalink() ) . '">';
		the_title();
		echo '</a></h2>';

		echo '<div class="yak-entry-date">' . get_the_date() . '</div>';

		echo '<div class="yak-entry-excerpt">';
		the_excerpt();
		echo '</div>';

	echo '</div>'; // .yak-archive-body
	echo '</div>'; // .yak-archive-entry
	echo '</div>'; // .yak-archive-card

}


remove_action( 'genesis_footer', 'genesis_do_footer' );
add_action( 'genesis_footer', 'yak_custom_site_footer' );
function yak_custom_site_footer() {

	$site_title = get_bloginfo( 'name' );
	echo '&copy; ' . date('Y') . ' &middot <a href="/">' . $site_title . '</a> &middot All rights reserved &middot Website by <a href="http://www.tomatillodesign.com" title="Amazing, Affordable Websites for Nonprofits" target="_blank">Tomatillo Design</a>';

}


// Roll my own Main Menu Search Icon
add_action( 'admin_head-nav-menus.php', function() {
	add_meta_box(
		'posttype-yak_custom',               // ✅ Use this special ID
		'Yak Custom Items',                 
		'yak_render_custom_menu_metabox',   
		'nav-menus',                        
		'side',                             
		'default'                           
	);
} );


function yak_render_custom_menu_metabox() {
	$item_id = 99934529; // Just a unique dummy ID
	?>
	<div id="posttype-yak_custom" class="posttypediv">
		<div id="tabs-panel-yak-custom" class="tabs-panel tabs-panel-active">
			<ul id="yak-custom-checklist" class="categorychecklist form-no-clear">
				<li>
					<label class="menu-item-title">
						<input type="checkbox"
							class="menu-item-checkbox"
							name="menu-item[<?php echo esc_attr( $item_id ); ?>][menu-item-object-id]"
							value="<?php echo esc_attr( $item_id ); ?>">
						Add Search Modal
					</label>

					<input type="hidden" name="menu-item[<?php echo $item_id; ?>][menu-item-type]" value="custom">
					<input type="hidden" name="menu-item[<?php echo $item_id; ?>][menu-item-url]" value="#yak-search-modal">
					<input type="hidden" name="menu-item[<?php echo $item_id; ?>][menu-item-classes]" value="yak-search-trigger">
					<input type="hidden" name="menu-item[<?php echo $item_id; ?>][menu-item-title]" value="__yak-icon-search__">
				</li>
			</ul>
		</div>
		<p class="button-controls">
			<span class="add-to-menu">
				<input type="submit"
					class="button yak-add-custom-menu-item"
					value="<?php esc_attr_e( 'Add to Menu' ); ?>"
					name="add-post-type-menu-item"
					id="submit-posttype-yak_custom">
				<span class="spinner"></span>
			</span>
		</p>
	</div>
	<?php
}


add_action( 'admin_footer-nav-menus.php', function() {
	?>
	<script>
		jQuery(function($) {
			$('.yak-add-custom-menu-item').on('click', function(e) {
				e.preventDefault();

				const $checkboxes = $('#yak-custom-checklist input.menu-item-checkbox:checked');
				if ($checkboxes.length === 0) return;

				$checkboxes.each(function() {
					const $li = $(this).closest('li');
					const iconHTML = '<i class="fa-light fa-magnifying-glass fa-lg"></i>';

					const postData = {
						action: 'add-menu-item',
						menu: $('#menu').val(),
						'menu-settings-column-nonce': $('#menu-settings-column-nonce').val(),
						'menu-item[-1][menu-item-type]': $li.find('input[name$="[menu-item-type]"]').val(),
						'menu-item[-1][menu-item-url]': $li.find('input[name$="[menu-item-url]"]').val(),
						'menu-item[-1][menu-item-classes]': $li.find('input[name$="[menu-item-classes]"]').val(),
						'menu-item[-1][menu-item-object-id]': $(this).val(),
						'menu-item[-1][menu-item-title]': iconHTML,
						'menu-item[-1][menu-item-parent-id]': 0
					};

					$.post(ajaxurl, postData, function(response) {
						if (response && response.trim()) {
							$('#menu-to-edit').append(response);
							wpNavMenu.refreshMenuTabs(true);
						}
					});
				});
			});
		});
	</script>
	<?php
} );



// Add FontAwesome JS
add_action( 'wp_enqueue_scripts', 'yak_enqueue_fontawesome' );
add_action( 'admin_enqueue_scripts', 'yak_enqueue_fontawesome' );
function yak_enqueue_fontawesome() {
	wp_enqueue_script(
		'yak-fontawesome',
		'https://kit.fontawesome.com/9d148ae9d1.js',
		[],
		null,
		false // Load in <head>
	);
}

add_filter( 'script_loader_tag', 'yak_add_crossorigin_to_fontawesome', 10, 3 );
function yak_add_crossorigin_to_fontawesome( $tag, $handle, $src ) {
	if ( $handle === 'yak-fontawesome' ) {
		return str_replace( '<script', '<script crossorigin="anonymous"', $tag );
	}
	return $tag;
}




////// roll my own BS -> YAKSTRAP


add_action( 'wp_enqueue_scripts', 'yak_enqueue_yakstrap' );
function yak_enqueue_yakstrap() {
	wp_enqueue_script(
		'yakstrap',
		get_stylesheet_directory_uri() . '/js/yakstrap.js',
		[],
		'1.0',
		true
	);
}

function yak_output_modal( $args = [] ) {
	$defaults = [
		'id'              => 'yak-modal',
		'title'           => '',
		'content'         => '',
		'classes'         => '',
		'aria_labelledby' => '', // optional override
	];
	$args = wp_parse_args( $args, $defaults );

	$modal_id  = esc_attr( $args['id'] );
	$title_id  = $args['aria_labelledby'] ?: $modal_id . '-title';
	$has_title = trim( $args['title'] ) !== '';
	?>

	<div id="<?php echo $modal_id; ?>"
		class="yak-modal modal fade <?php echo esc_attr( $args['classes'] ); ?>"
		tabindex="-1"
		role="dialog"
		aria-hidden="true"
		inert
		<?php if ( $has_title ) : ?>
			aria-labelledby="<?php echo esc_attr( $title_id ); ?>"
		<?php endif; ?>>

		<div class="yak-modal-dialog modal-dialog" role="document">
			<div class="yak-modal-content modal-content">

				<?php if ( $has_title ) : ?>
				<div class="yak-modal-header modal-header">
					<h5 class="yak-modal-title modal-title" id="<?php echo esc_attr( $title_id ); ?>">
						<?php echo esc_html( $args['title'] ); ?>
					</h5>
					<button type="button" class="yak-modal-close close" data-bs-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<?php else : ?>
				<div class="yak-modal-header modal-header">
					<button type="button" class="yak-modal-close close ml-auto" data-bs-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<?php endif; ?>

				<div class="yak-modal-body modal-body">
					<?php echo $args['content']; ?>
				</div>

			</div>
		</div>
	</div>
	<?php
}



// search
add_action( 'wp_footer', 'yak_output_global_search_modal', 20 );
function yak_output_global_search_modal() {
	if ( is_admin() ) return;

	yak_output_modal( [
		'id'    => 'yak-search-modal',
		'title' => 'Search',
		'content' => yak_get_search_form_html(),
		'classes' => 'yak-search-modal',
	] );
}


function yak_get_search_form_html() {
	ob_start();
	?>
	<form role="search" method="get" class="yak-modal-search-form search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label class="screen-reader-text" for="yak-search-field"><?php esc_html_e( 'Search for:', 'yak' ); ?></label>
		<input type="search" id="yak-search-field" class="search-field" placeholder="<?php echo esc_attr_x( 'Search…', 'placeholder', 'yak' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
		<button type="submit" class="search-submit">
			<span class="yak-search-icon" aria-hidden="true">&#x1F50D;</span>
			<span class="screen-reader-text"><?php esc_html_e( 'Search', 'yak' ); ?></span>
		</button>
	</form>
	<?php
	return ob_get_clean();
}

