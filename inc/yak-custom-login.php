<?php
/**
 * Yak Login Screen Customization
 * Supports background image or radial gradient, custom logo, and optional message.
 */

add_action('login_enqueue_scripts', 'yak_custom_login_styles');
function yak_custom_login_styles() {
	wp_enqueue_style(
		'yak-login-style',
		get_stylesheet_directory_uri() . '/login/login-style.css',
		[],
		filemtime(get_stylesheet_directory() . '/login/login-style.css')
	);

	$type    = get_field('login_background_type', 'option');
	$image   = get_field('login_background_image', 'option');
	$center  = get_field('login_gradient_center', 'option') ?: '#ffffff';
	$edge    = get_field('login_gradient_edge', 'option') ?: '#cccccc';
	$logo    = get_field('login_logo', 'option');

	echo '<style>';

	// Background output based on selection
	if ($type === 'image' && $image) {
		echo 'body.login {
			background-image: url(' . esc_url($image) . ') !important;
			background-size: cover !important;
			background-position: center center !important;
			background-repeat: no-repeat !important;
		}';
	} elseif ($type === 'gradient') {
		echo 'body.login {
			background: radial-gradient(circle, ' . esc_html($center) . ' 0%, ' . esc_html($edge) . ' 100%) !important;
		}';
	}

	// Custom logo styles
	if ($logo) {
		echo 'body.login h1 a {
			background-image: url(' . esc_url($logo) . ') !important;
			background-size: contain !important;
			background-repeat: no-repeat !important;
			width: 320px !important;
			height: 100px !important;
			display: block !important;
			text-indent: -9999px !important;
			overflow: hidden !important;
			margin: 0 auto 30px !important;
		}';
	}

	echo '</style>';
}

add_action('login_message', 'yak_custom_login_message');
function yak_custom_login_message($message) {
	$custom = get_field('login_message', 'option');
	if ($custom) {
		return '<div class="yak-custom-login-message" style="text-align:center; margin-bottom:20px;">' . wp_kses_post($custom) . '</div>' . $message;
	}
	return $message;
}

if (function_exists('acf_add_local_field_group')) {
	acf_add_local_field_group([
		'key' => 'group_login_screen',
		'title' => 'Login Screen Customization',
		'fields' => [

			// Background type selector
			[
				'key' => 'field_login_bg_type',
				'label' => 'Background Type',
				'name' => 'login_background_type',
				'type' => 'button_group',
				'choices' => [
					'image' => 'Image',
					'gradient' => 'Radial Gradient',
				],
				'default_value' => 'image',
				'layout' => 'horizontal',
			],

			// Background image (conditional)
			[
				'key' => 'field_login_bg_image',
				'label' => 'Background Image',
				'name' => 'login_background_image',
				'type' => 'image',
				'instructions' => 'Upload a background image for the login screen.',
				'return_format' => 'url',
				'preview_size' => 'medium',
				'library' => 'all',
				'conditional_logic' => [
					[
						[
							'field' => 'field_login_bg_type',
							'operator' => '==',
							'value' => 'image',
						],
					],
				],
			],

			// Gradient center color (conditional)
			[
				'key' => 'field_login_gradient_center',
				'label' => 'Gradient Center Color',
				'name' => 'login_gradient_center',
				'type' => 'color_picker',
				'conditional_logic' => [
					[
						[
							'field' => 'field_login_bg_type',
							'operator' => '==',
							'value' => 'gradient',
						],
					],
				],
			],

			// Gradient edge color (conditional)
			[
				'key' => 'field_login_gradient_edge',
				'label' => 'Gradient Outer Edge Color',
				'name' => 'login_gradient_edge',
				'type' => 'color_picker',
				'conditional_logic' => [
					[
						[
							'field' => 'field_login_bg_type',
							'operator' => '==',
							'value' => 'gradient',
						],
					],
				],
			],

			// Optional logo
			[
				'key' => 'field_login_logo',
				'label' => 'Logo Image',
				'name' => 'login_logo',
				'type' => 'image',
				'instructions' => 'Optional logo to display above the login form.',
				'return_format' => 'url',
				'preview_size' => 'medium',
				'library' => 'all',
			],

			// Optional message
			[
				'key' => 'field_login_message',
				'label' => 'Custom Message',
				'name' => 'login_message',
				'type' => 'textarea',
				'instructions' => 'Optional message or welcome text displayed on the login screen.',
				'default_value' => '',
				'placeholder' => 'Welcome to the [Site Name] login.',
				'maxlength' => '',
				'rows' => 4,
			],
		],
		'location' => [
			[
				[
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'yak-options-login',
				],
			],
		],
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
	]);
}
