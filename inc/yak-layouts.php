<?php


if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group([
        'key' => 'group_yak_layout_settings',
        'title' => 'Layout Settings',
        'fields' => [
            [
                'key' => 'field_yak_content_width_note',
                'label' => '',
                'name' => '',
                'type' => 'message',
                'message' => '
                    <strong>Content Width Tips:</strong><br>
                    <strong>ch</strong> (character units): Best for readable text. Use <code>60–75ch</code>.<br>
                    <strong>rem</strong>: Scalable with font size. Use <code>40–55rem</code>.<br>
                    <strong>px</strong>: Fixed layout. Range: <code>640–960px</code>.<br>
                    <strong>Recommended default:</strong> <code>70ch</code>
                ',
                'wrapper' => ['width' => '100%'],
            ],            
            [
                'key' => 'field_yak_content_max_width',
                'label' => 'Content Max Width',
                'name' => 'yak_content_max_width',
                'type' => 'number',
                'instructions' => 'Maximum width for main content areas. Example: 75',
                'default_value' => 75,
                'placeholder' => 75,
                'min' => 0,
                'step' => 1,
                'prepend' => 'width:',
                'wrapper' => ['width' => '50%'],
            ],
            [
                'key' => 'field_yak_content_max_width_unit',
                'label' => 'Units',
                'name' => 'yak_content_max_width_unit',
                'type' => 'select',
                'instructions' => 'Choose the unit for max width. ',
                'choices' => [
                    'ch' => 'ch',
                    'rem' => 'rem',
                    'px' => 'px',
                ],
                'default_value' => 'ch',
                'allow_null' => 0,
                'multiple' => 0,
                'ui' => 0,
                'wrapper' => ['width' => '50%'],
            ],
            [
                'key' => 'field_yak_border_radius',
                'label' => 'Border Radius',
                'name' => 'yak_border_radius',
                'type' => 'number',
                'instructions' => 'Applies to buttons, inputs, containers, and card corners.',
                'default_value' => 0,
                'min' => 0,
                'max' => 20,
                'step' => 1,
                'prepend' => '',
                'append' => 'px',
                'wrapper' => ['width' => '50%'],
            ],
            [
                'key' => 'field_yak_blog_format',
                'label' => 'Blog Format',
                'name' => 'yak_blog_format',
                'type' => 'radio',
                'instructions' => 'Choose how blog posts are displayed on archive pages.',
                'choices' => [
                    'list'  => 'List',
                    'cards' => 'Cards',
                ],
                'default_value' => 'list',
                'layout' => 'horizontal',
                'return_format' => 'value',
                'wrapper' => ['width' => '50%'],
            ],
            [
                'key' => 'yak_number_of_footer_widgets',
                'label' => 'Number of Footer Widgets',
                'name' => 'yak_number_of_footer_widgets',
                'type' => 'range',
                'instructions' => 'Choose how many footer widget areas to display (0–4)',
                'min' => 0,
                'max' => 4,
                'step' => 1,
                'default_value' => 3,
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
                    'value' => 'yak-options-layouts',
                ],
            ],
        ],
        'menu_order' => 0,
    ]);
}




add_action('wp_head', 'yak_output_layout_css_vars');
add_action('admin_head', 'yak_output_layout_css_vars');

function yak_output_layout_css_vars() {
	if (!function_exists('get_field')) return;

	// Get max content width
	$width = get_field('yak_content_max_width', 'option');
	$unit  = get_field('yak_content_max_width_unit', 'option');
	$css_width = ($width && $unit) ? floatval($width) . $unit : '75ch';

	// Get border radius
	$radius = get_field('yak_border_radius', 'option');
	$radius = ($radius !== '' && $radius !== null) ? intval($radius) : 0;
	$radius = max(0, min(20, $radius)); // Enforce bounds

	// Output CSS variables
	echo '<style id="yak-layout-vars">' . PHP_EOL;
	echo ':root {' . PHP_EOL;
	echo "  --yak-content-max-width: {$css_width};" . PHP_EOL;
	echo "  --yak-radius: {$radius}px;" . PHP_EOL;
	echo '}' . PHP_EOL;
	echo '</style>' . PHP_EOL;
}



add_filter('body_class', 'yak_add_sticky_header_body_class');
function yak_add_sticky_header_body_class($classes) {
	if (is_admin()) {
		return $classes;
	}

	$enabled = get_field('yak_sticky_header_desktop', 'option');
	if ($enabled) {
		$classes[] = 'yak-has-sticky-header';
	}
	return $classes;
}
