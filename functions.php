<?php
/**
 * Yak custom child theme by Chris Liu-Beers, Tomatillo Design
 * Modified Genesis Sample.
 */


// Starts the engine.
require_once get_template_directory() . '/lib/init.php';

// CLB early customizations ///////

/// HARD BLOCK: Bail out early if ACF is not active
if (!function_exists('get_field')) {

	// Special override: if user clicked our custom "return to themes" button, force switch to fallback
	if (is_admin() && isset($_GET['yak_force_switch'])) {
		switch_theme('twentytwentyfive'); // fallback theme slug
		wp_safe_redirect(admin_url('themes.php?yak_switched_back=1'));
		exit;
	}

	if (is_admin()) {
		// Show blocking error page with working theme switch button
		wp_die(
			'<h1>Yak WP Theme Error</h1>
			 <p>This theme requires the Advanced Custom Fields plugin (free or PRO) to be installed and activated before it can be used.</p>
			 <p>Contact: Chris Liu-Beers, <a href="http://www.tomatillodesign.com/contact" target="_blank">Tomatillo Design</a></p>
			 <p><a href="' . esc_url(admin_url('themes.php?yak_force_switch=1')) . '" class="button button-primary">← Return to Themes</a></p>',
			'Missing Plugin: ACF',
			['response' => 500]
		);
	} else {
		wp_die(
			'<h2>Temporarily down for site maintenance and repairs.</h2><p>Please contact this website owner to notify them about this message.</p>',
			'Yak Theme Error',
			['response' => 500]
		);
	}
}

// // Prevent Genesis "Getting Started" redirect on theme activation (Genesis 3.6+)
// // Remove Genesis welcome redirect after Genesis loads
// add_action('after_setup_theme', function () {
// 	remove_action('admin_init', 'genesis_getting_started_redirect');
// }, 100); // Must be later than Genesis's default priority (10)


//////////////////////////////////////////////////////////

// Adds helper functions.
require_once get_stylesheet_directory() . '/lib/helper-functions.php';

// Adds image upload and color select to Customizer.
require_once get_stylesheet_directory() . '/lib/customize.php';

// Includes Customizer CSS.
require_once get_stylesheet_directory() . '/lib/output.php';

// Adds WooCommerce support.
require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-setup.php';

// Adds the required WooCommerce styles and Customizer CSS.
require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-output.php';

// Adds the Genesis Connect WooCommerce notice.
require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-notice.php';

// Removes header right widget area.
unregister_sidebar( 'header-right' );

// Removes secondary sidebar.
unregister_sidebar( 'sidebar-alt' );

// Removes site layouts.
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-content-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );

// Repositions primary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_header', 'genesis_do_nav', 12 );

// Repositions the secondary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_footer', 'genesis_do_subnav', 10 );

function register_additional_menu() {
    register_nav_menu( 'above-header' ,__( 'Above Header' ));
}

add_action( 'init', 'register_additional_menu' );
add_action( 'genesis_before_header', 'add_third_nav_genesis', 6 ); 
    
function add_third_nav_genesis() {
    
    if ( has_nav_menu( 'above-header' ) ) {
        wp_nav_menu( array( 'theme_location' => 'above-header', 'container_class' => 'genesis-nav-menu' ) );
    }
    
}

