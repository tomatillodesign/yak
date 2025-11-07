<?php
/**
 * Wide Page Content Template.
 *
 * This file adds the wide page content template to the Yak Theme.
 * Sets all content to match the theme's alignwide width (1200px).
 *
 * Template Name: Wide Page Content
 *
 * @package Yak
 * @author  Custom
 * @license GPL-2.0-or-later
 */

add_filter( 'body_class', 'yak_wide_page_content_body_class' );
/**
 * Adds wide-page-content body class.
 *
 * @since 1.0.0
 *
 * @param array $classes Original body classes.
 * @return array Modified body classes.
 */
function yak_wide_page_content_body_class( $classes ) {

	$classes[] = 'wide-page-content';
	return $classes;

}

// Runs the Genesis loop.
genesis();

