<?php


if ( function_exists( 'acf_add_local_field_group' ) ) :

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
				'wrapper' => [
					'width' => '',
					'class' => 'yak-recommended-plugins-admin-panel',
					'id' => '',
				],
			],
		],
		'location' => [
			[
				[
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'theme-settings',
				],
			],
		],
		'menu_order' => 1,
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'active' => true,
		'description' => '',
	] );

	acf_add_local_field_group(array(
		'key' => 'group_yak_settings_permissions',
		'title' => 'Yak Theme Settings Access',
		'fields' => array(
			array(
				'key' => 'field_yak_allowed_users',
				'label' => 'Authorized Users',
				'name' => 'yak_allowed_users',
				'type' => 'user',
				'instructions' => 'Only these users can access the Yak Theme Settings page. Admin user #1 is always allowed.',
				'multiple' => 1,
				'role' => array('administrator', 'editor'),
				'return_format' => 'id',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'theme-settings',
				),
			),
		),
		'menu_order' => 2,
	));
	

	acf_add_local_field_group( [
		'key' => 'group_yak_theme_settings_logo',
		'title' => 'Yak Theme: Logo & Branding',
		'fields' => [
			[
				'key' => 'field_yak_logo_image',
				'label' => 'Logo',
				'name' => 'yak_logo_image',
				'type' => 'image',
				'instructions' => 'Upload your primary logo.',
				'return_format' => 'array',
				'preview_size' => 'medium',
				'library' => 'all',
			],
			[
				'key' => 'field_yak_favicon',
				'label' => 'Favicon',
				'name' => 'yak_favicon',
				'type' => 'image',
				'instructions' => 'Upload a 512x512 PNG favicon image.',
				'return_format' => 'array',
				'preview_size' => 'thumbnail',
				'library' => 'all',
			],
			[
				'key' => 'field_yak_logo_type',
				'label' => 'Logo Setting',
				'name' => 'yak_logo_type',
				'type' => 'button_group',
				'instructions' => 'Choose how your logo should be displayed.',
				'choices' => [
					'image' => 'Image Only',
					'image_text' => 'Image + Site Title Text',
				],
				'default_value' => 'image',
				'return_format' => 'value',
				'allow_null' => 0,
				'layout' => 'horizontal',
			],
			[
				'key' => 'field_yak_logo_max_width',
				'label' => 'Max Logo Width (px)',
				'name' => 'yak_logo_max_width',
				'type' => 'number',
				'instructions' => 'Set a maximum width for the logo in pixels.',
				'default_value' => 240,
				'min' => 50,
				'append' => 'px',
			],
			[
				'key' => 'field_yak_show_site_description',
				'label' => 'Show Site Description',
				'name' => 'yak_show_site_description',
				'type' => 'true_false',
				'ui' => 1,
				'default_value' => 0,
				'instructions' => 'Toggle to show or hide the site description in your header.',
				'wrapper' => [
                    'width' => '50',
                ],
			],
			[
                'key' => 'yak_sticky_header_desktop',
                'label' => 'Sticky Header (Desktop)',
                'name' => 'yak_sticky_header_desktop',
                'type' => 'true_false',
                'instructions' => 'Enable sticky header on large screens?',
                'default_value' => 1,
                'ui' => 1,
                'wrapper' => [
                    'width' => '50',
                ],
            ],
		],
		'location' => [
			[
				[
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'theme-settings',
				],
			],
		],
		'menu_order' => 2,
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'active' => true,
		'description' => '',
	] );
	
	endif;
	


	