// Yak Theme: Enable essential theme supports for block editor + client-safe defaults
add_action('after_setup_theme', 'yak_theme_setup_features');
function yak_theme_setup_features() {

	// Modern block editor support
	add_theme_support( 'editor-styles' );                // Loads theme-defined CSS in the editor
	add_theme_support( 'wp-block-styles' );              // Enables default WP block styles
	add_theme_support( 'align-wide' );                   // Allows wide/full block alignments
	add_theme_support( 'responsive-embeds' );            // Makes embeds (e.g., YouTube) mobile-friendly
	add_theme_support( 'disable-custom-colors' );        // Prevents users from picking arbitrary colors
	add_theme_support( 'disable-custom-font-sizes' );    // Prevents arbitrary font sizes

	// Media & markup
	add_theme_support( 'post-thumbnails' );              // Enables featured image support
	add_theme_support( 'html5', array(                   // Uses semantic HTML5 for core elements
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );
	add_theme_support( 'title-tag' );                    // Let WP manage the <title> tag
	add_theme_support( 'custom-logo' );                  // Allows logo upload in Customizer

	// Optional: Genesis accessibility if you're using Genesis
	add_theme_support( 'genesis-accessibility', array(
		'404-page',
		'drop-down-menu',
		'headings',
		'search-form',
		'skip-links',
	) );
}

// Yak Theme WP Dashboard Customizations and Improvements

// Yak Theme – Add branded dashboard widget with headshot
add_action( 'wp_dashboard_setup', 'yak_register_dashboard_widget' );

function yak_register_dashboard_widget() {
	add_meta_box(
		'yak_dashboard_widget',
		get_bloginfo( 'name' ), // Site title as heading
		'yak_render_dashboard_widget',
		'dashboard',
		'normal',
		'high'
	);
}

function yak_render_dashboard_widget() {
	$img_url = esc_url( 'https://www.tomatillodesign.com/wp-content/uploads/2024/02/clb-headshot-square-scaled.jpg' );
	$site_name = esc_html( get_bloginfo( 'name' ) );
	$contact_email = 'chris@tomatillodesign.com';
	$contact_link = 'https://www.tomatillodesign.com';

	echo '<div style="overflow:hidden;">';
	echo '<img src="' . $img_url . '" alt="Chris Liu-Beers" style="float:right; margin-left:1rem; width:90px; height:90px; border-radius:50%; object-fit:cover;" loading="lazy">';
	echo '<p><strong>Welcome to ' . $site_name . '</strong></p>';
	echo '<p>Congratulations on launching your new website!</p>';
	echo '<p>If you have any questions, please contact me:<br>';
	echo '<a href="mailto:' . antispambot( $contact_email ) . '">' . antispambot( $contact_email ) . '</a><br>';
	echo '919.576.0180<br>';
	echo '<a href="' . esc_url( $contact_link ) . '" target="_blank" rel="noopener">Tomatillo Design</a></p>';
	echo '</div>';
}

// clean up and remove default WP Dashboard widgets
add_action('wp_dashboard_setup', 'yak_remove_default_dashboard_widgets');
function yak_remove_default_dashboard_widgets() {
    // Removes Quick Draft
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');

    // Removes WordPress Events and News
    remove_meta_box('dashboard_primary', 'dashboard', 'side');

    // Optional removals
    // remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
    // remove_meta_box('dashboard_activity', 'dashboard', 'normal');
    // remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
}

// Add new credit line in the WP admin footer
add_filter('admin_footer_text', 'yak_custom_admin_footer_text');
function yak_custom_admin_footer_text($footer_text) {
    $footer_text .= ' | <span class="yak-admin-credit">A website by Chris Liu-Beers @ <a href="http://www.tomatillodesign.com" target="_blank" rel="noopener noreferrer">Tomatillo Design</a></span>';
    return $footer_text;
}

// Theme Options Page via ACF
// In functions.php or a theme setup file
add_action('acf/init', function () {
	if (function_exists('acf_add_options_page')) {
		acf_add_options_page([
			'page_title' => 'Theme Settings',
			'menu_title' => 'Theme Settings',
			'menu_slug'  => 'theme-settings',
            'icon_url' => 'dashicons-superhero',
			'capability' => 'manage_options',
			'redirect'   => false,
		]);

		acf_add_options_sub_page([
			'page_title'  => 'Colors',
			'menu_title'  => 'Colors',
			'parent_slug' => 'theme-settings',
		]);

		acf_add_options_sub_page([
			'page_title'  => 'Typography',
			'menu_title'  => 'Typography',
			'parent_slug' => 'theme-settings',
		]);

		acf_add_options_sub_page([
			'page_title'  => 'Spacing & Layout',
			'menu_title'  => 'Spacing & Layout',
			'parent_slug' => 'theme-settings',
		]);
	}
});







////// Custom Login
add_action('login_enqueue_scripts', 'yak_custom_login_styles');
function yak_custom_login_styles() {
    wp_enqueue_style(
        'yak-login-style',
        get_stylesheet_directory_uri() . '/login/login-style.css',
        [],
        filemtime(get_stylesheet_directory() . '/login/login-style.css')
    );

    // ACF background/logo styles here if needed
}























// Update CSS within in Admin
function clb_custom_admin_styles() {

	wp_enqueue_style('custom-yak-admin-styles', get_stylesheet_directory_uri() . '/css/clb-custom-yak-admin-styles.css');

}
add_action('admin_enqueue_scripts', 'clb_custom_admin_styles');





// Enqueue custom scripts & styles
add_action( 'wp_enqueue_scripts', 'clb_enqueue_custom_scripts_styles', 100 );
function clb_enqueue_custom_scripts_styles() {

	// custom JS
    wp_enqueue_script( 'clb-parallax', get_stylesheet_directory_uri() . '/js/parallax.min.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'clb-custom-ironwood-scripts', get_stylesheet_directory_uri() . '/js/clb-custom-ironwood-scripts.js', array( 'jquery' ), '', true );
    

	// custom front-end CSS
	wp_enqueue_style( 'clb-custom-ironwood-styles', get_stylesheet_directory_uri() . '/css/clb-custom-ironwood-styles.css', array(), '1.0.0', 'all');

}





// add_action('acf/init', 'my_acf_op_init');
// function my_acf_op_init() {

//     // Check function exists.
//     if( function_exists('acf_add_options_page') ) {

//         // Register options page.
//         $option_page = acf_add_options_page(array(
//             'page_title'    => __('Theme General Settings'),
//             'menu_title'    => __('Theme Settings'),
//             'icon_url' => 'dashicons-superhero',
//             'menu_slug'     => 'theme-general-settings',
//             'capability'    => 'update_plugins',
//             'redirect'      => false
//         ));
//     }
// }


if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
'key' => 'group_62a6319cdf400',
'title' => 'Design Settings',
'fields' => array(
array(
'key' => 'field_62a631b007df9',
'label' => 'Primary Color',
'name' => 'primary_color',
'type' => 'color_picker',
'instructions' => '',
'required' => 0,
'conditional_logic' => 0,
'wrapper' => array(
     'width' => '50',
     'class' => '',
     'id' => '',
),
'default_value' => '',
'enable_opacity' => 0,
'return_format' => 'string',
),
array(
'key' => 'field_62a631bf07dfa',
'label' => 'Secondary Color',
'name' => 'secondary_color',
'type' => 'color_picker',
'instructions' => '',
'required' => 0,
'conditional_logic' => 0,
'wrapper' => array(
     'width' => '50',
     'class' => '',
     'id' => '',
),
'default_value' => '',
'enable_opacity' => 0,
'return_format' => 'string',
),
array(
'key' => 'field_62a631d707dfb',
'label' => 'Borders',
'name' => 'borders',
'type' => 'radio',
'instructions' => '',
'required' => 0,
'conditional_logic' => 0,
'wrapper' => array(
     'width' => '',
     'class' => '',
     'id' => '',
),
'choices' => array(
     'square' => 'Square',
     'rounded-small' => 'Slightly Rounded',
	 'rounded-medium' => 'Medium Rounded',
     'rounded-large' => 'Very Rounded',
),
'allow_null' => 0,
'other_choice' => 0,
'default_value' => '',
'layout' => 'vertical',
'return_format' => 'value',
'save_other_choice' => 0,
),
array(
'key' => 'field_62b5f810c18e7',
'label' => 'Show Site Description?',
'name' => 'show_site_description',
'type' => 'true_false',
'instructions' => '',
'required' => 0,
'conditional_logic' => 0,
'wrapper' => array(
     'width' => '',
     'class' => '',
     'id' => '',
),
'message' => '',
'default_value' => 0,
'ui' => 1,
'ui_on_text' => '',
'ui_off_text' => '',
),
array(
    'key' => 'field_637cecd751025',
    'label' => 'Primary Font Family',
    'name' => 'primary_font_family',
    'aria-label' => '',
    'type' => 'text',
    'instructions' => '',
    'required' => 0,
    'conditional_logic' => 0,
    'wrapper' => array(
        'width' => '33',
        'class' => '',
        'id' => '',
    ),
    'default_value' => '',
    'maxlength' => '',
    'placeholder' => '',
    'prepend' => 'font-family:',
    'append' => '',
),
array(
    'key' => 'field_637cecfa51026',
    'label' => 'Secondary Font Family',
    'name' => 'secondary_font_family',
    'aria-label' => '',
    'type' => 'text',
    'instructions' => '',
    'required' => 0,
    'conditional_logic' => 0,
    'wrapper' => array(
        'width' => '33',
        'class' => '',
        'id' => '',
    ),
    'default_value' => '',
    'maxlength' => '',
    'placeholder' => '',
    'prepend' => 'font-family:',
    'append' => '',
),
array(
    'key' => 'field_637ced1551027',
    'label' => 'Accent / Italics / Handwriting Font Family',
    'name' => 'accent_font_family',
    'aria-label' => '',
    'type' => 'text',
    'instructions' => '',
    'required' => 0,
    'conditional_logic' => 0,
    'wrapper' => array(
        'width' => '33',
        'class' => '',
        'id' => '',
    ),
    'default_value' => '',
    'maxlength' => '',
    'placeholder' => '',
    'prepend' => 'font-family:',
    'append' => '',
),
),
'location' => array(
array(
array(
     'param' => 'options_page',
     'operator' => '==',
     'value' => 'theme-general-settings',
),
),
),
'menu_order' => 0,
'position' => 'normal',
'style' => 'default',
'label_placement' => 'top',
'instruction_placement' => 'label',
'hide_on_screen' => '',
'active' => true,
'description' => '',
'show_in_rest' => 0,
));

