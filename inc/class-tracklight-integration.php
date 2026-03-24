<?php
declare(strict_types=1);

namespace Yak\Tracklight;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Yak Theme ↔ Tracklight integration.
 *
 * Emits Tracklight events using the external intake:
 *   - tracklight_log_event( $payload )  (preferred)
 *   - do_action( 'tracklight/log', $payload ) (fallback)
 *
 * Event types:
 *   yak_theme_activated
 *   yak_theme_settings_created
 *   yak_theme_settings_updated
 *   yak_theme_settings_deleted
 *
 * Configure the option name(s) via:
 *   add_filter( 'yak/tracklight/option_names', fn() => ['yak_theme_settings'] );
 */
final class Integration {

	public static function boot(): void {
		// Allow site owners to disable integration globally.
		if ( defined( 'YAK_TL_DISABLE' ) && YAK_TL_DISABLE ) {
			return;
		}

		// Log theme activation (fires after a theme is switched to Yak).
		add_action( 'after_switch_theme', [ __CLASS__, 'on_activation' ], 10, 2 );

		// Watch Yak Theme options. Replace defaults via the filter below.
		foreach ( self::option_names() as $opt ) {
			add_action( "add_option_{$opt}",    fn( $option, $value )      => self::on_created( $option, $value ), 10, 2 );
			add_action( "update_option_{$opt}", fn( $old, $new, $option )  => self::on_updated( $option, $old, $new ), 10, 3 );
			add_action( 'deleted_option',       function( $deleted_option ) use ( $opt ) {
				if ( $deleted_option === $opt ) { self::on_deleted( $deleted_option ); }
			}, 10, 1 );
		}
	}

	/** Option names Yak Theme uses for its settings (override via filter). */
	private static function option_names(): array {
		$defaults = [ 'yak_theme_settings' ]; // ← set your real option here if different
		return (array) apply_filters( 'yak/tracklight/option_names', $defaults );
	}

	/* ---------- Event sources ---------- */

	public static function on_activation( $old_name = '', $old_theme = null ): void {
		$theme = self::theme_obj();
		self::log([
			'event'            => __( 'Yak Theme activated', 'yak' ),
			'_tl_type'         => 'yak_theme_activated',
			'_tl_source'       => 'yak_theme',
			'_tl_actor'        => self::actor(),
			'_tl_object_type'  => 'theme',
			'_tl_object_id'    => $theme['id'],
			'_tl_object_title' => $theme['title'],
			'_tl_bucket'       => 'administrative',
			'_tl_when'         => gmdate('Y-m-d H:i:s'),
			'_tl_context'      => array_filter([
				'from' => is_object( $old_theme ) ? (string) $old_theme->get( 'Name' ) : (string) $old_name,
			]),
		]);
	}

	public static function on_created( string $option, $value ): void {
		$theme = self::theme_obj();
		$val   = self::to_array_shape( $value );

		self::log([
			'event'            => __( 'Yak settings created', 'yak' ),
			'_tl_type'         => 'yak_theme_settings_created',
			'_tl_source'       => 'yak_theme',
			'_tl_actor'        => self::actor(),
			'_tl_object_type'  => 'theme',
			'_tl_object_id'    => $theme['id'],
			'_tl_object_title' => $theme['title'],
			'_tl_bucket'       => 'administrative',
			'_tl_when'         => gmdate('Y-m-d H:i:s'),
			'_tl_context'      => [
				'option' => $option,
				'keys'   => array_slice( array_keys( $val ), 0, 20 ),
			],
			// Keep values out of logs; a tiny sample of keys only (privacy).
		]);
	}

	public static function on_updated( string $option, $old, $new ): void {
		$oldA = self::to_array_shape( $old );
		$newA = self::to_array_shape( $new );

		// Bail if no meaningful change.
		if ( wp_json_encode( $oldA ) === wp_json_encode( $newA ) ) { return; }

		$diff  = self::diff_keys( $oldA, $newA );
		$theme = self::theme_obj();

		self::log([
			'event'            => __( 'Yak settings updated', 'yak' ),
			'_tl_type'         => 'yak_theme_settings_updated',
			'_tl_source'       => 'yak_theme',
			'_tl_actor'        => self::actor(),
			'_tl_object_type'  => 'theme',
			'_tl_object_id'    => $theme['id'],
			'_tl_object_title' => $theme['title'],
			'_tl_bucket'       => 'administrative',
			'_tl_when'         => gmdate('Y-m-d H:i:s'),
			'_tl_context'      => [
				'option'          => $option,
				'keys_added'      => count( $diff['added'] ),
				'keys_removed'    => count( $diff['removed'] ),
				'keys_changed'    => count( $diff['changed'] ),
				'sample_added'    => array_slice( $diff['added'],   0, 8 ),
				'sample_removed'  => array_slice( $diff['removed'], 0, 8 ),
				'sample_changed'  => array_slice( $diff['changed'], 0, 8 ),
			],
		]);
	}

	public static function on_deleted( string $option ): void {
		$theme = self::theme_obj();
		self::log([
			'event'            => __( 'Yak settings deleted', 'yak' ),
			'_tl_type'         => 'yak_theme_settings_deleted',
			'_tl_source'       => 'yak_theme',
			'_tl_actor'        => self::actor(),
			'_tl_object_type'  => 'theme',
			'_tl_object_id'    => $theme['id'],
			'_tl_object_title' => $theme['title'],
			'_tl_bucket'       => 'administrative',
			'_tl_when'         => gmdate('Y-m-d H:i:s'),
			'_tl_context'      => [ 'option' => $option ],
		]);
	}

	/* ---------- Helpers ---------- */

	private static function actor(): string {
		$uid = get_current_user_id();
		return $uid > 0 ? (string) $uid : 'system';
	}

	private static function theme_obj(): array {
		$theme = wp_get_theme();
		return [
			'id'    => function_exists( 'get_stylesheet' ) ? (string) get_stylesheet() : '',
			'title' => $theme ? (string) $theme->get( 'Name' ) : 'Theme',
		];
	}

	/** Call Tracklight intake safely. */
	private static function log( array $payload ): void {
		if ( function_exists( 'tracklight_log_event' ) ) {
			tracklight_log_event( $payload );
		} else {
			// If Tracklight isn’t active, this is a harmless no-op for listeners.
			do_action( 'tracklight/log', $payload );
		}
	}

	/** Normalize any value to an array shape (without leaking actual values). */
	private static function to_array_shape( $v ): array {
		if ( is_array( $v ) ) {
			return $v;
		}
		// Represent scalars without content (we don’t log raw values).
		return [ '__scalar__' => is_scalar( $v ) ? (string) $v : gettype( $v ) ];
	}

	/** Return sets of added/removed/changed top-level keys (shallow). */
	private static function diff_keys( array $old, array $new ): array {
		$added = $removed = $changed = [];
		$keys  = array_unique( array_merge( array_keys( $old ), array_keys( $new ) ) );
		foreach ( $keys as $k ) {
			$ok = array_key_exists( $k, $old );
			$nk = array_key_exists( $k, $new );
			if ( ! $ok && $nk )        { $added[]   = (string) $k; continue; }
			if ( $ok && ! $nk )        { $removed[] = (string) $k; continue; }
			if ( wp_json_encode( $old[ $k ] ) !== wp_json_encode( $new[ $k ] ) ) {
				$changed[] = (string) $k;
			}
		}
		return compact( 'added', 'removed', 'changed' );
	}
}
