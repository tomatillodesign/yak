<?php


/////////////////////////////////////////////////////////////////////////////////
// *** Custom Login 
/////////////////////////////////////////////////////////////////////////////////
add_action('login_enqueue_scripts', 'yak_custom_login_styles');
function yak_custom_login_styles() {
	wp_enqueue_style(
		'yak-login-style',
		get_stylesheet_directory_uri() . '/login/login-style.css',
		[],
		filemtime(get_stylesheet_directory() . '/login/login-style.css')
	);

	$bg_url = get_field('login_background_image', 'option');
	$logo_url = get_field('login_logo', 'option');

	if ($bg_url || $logo_url) {
		echo '<style>';
		if ($bg_url) {
			echo 'body.login { 
				background-image: url(' . esc_url($bg_url) . ') !important; 
				background-size: cover !important; 
				background-position: center center !important;
				background-repeat: no-repeat !important;
			}';
		}
		if ($logo_url) {
			echo 'body.login h1 a {
				background-image: url(' . esc_url($logo_url) . ') !important;
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
			[
				'key' => 'field_login_bg',
				'label' => 'Background Image',
				'name' => 'login_background_image',
				'type' => 'image',
				'instructions' => 'Upload a background image for the login screen.',
				'return_format' => 'url',
				'preview_size' => 'medium',
				'library' => 'all',
			],
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


/////////////////////////////////////////////////////////////////////////////////
// END Custom Login 
/////////////////////////////////////////////////////////////////////////////////

