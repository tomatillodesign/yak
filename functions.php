<?php

/**
 * ============================================================================
 * YAK Theme – functions.php (Organized & Commented)
 * ============================================================================
 *
 * All functions preserved. Grouped by purpose for readability and maintenance.
 *
 * Sections:
 * 1.  ACF Bailout & Safety
 * 2.  Genesis Init & Theme Support
 * 3.  Includes (lib/ and inc/ loaders)
 * 4.  Asset Enqueue (JS, CSS, Block Editor)
 * 5.  Global UI Components (Modals, Notices, Hello Bar)
 * 6.  Featured Images
 * 7.  Genesis Layout & Archive Hooks
 * 8.  WP Filters & Shortcode Enhancements
 * 9.  Admin UI & Dashboard
 * 10. ACF Options Pages (Theme Settings Panel)
 * 11. Theme Settings Access Control (yak_allowed_users)
 * 12. SearchWP Integration
 */

 /**
 * Yak custom child theme by Chris Liu-Beers, Tomatillo Design.
 * Based on Genesis Sample Theme.
 */

// =============================================================================
// YAK Configurable Constants
// =============================================================================

define( 'YAK_PRIMARY_USER_ID', 1 ); // ← Set this to your own admin user ID
                                    // required to access custom theme panels


// =============================================================================
// 1. ACF Bailout & Safety
// =============================================================================

/**
 * Bail out early if ACF is not active.
 * This prevents the site from running with missing required ACF fields.
 */
if ( ! function_exists( 'get_field' ) ) {

	// Special override: let user force a theme switch
	if ( is_admin() && isset( $_GET['yak_force_switch'] ) ) {
		switch_theme( 'twentytwentyfive' ); // fallback theme slug
		wp_safe_redirect( admin_url( 'themes.php?yak_switched_back=1' ) );
		exit;
	}

	if ( is_admin() ) {
		// Admin-side error page
		wp_die(
			'<h1>Yak WP Theme Error</h1>
			 <p>This theme requires the Advanced Custom Fields plugin (free or PRO) to be installed and activated before it can be used.</p>
			 <p>Contact: Chris Liu-Beers, <a href="http://www.tomatillodesign.com/contact" target="_blank">Tomatillo Design</a></p>
			 <p><a href="' . esc_url( admin_url( 'themes.php?yak_force_switch=1' ) ) . '" class="button button-primary">← Return to Themes</a></p>',
			'Missing Plugin: ACF',
			[ 'response' => 500 ]
		);
	} else {
		// Frontend fallback message
		wp_die(
			'<h2>Temporarily down for site maintenance and repairs.</h2><p>Please contact this website owner to notify them about this message.</p>',
			'Yak Theme Error',
			[ 'response' => 500 ]
		);
	}
}



// =============================================================================
// 2. Genesis Init & Theme Support
// =============================================================================

/**
 * Load Genesis engine (required).
 */
require_once get_template_directory() . '/lib/init.php';

/**
 * Remove default Genesis sidebars and layouts not needed for Yak.
 */
unregister_sidebar( 'header-right' );
unregister_sidebar( 'sidebar-alt' );
unregister_sidebar( 'sidebar' ); // 'sidebar' is the ID for Primary Sidebar in Genesis

genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-content-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );

add_action( 'widgets_init', 'yak_unregister_sidebars', 11 );
function yak_unregister_sidebars() {
	remove_action( 'genesis_sidebar', 'genesis_do_sidebar' );
}

/**
 * Reposition primary and secondary navigation menus.
 */
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_header', 'genesis_do_nav', 12 );

remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_footer', 'genesis_do_subnav', 10 );

/**
 * Enable essential theme supports for block editor and frontend markup.
 * These settings define editor behavior, media features, and HTML5 output.
 */
add_action( 'after_setup_theme', 'yak_theme_setup_features' );
function yak_theme_setup_features() {

	// Modern block editor support
	add_theme_support( 'editor-styles' );                // Load theme-defined CSS in editor
	add_theme_support( 'wp-block-styles' );              // Enable WP block styles
	add_theme_support( 'align-wide' );                   // Wide/full block alignment
	add_theme_support( 'responsive-embeds' );            // Mobile-friendly media
	add_theme_support( 'editor-filter-duotone' );        // Duotone support (if applicable)

	// Media & HTML
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	] );
	add_theme_support( 'title-tag' );
	add_theme_support( 'custom-logo' );

	// Optional: Genesis accessibility features
	add_theme_support( 'genesis-accessibility', [
		'404-page',
		'drop-down-menu',
		'headings',
		'search-form',
		'skip-links',
	] );
}

/**
 * Optional: Prevent Genesis "Getting Started" redirect on activation.
 */
// add_action( 'after_setup_theme', function () {
// 	remove_action( 'admin_init', 'genesis_getting_started_redirect' );
// }, 100 );



