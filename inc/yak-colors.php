<?php

/////////////////////////////////////////////////////////////////////////////////
// *** Theme Colors 
/////////////////////////////////////////////////////////////////////////////////

if (function_exists('acf_add_local_field_group')) {
	acf_add_local_field_group([
		'key' => 'group_yak_theme_colors',
		'title' => 'Theme Colors',
		'fields' => [
			[
				'key' => 'field_yak_base_color',
				'label' => 'Base Color',
				'name' => 'yak_base_color',
				'type' => 'color_picker',
				'instructions' => 'Primary brand color used to generate your theme palette.',
				'default_value' => '#2F5E98',
			],
			[
				'key' => 'field_yak_accent_color',
				'label' => 'Accent Color',
				'name' => 'yak_accent_color',
				'type' => 'color_picker',
				'instructions' => 'Optional secondary or highlight color.',
				'default_value' => '',
			],
			[
				'key' => 'field_yak_additional_colors',
				'label' => 'Additional Colors',
				'name' => 'yak_additional_colors',
				'type' => 'repeater',
				'instructions' => 'Manually add any other brand or UI colors.',
				'collapsed' => 'field_yak_color_name',
				'min' => 0,
				'layout' => 'row',
				'button_label' => 'Add Color',
				'sub_fields' => [
					[
						'key' => 'field_yak_color_name',
						'label' => 'Color Name',
						'name' => 'name',
						'type' => 'text',
						'instructions' => 'A human-readable name for the color (e.g. "Sky Blue", "Error Red")',
						'required' => 1,
					],
					[
						'key' => 'field_yak_color_value',
						'label' => 'Color Value',
						'name' => 'hex',
						'type' => 'color_picker',
						'required' => 1,
					],
				],
			],
			[
				'key' => 'field_yak_disable_custom_colors',
				'label' => 'Disable Custom Colors',
				'name' => 'yak_disable_custom_colors',
				'type' => 'true_false',
				'ui' => 1,
				'instructions' => 'Prevent users from picking arbitrary colors in the editor. Only your theme palette will be available.',
				'default_value' => 1,
			],
			[
				'key' => 'field_yak_selected_editor_colors',
				'label' => 'Custom WP Editor Colors',
				'name' => 'yak_selected_editor_colors',
				'type' => 'repeater',
				'instructions' => '',
				'required' => 0,
				'collapsed' => 'field_yak_selected_slug',
				'min' => 0,
				'layout' => 'table',
				'button_label' => 'Add Color',
				'sub_fields' => [
					[
						'key' => 'field_yak_selected_slug',
						'label' => 'Name',
						'name' => 'name',
						'type' => 'text',
						'required' => 1,
					],
					[
						'key' => 'field_yak_selected_hex',
						'label' => 'Color',
						'name' => 'hex',
						'type' => 'color_picker',
						'required' => 1,
					],
				],
				'wrapper' => [
					'width' => '',
					'class' => 'yak-hidden-editor-color-selector',
					'id' => '',
				],
			]			
		],
		'location' => [
			[
				[
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'yak-options-colors',
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

	acf_add_local_field_group([
		'key' => 'group_yak_gradients',
		'title' => 'Yak Theme Gradients',
		'fields' => [
			[
				'key' => 'field_yak_disable_custom_gradients',
				'label' => 'Disable Custom Gradients',
				'name' => 'yak_disable_custom_gradients',
				'type' => 'true_false',
				'ui' => 1,
				'instructions' => 'Prevent users from creating custom gradients. Only theme-defined gradients will be available.',
				'default_value' => 1,
			],			
			[
				'key' => 'field_yak_gradients',
				'label' => 'Custom Editor Gradients',
				'name' => 'yak_gradients',
				'type' => 'repeater',
				'instructions' => 'Define gradients to appear in the editor gradient picker.',
				'collapsed' => 'field_yak_gradient_name',
				'layout' => 'row',
				'button_label' => 'Add Gradient',
				'sub_fields' => [
					[
						'key' => 'field_yak_gradient_name',
						'label' => 'Gradient Name',
						'name' => 'name',
						'type' => 'text',
						'required' => 1,
					],
					[
						'key' => 'field_yak_gradient_color1',
						'label' => 'Color 1',
						'name' => 'color_1',
						'type' => 'color_picker',
						'required' => 1,
					],
					[
						'key' => 'field_yak_gradient_color2',
						'label' => 'Color 2',
						'name' => 'color_2',
						'type' => 'color_picker',
						'required' => 1,
					],
					[
						'key' => 'field_yak_gradient_direction',
						'label' => 'Direction',
						'name' => 'direction',
						'type' => 'select',
						'choices' => [
							'to right'        => 'Left to Right',
							'to left'         => 'Right to Left',
							'to bottom'       => 'Top to Bottom',
							'to top'          => 'Bottom to Top',
							'45deg'           => 'Diagonal (↘)',
							'135deg'          => 'Diagonal (↙)',
							'radial'          => 'Radial (Color 1 in center)'
						],
						'default_value' => 'to right',
						'ui' => 1,
					],
				],
			],
		],
		'location' => [
			[
				[
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'yak-options-colors',
				],
			],
		],
		'menu_order' => 2,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
	]);
		
}


// controls whether to allow custom colors & gradients
add_action('after_setup_theme', 'yak_maybe_disable_editor_customizations');
function yak_maybe_disable_editor_customizations() {
	if (!function_exists('get_field')) return;

	$disable = get_field('yak_disable_custom_colors', 'option');
	if ($disable) {
		add_theme_support('disable-custom-colors');
	}
}


// output the new swatches on the options page
add_action('admin_footer', 'yak_output_theme_palette_preview');
function yak_output_theme_palette_preview() {
	if (!isset($_GET['page']) || $_GET['page'] !== 'yak-options-colors') return;

	$base    = get_field('yak_base_color', 'option');
	$accent  = get_field('yak_accent_color', 'option');
	$customs = get_field('yak_additional_colors', 'option') ?: [];

	echo '<div class="yak-color-palette-preview">
		<div class="wrap">';

	// --- Base color ---
	if ($base && is_string($base)) {
		list($palette, $highlight) = yak_generate_color_palette($base, 'yak-base');
		echo '<h2 style="font-size:18px; margin-bottom:20px;">Base Color Palette Preview</h2>';
		yak_render_palette_group('Light Grays', $palette, $highlight, range(1, 3));
		yak_render_palette_group('Main Color Scale', $palette, $highlight, range(4, 9));
		yak_render_palette_group('Dark Mode Neutrals', $palette, $highlight, range(10, 12));
	}

	// --- Accent color ---
	if ($accent && is_string($accent)) {
		list($palette, $highlight) = yak_generate_color_palette($accent, 'yak-accent');
		echo '<h2 style="font-size:18px; margin:40px 0 16px;">Accent Color Palette</h2>';
		yak_render_palette_group('Light Grays', $palette, $highlight, range(1, 3));
		yak_render_palette_group('Accent Scale', $palette, $highlight, range(4, 9));
		yak_render_palette_group('Dark Mode Neutrals', $palette, $highlight, range(10, 12));
	}

	// --- Additional custom colors ---
	foreach ($customs as $item) {
		if (empty($item['name']) || empty($item['hex']) || !is_string($item['hex'])) continue;

		$slug  = 'yak-' . sanitize_title($item['name']);
		$title = esc_html($item['name']);
		list($palette, $highlight) = yak_generate_color_palette($item['hex'], $slug);

		echo '<h2 style="font-size:18px; margin:40px 0 16px;">' . $title . ' Palette</h2>';
		yak_render_palette_group('Light Grays', $palette, $highlight, range(1, 3));
		yak_render_palette_group('Color Scale', $palette, $highlight, range(4, 9));
		yak_render_palette_group('Dark Mode Neutrals', $palette, $highlight, range(10, 12));
	}

	echo '</div></div>';
	
}


// update WP admin colors
add_action('admin_head', 'yak_output_admin_theme_css_vars');
function yak_output_admin_theme_css_vars() {
	if (!is_admin()) return;

	$base = get_field('yak_base_color', 'option');
	if (!$base) return;

	list($palette) = yak_generate_color_palette($base, 'yak-admin');

	$vars = [
		'--yak-admin-main'       => $palette['yak-admin-main'] ?? '#23282d',
		'--yak-admin-hover'      => $palette['yak-admin-hover'] ?? '#2c3338',
		'--yak-admin-subtle-bg'  => $palette['yak-admin-subtle-bg'] ?? '#f1f2f3',
		'--yak-admin-border'     => $palette['yak-admin-border'] ?? '#e1e2e4',
		'--yak-admin-text'       => $palette['yak-admin-text'] ?? '#ffffff',
	];

	echo '<style>:root {';
	foreach ($vars as $key => $val) {
		echo "{$key}: {$val};";
	}
	echo '}</style>';
}


add_action('after_setup_theme', 'yak_register_editor_color_palette');
function yak_register_editor_color_palette() {
	if (!function_exists('get_field')) return;

	$palette = yak_get_editor_palette_colors();
	if (empty($palette)) return; // nothing to register

	add_theme_support('editor-color-palette', $palette);
}





function yak_get_editor_palette_colors() {
	$colors = [];

	$rows = get_field('yak_selected_editor_colors', 'option');
	if (!empty($rows) && is_array($rows)) {
		foreach ($rows as $row) {
			if (empty($row['name']) || empty($row['hex'])) continue;

			$name = trim($row['name']);
			$slug = sanitize_title($name);

			$colors[] = [
				'name'  => $name,
				'slug'  => $slug,
				'color' => $row['hex'],
			];
		}
	}

	// Fallback: if no repeater rows, build palette from ACF options
	if (empty($colors)) {
		$base   = get_field('yak_base_color', 'option');
		$accent = get_field('yak_accent_color', 'option');
		$custom = get_field('yak_additional_colors', 'option') ?: [];

		if ($base) {
			$colors[] = [
				'name'  => 'Primary',
				'slug'  => 'primary',
				'color' => $base,
			];
		}

		if ($accent) {
			$colors[] = [
				'name'  => 'Accent',
				'slug'  => 'accent',
				'color' => $accent,
			];
		}

		foreach ($custom as $item) {
			if (empty($item['name']) || empty($item['hex'])) continue;

			$slug = sanitize_title($item['name']);
			$name = ucwords($item['name']);

			$colors[] = [
				'name'  => $name,
				'slug'  => $slug,
				'color' => $item['hex'],
			];
		}

		// Always include white and black if fallback is used
		$colors[] = [
			'name'  => 'White',
			'slug'  => 'white',
			'color' => '#ffffff',
		];
		$colors[] = [
			'name'  => 'Black',
			'slug'  => 'black',
			'color' => '#000000',
		];
	}

	return $colors;
}




function yak_output_editor_palette_css_vars() {
	$colors = yak_get_editor_palette_colors(); // get selected ACF repeater
	if (empty($colors)) $colors = [];

	// Fallback core theme vars referencing ACF-based generated color vars
	$base   = get_field('yak_base_color', 'option');
	$accent   = get_field('yak_accent_color', 'option');
	$core_colors = [
		'yak-color-primary' => $base,       // base color
		'yak-color-accent' => $accent,       // base color
		'yak-color-black'   => 'var(--yak-base-12)',    // dark shade
		'yak-color-muted'   => 'var(--yak-base-2)',     // lightest shade
		'yak-color-white'   => '#ffffff',               // hard-coded white
	];

	// Ensure required theme color vars are included in the output
	foreach ($core_colors as $slug => $value) {
		$exists = array_filter($colors, function($c) use ($slug) {
			return sanitize_title($c['slug']) === $slug;
		});
		if (empty($exists)) {
			$colors[] = [ 'slug' => $slug, 'color' => $value ];
		}
	}

	// Start CSS output
	$css  = "<style id='yak-editor-colors'>\n";
	$css .= ":root {\n";

	foreach ($colors as $c) {
		$slug = sanitize_title($c['slug']);
		$val  = esc_attr($c['color']);
		$css .= "  --{$slug}: {$val};\n";
	}
	$css .= "}\n";

	// Generate color utility classes
	foreach ($colors as $c) {
		$slug = sanitize_title($c['slug']);
		$css .= ".has-{$slug}-color { color: var(--{$slug}); }\n";
		$css .= ".has-{$slug}-background-color { background-color: var(--{$slug}); }\n";
	}
	$css .= "</style>\n";

	echo $css;
}


// Frontend
add_action('wp_head', 'yak_output_editor_palette_css_vars');


// Block editor (iframe-safe)
add_action( 'enqueue_block_editor_assets', function () {
	// Enqueue a dummy or real editor stylesheet
	wp_enqueue_style( 'yak-editor-palette', false );

	// Inject your CSS variables into the editor iframe
	wp_add_inline_style( 'yak-editor-palette', ':root { ' . yak_output_all_color_swatches_css_vars() . ' }' );
});





// helpers for color work
function yak_render_palette_group($label, $palette, $highlight_key, $indices) {
	echo '<h3 style="margin:32px 0 12px; font-size:16px; color:#333;">' . esc_html($label) . '</h3>';
	echo '<div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(180px, 1fr));gap:16px;">';

	// Determine the prefix (e.g., yak-base, yak-accent, yak-brand-blue)
	$prefix = null;
	foreach ($palette as $key => $_) {
		if (preg_match('/^(.*)-\d+$/', $key, $match)) {
			$prefix = $match[1];
			break;
		}
	}


	// Optionally inject yak-white for base Light Grays only
	if ($label === 'Light Grays' && $prefix === 'yak-base') {
		echo yak_render_color_card('yak-white', '#ffffff', null);
	}

	// Output matching indexed swatches
	foreach ($indices as $i) {
		foreach ($palette as $key => $hex) {
			if (preg_match("/-(\d+)$/", $key, $match) && (int)$match[1] === $i) {
				$highlight = ($key === $highlight_key) ? 'Base' : null;
				echo yak_render_color_card($key, $hex, $highlight);
				break;
			}
		}
	}


	echo '</div>';
}


function yak_generate_color_palette($hex, $prefix = 'yak-base') {
	if (!is_string($hex)) return [[], null];
	if (!preg_match('/^#?[a-f0-9]{6}$/i', $hex)) return [[], null];

	$hex = strtolower(ltrim($hex, '#'));
	$r = hexdec(substr($hex, 0, 2));
	$g = hexdec(substr($hex, 2, 2));
	$b = hexdec(substr($hex, 4, 2));

	list($h, $s, $l) = yak_rgb_to_hsl($r, $g, $b);
	$palette = [];
	$highlight_key = null;
	$closest_dist = PHP_INT_MAX;

	// Swatches 1–3: light grays (base hue, desaturated, light)
	$light_grays = [
		['s' => 0.06, 'l' => 0.94], // 🔧 adjusted from 0.96
		['s' => 0.08, 'l' => 0.91],
		['s' => 0.12, 'l' => 0.87],
	];
	for ($i = 0; $i < 3; $i++) {
		$index = $i + 1;
		$rgb = yak_hsl_to_rgb($h, $light_grays[$i]['s'], $light_grays[$i]['l']);
		$hex_out = yak_rgb_to_hex($rgb);
		$palette["{$prefix}-{$index}"] = $hex_out;
	}

	// Swatches 4–9: base color ramp, consistent saturation, stepped lightness
	$base_s = min(1.0, $s + 0.05); // slight bump to saturation
	$lightness_values = [0.82, 0.68, 0.54, 0.40, 0.26, 0.14];

	$color_steps = array_map(function ($l) use ($base_s) {
		return ['s' => $base_s, 'l' => $l];
	}, $lightness_values);

	for ($i = 0; $i < 6; $i++) {
		$index = $i + 4;
		$rgb = yak_hsl_to_rgb($h, $color_steps[$i]['s'], $color_steps[$i]['l']);
		$hex_out = yak_rgb_to_hex($rgb);
		$palette["{$prefix}-{$index}"] = $hex_out;

		// Track distance to original
		$dist = yak_rgb_distance([$r, $g, $b], $rgb);
		if ($dist < $closest_dist) {
			$closest_dist = $dist;
			$highlight_key = "{$prefix}-{$index}";
		}
	}

	// Swatches 10–12: dark mode neutrals (base hue, low sat, low light)
	$dark_neutrals = [
		['s' => 0.2, 'l' => 0.18],
		['s' => 0.1, 'l' => 0.15],
		['s' => 0.05, 'l' => 0.1],
	];
	for ($i = 0; $i < 3; $i++) {
		$index = $i + 10;
		$rgb = yak_hsl_to_rgb($h, $dark_neutrals[$i]['s'], $dark_neutrals[$i]['l']);
		$hex_out = yak_rgb_to_hex($rgb);
		$palette["{$prefix}-{$index}"] = $hex_out;
	}

	// Force exact original color into closest swatch
	$palette[$highlight_key] = '#' . $hex;

	// --- Admin-specific CSS variable-friendly names ---------------------
	$admin_palette = [
		'yak-admin-main'       => yak_hsl_to_hex([$h, min(0.35, $s * 0.8), 0.28]), // sidebar / topbar
		'yak-admin-hover'      => yak_hsl_to_hex([$h, min(0.40, $s * 0.9), 0.36]), // hover state
		'yak-admin-subtle-bg'  => yak_hsl_to_hex([$h, 0.08, 0.94]),               // panels, submenus
		'yak-admin-border'     => yak_hsl_to_hex([$h, 0.06, 0.86]),               // border color
		'yak-admin-black'     => yak_hsl_to_hex([$h, 0.05, 0.1]),               // border color
		'yak-admin-text'       => (yak_calculate_contrast(yak_hsl_to_hex([$h, $s, 0.28]), '#ffffff') > 4.5) ? '#ffffff' : '#000000',
	];

	return [array_merge($palette, $admin_palette), $highlight_key];
}


// add_action('admin_head', 'yak_output_all_color_swatches_css_vars');
add_action('wp_head', 'yak_output_all_color_swatches_css_vars');
function yak_output_all_color_swatches_css_vars() {
	if (!function_exists('get_field')) return;

	$base    = get_field('yak_base_color', 'option');
	$accent  = get_field('yak_accent_color', 'option');
	$customs = get_field('yak_additional_colors', 'option') ?: [];

	$all_palettes = [];

	// 1. Base palette
	if ($base) {
		list($palette) = yak_generate_color_palette($base, 'yak-base');
		$all_palettes = array_merge($all_palettes, $palette);
	}

	// 2. Accent palette
	if ($accent) {
		list($palette) = yak_generate_color_palette($accent, 'yak-accent');
		$all_palettes = array_merge($all_palettes, $palette);
	}

	// 3. Additional custom palettes
	foreach ($customs as $item) {
		if (empty($item['name']) || empty($item['hex'])) continue;
		$slug = 'yak-' . sanitize_title($item['name']);
		list($palette) = yak_generate_color_palette($item['hex'], $slug);
		$all_palettes = array_merge($all_palettes, $palette);
	}

	// Output CSS to front end
	if (!empty($all_palettes)) {
		echo "<style id='yak-all-swatches'>\n";
		echo ":root {\n";
		foreach ($all_palettes as $slug => $hex) {
			echo "  --{$slug}: {$hex};\n";
		}
		echo "}\n</style>\n";
	}
}







function yak_rgb_to_hsl($r, $g, $b) {
	$r /= 255; $g /= 255; $b /= 255;
	$max = max($r, $g, $b);
	$min = min($r, $g, $b);
	$h = $s = $l = ($max + $min) / 2;

	if ($max === $min) {
		$h = $s = 0;
	} else {
		$d = $max - $min;
		$s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
		switch ($max) {
			case $r: $h = ($g - $b) / $d + ($g < $b ? 6 : 0); break;
			case $g: $h = ($b - $r) / $d + 2; break;
			case $b: $h = ($r - $g) / $d + 4; break;
		}
		$h /= 6;
	}
	return [$h, $s, $l];
}

function yak_hsl_to_hex(array $hsl) {
	list($h, $s, $l) = $hsl;
	$rgb = yak_hsl_to_rgb($h, $s, $l);
	return yak_rgb_to_hex($rgb);
}


function yak_hsl_to_rgb($h, $s, $l) {
	$r = $g = $b = 0;

	if ($s == 0) {
		$r = $g = $b = $l;
	} else {
		$hue2rgb = function($p, $q, $t) {
			if ($t < 0) $t += 1;
			if ($t > 1) $t -= 1;
			if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
			if ($t < 1/2) return $q;
			if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
			return $p;
		};

		$q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
		$p = 2 * $l - $q;
		$r = $hue2rgb($p, $q, $h + 1/3);
		$g = $hue2rgb($p, $q, $h);
		$b = $hue2rgb($p, $q, $h - 1/3);
	}
	return [round($r * 255), round($g * 255), round($b * 255)];
}

function yak_rgb_to_hex($rgb) {
	return sprintf("#%02x%02x%02x", $rgb[0], $rgb[1], $rgb[2]);
}

function yak_calculate_contrast($hex1, $hex2) {
	$rgb1 = yak_hex_to_rgb($hex1);
	$rgb2 = yak_hex_to_rgb($hex2);

	$l1 = yak_relative_luminance($rgb1);
	$l2 = yak_relative_luminance($rgb2);

	return ($l1 > $l2) ? (($l1 + 0.05) / ($l2 + 0.05)) : (($l2 + 0.05) / ($l1 + 0.05));
}

function yak_hex_to_rgb($hex) {
	$hex = ltrim($hex, '#');
	if (strlen($hex) === 3) {
		$hex = $hex[0].$hex[0] . $hex[1].$hex[1] . $hex[2].$hex[2];
	}
	return [
		hexdec(substr($hex, 0, 2)),
		hexdec(substr($hex, 2, 2)),
		hexdec(substr($hex, 4, 2))
	];
}

function yak_relative_luminance($rgb) {
	foreach ($rgb as &$val) {
		$val /= 255;
		$val = ($val <= 0.03928) ? $val / 12.92 : pow((($val + 0.055) / 1.055), 2.4);
	}
	return 0.2126 * $rgb[0] + 0.7152 * $rgb[1] + 0.0722 * $rgb[2];
}

function yak_render_color_card($slug, $hex, $highlight_label = null) {
	$contrast_white = yak_calculate_contrast($hex, '#ffffff');
	$contrast_black = yak_calculate_contrast($hex, '#000000');

	$wcag = function ($ratio) {
		if ($ratio >= 7) return 'AAA';
		if ($ratio >= 4.5) return 'AA';
		return '❌';
	};

	// Outer wrapper, JS hooks via yak-color-swatch class
	$html  = '<button type="button" class="yak-color-swatch" data-slug="' . esc_attr($slug) . '" data-hex="' . esc_attr($hex) . '" aria-label="Copy ' . esc_attr($slug) . ' (' . esc_attr($hex) . ')">';
	
	// Fill box (color square)
	$html .= '<div class="yak-color-swatch-fill" style="background:' . esc_attr($hex) . ';height:120px;border-radius:6px 6px 0 0;"></div>';

	// Swatch label + contrast info
	$html .= '<div class="yak-color-swatch-label" style="padding:10px;font-size:12px;text-align:center;line-height:1.4;background:#fff;border-radius:0 0 6px 6px;box-shadow:0 1px 3px rgba(0,0,0,0.1);border:1px solid #ddd;border-top:none;">';
	$html .= '<div style="font-weight:600;">' . esc_html($slug);
	if ($highlight_label) {
		$html .= ' <span style="color:#0073aa;">(' . esc_html($highlight_label) . ')</span>';
	}
	$html .= '</div>';
	$html .= '<div style="color:#666;">' . esc_html($hex) . '</div>';
	$html .= '<div style="margin-top:4px;font-size:11px;line-height:1.6;">';
	$html .= '🤍 ' . round($contrast_white, 2) . ' (' . $wcag($contrast_white) . ')<br>';
	$html .= '🖤 ' . round($contrast_black, 2) . ' (' . $wcag($contrast_black) . ')';
	$html .= '</div></div>';

	// Checkmark overlay (hidden unless selected)
	$html .= '<div class="yak-swatch-check" style="position:absolute;top:6px;right:6px;font-size:16px;color:#0073aa;display:none;">✔</div>';

	// Close wrapper
	$html .= '</button>';

	return $html;
}



function yak_rgb_distance($a, $b) {
	return sqrt(
		pow($a[0] - $b[0], 2) +
		pow($a[1] - $b[1], 2) +
		pow($a[2] - $b[2], 2)
	);
}

// enqueue js on colors page for interactive features
add_action('acf/input/admin_enqueue_scripts', function() {
	if (!isset($_GET['page']) || $_GET['page'] !== 'yak-options-colors') return;

	wp_enqueue_script(
		'yak-editor-swatch-js',
		get_stylesheet_directory_uri() . '/js/yak-editor-swatch.js',
		['acf-input', 'jquery'],
		filemtime(get_stylesheet_directory() . '/js/yak-editor-swatch.js'),
		true
	);
});


// custom gradient work
/**
 * Yak Color and Gradient Support
 *
 * Registers custom editor gradients and disables WP defaults.
 */
add_action( 'after_setup_theme', 'yak_register_acf_gradients_from_theme_options' );

function yak_register_acf_gradients_from_theme_options() {
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}

	if ( get_field( 'yak_disable_custom_gradients', 'option' ) ) {
		add_theme_support( 'disable-custom-gradients' );
	}
	

	$gradients = get_field( 'yak_gradients', 'option' );

	if ( empty( $gradients ) || ! is_array( $gradients ) ) {
		return;
	}

	$registered = [];

	foreach ( $gradients as $gradient ) {
		$name      = trim( $gradient['name'] ?? '' );
		$color1    = $gradient['color_1'] ?? '';
		$color2    = $gradient['color_2'] ?? '';
		$direction = $gradient['direction'] ?? 'to right';

		if ( ! $name || ! $color1 || ! $color2 ) {
			continue;
		}

		$slug = sanitize_title( $name );

		// Handle radial separately
		if ( $direction === 'radial' ) {
			$css_gradient = "radial-gradient(circle, {$color1} 0%, {$color2} 100%)";
		} else {
			$css_gradient = "linear-gradient({$direction}, {$color1} 0%, {$color2} 100%)";
		}

		$registered[] = [
			'name'     => $name,
			'slug'     => $slug,
			'gradient' => $css_gradient,
		];
	}

	if ( ! empty( $registered ) ) {
		add_theme_support( 'editor-gradient-presets', $registered );
	}
}


add_action( 'wp_head', 'yak_output_gradient_styles_frontend', 100 );
add_action( 'admin_head', 'yak_output_gradient_styles_editor', 100 );

function yak_output_gradient_styles_frontend() {
	yak_output_gradient_styles(); // Shared logic
}

function yak_output_gradient_styles_editor() {
	// Only output inside the block editor
	$screen = get_current_screen();
	if ( $screen && $screen->is_block_editor() ) {
		yak_output_gradient_styles();
	}
}

function yak_output_gradient_styles() {
	if ( ! function_exists( 'get_field' ) ) return;

	$gradients = get_field( 'yak_gradients', 'option' );
	if ( empty( $gradients ) || ! is_array( $gradients ) ) return;

	echo "<style id='yak-gradient-presets'>\n";

	foreach ( $gradients as $g ) {
		$name      = trim( $g['name'] ?? '' );
		$slug      = sanitize_title( $name );
		$color1    = $g['color_1'] ?? '';
		$color2    = $g['color_2'] ?? '';
		$direction = $g['direction'] ?? 'to right';

		if ( ! $slug || ! $color1 || ! $color2 ) continue;

		$gradient = ( $direction === 'radial' )
			? "radial-gradient(circle, {$color1} 0%, {$color2} 100%)"
			: "linear-gradient({$direction}, {$color1} 0%, {$color2} 100%)";

		echo ".has-{$slug}-gradient-background { background: {$gradient}; }\n";
	}

	echo "</style>\n";
}
