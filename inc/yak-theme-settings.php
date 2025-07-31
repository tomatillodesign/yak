<?php

/////////////////////////////////////////////////////////////////////////////////
// Yak Theme: ACF-Powered Theme Options and Display Logic
// This file contains all logic for admin configuration and front-end output
// related to recommended plugins, user permissions, logo/branding, and favicon.
/////////////////////////////////////////////////////////////////////////////////

// Check if ACF is active before defining local field groups
if ( function_exists( 'acf_add_local_field_group' ) ) :

	// Register admin message panel showing recommended Yak-related plugins
	acf_add_local_field_group( [
		'key' => 'group_yak_theme_recommended_plugins',
		'title' => 'Yak Theme: Recommended Plugins',
		'fields' => [
			[
				'key' => 'field_yak_recommended_plugins',
				'label' => '',
				'name' => 'yak_recommended_plugins',
				'type' => 'message',
				'message' => yak_get_recommended_plugins_message(),
				'wrapper' => [ 'class' => 'yak-recommended-plugins-admin-panel' ],
			],
		],
		'location' => [[['param' => 'options_page','operator' => '==','value' => 'theme-settings']]],
	] );

	// Limit Yak theme settings page access to specific users
	acf_add_local_field_group([
		'key' => 'group_yak_settings_permissions',
		'title' => 'Yak Theme: Settings Access',
		'fields' => [
			[
				'key' => 'field_yak_allowed_users',
				'label' => 'Additional Authorized Users',
				'name' => 'yak_allowed_users',
				'type' => 'user',
				'instructions' => 'These additional users can access the Yak Theme Settings page.',
				'multiple' => 1,
				'role' => [ 'administrator', 'site-manager', 'editor' ],
				'return_format' => 'id',
			],
			[
				'key' => 'field_yak_dev_mode',
				'label' => 'Enable Developer Mode',
				'name' => 'yak_dev_mode',
				'type' => 'true_false',
				'instructions' => 'When enabled, developer tools and debugging output will be available throughout the theme.',
				'message' => 'Developer Mode',
				'default_value' => 0,
				'ui' => 1,
			],
		],
		'location' => [
			[
				[
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'theme-settings',
				],
			],
		],
	]);

	// Branding settings: logo image, favicon, and display options
	acf_add_local_field_group( [
		'key' => 'group_yak_theme_settings_logo',
		'title' => 'Yak Theme: Logo & Branding',
		'fields' => [
			// Main logo upload
			[ 'key' => 'field_yak_logo_image', 'label' => 'Logo', 'name' => 'yak_logo_image', 'type' => 'image', 'instructions' => 'Upload your primary logo.', 'return_format' => 'array', 'preview_size' => 'medium', 'library' => 'all' ],
			// Favicon upload (512x512 PNG preferred)
			[ 'key' => 'field_yak_favicon', 'label' => 'Favicon', 'name' => 'yak_favicon', 'type' => 'image', 'instructions' => 'Upload a 512x512 PNG favicon image.', 'return_format' => 'array', 'preview_size' => 'thumbnail', 'library' => 'all' ],
			// Whether logo includes text next to image
			[ 'key' => 'field_yak_logo_type', 'label' => 'Logo Setting', 'name' => 'yak_logo_type', 'type' => 'button_group', 'instructions' => 'Choose how your logo should be displayed.', 'choices' => [ 'image' => 'Image Only', 'image_text' => 'Image + Site Title Text' ], 'default_value' => 'image', 'return_format' => 'value', 'layout' => 'horizontal' ],
			// Set max width for header logo
			[ 'key' => 'field_yak_logo_max_width', 'label' => 'Max Logo Width (px)', 'name' => 'yak_logo_max_width', 'type' => 'number', 'instructions' => 'Set a maximum width for the logo in pixels.', 'default_value' => 240, 'min' => 50, 'append' => 'px' ],
			// Show/hide site description text
			[ 'key' => 'field_yak_show_site_description', 'label' => 'Show Site Description', 'name' => 'yak_show_site_description', 'type' => 'true_false', 'instructions' => 'Toggle to show or hide the site description in your header.', 'default_value' => 0, 'ui' => 1, 'wrapper' => ['width' => '50'] ],
			// Enable sticky header on desktop only
			[ 'key' => 'yak_sticky_header_desktop', 'label' => 'Sticky Header (Desktop)', 'name' => 'yak_sticky_header_desktop', 'type' => 'true_false', 'instructions' => 'Enable sticky header on large screens?', 'default_value' => 1, 'ui' => 1, 'wrapper' => ['width' => '50'] ],
		],
		'location' => [[['param' => 'options_page','operator' => '==','value' => 'theme-settings']]],
	] );