// =============================================================================
// 3. Includes
// =============================================================================

require_once get_stylesheet_directory() . '/lib/helper-functions.php';
require_once get_stylesheet_directory() . '/lib/customize.php';
// require_once get_stylesheet_directory() . '/lib/output.php'; // ⚠️ Optional: unused or legacy?

// WooCommerce integration
require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-setup.php';
require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-output.php';
require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-notice.php';
/**
 * Load custom Yak theme option panels and systems.
 * Each file encapsulates logic for a major theme subsystem.
 */
require_once get_stylesheet_directory() . '/inc/yak-theme-settings.php';
require_once get_stylesheet_directory() . '/inc/yak-custom-login.php';
require_once get_stylesheet_directory() . '/inc/yak-colors.php';
require_once get_stylesheet_directory() . '/inc/yak-typography.php';
require_once get_stylesheet_directory() . '/inc/yak-layouts.php';


// =============================================================================
// 4. Asset Enqueue – Editor & Frontend Styles
// =============================================================================

/**
 * Load Yak block styles on the frontend.
 * Note: runs late (priority 20) to override core block styles.
 */
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'yak-blocks',
		get_stylesheet_directory_uri() . '/css/yak-blocks.css',
		[],
		null
	);
}, 20 );


/**
 * Load custom admin styles for branding or tweaks.
 */
function clb_custom_admin_styles() {
	wp_enqueue_style(
		'custom-yak-admin-styles',
		get_stylesheet_directory_uri() . '/css/clb-custom-yak-admin-styles.css'
	);
}
add_action( 'admin_enqueue_scripts', 'clb_custom_admin_styles' );


/**
 * Load frontend JS scripts and layout enhancements.
 */
add_action( 'wp_enqueue_scripts', 'clb_enqueue_custom_scripts_styles', 100 );
function clb_enqueue_custom_scripts_styles() {
	wp_enqueue_script(
		'clb-custom-yak-scripts',
		get_stylesheet_directory_uri() . '/js/clb-custom-yak-scripts.js',
		[ 'jquery' ],
		'',
		true
	);

	wp_enqueue_script(
		'yak-mobile-menu',
		get_stylesheet_directory_uri() . '/js/yak-mobile-menu.js',
		[],
		filemtime( get_stylesheet_directory() . '/js/yak-mobile-menu.js' ),
		true
	);
}


/**
 * Load custom Gutenberg block editor enhancements.
 * Injects block settings UI (like device toggles) using JS hooks.
 */
add_action( 'enqueue_block_editor_assets', function () {
	wp_enqueue_script(
		'yak-block-enhancements',
		get_stylesheet_directory_uri() . '/js/block-enhancements.js',
		[ 'wp-blocks', 'wp-dom-ready', 'wp-edit-post', 'wp-hooks', 'wp-components', 'wp-element' ],
		filemtime( get_stylesheet_directory() . '/js/block-enhancements.js' ),
		true
	);
} );

/**
 * Disable all default Gutenberg and classic WP styles.
 * These are replaced by your custom block styles in /css/yak-blocks.css
 */
add_action( 'wp_enqueue_scripts', function () {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'global-styles' );           // WP 5.9+ global styles
	wp_dequeue_style( 'classic-theme-styles' );
}, 100 );

/**
 * Remove Genesis's Superfish menu JS and CSS (legacy dropdown behavior).
 */
remove_action( 'wp_enqueue_scripts', 'genesis_load_superfish_scripts', 20 );

/**
 * Ensure dashicons are available on the frontend.
 * Useful if you're referencing them in UI elements.
 */
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'dashicons' );
} );

/**
 * Load FontAwesome JS (Kit-based) on both frontend and admin.
 */
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

/**
 * Add crossorigin="anonymous" to FontAwesome script tag for proper font loading.
 */
add_filter( 'script_loader_tag', 'yak_add_crossorigin_to_fontawesome', 10, 3 );
function yak_add_crossorigin_to_fontawesome( $tag, $handle, $src ) {
	if ( $handle === 'yak-fontawesome' ) {
		return str_replace( '<script', '<script crossorigin="anonymous"', $tag );
	}
	return $tag;
}

/**
 * Enqueue Yakstrap JS – lightweight modal and collapse functionality.
 * This script replaces Bootstrap’s heavier dependencies.
 */
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


// =============================================================================
// 5. Global UI Components (Modals, Notices)
// =============================================================================

/**
 * Output a Yak modal window with flexible content, title, and ARIA labeling.
 * Used for search, announcements, and other popup UI.
 *
 * @param array $args {
 *     @type string $id               HTML ID of the modal (required)
 *     @type string $title            Optional modal heading
 *     @type string $content          Modal inner content
 *     @type string $classes          Additional class names
 *     @type string $aria_labelledby  Optional ARIA override
 * }
 */
