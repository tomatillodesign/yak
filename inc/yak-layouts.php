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




add_action('wp_head', function () {
    if (!function_exists('get_field')) return;

    $width = get_field('yak_content_max_width', 'option');
    $unit  = get_field('yak_content_max_width_unit', 'option');

    // Fallback to 75ch
    if (!$width || !$unit) {
        $css_value = '75ch';
    } else {
        $css_value = floatval($width) . $unit;
    }

    echo '<style id="yak-content-width-vars">' . PHP_EOL;
    echo ":root {" . PHP_EOL;
    echo "    --yak-content-max-width: {$css_value};" . PHP_EOL;
    echo "}" . PHP_EOL;
    echo '</style>' . PHP_EOL;
}, 20);