endif;


// Render a custom plugin checklist for Yak users
function yak_get_recommended_plugins_message() {
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	// List of recommended plugins with possible slugs
	$plugins = [
		[ 'slugs' => ['advanced-custom-fields-pro/acf.php'], 'label' => 'ACF Pro (required)', 'link' => 'https://www.advancedcustomfields.com/' ],
		[ 'slugs' => ['tomatillo-design-yak-info-cards/yak-card-deck.php'], 'label' => 'Tomatillo Design ~ Info Cards', 'link' => 'https://github.com/tomatillodesign/tomatillo-design-yak-info-cards' ],
		[ 'slugs' => ['tomatillo-design-yak-events-calendar/yak-events-calendar.php'], 'label' => 'Tomatillo Design ~ Events Calendar (if applicable)', 'link' => 'https://github.com/tomatillodesign/tomatillo-design-yak-events-calendar' ],
		[ 'slugs' => ['tomatillo-design-avif-everywhere/tomatillo-avif-everywhere.php'], 'label' => 'Tomatillo Design ~ AVIF Everywhere', 'link' => 'https://github.com/tomatillodesign/tomatillo-design-avif-everywhere' ],
		[ 'slugs' => ['tomatillo-design-simple-collapse/tomatillo-design-simple-collapse.php'], 'label' => 'Tomatillo Design ~ Simple Collapse', 'link' => 'https://github.com/tomatillodesign/tomatillo-design-simple-collapse' ],
		[ 'slugs' => ['tomatillo-design-site-manager-role/tomatillo-design-site-manager.php'], 'label' => 'Tomatillo Design ~ Site Manager', 'link' => 'https://github.com/tomatillodesign/tomatillo-design-site-manager-role' ],
		[ 'slugs' => ['tomatillo-design-yakstretch-cover-block/yakstretch-cover-block.php'], 'label' => 'Tomatillo Design ~ Yakstretch Cover Block', 'link' => 'https://github.com/tomatillodesign/tomatillo-design-yakstretch-cover-block' ],
	];

	// Get list of all installed plugins
	$all_plugins = get_plugins();

	ob_start();
	echo '<p><strong>Yak recommends the following custom plugins, optimized for this theme:</strong></p><ul>';

	foreach ( $plugins as $plugin ) {
		$active = false;

		foreach ( $plugin['slugs'] as $expected_slug ) {
			$expected_filename = basename( $expected_slug );

			foreach ( $all_plugins as $installed_path => $plugin_data ) {
				if ( str_ends_with( $installed_path, $expected_filename ) ) {
					if ( is_plugin_active( $installed_path ) ) {
						$active = true;
						break 2; // Break both loops
					}
				}
			}
		}

		$status = $active ? '✅' : '📦';
		$label  = esc_html( $plugin['label'] );
		if ( ! empty( $plugin['link'] ) ) {
			$url   = esc_url( $plugin['link'] );
			$label = "<a href=\"{$url}\" target=\"_blank\" rel=\"noopener noreferrer\">{$label}</a>";
		}
		echo "<li>{$status} {$label}</li>";
	}

	echo '</ul>';
	return ob_get_clean();
}





// ------------------------------------------
// Body class for site description visibility toggle
// Adds a class to <body> based on ACF toggle for site description
// ------------------------------------------
add_filter( 'body_class', 'yak_add_custom_body_class_show_hide_site_desc_23553541' );
function yak_add_custom_body_class_show_hide_site_desc_23553541( $classes ) {
	if ( get_field( 'yak_show_site_description', 'option' ) === false ) {
		$classes[] = 'yak-hide-site-description';
	} else {
		$classes[] = 'yak-show-site-description';
	}
	return $classes;
}