function yak_output_modal( $args = [] ) {
	$defaults = [
		'id'              => 'yak-modal',
		'title'           => '',
		'content'         => '',
		'classes'         => '',
		'aria_labelledby' => '',
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

/**
 * Output a global search modal in the footer using yak_output_modal().
 * Renders the WP search form with site title in modal heading.
 */
add_action( 'wp_footer', 'yak_output_global_search_modal', 20 );
function yak_output_global_search_modal() {
	if ( is_admin() ) return;

	$site_name = get_bloginfo( 'name' );

	yak_output_modal( [
		'id'      => 'yak-search-modal',
		'title'   => 'Search ' . $site_name,
		'content' => get_search_form( false ),
		'classes' => 'yak-search-modal',
	] );
}

/**
 * Return raw HTML of the Yak-styled search form.
 * Not currently used above but useful for JS or inline injection elsewhere.
 */
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

/**
 * Output a hidden version of the default WP search form in a div.
 * Used in mobile menu inline search via JS cloning.
 */
add_action( 'wp_head', 'yak_output_mobile_wp_search_form' );
function yak_output_mobile_wp_search_form() {
	?>
	<div id="yak-inline-search-template" style="display: none;">
		<?php get_search_form(); ?>
	</div>
	<?php
}

/**
 * Display the Top Notice Bar on the homepage, above the site header.
 * Dates and conditions are respected based on ACF config.
 */
add_action( 'genesis_before_header', 'yak_display_hello_bar' );
function yak_display_hello_bar() {

	if ( ! is_front_page() ) return;

	$home_id     = get_option( 'page_on_front' );
	$now         = time();
	$text        = get_field( 'hello_bar_text', $home_id );
	$start_date  = get_field( 'hello_bar_start_date', $home_id );
	$end_date    = get_field( 'hello_bar_end_date', $home_id );
	$link        = get_field( 'hello_bar_link', $home_id );
	$link_format = get_field( 'link_format', $home_id );
	$target_attr = ( get_field( 'link_target', $home_id ) === 'New Tab' ) ? ' target="_blank"' : ' target="_self"';

	if ( ! $text ) return;

	// Conditional timing logic
	if ( $start_date && strtotime( $start_date ) > $now ) return;
	if ( $end_date && strtotime( $end_date ) < $now ) return;

	// Generate output HTML
	if ( ! $link ) {
		$output = esc_html( $text );
	} elseif ( $link_format === 'No Button' ) {
		$output = '<a href="' . esc_url( $link ) . '"' . $target_attr . '>' . esc_html( $text ) . '</a>';
	} elseif ( $link_format === 'Button' ) {
		$button_text = get_field( 'button_text', $home_id ) ?: 'Learn More';
		$output = '<span class="clb-hello-bar-text-wrapper">' . esc_html( $text ) . '</span>';
		$output .= '<span class="clb-hello-bar-button-wrapper"><a href="' . esc_url( $link ) . '" class="button"' . $target_attr . '>' . esc_html( $button_text ) . '</a></span>';
	} else {
		return;
	}

	echo '<div class="clb-hello-bar-wrapper">' . $output . '</div>';
}



// =============================================================================
// 6. Featured Image Logic – Above Page Title
// =============================================================================

/**
 * Output a featured image banner above the content area.
 * Only displays if ACF toggle is enabled and post has a thumbnail.
 */
add_action( 'genesis_after_header', 'yak_output_featured_image_top' );
function yak_output_featured_image_top() {
	if (
		! is_singular() ||
		! get_field( 'yak_show_featured_image', 'option' ) ||
		get_field( 'yak_remove_featured_image' )
	) {
		return;
	}

	$image_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
	if ( ! $image_url ) return;

	$custom_height = get_field( 'yak_featured_image_height' );
	$custom_height = is_numeric( $custom_height ) ? max( 100, min( $custom_height, 800 ) ) : 400;

	echo '<div class="yak-featured-image-top-wrapper" style="height: ' . esc_attr( $custom_height ) . 'px;">';
	echo '  <img class="yak-featured-image-bg" src="' . esc_url( $image_url ) . '" alt="' . esc_attr( get_the_title() ) . '" />';
	echo '  <div class="yak-featured-image-title">';
	echo '    <h1>' . esc_html( get_the_title() ) . '</h1>';
	echo '  </div>';
	echo '</div>';
}


/**
 * Remove default post title output inside the loop if featured image is enabled.
 * This prevents the same title from appearing twice when featured image is active.
 */
add_action( 'genesis_before', function() {
	if (
		! is_singular() ||
		get_field( 'yak_remove_featured_image' )
	) {
		return;
	}

	if ( get_field( 'yak_show_featured_image', 'option' ) && has_post_thumbnail() ) {
		remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
	}
}, 15 );



// =============================================================================
// 7. Genesis Layout & Archive Hooks
// =============================================================================

/**
 * Register and display a custom menu above the site header.
 */
function register_additional_menu() {
	register_nav_menu( 'above-header', __( 'Above Header' ) );
}
add_action( 'init', 'register_additional_menu' );

add_action( 'genesis_before_header', 'add_third_nav_genesis', 6 );

function add_third_nav_genesis() {
	if ( has_nav_menu( 'above-header' ) ) {
		wp_nav_menu( [
			'theme_location'  => 'above-header',
			'container_class' => 'genesis-nav-menu',
		] );
	}
}

/**
 * Customize nav menu classes for the primary menu.
 * Adds yak-main-nav to the top-level menu for custom styling.
 */
add_filter( 'wp_nav_menu_args', function ( $args ) {
	if ( ! empty( $args['theme_location'] ) && $args['theme_location'] === 'primary' ) {
		$args['menu_class'] = 'menu genesis-nav-menu yak-main-nav';
	}
	return $args;
} );


/**
 * Dynamically register Yak footer widget areas based on ACF setting.
 * Allows between 1–4 widget columns depending on admin input.
 */
add_action( 'widgets_init', function () {
	$count = (int) get_field( 'yak_number_of_footer_widgets', 'option' );
	$count = max( 0, min( $count, 4 ) ); // Clamp to range 0–4

	for ( $i = 1; $i <= $count; $i++ ) {
		register_sidebar( [
			'name'          => "Footer Widget Area {$i}",
			'id'            => "yak-footer-widget-{$i}",
			'description'   => "Footer widget area #{$i}",
			'before_widget' => '<div id="%1$s" class="yak-footer-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="yak-footer-widget-title">',
			'after_title'   => '</h4>',
		] );
	}
} );


/**
 * Output the Yak footer widget container just before the <footer>.
 */
add_action( 'genesis_before_footer', 'yak_output_footer_widgets', 10 );
function yak_output_footer_widgets() {
	$count = (int) get_field( 'yak_number_of_footer_widgets', 'option' );
	$count = max( 0, min( $count, 4 ) );

	if ( $count === 0 ) return;

	// Ensure at least one widget area is active before rendering
	$has_active = false;
	for ( $i = 1; $i <= $count; $i++ ) {
		if ( is_active_sidebar( "yak-footer-widget-{$i}" ) ) {
			$has_active = true;
			break;
		}
	}
	if ( ! $has_active ) return;

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

/**
 * Remove Genesis's default sidebar registration (if unused).
 */
add_action( 'widgets_init', 'yak_unregister_default_sidebars', 11 );
function yak_unregister_default_sidebars() {
	unregister_sidebar( 'sidebar' );
}

/**
 * Remove Genesis Blocks Pro "Portfolio Items" CPT from the dashboard.
 * Courtesy of Phil Johnston.
 */
function clb_ironwood_disable_gpb_portfolio_post_type() {
	remove_action( 'init', 'Genesis\PageBuilder\Portfolio\register_portfolio_post_type' );
}
add_action( 'init', 'clb_ironwood_disable_gpb_portfolio_post_type', 9 );

/**
 * Completely remove the "Edit" link from Genesis post output.
 */
add_filter( 'genesis_edit_post_link', '__return_false' );

/**
 * Customize placeholder text for Genesis search form.
 * Uses the site name for more context.
 */
add_filter( 'genesis_search_text', 'yak_custom_search_placeholder' );
function yak_custom_search_placeholder( $text ) {
	$site_name = get_bloginfo( 'name' );
	return esc_attr__( 'Search ' . $site_name . '...', 'yak' );
}

/**
 * [Legacy/Optional] Show featured image above title inside <article> (archive).
 * Commented out in favor of full layout control via yak_custom_archive_entry_markup().
 */
// add_action( 'genesis_entry_header', 'yak_add_featured_image_to_archives', 5 );
// function yak_add_featured_image_to_archives() {
// 	if ( is_singular() ) return;

// 	if ( has_post_thumbnail() ) {
// 		echo '<div class="yak-post-thumbnail">';
// 		the_post_thumbnail( 'large', ['loading' => 'lazy'] );
// 		echo '</div>';
// 	}
// }

/**
 * Open a layout wrapper around the Genesis archive loop output.
 * Controlled via ACF "yak_blog_format" field: 'cards' or 'list'.
 */
add_action( 'genesis_loop', 'yak_open_archive_wrapper', 6 );
add_action( 'genesis_after_loop', 'yak_close_archive_wrapper', 6 );

function yak_open_archive_wrapper() {
	if ( ! is_home() && ! is_archive() ) return;

	$format       = get_field( 'yak_blog_format', 'option' );
	$format_class = $format === 'cards' ? 'yak-blog-format-cards' : 'yak-blog-format-list';

	echo '<div class="entry-content"><div class="yak-archive-wrapper ' . esc_attr( $format_class ) . ' alignwide">';
}

function yak_close_archive_wrapper() {
	if ( ! is_home() && ! is_archive() ) return;
	echo '</div></div>';
}

/**
 * Remove all default Genesis markup for entry header, meta, image, and footer.
 * Inject our own custom card layout for clean archive display.
 */
add_action( 'genesis_before', 'yak_clean_archive_entry_markup' );
function yak_clean_archive_entry_markup() {
	if ( is_home() || is_archive() || is_search() ) {

		// Remove header + meta
		remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
		remove_action( 'genesis_entry_header', 'genesis_post_info', 8 );
		remove_action( 'genesis_entry_meta', 'genesis_post_info' );
		add_filter( 'genesis_post_info', '__return_false' );

		// Remove default content/image
		remove_action( 'genesis_entry_content', 'genesis_do_post_content' );
		remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );

		// Remove footer/meta
		remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
		remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
		remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );

		// Remove wrapper (for some Genesis versions)
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );

		// Replace with custom layout
		add_action( 'genesis_entry_content', 'yak_custom_archive_entry_markup' );
	}
}