endif;



if( function_exists('acf_add_local_field_group') ):

    acf_add_local_field_group(array(
        'key' => 'group_62e18690a4555',
        'title' => 'Hello Bar',
        'fields' => array(
            array(
                'key' => 'field_62e186bc9cae6',
                'label' => 'Text',
                'name' => 'hello_bar_text',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
            array(
                'key' => 'field_62e186e19cae7',
                'label' => 'Link',
                'name' => 'hello_bar_link',
                'type' => 'url',
                'instructions' => 'Optional',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
            ),
            array(
                'key' => 'field_62e186f09cae8',
                'label' => 'Link Format',
                'name' => 'link_format',
                'type' => 'radio',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ),
                'choices' => array(
                    'No Button' => 'No Button',
                    'Button' => 'Button',
                ),
                'allow_null' => 0,
                'other_choice' => 0,
                'default_value' => '',
                'layout' => 'vertical',
                'return_format' => 'value',
                'save_other_choice' => 0,
            ),
            array(
                'key' => 'field_62e186f09caz92',
                'label' => 'Link Target',
                'name' => 'link_target',
                'type' => 'radio',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ),
                'choices' => array(
                    'Same Tab' => 'Same Tab',
                    'New Tab' => 'New Tab',
                ),
                'allow_null' => 0,
                'other_choice' => 0,
                'default_value' => '',
                'layout' => 'vertical',
                'return_format' => 'value',
                'save_other_choice' => 0,
            ),
            array(
                'key' => 'field_62e187079cae9',
                'label' => 'Button Text',
                'name' => 'button_text',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_62e186f09cae8',
                            'operator' => '==',
                            'value' => 'Button',
                        ),
                    ),
                ),
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
            array(
                'key' => 'field_62e18c943df8a',
                'label' => 'Start Date',
                'name' => 'hello_bar_start_date',
                'type' => 'date_time_picker',
                'instructions' => 'Optional',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ),
                'display_format' => 'F j, Y g:i a',
                'return_format' => 'F j, Y g:i a',
                'first_day' => 0,
            ),
            array(
                'key' => 'field_62e18caa3df8b',
                'label' => 'End Date',
                'name' => 'hello_bar_end_date',
                'type' => 'date_time_picker',
                'instructions' => 'Optional',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ),
                'display_format' => 'F j, Y g:i a',
                'return_format' => 'F j, Y g:i a',
                'first_day' => 0,
            ),
        ),
        
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'theme-general-settings',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ));
    
    endif;		




