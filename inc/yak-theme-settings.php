<?php


if (function_exists('acf_add_local_field_group')) {
	acf_add_local_field_group([
		'key' => 'group_yak_theme_settings',
		'title' => 'Yak Theme Settings',
		'fields' => [
			[
				'key' => 'field_yak_show_site_description',
				'label' => 'Show Site Description',
				'name' => 'yak_show_site_description',
				'type' => 'true_false',
				'ui' => 1,
				'default_value' => 0,
				'instructions' => 'Toggle to show or hide the site description in your header.',
			],
			// ... (add other settings here if needed)
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
	]);
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
