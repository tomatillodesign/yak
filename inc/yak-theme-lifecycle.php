<?php
// ABOUTME: Theme activation and deactivation lifecycle hooks
// ABOUTME: Version stamp for future migrations; stub cleanup when switching away from this child theme

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stylesheet slug (theme directory name) for this child theme.
 */
function yak_theme_stylesheet_slug() {
	static $slug = null;
	if ( null === $slug ) {
		$slug = basename( dirname( __DIR__ ) );
	}
	return $slug;
}

add_action( 'after_switch_theme', 'yak_theme_after_switch', 10, 2 );
/**
 * Runs when any theme is activated; only acts when the active theme is this child.
 *
 * Stores `yak_theme_db_version` from style.css Version for future upgrade routines.
 *
 * @param string   $old_name  Previous theme name.
 * @param WP_Theme $old_theme Previous theme object.
 */
function yak_theme_after_switch( $old_name, $old_theme = null ) {
	$current = wp_get_theme();
	if ( ! $current->exists() ) {
		return;
	}
	if ( $current->get_stylesheet() !== yak_theme_stylesheet_slug() ) {
		return;
	}

	$version = $current->get( 'Version' );
	if ( ! is_string( $version ) || $version === '' ) {
		return;
	}

	update_option( 'yak_theme_db_version', sanitize_text_field( $version ) );

	/**
	 * After Yak records activation version (migrations, defaults, rewrite flush, etc.).
	 *
	 * @param string $version   Sanitized Version header from style.css.
	 * @param string $old_name  Previous theme name.
	 * @param WP_Theme|null $old_theme Previous theme.
	 */
	do_action( 'yak_theme_after_switch', $version, $old_name, $old_theme );
}

add_action( 'switch_theme', 'yak_theme_on_leave', 10, 3 );
/**
 * Runs when switching themes; only acts when *leaving* this child theme.
 *
 * Stub: add transient cleanup, cron unscheduling, or caps cleanup here if needed.
 *
 * @param string   $new_name  New theme stylesheet or name (WP passes stylesheet).
 * @param WP_Theme $new_theme New theme.
 * @param WP_Theme $old_theme Previous theme.
 */
function yak_theme_on_leave( $new_name, $new_theme, $old_theme ) {
	if ( ! $old_theme instanceof WP_Theme ) {
		return;
	}
	if ( $old_theme->get_stylesheet() !== yak_theme_stylesheet_slug() ) {
		return;
	}

	/**
	 * Yak is being deactivated (another theme is taking over).
	 *
	 * @param string   $new_name  New theme stylesheet.
	 * @param WP_Theme $new_theme New theme.
	 * @param WP_Theme $old_theme This child theme (Yak).
	 */
	do_action( 'yak_theme_on_leave', $new_name, $new_theme, $old_theme );
}