// ------------------------------------------
// Output favicon and Apple Touch Icon in <head>
// Pulls from ACF fields and supports fallback to square crop
// ------------------------------------------
function yak_output_theme_favicons() {
	$favicon = get_field( 'yak_favicon', 'option' );
	if ( ! $favicon || empty( $favicon['url'] ) ) return;

	// Check for pre-cropped square version
	$favicon_cropped = get_field( 'yak_favicon_cropped_url', 'option' );
	$favicon_url = $favicon_cropped ?: $favicon['url'];

	if ( $favicon_url ) {
		echo '<link rel="icon" href="' . esc_url( $favicon_url ) . '?v=' . time() . '" type="image/png">';
	}

	// Optional Apple Touch Icon
	$apple_icon = get_field( 'yak_apple_touch_icon', 'option' );
	if ( $apple_icon && ! empty( $apple_icon['url'] ) ) {
		echo '<link rel="apple-touch-icon" href="' . esc_url( $apple_icon['url'] ) . '">';
	}
}
add_action( 'wp_head', 'yak_output_theme_favicons' );
add_action( 'admin_head', 'yak_output_theme_favicons' );
add_action( 'login_head', 'yak_output_theme_favicons' );

// Remove default Genesis favicon if present
remove_action( 'wp_head', 'genesis_load_favicon' );


// ------------------------------------------
// Automatically crop favicon image if needed (512x512 square)
// Runs after ACF saves options, triggered only for 'options' post ID
// ------------------------------------------
add_action( 'acf/save_post', 'yak_crop_favicon_if_needed', 20 );
function yak_crop_favicon_if_needed( $post_id ) {
	if ( $post_id !== 'options' ) return;

	$favicon = get_field( 'yak_favicon', 'option' );
	if ( ! $favicon || empty( $favicon['ID'] ) ) return;

	$file_path = get_attached_file( $favicon['ID'] );
	if ( ! file_exists( $file_path ) ) return;

	$ext = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );
	$is_png = ( $ext === 'png' );

	$editor = wp_get_image_editor( $file_path );
	if ( is_wp_error( $editor ) ) return;

	$size = $editor->get_size();
	$is_square = $size['width'] === $size['height'];

	if ( $is_square && $is_png ) {
		// Already acceptable, reuse original
		delete_field( 'yak_favicon_cropped_url', 'option' );
		update_field( 'yak_favicon_cropped_url', esc_url_raw( $favicon['url'] ), 'option' );
		return;
	}

	// Crop and resize to 512x512
	$dim = min( $size['width'], $size['height'] );
	$left = ( $size['width'] - $dim ) / 2;
	$top = ( $size['height'] - $dim ) / 2;

	$editor->crop( $left, $top, $dim, $dim );
	$editor->resize( 512, 512, true );

	$upload_dir = wp_upload_dir();
	$filename = 'yak-favicon-cropped-' . $favicon['ID'] . '.png';
	$save_path = trailingslashit( $upload_dir['path'] ) . $filename;

	$saved = $editor->save( $save_path, 'image/png', 5 );
	if ( is_wp_error( $saved ) ) return;

	$cropped_url = trailingslashit( $upload_dir['url'] ) . $filename;
	update_field( 'yak_favicon_cropped_url', esc_url_raw( $cropped_url ), 'option' );
}