/**
 * Output full custom archive markup for cards or list view.
 * Includes thumbnail, title, date, and excerpt.
 */
function yak_custom_archive_entry_markup() {
	if ( is_search() ) return;

	$class = has_post_thumbnail() ? ' yak-has-thumbnail' : ' yak-missing-thumbnail';

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

	// Title, date, excerpt
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

/**
 * Override the default Genesis footer output.
 * Outputs copyright + design credit with inline HTML.
 */
remove_action( 'genesis_footer', 'genesis_do_footer' );
add_action( 'genesis_footer', 'yak_custom_site_footer' );
function yak_custom_site_footer() {
	$site_title = get_bloginfo( 'name' );
	echo '&copy; ' . date( 'Y' ) . ' <a href="/">' . esc_html( $site_title ) . '</a> &middot; All rights reserved &middot; Website by <a href="http://www.tomatillodesign.com" title="Amazing, Affordable Websites for Nonprofits" target="_blank">Tomatillo Design</a>';
}


// =============================================================================
// 8. WP Filters & Shortcode Enhancements
// =============================================================================

/**
 * Append user role class to <body> when user is logged in.
 * Results in class like user-logged-in-editor or user-logged-in-subscriber.
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

/**
 * Get the current editor color palette (from theme support) and format it for ACF/Iris.
 * Returns a JS-ready array of hex codes.
 */
function output_the_colors() {
	$color_palette = current( (array) get_theme_support( 'editor-color-palette' ) );

	if ( ! $color_palette ) return;

	ob_start();
	echo '[';
	foreach ( $color_palette as $color ) {
		echo "'" . $color['color'] . "', ";
	}
	echo ']';

	return ob_get_clean();
}

/**
 * Inject the color palette swatches into ACF’s color picker using Iris/JS.
 * Runs on admin footer during ACF input.
 */
add_action( 'acf/input/admin_footer', 'gutenberg_sections_register_acf_color_palette' );
function gutenberg_sections_register_acf_color_palette() {
	$color_palette = output_the_colors();
	if ( ! $color_palette ) return;

	?>
	<script type="text/javascript">
		(function( $ ) {
			acf.add_filter( 'color_picker_args', function( args, $field ){
				args.palettes = <?php echo $color_palette; ?>;
				return args;
			});
		})(jQuery);
	</script>
	<?php
}


// =============================================================================
// 9. Admin UI & Dashboard Enhancements
// =============================================================================

/**
 * Add a branded custom dashboard widget with welcome message.
 * Includes a photo, contact info, and custom greeting.
 */
add_action( 'wp_dashboard_setup', 'yak_register_dashboard_widget' );
function yak_register_dashboard_widget() {
	add_meta_box(
		'yak_dashboard_widget',
		'Welcome to ' . get_bloginfo( 'name' ),
		'yak_render_dashboard_widget',
		'dashboard',
		'normal',
		'high'
	);
}

function yak_render_dashboard_widget() {
	$img_url       = esc_url( 'https://www.tomatillodesign.com/wp-content/uploads/2024/02/clb-headshot-square-scaled.jpg' );
	$site_name     = esc_html( get_bloginfo( 'name' ) );
	$contact_email = 'chris@tomatillodesign.com';
	$contact_link  = 'https://www.tomatillodesign.com';

	echo '<div style="overflow:hidden;">';
	echo '<img src="' . $img_url . '" alt="Chris Liu-Beers" style="float:right; margin-left:1rem; width:90px; height:90px; border-radius:50%; object-fit:cover;" loading="lazy">';
	echo '<p>Congratulations on your new website!</p>';
	echo '<p>If you have any questions, please contact me:<br>';
	echo '<a href="mailto:' . antispambot( $contact_email ) . '">' . antispambot( $contact_email ) . '</a><br>';
	echo '919.576.0180<br>';
	echo '<a href="' . esc_url( $contact_link ) . '" target="_blank" rel="noopener">Tomatillo Design</a></p>';
	echo '</div>';
}

/**
 * Remove default WP dashboard widgets to reduce clutter.
 */
add_action( 'wp_dashboard_setup', 'yak_remove_default_dashboard_widgets' );
function yak_remove_default_dashboard_widgets() {
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );

	// ⚠️ Optional removals – comment in if needed
	// remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
	// remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
	// remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
}

