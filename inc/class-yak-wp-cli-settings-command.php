<?php
// ABOUTME: Registers WP-CLI commands for Yak Theme Settings agent workflows.
// ABOUTME: Loads only under WP-CLI; emits JSON and maps validation errors to exit codes.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * WP-CLI entrypoint: wp yak settings <schema|get|patch>
 */
class Yak_WP_CLI_Settings_Command extends WP_CLI_Command {

	/**
	 * Print the self-describing Theme Settings schema (ACF options pages).
	 *
	 * ## OPTIONS
	 *
	 * [--pretty]
	 * : Pretty-print JSON output.
	 *
	 * ## EXAMPLES
	 *
	 *     wp yak settings schema --path=/path/to/wordpress --pretty
	 *
	 * @when after_wp_load
	 */
	public function schema( $args, $assoc_args ) {
		$doc = Yak_Settings_Agent_Service::get_schema_document();
		if ( is_wp_error( $doc ) ) {
			WP_CLI::error( $doc->get_error_message() );
		}

		$flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
		if ( ! empty( $assoc_args['pretty'] ) ) {
			$flags |= JSON_PRETTY_PRINT;
		}

		WP_CLI::line( (string) wp_json_encode( $doc, $flags ) );
	}

	/**
	 * Print current Theme Settings option values as JSON.
	 *
	 * ## OPTIONS
	 *
	 * [--pretty]
	 * : Pretty-print JSON output.
	 *
	 * ## EXAMPLES
	 *
	 *     wp yak settings get --path=/path/to/wordpress --pretty
	 *
	 * @when after_wp_load
	 */
	public function get( $args, $assoc_args ) {
		$vals = Yak_Settings_Agent_Service::get_all_values();
		if ( is_wp_error( $vals ) ) {
			WP_CLI::error( $vals->get_error_message() );
		}

		$flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
		if ( ! empty( $assoc_args['pretty'] ) ) {
			$flags |= JSON_PRETTY_PRINT;
		}

		WP_CLI::line( (string) wp_json_encode( $vals, $flags ) );
	}

	/**
	 * Merge partial Theme Settings updates from JSON (root field keys only).
	 *
	 * ## OPTIONS
	 *
	 * [<file>]
	 * : Path to a JSON file. If omitted, JSON is read from STDIN.
	 *
	 * ## EXAMPLES
	 *
	 *     wp yak settings patch settings.json --user=2 --path=/path/to/wordpress
	 *     wp yak settings patch --user=2 --path=/path/to/wordpress < settings.json
	 *
	 * @when after_wp_load
	 */
	public function patch( $args, $assoc_args ) {
		$user_id = get_current_user_id();
		if ( $user_id < 1 ) {
			WP_CLI::error( 'Specify --user=<id> with a Yak-authorized WordPress user (required for patch).' );
		}

		if ( ! function_exists( 'yak_user_is_yak_authorized' ) || ! yak_user_is_yak_authorized( $user_id ) ) {
			WP_CLI::error( 'The selected user is not authorized for Yak Theme Settings.' );
		}

		$json = '';
		if ( ! empty( $args[0] ) ) {
			$file = $args[0];
			if ( ! is_readable( $file ) ) {
				WP_CLI::error( sprintf( 'Cannot read file: %s', $file ) );
			}
			$raw = file_get_contents( $file );
			$json = is_string( $raw ) ? $raw : '';
		} else {
			$json = (string) \WP_CLI\Utils\read_stdin();
		}

		if ( $json === '' ) {
			WP_CLI::error( 'Provide a JSON file path or pipe JSON on stdin.' );
		}

		$patch = json_decode( $json, true );
		if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $patch ) ) {
			WP_CLI::error( 'Invalid JSON: ' . json_last_error_msg() );
		}

		$result = Yak_Settings_Agent_Service::patch_values( $patch );
		if ( is_wp_error( $result ) ) {
			WP_CLI::error( $result->get_error_message() );
		}

		WP_CLI::success( 'Theme settings updated.' );
	}
}

WP_CLI::add_command( 'yak settings', 'Yak_WP_CLI_Settings_Command' );