// update body classes
add_action( 'wp_head', 'clb_update_custom_theme_vars' );
add_action( 'admin_head', 'clb_update_custom_theme_vars' );
function clb_update_custom_theme_vars() {

	if( !function_exists('get_field') ) { return; }

     $primary_color = get_field('primary_color', 'option');
     if( $primary_color ) {
          $primary_hsl_array = hex2hsl( $primary_color );
          $h = $primary_hsl_array['h'];
          $s = $primary_hsl_array['s'];
          echo '<style>
                    :root {
                         --ironwood-primary-color: ' . $primary_color . ';
                         --ironwood-primary-color-95: hsl(' . $h . ', ' . $s . '%, 95%);
                         --ironwood-primary-color-90: hsl(' . $h . ', ' . $s . '%, 90%);
                         --ironwood-primary-color-85: hsl(' . $h . ', ' . $s . '%, 85%);
                         --ironwood-primary-color-80: hsl(' . $h . ', ' . $s . '%, 80%);
                         --ironwood-primary-color-70: hsl(' . $h . ', ' . $s . '%, 70%);
                         --ironwood-primary-color-60: hsl(' . $h . ', ' . $s . '%, 60%);
                         --ironwood-primary-color-50: hsl(' . $h . ', ' . $s . '%, 50%);
                         --ironwood-primary-color-40: hsl(' . $h . ', ' . $s . '%, 40%);
                         --ironwood-primary-color-30: hsl(' . $h . ', ' . $s . '%, 30%);
                         --ironwood-primary-color-20: hsl(' . $h . ', ' . $s . '%, 20%);
                         --ironwood-primary-color-10: hsl(' . $h . ', ' . $s . '%, 10%);
                         --ironwood-primary-color-5: hsl(' . $h . ', ' . $s . '%, 5%);

                         --ironwood-black: var(--ironwood-primary-color-5);
                    }
                    </style>';
     }


     $secondary_color = get_field('secondary_color', 'option');
     if( $secondary_color ) {
          $secondary_color_array = hex2hsl( $secondary_color );
          $h = $secondary_color_array['h'];
          $s = $secondary_color_array['s'];
          echo '<style>
                    :root {
                         --ironwood-secondary-color: ' . $secondary_color . ';
                         --ironwood-secondary-color-95: hsl(' . $h . ', ' . $s . '%, 95%);
                         --ironwood-secondary-color-90: hsl(' . $h . ', ' . $s . '%, 90%);
                         --ironwood-secondary-color-85: hsl(' . $h . ', ' . $s . '%, 85%);
                         --ironwood-secondary-color-80: hsl(' . $h . ', ' . $s . '%, 80%);
                         --ironwood-secondary-color-70: hsl(' . $h . ', ' . $s . '%, 70%);
                         --ironwood-secondary-color-60: hsl(' . $h . ', ' . $s . '%, 60%);
                         --ironwood-secondary-color-50: hsl(' . $h . ', ' . $s . '%, 50%);
                         --ironwood-secondary-color-40: hsl(' . $h . ', ' . $s . '%, 40%);
                         --ironwood-secondary-color-30: hsl(' . $h . ', ' . $s . '%, 30%);
                         --ironwood-secondary-color-20: hsl(' . $h . ', ' . $s . '%, 20%);
                         --ironwood-secondary-color-10: hsl(' . $h . ', ' . $s . '%, 10%);
                         --ironwood-secondary-color-5: hsl(' . $h . ', ' . $s . '%, 5%);
                    }
                    </style>';
     }

	$borders = get_field('borders', 'option');
	$border_radius_variable = '--ironwood-border-radius-' . $borders;
	if( $borders ) {
		echo '<style>
				:root {
						--ironwood-border-radius: var(' . $border_radius_variable . ');
				}
				</style>';
	}

}



