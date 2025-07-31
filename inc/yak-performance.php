<?php

/////////////////////////////////////////////////////////////////////////////////
// *** Performance & Optimization Settings
/////////////////////////////////////////////////////////////////////////////////

if ( function_exists( 'acf_add_local_field_group' ) ) {
    acf_add_local_field_group( [
        'key' => 'group_yak_performance_settings',
        'title' => 'Performance & Optimization',
        'fields' => [

            [
                'key' => 'field_yak_spec_loading_note',
                'label' => '',
                'name' => '',
                'type' => 'message',
                'message' => '
                    <strong>Speculative Loading (Prefetch/Prerender)</strong><br>
                    Control how aggressively the browser preloads future pages.<br><br>
                    <strong>Prefetch:</strong> Light, loads only HTML.<br>
                    <strong>Prerender:</strong> Heavy, fully renders page in background.<br><br>
                    <strong>Eagerness:</strong><br>
                    <em>Conservative:</em> On click start (default).<br>
                    <em>Moderate:</em> On hover/focus.<br>
                    <em>Eager:</em> Aggressively preloads links.<br><br>
                    <strong>Tip:</strong> Use <em>Prefetch + Moderate</em> for most sites. Use <em>Prerender</em> only for key CTAs if hosting/CDN caching is strong.
                ',
                'wrapper' => [ 'width' => '100%' ],
            ],

            [
                'key' => 'field_yak_spec_loading_action',
                'label' => 'Action',
                'name' => 'yak_spec_loading_action',
                'type' => 'radio',
                'choices' => [
                    'prefetch'  => 'Prefetch (HTML only)',
                    'prerender' => 'Prerender (Full page)',
                ],
                'default_value' => 'prefetch',
                'layout' => 'horizontal',
                'wrapper' => [ 'width' => '50%' ],
            ],

            [
                'key' => 'field_yak_spec_loading_eagerness',
                'label' => 'Eagerness',
                'name' => 'yak_spec_loading_eagerness',
                'type' => 'select',
                'choices' => [
                    'conservative' => 'Conservative',
                    'moderate'     => 'Moderate',
                    'eager'        => 'Eager',
                ],
                'default_value' => 'conservative',
                'ui' => 1,
                'wrapper' => [ 'width' => '50%' ],
            ],

            [
                'key' => 'field_yak_spec_loading_selectors',
                'label' => 'Targeted Selectors (Optional)',
                'name' => 'yak_spec_loading_selectors',
                'type' => 'textarea',
                'instructions' => 'Enter one CSS selector per line. These links will use the chosen action. If empty, all links are targeted.',
                'rows' => 3,
                'wrapper' => [ 'width' => '100%' ],
            ],

            // Add to your Performance & Optimization group
            [
                'key' => 'field_yak_crossfade_toggle',
                'label' => 'Enable Cross‑Page Fade',
                'name' => 'yak_crossfade_toggle',
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 0,
                'instructions' => 'Adds smooth fade transitions between pages using the View Transitions API (Chrome/Edge only).',
                'wrapper' => ['width' => '50%'],
            ],


        ],
        'location' => [
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'yak-options-performance',
                ],
            ],
        ],
        'menu_order' => 0,
    ] );
}

/////////////////////////////////////////////////////////////////////////////////
// Apply Speculative Loading Settings via WP 6.8 API
/////////////////////////////////////////////////////////////////////////////////

// Force-enable speculation rules
add_filter( 'wp_enable_speculation_rules', '__return_true', 5 );

// Override global mode/eagerness from ACF
add_filter( 'wp_speculation_rules_configuration', function( $config ) {
    if ( ! function_exists( 'get_field' ) || ! is_array( $config ) ) {
        return $config;
    }

    $action    = get_field( 'yak_spec_loading_action', 'option' ) ?: 'prefetch';
    $eagerness = get_field( 'yak_spec_loading_eagerness', 'option' ) ?: 'conservative';

    $config['mode']      = $action;
    $config['eagerness'] = $eagerness;

    return $config;
}, 5 );

// Add targeted selectors (only relevant if provided)
add_action( 'wp_load_speculation_rules', function( $rules ) {
    if ( ! function_exists( 'get_field' ) || ! is_a( $rules, 'WP_Speculation_Rules' ) ) {
        return;
    }

    $action    = get_field( 'yak_spec_loading_action', 'option' ) ?: 'prefetch';
    $eagerness = get_field( 'yak_spec_loading_eagerness', 'option' ) ?: 'conservative';
    $selectors = trim( get_field( 'yak_spec_loading_selectors', 'option' ) );

    if ( $selectors ) {
        $lines = array_filter( array_map( 'trim', explode( "\n", $selectors ) ) );
        foreach ( $lines as $i => $selector ) {
            $rules->add_rule(
                $action,
                'yak_spec_rule_' . $i,
                [
                    'source'    => 'document',
                    'where'     => [ 'selector_matches' => $selector ],
                    'eagerness' => $eagerness,
                ]
            );
        }
    }
}, 10 );







add_action( 'wp_head', function() {
    if ( ! function_exists( 'get_field' ) ) return;
    if ( ! get_field( 'yak_crossfade_toggle', 'option' ) ) return;

    echo '<style id="yak-crossfade">' . PHP_EOL;
    echo '@view-transition { navigation: auto; }' . PHP_EOL;
    echo '::view-transition-old(root), ::view-transition-new(root) {' . PHP_EOL;
    echo '  animation: fade 0.3s ease both;' . PHP_EOL;
    echo '}' . PHP_EOL;
    echo '@keyframes fade { from { opacity: 0; } to { opacity: 1; } }' . PHP_EOL;
    echo '</style>' . PHP_EOL;
}, 50 );