/**
 * Add custom credit line to the bottom of the WP admin dashboard.
 */
add_filter( 'admin_footer_text', 'yak_custom_admin_footer_text' );
function yak_custom_admin_footer_text( $footer_text ) {
	$footer_text .= ' | <span class="yak-admin-credit">A website by Chris Liu-Beers @ <a href="http://www.tomatillodesign.com" target="_blank" rel="noopener noreferrer">Tomatillo Design</a></span>';
	return $footer_text;
}

/**
 * Add a custom metabox in Appearance → Menus.
 * Adds a predefined item (search modal trigger) to the available menu items.
 */
add_action( 'admin_head-nav-menus.php', function() {
	add_meta_box(
		'posttype-yak_custom',               // Custom ID
		'Yak Custom Items',                 
		'yak_render_custom_menu_metabox',   
		'nav-menus',                        
		'side',                             
		'default'                           
	);
} );

/**
 * Output the contents of the "Yak Custom Items" metabox.
 * Provides a checkbox to insert a search icon item into menus.
 */
function yak_render_custom_menu_metabox() {
	$item_id = 99934529; // Arbitrary unique ID for the menu item
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

/**
 * Inject JavaScript to dynamically add the custom search modal item to the menu.
 * This captures the click event and inserts the menu item via AJAX.
 */
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





// =============================================================================
// 10. ACF Options Pages & ACF Settings
// =============================================================================

/**
 * Register ACF options pages for theme-wide settings.
 * This creates a top-level Theme Settings panel and multiple organized subpages.
 */
add_action( 'acf/init', function () {
	if ( function_exists( 'acf_add_options_page' ) ) {

		acf_add_options_page( [
			'page_title'  => 'Theme Settings',
			'menu_title'  => 'Theme Settings',
			'menu_slug'   => 'theme-settings',
			'icon_url'    => 'dashicons-superhero',
			'capability'  => 'manage_options',
			'redirect'    => false,
		] );

		acf_add_options_sub_page( [
			'page_title'  => 'Colors',
			'menu_title'  => 'Colors',
			'menu_slug'   => 'yak-options-colors',
			'parent_slug' => 'theme-settings',
		] );

		acf_add_options_sub_page( [
			'page_title'  => 'Typography',
			'menu_title'  => 'Typography',
			'menu_slug'   => 'yak-options-typography',
			'parent_slug' => 'theme-settings',
		] );

		acf_add_options_sub_page( [
			'page_title'  => 'Layouts',
			'menu_title'  => 'Layouts',
			'menu_slug'   => 'yak-options-layouts',
			'parent_slug' => 'theme-settings',
		] );

		acf_add_options_sub_page( [
			'page_title'  => 'Login Screen',
			'menu_title'  => 'Login Screen',
			'menu_slug'   => 'yak-options-login',
			'parent_slug' => 'theme-settings',
		] );
	}
} );

/**
 * Set the Google Maps API key for ACF map fields.
 * Note: This is a global ACF setting.
 */
function my_acf_init() {
	acf_update_setting( 'google_api_key', 'AIzaSyD-kMkqmuRLsPQe88VLRf6Xwoy_cCelJdQ' );
}
add_action( 'acf/init', 'my_acf_init' );

/**
 * Register ACF fields for the homepage notice/alert bar.
 * These fields are attached to the front page only.
 */
add_action( 'acf/init', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) return;

	acf_add_local_field_group( [
		'key' => 'group_site_notice_bar',
		'title' => 'Top Notice Bar Settings',
		'fields' => [
			[
				'key' => 'field_hello_bar_text',
				'label' => 'Notice Text',
				'name'  => 'hello_bar_text',
				'type'  => 'text',
			],
			[
				'key'   => 'field_hello_bar_start_date',
				'label' => 'Start Date',
				'name'  => 'hello_bar_start_date',
				'type'  => 'date_picker',
				'display_format' => 'Y-m-d',
				'return_format'  => 'Y-m-d',
				'first_day'      => 1,
				'wrapper'        => [ 'width' => '33' ],
			],
			[
				'key'   => 'field_hello_bar_end_date',
				'label' => 'End Date',
				'name'  => 'hello_bar_end_date',
				'type'  => 'date_picker',
				'display_format' => 'Y-m-d',
				'return_format'  => 'Y-m-d',
				'first_day'      => 1,
				'wrapper'        => [ 'width' => '33' ],
			],
			[
				'key'   => 'field_hello_bar_link',
				'label' => 'Link URL',
				'name'  => 'hello_bar_link',
				'type'  => 'url',
				'wrapper' => [ 'width' => '33' ],
			],
			[
				'key'   => 'field_link_format',
				'label' => 'Link Format',
				'name'  => 'link_format',
				'type'  => 'radio',
				'choices' => [
					'No Button' => 'No Button',
					'Button'    => 'Button',
				],
				'default_value' => 'No Button',
				'layout'        => 'horizontal',
			],
			[
				'key'               => 'field_button_text',
				'label'             => 'Button Text',
				'name'              => 'button_text',
				'type'              => 'text',
				'conditional_logic' => [
					[
						[
							'field'    => 'field_link_format',
							'operator' => '==',
							'value'    => 'Button',
						],
					],
				],
			],
			[
				'key'   => 'field_link_target',
				'label' => 'Link Target',
				'name'  => 'link_target',
				'type'  => 'select',
				'choices' => [
					'Same Tab' => 'Same Tab',
					'New Tab'  => 'New Tab',
				],
				'default_value' => 'Same Tab',
				'ui'            => 1,
			],
		],
		'location' => [
			[
				[
					'param'    => 'page',
					'operator' => '==',
					'value'    => get_option( 'page_on_front' ),
				],
			],
		],
		'menu_order'            => 3,
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'active'                => true,
		'description'           => '',
	] );
} );