function  hex2hsl($hexstr) {
        $hexstr = ltrim($hexstr, '#');
        if (strlen($hexstr) == 3) {
            $hexstr = $hexstr[0] . $hexstr[0] . $hexstr[1] . $hexstr[1] . $hexstr[2] . $hexstr[2];
        }
        $R = hexdec($hexstr[0] . $hexstr[1]);
        $G = hexdec($hexstr[2] . $hexstr[3]);
        $B = hexdec($hexstr[4] . $hexstr[5]);
        $RGB = array($R,$G,$B);
//scale value 0 to 255 to floats from 0 to 1
        $r = $RGB[0]/255;
        $g = $RGB[1]/255;
        $b = $RGB[2]/255;
        // using https://gist.github.com/brandonheyer/5254516
        $max = max( $r, $g, $b );
        $min = min( $r, $g, $b );
        // lum
        $l = ( $max + $min ) / 2;

        // sat
        $d = $max - $min;
        if( $d == 0 ){
            $h = $s = 0; // achromatic
        } else {
            $s = $d / ( 1 - abs( (2 * $l) - 1 ) );
            // hue
            switch( $max ){
                case $r:
                    $h = 60 * fmod( ( ( $g - $b ) / $d ), 6 );
                    if ($b > $g) {
                        $h += 360;
                    }
                    break;
                case $g:
                    $h = 60 * ( ( $b - $r ) / $d + 2 );
                    break;
                case $b:
                    $h = 60 * ( ( $r - $g ) / $d + 4 );
                    break;
            }
        }
        $hsl = array( 'h' => round( $h ), 's' => round( $s*100 ), 'l' => round( $l*100 ) );
        //$hslstr = 'hsl('.($hsl[0]).','.($hsl[1]).'%,'.($hsl[2]).'%)';
        return $hsl;
// or return the $hsl array if you want to make adjustments to values
    }




    /**
     * Input: HSL in format Deg, Perc, Perc
     * Output: An array containing HSL in ranges 0-1
     *
     * Divides $h by 60, and $s and $l by 100.
     *
     * hslToRgb calls this by default.
    */
    function degPercPercToHsl($h, $s, $l) {
        //convert h, s, and l back to the 0-1 range

        //convert the hue's 360 degrees in a circle to 1
        $h /= 360;

        //convert the saturation and lightness to the 0-1
        //range by multiplying by 100
        $s /= 100;
        $l /= 100;

        $hsl['h'] =  $h;
        $hsl['s'] = $s;
        $hsl['l'] = $l;

        return $hsl;
    }




    /**
     * Converts an HSL color value to RGB. Conversion formula
     * adapted from http://www.niwa.nu/2013/05/math-behind-colorspace-conversions-rgb-hsl/.
     * Assumes h, s, and l are in the format Degrees,
     * Percent, Percent, and returns r, g, and b in
     * the range [0 - 255].
     *
     * Called by hslToHex by default.
     *
     * Calls:
     *   degPercPercToHsl
     *   hueToRgb
     *
     * @param   Number  h       The hue value
     * @param   Number  s       The saturation level
     * @param   Number  l       The luminence
     * @return  Array           The RGB representation
     */
    function hslToRgb($h, $s, $l){
        $hsl = degPercPercToHsl($h, $s, $l);
        $h = $hsl['h'];
        $s = $hsl['s'];
        $l = $hsl['l'];

        //If there's no saturation, the color is a greyscale,
        //so all three RGB values can be set to the lightness.
        //(Hue doesn't matter, because it's grey, not color)
        if ($s == 0) {
            $r = $l * 255;
            $g = $l * 255;
            $b = $l * 255;
        }
        else {
            //calculate some temperary variables to make the
            //calculation eaisier.
            if ($l < 0.5) {
                $temp2 = $l * (1 + $s);
            } else {
                $temp2 = ($l + $s) - ($s * $l);
            }
            $temp1 = 2 * $l - $temp2;

            //run the calculated vars through hueToRgb to
            //calculate the RGB value.  Note that for the Red
            //value, we add a third (120 degrees), to adjust
            //the hue to the correct section of the circle for
            //red.  Simalarly, for blue, we subtract 1/3.
            $r = 255 * hueToRgb($temp1, $temp2, $h + (1 / 3));
            $g = 255 * hueToRgb($temp1, $temp2, $h);
            $b = 255 * hueToRgb($temp1, $temp2, $h - (1 / 3));
        }

        $rgb['r'] = $r;
        $rgb['g'] = $g;
        $rgb['b'] = $b;

        return $rgb;
    }



    /**
 * Converts an HSL hue to it's RGB value.
 *
 * Input: $temp1 and $temp2 - temperary vars based on
 * whether the lumanence is less than 0.5, and
 * calculated using the saturation and luminence
 * values.
 *  $hue - the hue (to be converted to an RGB
 * value)  For red, add 1/3 to the hue, green
 * leave it alone, and blue you subtract 1/3
 * from the hue.
 *
 * Output: One RGB value.
 *
 * Thanks to Easy RGB for this function (Hue_2_RGB).
 * http://www.easyrgb.com/index.php?X=MATH&$h=19#text19
 *
*/
function hueToRgb($temp1, $temp2, $hue) {
    if ($hue < 0) {
        $hue += 1;
    }
    if ($hue > 1) {
        $hue -= 1;
    }

    if ((6 * $hue) < 1 ) {
        return ($temp1 + ($temp2 - $temp1) * 6 * $hue);
    } elseif ((2 * $hue) < 1 ) {
        return $temp2;
    } elseif ((3 * $hue) < 2 ) {
        return ($temp1 + ($temp2 - $temp1) * ((2 / 3) - $hue) * 6);
    }
    return $temp1;
}



    /**
     * Source: https://stackoverflow.com/questions/2353211/hsl-to-rgb-color-conversion/34363975#34363975
     * Converts HSL to Hex by converting it to
     * RGB, then converting that to hex.
     *
     * string hslToHex($h, $s, $l[, $prependPound = true]
     *
     * $h is the Degrees value of the Hue
     * $s is the Percentage value of the Saturation
     * $l is the Percentage value of the Lightness
     * $prependPound is a bool, whether you want a pound
     *  sign prepended. (optional - default=true)
     *
     * Calls:
     *   hslToRgb
     *
     * Output: Hex in the format: #00ff88 (with
     * pound sign).  Rounded to the nearest whole
     * number.
    */
    function hslToHex($h, $s, $l, $prependPound = true) {
        //convert hsl to rgb
        $rgb = hslToRgb($h,$s,$l);

        //convert rgb to hex
        $hexR = $rgb['r'];
        $hexG = $rgb['g'];
        $hexB = $rgb['b'];

        //round to the nearest whole number
        $hexR = round($hexR);
        $hexG = round($hexG);
        $hexB = round($hexB);

        //convert to hex
        $hexR = dechex($hexR);
        $hexG = dechex($hexG);
        $hexB = dechex($hexB);

        //check for a non-two string length
        //if it's 1, we can just prepend a
        //0, but if it is anything else non-2,
        //it must return false, as we don't
        //know what format it is in.
        if (strlen($hexR) != 2) {
            if (strlen($hexR) == 1) {
                //probably in format #0f4, etc.
                $hexR = "0" . $hexR;
            } else {
                //unknown format
                return false;
            }
        }
        if (strlen($hexG) != 2) {
            if (strlen($hexG) == 1) {
                $hexG = "0" . $hexG;
            } else {
                return false;
            }
        }
        if (strlen($hexB) != 2) {
            if (strlen($hexB) == 1) {
                $hexB = "0" . $hexB;
            } else {
                return false;
            }
        }

        //if prependPound is set, will prepend a
        //# sign to the beginning of the hex code.
        //(default = true)
        $hex = "";
        if ($prependPound) {
            $hex = "#";
        }

        $hex = $hex . $hexR . $hexG . $hexB;

        return $hex;
    }