function yak_get_recommended_plugins_message() {
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$plugins = [
		[
			'slugs' => ['acf/acf.php', 'advanced-custom-fields-pro/acf.php'],
			'label' => 'ACF or ACF Pro (required)',
			'link'  => 'https://www.advancedcustomfields.com/',
		],
		[
			'slugs' => ['safe-svg/safe-svg.php'],
			'label' => 'Safe SVG',
			'link'  => 'https://wordpress.org/plugins/safe-svg/',
		],
		[
			'slugs' => ['disable-comments/disable-comments.php'],
			'label' => 'Disable Comments',
			'link'  => 'https://wordpress.org/plugins/disable-comments/',
		],
		[
			'slugs' => ['yak-card-deck/yak-card-deck.php'],
			'label' => 'Tomatillo Design ~ Yak Card Deck',
			'link'  => 'https://github.com/tomatillodesign/yak-card-deck', // Example link
		],
		[
			'slugs' => ['yak-events-calendar/yak-events-calendar.php'],
			'label' => 'Tomatillo Design ~ Events Calendar',
			'link'  => 'https://github.com/tomatillodesign/yak-events-calendar', // Example link
		],
		[
			'slugs' => ['tomatillo-design-simple-collapse/tomatillo-design-simple-collapse.php'],
			'label' => 'Tomatillo Design ~ Simple Collapse',
			'link'  => 'https://github.com/tomatillodesign/tomatillo-design-simple-collapse', // Example link
		],
	];

	ob_start();
	echo '<p><strong>Yak recommends the following plugins, optimized for this theme:</strong></p><ul>';

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
	
	
	
	



add_filter('body_class', 'yak_add_custom_body_class_show_hide_site_desc_23553541');
function yak_add_custom_body_class_show_hide_site_desc_23553541($classes) {
	if (get_field('yak_show_site_description', 'option') === false) {
		$classes[] = 'yak-hide-site-description';
	} else {
        $classes[] = 'yak-show-site-description';
    }
	return $classes;
}



/**
 * Output all supported favicon types from Yak Theme Settings.
 */
function yak_output_theme_favicons() {
	$favicon = get_field( 'yak_favicon', 'option' );

	// error_log( '[yak] favicon field: ' . $favicon );

	if ( ! $favicon || empty( $favicon['url'] ) ) {
		return;
	}

	// $favicon_url = get_field( 'yak_favicon_cropped_url', 'option' );
	// if ( ! $favicon_url ) {
	// 	$favicon = get_field( 'yak_favicon', 'option' );
	// 	$favicon_url = $favicon['url'] ?? '';
	// }

	$favicon_cropped = get_field( 'yak_favicon_cropped_url', 'option' );
	$favicon = get_field( 'yak_favicon', 'option' );
	$favicon_url = '';

	if ( $favicon_cropped ) {
		$favicon_url = esc_url( $favicon_cropped );
	} elseif ( $favicon && ! empty( $favicon['url'] ) ) {
		$favicon_url = esc_url( $favicon['url'] );
	}


	if ( $favicon_url ) {
		echo '<link rel="icon" href="' . esc_url( $favicon_url ) . '?v=' . time() . '" type="image/png">';
	}


	// switch ( $favicon_ext ) {
	// 	case 'svg':
	// 		echo "<link rel='icon' href='{$favicon_url}' type='image/svg+xml'>\n";
	// 		break;

	// 	case 'ico':
	// 		echo "<link rel='icon' href='{$favicon_url}' type='image/x-icon'>\n";
	// 		break;

	// 	case 'png':
	// 	default:
	// 		// Standard high-res favicon
	// 		echo "<link rel='icon' href='{$favicon_url}' sizes='any' type='image/png'>\n";
	// 		break;
	// }

	// Also check for an Apple Touch Icon (if uploaded separately)
	$apple_icon = get_field( 'yak_apple_touch_icon', 'option' );
	if ( $apple_icon && ! empty( $apple_icon['url'] ) ) {
		$apple_url = esc_url( $apple_icon['url'] );
		echo "<link rel='apple-touch-icon' href='{$apple_url}'>\n";
	}

}

add_action( 'wp_head', 'yak_output_theme_favicons' );
add_action( 'admin_head', 'yak_output_theme_favicons' );
add_action( 'login_head', 'yak_output_theme_favicons' );

// remove default genesis favicon actions
remove_action( 'wp_head', 'genesis_load_favicon' );

// logic upon save, generate the 512x512 PNG file
add_action( 'acf/save_post', 'yak_crop_favicon_if_needed', 20 );
function yak_crop_favicon_if_needed( $post_id ) {
	if ( $post_id !== 'options' ) return;

	$favicon = get_field( 'yak_favicon', 'option' );

	if ( ! $favicon || empty( $favicon['ID'] ) ) return;

	$attachment_id = $favicon['ID'];
	$file_path = get_attached_file( $attachment_id );

	if ( ! file_exists( $file_path ) ) return;

	$ext = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );
	$is_png = $ext === 'png';

	$editor = wp_get_image_editor( $file_path );
	if ( is_wp_error( $editor ) ) return;

	$size = $editor->get_size();
	$is_square = $size['width'] === $size['height'];

	// ✅ If already a square PNG, skip crop and use original
	if ( $is_square && $is_png ) {
		delete_field( 'yak_favicon_cropped_url', 'option' );
		update_field( 'yak_favicon_cropped_url', esc_url_raw( $favicon['url'] ), 'option' );
		return;
	}

	// Crop square center from shortest side
	$dimension = min( $size['width'], $size['height'] );
	$left = ( $size['width'] - $dimension ) / 2;
	$top  = ( $size['height'] - $dimension ) / 2;

	$editor->crop( $left, $top, $dimension, $dimension );
	$editor->resize( 512, 512, true ); // force 512x512 output

	$upload_dir = wp_upload_dir();
	$filename   = 'yak-favicon-cropped-' . $attachment_id . '.png';
	$save_path  = trailingslashit( $upload_dir['path'] ) . $filename;

	$saved = $editor->save( $save_path, 'image/png', 5 );
	if ( is_wp_error( $saved ) ) return;

	$cropped_url = trailingslashit( $upload_dir['url'] ) . $filename;
	update_field( 'yak_favicon_cropped_url', esc_url_raw( $cropped_url ), 'option' );
}


