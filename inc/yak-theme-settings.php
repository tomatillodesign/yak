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
				'label' => 'Authorized Users',
				'name' => 'yak_allowed_users',
				'type' => 'user',
				'instructions' => 'Only these users can access the Yak Theme Settings page. Admin user #1 is always allowed.',
				'multiple' => 1,
				'role' => [ 'administrator', 'site-manager', 'editor' ],
				'return_format' => 'id',
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

	$plugins = [
		// Core plugin dependencies
		[ 'slugs' => ['acf/acf.php', 'advanced-custom-fields-pro/acf.php'], 'label' => 'ACF or ACF Pro (required)', 'link' => 'https://www.advancedcustomfields.com/' ],
		[ 'slugs' => ['yak-card-deck/yak-card-deck.php'], 'label' => 'Tomatillo Design ~ Yak Card Deck (Info Cards)', 'link' => 'https://github.com/tomatillodesign/yak-card-deck' ],
		[ 'slugs' => ['yak-events-calendar/yak-events-calendar.php'], 'label' => 'Tomatillo Design ~ Events Calendar (if applicable)', 'link' => 'https://github.com/tomatillodesign/yak-events-calendar' ],
		[ 'slugs' => ['tomatillo-design-simple-collapse/tomatillo-design-simple-collapse.php'], 'label' => 'Tomatillo Design ~ Simple Collapse', 'link' => 'https://github.com/tomatillodesign/tomatillo-design-simple-collapse' ],
		[ 'slugs' => ['tomatillo-design-site-manager/tomatillo-design-site-manager.php'], 'label' => 'Tomatillo Design ~ Site Manager', 'link' => 'https://github.com/tomatillodesign/site-manager' ],
		[ 'slugs' => ['yakstretch-cover-block/yakstretch-cover-block.php'], 'label' => 'Tomatillo Design ~ Yakstretch', 'link' => 'https://github.com/tomatillodesign/yakstretch-cover-block' ],
	];

	ob_start();
	echo '<p><strong>Yak recommends the following custom plugins, optimized for this theme:</strong></p><ul>';

	foreach ( $plugins as $plugin ) {
		$active = false;
		foreach ( $plugin['slugs'] as $slug ) {
			if ( is_plugin_active( $slug ) ) {
				$active = true;
				break;
			}
		}
		$status = $active ? '✅' : '❌';
		$label = esc_html( $plugin['label'] );
		if ( ! empty( $plugin['link'] ) ) {
			$url = esc_url( $plugin['link'] );
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