// custom fonts
add_action('wp_head', 'clb_add_custom_font_families');
add_action('admin_head', 'clb_add_custom_font_families');
function clb_add_custom_font_families() {

    echo '<link rel="stylesheet" href="https://use.typekit.net/gnc0ulw.css">';

}

// implement custom fonts via variable stylesheet injection override
add_action('wp_head', 'clb_ironwood_add_custom_font_families_via_css_variable', 16);
add_action('admin_head', 'clb_ironwood_add_custom_font_families_via_css_variable', 16);
function clb_ironwood_add_custom_font_families_via_css_variable() {

    $primary_font_family = get_field('primary_font_family', 'option');
    if( $primary_font_family ) {
        echo '<style>
                :root {
                    --ironwood-font-primary: ' . $primary_font_family . ';
                }
                </style>';
    }

    $secondary_font_family = get_field('secondary_font_family', 'option');
    if( $secondary_font_family ) {
        echo '<style>
                :root {
                    --ironwood-font-secondary: ' . $secondary_font_family . ';
                }
                </style>';
    }

    $accent_font_family = get_field('accent_font_family', 'option');
    if( $accent_font_family ) {
        echo '<style>
                :root {
                    --ironwood-font-accent: ' . $accent_font_family . ';
                }
                </style>';
    }

}


// Add Bootstrap functionality
add_action('wp_head', 'clb_add_bootstrap_5_2', 4);
//add_action('admin_head', 'clb_add_bootstrap_5_2', 4);
function clb_add_bootstrap_5_2() {

    //echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">';
    echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>';

    echo '
    <!-- link tag to stylesheet that define your layers -->
    <link rel="stylesheet" href="' . get_stylesheet_directory_uri() . '/style.css">
    <style>
    @import url("https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css") layer(bootstrap);
    </style>';

}





// Add Custom Color Palette to Theme

////////////////////////////////////////////////////////////////
// Don't forget to add these colors to your CSS, or else they won't appear on the front end
// Use: https://www.sassmeister.com/

/*
// Gutenberg color options
// -- see editor-color-palette in functions.php
     $colors: (
     	'salmon' 		: #F96D48,
     	'blue' 	: #009CFF,
     	'light-gray' 	: #EEE,
     	'black' 		: #333,
          'white'        : #FFF,
     );
     @each $name, $color in $colors {
     	.has-#{$name}-color {
     		color: $color;
     	}
     	.has-#{$name}-background-color {
     		background-color: $color;
     	}
     }
*/
////////////////////////////////////////////////////////////////
add_action( 'after_setup_theme', 'clb_ironwood_setup_theme_supported_features' );
function clb_ironwood_setup_theme_supported_features() {
    add_theme_support( 'editor-color-palette', array(
        array(
            'name'  => 'Salmon',
            'slug'  => 'salmon',
            'color'	=> '#F96D48',
        ),
        array(
            'name'  => 'Blue',
            'slug'  => 'blue',
            'color' => '#009CFF',
        ),
        array(
            'name'  => 'Light Gray',
            'slug'  => 'light-gray',
            'color' => '#EEE',
        ),
        array(
            'name'	=> 'Black',
            'slug'	=> 'black',
            'color'	=> '#333',
        ),
        array(
            'name'	=> 'White',
            'slug'	=> 'white',
            'color'	=> '#FFF',
        ),
    ) );
}



/**
 * Add User Role Class to Body
 * Referenced code from http://www.studiok40.com/
 * Source: https://lakewood.media/add-user-role-id-body-class-wordpress/
 */
function clb_ironwood_admin_print_user_classes() {
    if ( is_user_logged_in() ) {
        add_filter('body_class','clb_ironwood_admin_class_to_body');
        add_filter('admin_body_class', 'clb_ironwood_admin_class_to_body_admin');
    }
}
add_action('init', 'clb_ironwood_admin_print_user_classes');
 
/// Add user role class to front-end body tag
function clb_ironwood_admin_class_to_body($classes) {
    global $current_user;
    $user_role = array_shift($current_user->roles);
    $classes[] = $user_role;
    return $classes;
}
 
/// Add user role class and user id to front-end body tag
 
// add 'class-name' to the $classes array
function clb_ironwood_admin_class_to_body_admin($classes) {
    global $current_user;
    $user_role = array_shift($current_user->roles);
    /* Adds the user id to the admin body class array */
    $user_ID = $current_user->ID;
    $classes = $user_role.' '.'user-id-'.$user_ID ;
    return $classes;
    return 'user-id-'.$user_ID;
}





// Method 2: Setting.
function my_acf_init() {
    acf_update_setting('google_api_key', 'AIzaSyD-kMkqmuRLsPQe88VLRf6Xwoy_cCelJdQ');
}
add_action('acf/init', 'my_acf_init');