// add logo to the site header
add_action( 'genesis_site_title', 'yak_output_custom_logo_and_title', 5 );
function yak_output_custom_logo_and_title() {
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}

	$logo       = get_field( 'yak_logo_image', 'option' );
	$logo_type  = get_field( 'yak_logo_type', 'option' );
	$show_desc  = get_field( 'yak_show_site_description', 'option' );
	$site_name  = get_bloginfo( 'name' );
	$site_url   = esc_url( home_url( '/' ) );
	$max_width  = get_field( 'yak_logo_max_width', 'option' ) ?: 240;

	// Logo (always shows if uploaded)
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

	// Only show title/desc if logo setting is 'image_text'
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







// add_action( 'genesis_site_title', 'yak_output_acf_site_logo', 5 );
// function yak_output_acf_site_logo() {
// 	if ( ! function_exists( 'get_field' ) ) return;

// 	$logo = get_field( 'yak_logo_image', 'option' );
// 	if ( ! $logo || empty( $logo['url'] ) ) return;

// 	$max_width = get_field( 'yak_logo_max_width', 'option' ) ?: 240;
// 	$site_name = get_bloginfo( 'name' );

// 	printf(
// 		'<a class="yak-site-logo" href="%s" aria-label="%s" style="display:inline-block;max-width:%dpx;">
// 			<img src="%s" alt="%s" style="width:100%%;height:auto;">
// 		</a>',
// 		esc_url( home_url( '/' ) ),
// 		esc_attr( $site_name ),
// 		absint( $max_width ),
// 		esc_url( $logo['url'] ),
// 		esc_attr( $site_name )
// 	);
// }

add_action( 'init', 'yak_remove_default_genesis_header_markup' );
function yak_remove_default_genesis_header_markup() {
	if ( is_admin() || ! function_exists( 'get_field' ) ) {
		return;
	}

	remove_action( 'genesis_site_title', 'genesis_seo_site_title' );
	remove_action( 'genesis_site_description', 'genesis_seo_site_description' );
}




add_filter( 'body_class', 'yak_add_sticky_header_class' );
function yak_add_sticky_header_class( $classes ) {
	if ( is_admin() ) {
		return $classes;
	}

	$enabled = get_field( 'yak_sticky_header_desktop', 'option' );

	if ( $enabled ) {
		$classes[] = 'yak-sticky-header-enabled';
	} else {
		$classes[] = 'yak-sticky-header-disabled';
	}

	return $classes;
}