// =============================================================================
// 11. Theme Access Control
// =============================================================================

/**
 * 🔒 Yak Theme Settings Access Control
 *
 * Only allow access to:
 * - Primary admin user ID (YAK_PRIMARY_USER_ID)
 * - Users listed in ACF field 'yak_allowed_users' (options page)
 *
 * Deny all others, including other administrators.
 */

/**
 * Filter capabilities for Yak theme settings access.
 * Ensures user #1 always gets full access, and allows additional users via ACF field.
 */
add_filter( 'user_has_cap', 'yak_restrict_theme_settings_capability', 10, 4 );
function yak_restrict_theme_settings_capability( $all_caps, $caps, $args, $user ) {
	// 🚨 ABSOLUTE FAILSAFE OVERRIDE FOR USER ID 1
	if ( isset( $user->ID ) && (int) $user->ID === 1 ) {
		foreach ( $caps as $cap ) {
			$all_caps[ $cap ] = true;
		}
		return $all_caps;
	}

	// Allow additional authorized users (for specific Yak pages)
	if ( in_array( 'manage_options', $caps, true ) && yak_user_is_yak_authorized( $user ) ) {
		$all_caps['manage_options'] = true;
	}

	return $all_caps;
}


/**
 * Block access to Yak settings pages for unauthorized users.
 */
