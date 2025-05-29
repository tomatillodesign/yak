<?php

/**
 * ============================================================================
 * 🅨 Yak Theme – Typography Settings and CSS Variable Output
 * ============================================================================
 *
 * This file defines ACF fields and output logic for customizing fonts
 * within the Yak theme. It supports user-defined font stacks, base font size,
 * and <link> or @import embed codes for external fonts (Google, Adobe, etc.).
 *
 * Features:
 * - ACF-powered typography panel (font family and base size)
 * - Font embed support (e.g., Google Fonts <link> tag)
 * - CSS variable injection via <style>: --yak-primary-font, etc.
 * - Optional admin/editor output support
 *
 * This module integrates with the Yak type system and global :root variables.
 */


// -----------------------------------------------------------------------------
// Register ACF field group for external font embed code
// -----------------------------------------------------------------------------
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
                'placeholder' => '<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">',
                'rows' => 4,
                'new_lines' => 'wpautop',
            ],
        ],
        'location' => [
            [[
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'yak-options-typography',
            ]],
        ],
    ]);
}


// -----------------------------------------------------------------------------
// Register ACF field group for assigning font stacks and base size
// -----------------------------------------------------------------------------
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
                'append' => 'px',
                'wrapper' => ['width' => '33%'],
            ],
            [
                'key' => 'field_yak_allow_core_font_sizes',
                'label' => 'Allow WP Core Font Sizes',
                'name' => 'yak_allow_core_font_sizes',
                'type' => 'true_false',
                'instructions' => 'Enable WordPress’s built-in & custom font size options in the editor.',
                'default_value' => 0,
                'ui' => 1,
            ],

        ],
        'location' => [
            [[
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'yak-options-typography',
            ]],
        ],
    ]);
}


// -----------------------------------------------------------------------------
// Output <link> or @import embed code into the site <head>
// -----------------------------------------------------------------------------
add_action('wp_head', function () {
    if (function_exists('get_field')) {
        $embed_code = get_field('yak_font_embed_code', 'option');
        if ($embed_code) {
            echo $embed_code;
        }
    }
}, 5); // output early for max performance

// Admin
add_action('admin_head', function () {
    if (function_exists('get_field')) {
        $embed_code = get_field('yak_font_embed_code', 'option');
        if ($embed_code) {
            echo $embed_code;
        }
    }
}, 5);


// -----------------------------------------------------------------------------
// Output dynamic font CSS variables in both frontend and admin
// -----------------------------------------------------------------------------
add_action('wp_head', 'yak_output_font_vars', 20);
add_action('admin_head', 'yak_output_font_vars', 20);

/**
 * Echoes <style> tag with :root font variables.
 */
function yak_output_font_vars() {
    if (!function_exists('get_field')) return;
    echo '<style id="yak-font-vars">' . PHP_EOL;
    echo yak_get_font_var_css();
    echo '</style>' . PHP_EOL;
}

/**
 * Returns :root CSS variable declarations for font settings.
 *
 * @return string CSS block for font family and base size
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




add_action( 'after_setup_theme', 'yak_theme_font_size_support', 20 );
function yak_theme_font_size_support() {
    if ( function_exists( 'get_field' ) && get_field( 'yak_allow_core_font_sizes', 'option' ) ) {
        add_theme_support( 'editor-font-sizes', [
            [
                'name' => __( 'Small', 'yak' ),
                'slug' => 'small',
                'size' => 14,
            ],
            [
                'name' => __( 'Medium', 'yak' ),
                'slug' => 'medium',
                'size' => 18,
            ],
            [
                'name' => __( 'Large', 'yak' ),
                'slug' => 'large',
                'size' => 22,
            ],
            [
                'name' => __( 'X-Large', 'yak' ),
                'slug' => 'x-large',
                'size' => 28,
            ],
        ] );
    } else {
        // Disable all font size options
        add_theme_support( 'editor-font-sizes', [] );
        add_theme_support( 'disable-custom-font-sizes' );
    }
}