// Add Hello Bar
add_action('genesis_before_header', 'clb_ironwood_publish_hello_bar');
function clb_ironwood_publish_hello_bar() {

    $hello_bar_to_publish = null;

    $hello_bar_text = get_field('hello_bar_text', 'option');
    $current = strtotime("now");
    $hello_bar_start_date = get_field('hello_bar_start_date', 'option');
    $hello_bar_end_date = get_field('hello_bar_end_date', 'option');

    $hello_bar_link = get_field('hello_bar_link', 'option');
    $link_format = get_field('link_format', 'option');
    $hello_bar_link_target = get_field('link_target', 'option');
    $target = ' target="_self"';
    if( $hello_bar_link_target == 'New Tab' ) { $target = ' target="_blank"'; }

    if( !$hello_bar_link ) {
        $hello_bar_to_publish = $hello_bar_text;
    } elseif( $hello_bar_link && $link_format == 'No Button' ) {
        $hello_bar_to_publish = '<a href="' . $hello_bar_link . '"' . $target . '>' . $hello_bar_text . '</a>';
    } elseif( $hello_bar_link && $link_format == 'Button' ) {
        $button_text = get_field('button_text', 'option');
        if( !$button_text ) { $button_text = 'Learn More'; }
        $hello_bar_to_publish = '<span class="clb-hello-bar-text-wrapper">' . $hello_bar_text . '</span><span class="clb-hello-bar-button-wrapper"><a href="' . $hello_bar_link . '" class="button" ' . $target . '>' . $button_text . '</a></span>';
    }

    //echo strtotime($hello_bar_start_date);

    if( $hello_bar_start_date ) {
        if( strtotime($hello_bar_start_date) > $current ) { return; }
    }

    if( $hello_bar_end_date ) {
        if( strtotime($hello_bar_end_date) < $current ) { return; }
    }

    if( $hello_bar_text ) {
        echo '<div class="clb-hello-bar-wrapper">' . $hello_bar_to_publish . '</div>';
    }

}





// Search Bar modal
add_action('genesis_after_header', 'clb_ironwood_modal_search');
function clb_ironwood_modal_search() {

	$blog_title = get_bloginfo();

?>

	<div class="clb-move-modals">
	
		<!-- Modal -->
		<div id="site-search" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">

				<h4 class="modal-title">Search <?php echo $blog_title; ?></h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>

			<div class="modal-body"><?php echo get_search_form(); ?></div>
		</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->


        <!-- Modal -->
		<div id="contact" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">

				<h4 class="modal-title">Contact The Modpodders</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>

			<div class="modal-body"><?php echo do_shortcode('[gravityform id="1" title="false" description="true" ajax="false"]'); ?></div>
		</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->

	</div>

<?php

}