// ------------------------------------------
// Render custom logo and optional site title/description in header
// Hooked into genesis_site_title in place of default Genesis logic
// ------------------------------------------
add_action( 'genesis_site_title', 'yak_output_custom_logo_and_title', 5 );
function yak_output_custom_logo_and_title() {
	if ( ! function_exists( 'get_field' ) ) return;

	$logo       = get_field( 'yak_logo_image', 'option' );
	$logo_type  = get_field( 'yak_logo_type', 'option' );
	$show_desc  = get_field( 'yak_show_site_description', 'option' );
	$site_name  = get_bloginfo( 'name' );
	$site_url   = esc_url( home_url( '/' ) );
	$max_width  = get_field( 'yak_logo_max_width', 'option' ) ?: 240;

	// Output logo if set
	if ( $logo && ! empty( $logo['url'] ) ) {
		printf(
			'<a class="yak-site-logo" href="%s" aria-label="%s" style="max-width:%dpx;">
				<img src="%s" alt="%s" style="width:100%%;height:auto;">
			</a>',
			$site_url,
			esc_attr( $site_name ),
			absint( $max_width ),
			esc_url( $logo['url'] ),
			esc_attr( $site_name )
		);
	}

	// If image + text option is selected, include title and optional tagline
	if ( $logo_type === 'image_text' ) {
		echo '<div class="yak-title-text">';
		printf(
			'<p class="site-title" itemprop="headline">
				<a href="%s">%s</a>
			</p>',
			$site_url,
			esc_html( $site_name )
		);

		if ( $show_desc ) {
			$description = get_bloginfo( 'description' );
			if ( $description ) {
				printf(
					'<p class="site-description" itemprop="description">%s</p>',
					esc_html( $description )
				);
			}
		}
		echo '</div>'; // .yak-title-text
	}
}


// ------------------------------------------
// Remove default Genesis SEO title/description markup
// Prevents output duplication when using custom logo logic
// ------------------------------------------
add_action( 'init', 'yak_remove_default_genesis_header_markup' );
function yak_remove_default_genesis_header_markup() {
	if ( is_admin() || ! function_exists( 'get_field' ) ) return;

	remove_action( 'genesis_site_title', 'genesis_seo_site_title' );
	remove_action( 'genesis_site_description', 'genesis_seo_site_description' );
}


// ------------------------------------------
// Add sticky header class to body
// Driven by ACF setting: yak_sticky_header_desktop
// ------------------------------------------
add_filter( 'body_class', 'yak_add_sticky_header_class' );
function yak_add_sticky_header_class( $classes ) {
	if ( is_admin() ) return $classes;

	$enabled = get_field( 'yak_sticky_header_desktop', 'option' );
	$classes[] = $enabled ? 'yak-sticky-header-enabled' : 'yak-sticky-header-disabled';
	return $classes;
}


// DEV Mode

add_filter( 'body_class', 'yak_add_dev_mode_body_class_admin_only_9991' );
function yak_add_dev_mode_body_class_admin_only_9991( $classes ) {
	if (
		function_exists( 'get_field' ) &&
		get_field( 'yak_dev_mode', 'option' ) &&
		current_user_can( 'manage_options' )
	) {
		$classes[] = 'yak-dev-mode-95ac0a';
	}
	return $classes;
}



function yak_is_dev_mode_enabled() {
	// if ( is_user_logged_in() && get_current_user_id() === 1 ) {
	// 	return true;
	// }

	// Use raw option if ACF isn't ready yet
	if ( ! function_exists( 'get_field' ) ) {
		return get_option( 'options_yak_dev_mode' ) ? true : false;
	}

	return get_field( 'yak_dev_mode', 'option' ) ? true : false;
}


add_action('genesis_before', 'clb_yak_dev_mode_genesis_markup');
function clb_yak_dev_mode_genesis_markup() {
	
	if ( yak_is_dev_mode_enabled() ) {

	$genesis_hooks_to_expose = [
		'genesis_before',
		'genesis_before_header',
		'genesis_header',
		'genesis_header_right',
		'genesis_after_header',
		'genesis_before_content_sidebar_wrap',
		'genesis_before_content',
		'genesis_before_loop',
		'genesis_loop',
		'genesis_before_entry',
		'genesis_entry_header',
		'genesis_entry_content',
		'genesis_entry_footer',
		'genesis_after_entry',
		'genesis_after_loop',
		'genesis_after_content',
		'genesis_sidebar',
		'genesis_before_footer',
		'genesis_footer',
		'genesis_after_footer',
	];

	foreach ( $genesis_hooks_to_expose as $hook_name ) {
		add_action( $hook_name, function() use ( $hook_name ) {
			echo '<div class="yak-genesis-hook" data-hook="' . esc_attr( $hook_name ) . '"></div>';
		}, 1 ); // early priority to avoid overlap
	}
	}
}