add_action( 'admin_init', 'yak_restrict_theme_settings_page_access' );
function yak_restrict_theme_settings_page_access() {
	if ( ! is_admin() || empty( $_GET['page'] ) ) {
		return;
	}

	$restricted_pages = [
		'theme-settings',
		'yak-options-colors',
		'yak-options-typography',
		'yak-options-layouts',
		'yak-options-login',
	];

	if ( ! in_array( $_GET['page'], $restricted_pages, true ) ) {
		return;
	}

	if ( get_current_user_id() !== 1 && ! yak_user_is_yak_authorized() ) {
		wp_die(
			__( 'You do not have permission to access this page.', 'yak' ),
			__( 'Access Denied', 'yak' ),
			[ 'response' => 403 ]
		);
	}
}

/**
 * Hide the Theme Settings menu from unauthorized users.
 */
add_action( 'admin_menu', 'yak_hide_theme_settings_menu_for_unauthorized', 99 );
function yak_hide_theme_settings_menu_for_unauthorized() {
	if ( get_current_user_id() === 1 || yak_user_is_yak_authorized() ) {
		return;
	}

	remove_menu_page( 'theme-settings' );
	remove_submenu_page( 'theme-settings', 'yak-options-colors' );
	remove_submenu_page( 'theme-settings', 'yak-options-typography' );
	remove_submenu_page( 'theme-settings', 'yak-options-layouts' );
	remove_submenu_page( 'theme-settings', 'yak-options-login' );
}

/**
 * Hide the Permissions field group unless user is YAK_PRIMARY_USER_ID.
 */