if( function_exists('acf_add_local_field_group') ):

    acf_add_local_field_group(array(
        'key' => 'group_62ec1e6c24973',
        'title' => 'Featured Image Height',
        'fields' => array(
            array(
                'key' => 'field_62ec1e8026938',
                'label' => 'Featured Image Height',
                'name' => 'featured_image_height',
                'aria-label' => '',
                'type' => 'number',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => 400,
                'placeholder' => '',
                'prepend' => '',
                'append' => 'px',
                'min' => '',
                'max' => '',
                'step' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'side',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ));
    
    endif;		



// Remove Genesis Blocks Pro Portfolio Items CPT from Dashboard from Phil Johnston

function clb_ironwood_disable_gpb_portfolio_post_type() {
	remove_action( 'init', 'Genesis\PageBuilder\Portfolio\register_portfolio_post_type' );
}
add_action( 'init', 'clb_ironwood_disable_gpb_portfolio_post_type', 9 );


// Reposition featured images on archive pages
add_action('genesis_before_entry_content', 'clb_add_featured_images_archives', 6);
function clb_add_featured_images_archives() {

	if( is_home() || is_archive() ) {

		echo '<div class="clb-archive-featured-image-area"><a href="' . get_the_permalink() . '">';
			the_post_thumbnail('large');
		echo '</a></div>';

	}

}


add_filter('post_class', 'clb_archive_check_for_featured_image', 10,3);
function clb_archive_check_for_featured_image($classes, $class, $post_id){

    if( get_post_thumbnail_id() ) {
	    $classes[] = 'clb-has-post-thumbnail';
    } else {
	    $classes[] = 'clb-missing-post-thumbnail';
    }

    // Return the array
    return $classes;
}

// Add Read More Link to Manual Excerpts
add_filter('get_the_excerpt', 'manual_excerpt_more');
function manual_excerpt_more($excerpt) {
	$excerpt_more = '';
		if( has_excerpt() ) {
		$excerpt_more = '... <a href="'.get_permalink().'">Read&nbsp;More&nbsp;&#x2192;</a>';
		}
		return $excerpt . $excerpt_more;
}



/** Remove the edit link */
add_filter ( 'genesis_edit_post_link' , '__return_false' );









// full bleed featured image on single pages
add_action('genesis_after_header', 'clb_page_publish_featured_image');
function clb_page_publish_featured_image() {

	if( is_front_page() ) { return; }

	if( is_page() && has_post_thumbnail() ) {

		$post_id = get_the_ID();
		$featured_image = get_the_post_thumbnail( $post_id, 'full' );
		$featured_image_height = get_field('featured_image_height');
		if( !$featured_image_height ) { $featured_image_height = 600; }

		echo '<div class="clb-single-page-featured-img-wrapper" style="max-height:' . $featured_image_height . 'px;">' . $featured_image . '<div class="clb-single-page-title-wrapper"><h1 class="entry-title clb-custom-featured-image-title">' . get_the_title() . '</h1></div></div>';

	}

	elseif( is_home() ) {

		$post_id = $page_for_posts = get_option( 'page_for_posts' );;
		$featured_image = get_the_post_thumbnail( $post_id, 'full' );
		$featured_image_height = get_field('featured_image_height', $post_id);
		if( !$featured_image_height ) { $featured_image_height = 600; }

		echo '<div class="clb-single-page-featured-img-wrapper" style="max-height:' . $featured_image_height . 'px;">' . $featured_image . '<div class="clb-single-page-title-wrapper"><h1 class="entry-title">' . get_the_title( $post_id ) . '</h1></div></div>';

	}

}

//* Add custom body class to the head
add_filter( 'body_class', 'clb_featured_image_body_class' );
function clb_featured_image_body_class( $classes ) {

	if( has_post_thumbnail() || is_home() ) { $classes[] = 'clb-has-featured-image'; }
	else { $classes[] = 'clb-missing-featured-image'; }
	
	return $classes;
	
}






add_action('genesis_loop', 'clb_grid_markup_start', 6);
function clb_grid_markup_start() {

	if( is_home() || is_archive() ) {
		echo '<div class="blog-grid">';
	}

}

// Show archive pagination in a format dependent on chosen setting above the posts on content archives.
remove_action( 'genesis_after_endwhile', 'genesis_posts_nav' );
add_action( 'genesis_loop', 'genesis_posts_nav', 16 );


add_action('genesis_loop', 'clb_grid_markup_end', 14);
function clb_grid_markup_end() {

	if( is_home() || is_archive() ) {
		echo '</div>';
	}

}




// // NEW custom markup for list view of blog
add_action('genesis_entry_header', 'clb_custom_list_view_markup', 6);
function clb_custom_list_view_markup() {

	if( is_home() || is_archive() ) {

		// get author and date too
		$author_id = get_the_author_meta( 'ID' );
		$date = get_the_date( 'M j, Y' );
		$featured_img = get_the_post_thumbnail();
		$featured_image_markup = null;
        $permalink = get_the_permalink();

		if( $featured_img ) {
			$featured_image_markup = '<div class="clb-list-view-img-wrapper"><a href="' . $permalink . '">' . $featured_img . '</a></div>';
			$custom_grid_class = ' clb-has-feat-img';
		} else {
			$custom_grid_class = ' clb-missing-feat-img';
		}

		echo '<div class="clb-custom-list-view-wrapper' . $custom_grid_class  . '">';
			echo $featured_image_markup;
			echo '<div class="clb-entry-card-wrapper">';
				echo '<h2 class="entry-title" itemprop="headline"><a class="entry-title-link" rel="bookmark" href="' . get_the_permalink() . '">' . get_the_title() . '</a></h2>';
				echo '<div class="clb-custom-meta">' . $date . '</div>';
				echo '<div class="clb-custom-excerpt">' . get_the_excerpt() . '</div>';
			echo '</div>';
		echo '</div>';

	}

}


// add blog page content to archive description
add_action('genesis_before_loop', 'clb_publish_blog_page_contents', 16);
function clb_publish_blog_page_contents() {

     if ( is_home() ) {

          $post_id = get_option( 'page_for_posts' );
          $post = get_post( $post_id ); // specific post
          $the_content = apply_filters('the_content', $post->post_content);

          // blog page
          echo '<div class="clb-blog-page-content">' . $the_content . '</div>';
     }

}





add_theme_support( 'enable-custom-gradients' );


/**
 * Get the colors formatted for use with Iris, Automattic's color picker
 */
function output_the_colors() {

	// get the colors
    $color_palette = current( (array) get_theme_support( 'editor-color-palette' ) );

	// bail if there aren't any colors found
	if ( !$color_palette )
		return;

	// output begins
	ob_start();

	// output the names in a string
	echo '[';
		foreach ( $color_palette as $color ) {
			echo "'" . $color['color'] . "', ";
		}
	echo ']';

    return ob_get_clean();

}

/**
 * Add the colors into Iris
 */
add_action( 'acf/input/admin_footer', 'gutenberg_sections_register_acf_color_palette' );
function gutenberg_sections_register_acf_color_palette() {

    $color_palette = output_the_colors();
    if ( !$color_palette )
        return;

    ?>
    <script type="text/javascript">
        (function( $ ) {
            acf.add_filter( 'color_picker_args', function( args, $field ){

                // add the hexadecimal codes here for the colors you want to appear as swatches
                args.palettes = <?php echo $color_palette; ?>

                // return colors
                return args;

            });
        })(jQuery);
    </script>
    <?php

}



// Source: https://www.splitbrain.org/blog/2008-09/18-calculating_color_contrast_with_php
// Use PHP to help automatically determine color contrasts
function lumdiff($R1,$G1,$B1,$R2,$G2,$B2){
    $L1 = 0.2126 * pow($R1/255, 2.2) +
          0.7152 * pow($G1/255, 2.2) +
          0.0722 * pow($B1/255, 2.2);
 
    $L2 = 0.2126 * pow($R2/255, 2.2) +
          0.7152 * pow($G2/255, 2.2) +
          0.0722 * pow($B2/255, 2.2);
 
    if($L1 > $L2){
        return ($L1+0.05) / ($L2+0.05);
    }else{
        return ($L2+0.05) / ($L1+0.05);
    }
}





//* Customize search form input box text
add_filter( 'genesis_search_text', 'sp_search_text' );
function sp_search_text( $text ) {
	return esc_attr( 'Search the Coalition\'s website...' );
}





// add_filter('genesis_post_info', 'replace_with_guest_author_name');
// function replace_with_guest_author_name($post_info) {
//     // Get the current post ID
//     $post_id = get_the_ID();

//     // Retrieve the value of the 'Guest Author Name' ACF field
//     $guest_author_name = get_field('guest_author_name', $post_id);

//     // Check if 'Guest Author Name' is entered
//     if (!empty($guest_author_name)) {
//         // Replace the default author with the guest author name
//         $post_info = sprintf(__('By %s', 'genesis'), esc_html($guest_author_name));
//     }

//     return $post_info;
// }


//* Customize the post info function
add_filter( 'genesis_post_info', 'sp_post_info_filter_clb_single_post' );
function sp_post_info_filter_clb_single_post($post_info) {
    
    if ( is_single() ) {
        $post_info = '[post_date]';
    }

    return $post_info;

}


