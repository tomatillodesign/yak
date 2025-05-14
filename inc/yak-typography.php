<?php




if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group([
        'key' => 'group_yak_typography_embeds',
        'title' => 'Font Embed Code',
        'fields' => [
            [
                'key' => 'field_yak_font_embed_code',
                'label' => 'Font Embed Code',
                'name' => 'yak_font_embed_code',
                'type' => 'textarea',
                'instructions' => 'Paste your <link> or @import code from Google Fonts or Adobe Fonts. This will be output in the <head> of your theme.',
                'default_value' => '',
                'placeholder' => '<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">',
                'rows' => 4,
                'new_lines' => 'wpautop',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'yak-options-typography',
                ],
            ],
        ],
        'menu_order' => 0,
    ]);
}



if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group([
        'key' => 'group_yak_font_assignments',
        'title' => 'Font Assignments',
        'fields' => [
            [
                'key' => 'field_yak_primary_font',
                'label' => 'Primary Font',
                'name' => 'yak_primary_font',
                'type' => 'text',
                'instructions' => 'Main font for body text. Example: Inter, sans-serif',
                'placeholder' => 'Inter, sans-serif',
                'prepend' => 'font-family:',
                'wrapper' => ['width' => '100%'],
            ],
            [
                'key' => 'field_yak_secondary_font',
                'label' => 'Secondary Font',
                'name' => 'yak_secondary_font',
                'type' => 'text',
                'instructions' => 'Optional font for headings. Example: "Playfair Display", serif',
                'placeholder' => '"Playfair Display", serif',
                'prepend' => 'font-family:',
                'wrapper' => ['width' => '100%'],
            ],
            [
                'key' => 'field_yak_accent_font',
                'label' => 'Accent Font',
                'name' => 'yak_accent_font',
                'type' => 'text',
                'instructions' => 'Optional font for UI elements, labels, or buttons.',
                'placeholder' => '"Space Mono", monospace',
                'prepend' => 'font-family:',
                'wrapper' => ['width' => '100%'],
            ],
            [
                'key' => 'field_yak_font_base_px',
                'label' => 'Body Font Size',
                'name' => 'yak_font_base_px',
                'type' => 'number',
                'instructions' => 'Base font size in pixels. This controls the root type scale.',
                'default_value' => 18,
                'min' => 10,
                'max' => 32,
                'step' => 1,
                'prepend' => '',
                'append' => 'px',
                'required' => 0,
                'wrapper' => ['width' => '33%'],
            ]            
        ],
        'location' => [
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'yak-options-typography',
                ],
            ],
        ],
        'menu_order' => 1,
    ]);
}



add_action('wp_head', function () {
    if (function_exists('get_field')) {
        $embed_code = get_field('yak_font_embed_code', 'option');
        if ($embed_code) {
            echo $embed_code;
        }
    }
}, 5); // Output early so fonts load before styles


add_action('wp_head', 'yak_output_font_vars', 20);
add_action('admin_head', 'yak_output_font_vars', 20);
// add_action('enqueue_block_editor_assets', function () {
// 	// Attach inside the editor iframe
// 	wp_enqueue_style('yak-editor-font-vars', false);
// 	wp_add_inline_style('yak-editor-font-vars', yak_get_font_var_css());
// }, 20);

/**
 * Outputs font variables in classic admin and frontend <head>.
 */
function yak_output_font_vars() {
	if (!function_exists('get_field')) return;

	echo '<style id="yak-font-vars">' . PHP_EOL;
	echo yak_get_font_var_css();
	echo '</style>' . PHP_EOL;
}

/**
 * Returns the CSS :root declaration for yak font variables.
 */
function yak_get_font_var_css() {
	if (!function_exists('get_field')) return '';

	$primary   = trim(get_field('yak_primary_font', 'option')) ?: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
	$secondary = trim(get_field('yak_secondary_font', 'option')) ?: $primary;
	$accent    = trim(get_field('yak_accent_font', 'option')) ?: $primary;

	$font_px = intval(get_field('yak_font_base_px', 'option'));
	if ($font_px < 10 || $font_px > 32) {
		$font_px = 18;
	}

	return <<<CSS
:root {
    --yak-primary-font: {$primary};
    --yak-secondary-font: {$secondary};
    --yak-accent-font: {$accent};
    --yak-font-base-px: {$font_px};
}
CSS;
}


add_theme_support('disable-custom-font-sizes');