add_filter( 'acf/get_field_group', 'yak_hide_permissions_panel_for_non_primary_admin' );
function yak_hide_permissions_panel_for_non_primary_admin( $group ) {
	if ( $group['key'] === 'group_yak_settings_permissions' && get_current_user_id() !== YAK_PRIMARY_USER_ID ) {
		$group['active'] = false;
	}
	return $group;
}

/**
 * Helper: Check if user is authorized for Yak Theme settings.
 *
 * @param WP_User|int|null $user Optional. Defaults to current user.
 * @return bool
 */
function yak_user_is_yak_authorized( $user = null ) {
	$user = $user ? ( is_object( $user ) ? $user : get_user_by( 'id', $user ) ) : wp_get_current_user();

	if ( ! $user instanceof WP_User ) {
		return false;
	}

	// ✅ Absolute override: User ID 1 always has access
	if ( (int) $user->ID === 1 ) {
		return true;
	}

	// ✅ If defined, allow access to primary user
	if ( defined( 'YAK_PRIMARY_USER_ID' ) && $user->ID === (int) YAK_PRIMARY_USER_ID ) {
		return true;
	}

	// ✅ If ACF isn't loaded, skip gracefully
	if ( ! function_exists( 'get_field' ) ) {
		return false;
	}

	$allowed_users = get_field( 'yak_allowed_users', 'option' );
	return is_array( $allowed_users ) && in_array( $user->ID, $allowed_users, true );
}





// =============================================================================
// 12. SearchWP Integration
// =============================================================================

/**
 * Customize Genesis archive output on search result pages.
 * Removes default title/image/content handlers for full override.
 */
add_action( 'genesis_before_loop', function () {
	if ( is_search() ) {
		remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
		remove_action( 'genesis_entry_content', 'genesis_do_post_content' );
		remove_action( 'genesis_entry_content', 'genesis_do_post_image' );
	}
} );

/**
 * Render fully custom search result layout using SearchWP enhancements.
 * Includes: title, post type badge, clean permalink, date, and excerpt highlighting.
 */
add_action( 'genesis_entry_content', 'clb_searchwp_custom_excerpt' );
function clb_searchwp_custom_excerpt() {
	global $post;

	if ( ! is_search() || ! $post ) return;

	// Post Type Badge
	$post_type_badge = null;
	$post_type = get_post_type_object( get_post_type() );
	if ( $post_type ) {
		$post_type_badge = '<span class="search-result-type badge">' . esc_html( ucfirst( $post_type->labels->singular_name ) ) . '</span>';
	}

	// Title
	$title     = get_the_title();
	$permalink = get_permalink();
	echo '<div class="yak-search-results-title-wrapper"><h2 class="search-result-title"><a href="' . esc_url( $permalink ) . '">' . esc_html( $title ) . '</a></h2>' . $post_type_badge . '</div>';

	// Permalink (clean format)
	$parsed = wp_parse_url( $permalink );
	$pretty_link = trim( $parsed['host'] . $parsed['path'], '/' );
	echo '<div class="search-result-url"><a href="' . esc_url( $permalink ) . '">' . esc_html( $pretty_link ) . '</a></div>';

	// Date
	echo '<div class="search-result-date">' . esc_html( get_the_date() ) . '</div>';

	// Excerpt w/ highlight
	echo '<div class="search-result-excerpt">';
	if ( function_exists( 'searchwp_term_highlight_the_excerpt_global' ) ) {
		searchwp_term_highlight_the_excerpt_global();
	} elseif ( class_exists( '\\SearchWP\\Entry' ) ) {
		$source     = \SearchWP\Utils::get_post_type_source_name( $post->post_type );
		$entry      = new \SearchWP\Entry( $source, $post->ID, false, false );
		$excerpt    = \SearchWP\Sources\Post::get_global_excerpt( $entry, get_search_query() );
		$highlighter = new \SearchWP\Highlighter();
		$excerpt    = $highlighter::apply( $excerpt, explode( ' ', get_search_query() ) );
		echo wp_kses_post( $excerpt );
	} else {
		the_excerpt();
	}
	echo '</div>';
}






// Properly unregister Genesis sidebars and prevent rendering.
add_action( 'after_setup_theme', function() {
	// Remove sidebar output early in Genesis setup.
	remove_action( 'genesis_sidebar', 'genesis_do_sidebar' );
}, 15 );

// Unregister the actual sidebar widget area.
add_action( 'widgets_init', function() {
	unregister_sidebar( 'sidebar' );
}, 11 );

// Force full-width layout (optional but recommended).
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );







/**
 * Yak Info Cards – Global Modal Relocation Target
 *
 * Outputs a bare container for modal relocation.
 */
add_action( 'wp_footer', 'yak_info_cards_modal_mount_point', 99 );
function yak_info_cards_modal_mount_point() {
	echo '<div id="yak-info-cards-modal-slot" class="yak-info-cards-modal-slot"></div>';
}
