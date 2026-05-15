<?php
// ABOUTME: Introspects Yak ACF Theme Settings and validates PATCH payloads for CLI agents.
// ABOUTME: Keeps programmatic updates aligned with wp-admin fields via update_field( ..., 'option' ).

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CLI/agent helpers for Yak Theme Settings (ACF options pages only).
 */
final class Yak_Settings_Agent_Service {

	private const SCHEMA_VERSION = '1.0';

	/** Max repeater rows accepted per PATCH (abuse guard). */
	private const MAX_REPEATER_ROWS = 200;

	/**
	 * Option page slugs registered under Appearance → Theme Settings.
	 *
	 * @var list<string>
	 */
	private const OPTION_PAGE_SLUGS = [
		'theme-settings',
		'yak-options-colors',
		'yak-options-typography',
		'yak-options-layouts',
		'yak-options-performance',
		'yak-options-login',
	];

	/** Field types that never carry stored option values. */
	private const NON_DATA_TYPES = [ 'message', 'accordion', 'tab' ];

	/**
	 * Footgun fields blocked unless YAK_AGENT_ALLOW_PERMISSION_FIELDS is true.
	 *
	 * @var list<string>
	 */
	private const PERMISSION_FIELD_NAMES = [ 'yak_allowed_users', 'yak_dev_mode' ];

	/**
	 * Allowed option page slugs (filterable).
	 *
	 * @return list<string>
	 */
	public static function get_option_page_slugs(): array {
		return array_values(
			array_unique(
				array_filter(
					(array) apply_filters( 'yak/agent/option_pages', self::OPTION_PAGE_SLUGS )
				)
			)
		);
	}

	/**
	 * Whether PATCH may touch permission-related fields.
	 */
	public static function permission_fields_patch_allowed(): bool {
		if ( defined( 'YAK_AGENT_ALLOW_PERMISSION_FIELDS' ) && YAK_AGENT_ALLOW_PERMISSION_FIELDS ) {
			return true;
		}
		return (bool) apply_filters( 'yak/agent/allow_permission_fields', false );
	}

	/**
	 * Full schema document for agents (JSON-serializable array).
	 *
	 * @return array<string,mixed>|WP_Error
	 */
	public static function get_schema_document(): WP_Error|array {
		if ( ! function_exists( 'acf_get_field_groups' ) || ! function_exists( 'acf_get_fields' ) ) {
			return new WP_Error( 'yak_agent_acf_missing', 'ACF functions are not available.' );
		}

		$groups = self::get_matching_field_groups();
		if ( is_wp_error( $groups ) ) {
			return $groups;
		}

		$groups_out       = [];
		$root_field_names = [];

		foreach ( $groups as $group ) {
			$key    = isset( $group['key'] ) ? (string) $group['key'] : '';
			$title  = isset( $group['title'] ) ? (string) $group['title'] : '';
			$fields = acf_get_fields( $group );
			if ( ! is_array( $fields ) ) {
				$fields = [];
			}

			$field_defs = [];
			foreach ( $fields as $field ) {
				if ( ! is_array( $field ) || empty( $field['name'] ) ) {
					continue;
				}
				$name = (string) $field['name'];
				if ( isset( $root_field_names[ $name ] ) ) {
					$root_field_names[ $name ]['duplicate'] = true;
				} else {
					$root_field_names[ $name ] = [ 'duplicate' => false ];
				}
				$field_defs[] = self::describe_field( $field, true );
			}

			$groups_out[] = [
				'key'     => $key,
				'title'   => $title,
				'fields'  => $field_defs,
			];
		}

		$dupes = array_keys(
			array_filter(
				$root_field_names,
				static fn( $meta ) => ! empty( $meta['duplicate'] )
			)
		);

		return [
			'schema_version' => self::SCHEMA_VERSION,
			'theme'          => 'yak',
			'storage'        => 'acf_option',
			'option_pages'   => self::get_option_page_slugs(),
			'field_groups'   => $groups_out,
			'notes'          => [
				'PATCH merges top-level keys only; repeaters replace the entire row set for that field.',
				'Permission fields (yak_allowed_users, yak_dev_mode) are omitted from writable schema unless explicitly allowed.',
				'Use update_field-compatible values (e.g. image fields accept attachment IDs).',
			],
			'warnings'       => $dupes ? [ 'duplicate_root_field_names' => $dupes ] : [],
		];
	}

	/**
	 * Map of root field name => field descriptor (merged across groups).
	 *
	 * @return array<string,array<string,mixed>>|WP_Error
	 */
	public static function get_root_field_map(): WP_Error|array {
		$groups = self::get_matching_field_groups();
		if ( is_wp_error( $groups ) ) {
			return $groups;
		}

		$map = [];
		foreach ( $groups as $group ) {
			$fields = acf_get_fields( $group );
			if ( ! is_array( $fields ) ) {
				continue;
			}
			foreach ( $fields as $field ) {
				if ( ! is_array( $field ) || empty( $field['name'] ) ) {
					continue;
				}
				$name          = (string) $field['name'];
				$map[ $name ] = self::describe_field( $field, true );
			}
		}

		return $map;
	}

	/**
	 * All stored root option values keyed by field name.
	 *
	 * @return array<string,mixed>|WP_Error
	 */
	public static function get_all_values(): WP_Error|array {
		$map = self::get_root_field_map();
		if ( is_wp_error( $map ) ) {
			return $map;
		}

		$values = [];
		foreach ( array_keys( $map ) as $name ) {
			if ( ! function_exists( 'get_field' ) ) {
				return new WP_Error( 'yak_agent_acf_missing', 'get_field() is not available.' );
			}
			$values[ $name ] = get_field( $name, 'option' );
		}

		return $values;
	}

	/**
	 * Partial update for root option fields.
	 *
	 * @param array<string,mixed> $patch
	 * @return true|WP_Error
	 */
	public static function patch_values( array $patch ): WP_Error|bool {
		if ( ! function_exists( 'update_field' ) ) {
			return new WP_Error( 'yak_agent_acf_missing', 'update_field() is not available.' );
		}

		$map = self::get_root_field_map();
		if ( is_wp_error( $map ) ) {
			return $map;
		}

		foreach ( array_keys( $patch ) as $key ) {
			if ( ! is_string( $key ) || $key === '' ) {
				return new WP_Error( 'yak_agent_bad_key', 'Patch keys must be non-empty strings.' );
			}
			if ( ! isset( $map[ $key ] ) ) {
				return new WP_Error(
					'yak_agent_unknown_field',
					sprintf( 'Unknown or non-option Theme Settings field: %s', $key ),
					[ 'field' => $key ]
				);
			}
			if ( empty( $map[ $key ]['writable'] ) ) {
				return new WP_Error(
					'yak_agent_readonly_field',
					sprintf( 'Field is not writable via agent API: %s', $key ),
					[ 'field' => $key ]
				);
			}
		}

		foreach ( $patch as $name => $raw ) {
			$descriptor = $map[ $name ];
			$sanitized  = self::sanitize_value_for_descriptor( $descriptor, $raw );
			if ( is_wp_error( $sanitized ) ) {
				return $sanitized;
			}

			$updated = update_field( $name, $sanitized, 'option' );
			// ACF may return false when the value is unchanged; verify current value.
			if ( false === $updated ) {
				$current = function_exists( 'get_field' ) ? get_field( $name, 'option' ) : null;
				if ( wp_json_encode( $current ) !== wp_json_encode( $sanitized ) ) {
					return new WP_Error(
						'yak_agent_update_failed',
						sprintf( 'update_field failed for %s', $name ),
						[ 'field' => $name ]
					);
				}
			}
		}

		return true;
	}

	/**
	 * @return list<array<string,mixed>>|WP_Error
	 */
	private static function get_matching_field_groups(): WP_Error|array {
		if ( ! function_exists( 'acf_get_field_groups' ) ) {
			return new WP_Error( 'yak_agent_acf_missing', 'acf_get_field_groups() is not available.' );
		}

		$allowed = array_flip( self::get_option_page_slugs() );
		$groups  = acf_get_field_groups( [ 'active' => true ] );
		if ( ! is_array( $groups ) ) {
			return [];
		}

		$matched = [];
		foreach ( $groups as $group ) {
			if ( ! is_array( $group ) || empty( $group['location'] ) || ! is_array( $group['location'] ) ) {
				continue;
			}
			if ( self::location_targets_allowed_options_pages( $group['location'], $allowed ) ) {
				$matched[] = $group;
			}
		}

		return $matched;
	}

	/**
	 * True when any OR-branch contains a rule targeting an allowed options page.
	 *
	 * @param array<int,array<int,array<string,mixed>>> $location
	 * @param array<string,int>                         $allowed_flip
	 */
	private static function location_targets_allowed_options_pages( array $location, array $allowed_flip ): bool {
		foreach ( $location as $and_group ) {
			if ( ! is_array( $and_group ) ) {
				continue;
			}
			foreach ( $and_group as $rule ) {
				if ( ! is_array( $rule ) ) {
					continue;
				}
				if ( ( $rule['param'] ?? '' ) === 'options_page' && ( $rule['operator'] ?? '' ) === '==' ) {
					$value = (string) ( $rule['value'] ?? '' );
					if ( isset( $allowed_flip[ $value ] ) ) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * @param array<string,mixed> $field
	 * @return array<string,mixed>
	 */
	private static function describe_field( array $field, bool $is_root ): array {
		$type = isset( $field['type'] ) ? (string) $field['type'] : 'unknown';
		$name = isset( $field['name'] ) ? (string) $field['name'] : '';

		$writable_type = ! in_array( $type, self::NON_DATA_TYPES, true );
		$blocked       = self::is_permission_field_blocked( $name );
		$writable      = $writable_type && ! $blocked && ! empty( $name );

		$desc = [
			'name'         => $name,
			'label'        => isset( $field['label'] ) ? (string) $field['label'] : '',
			'type'         => $type,
			'instructions' => isset( $field['instructions'] ) ? (string) $field['instructions'] : '',
			'required'     => ! empty( $field['required'] ),
			'writable'     => $writable,
			'is_root'      => $is_root,
		];

		if ( isset( $field['default_value'] ) ) {
			$desc['default_value'] = $field['default_value'];
		}
		if ( isset( $field['return_format'] ) ) {
			$desc['return_format'] = $field['return_format'];
		}
		if ( isset( $field['min'] ) ) {
			$desc['min'] = $field['min'];
		}
		if ( isset( $field['max'] ) ) {
			$desc['max'] = $field['max'];
		}
		if ( isset( $field['step'] ) ) {
			$desc['step'] = $field['step'];
		}
		if ( ! empty( $field['choices'] ) && is_array( $field['choices'] ) ) {
			$desc['choices'] = self::normalize_choices( $field['choices'] );
		}

		if ( $blocked ) {
			$desc['blocked_reason'] = 'permission_field_requires_YAK_AGENT_ALLOW_PERMISSION_FIELDS';
		}

		if ( 'repeater' === $type && ! empty( $field['sub_fields'] ) && is_array( $field['sub_fields'] ) ) {
			$desc['sub_fields'] = [];
			foreach ( $field['sub_fields'] as $sub ) {
				if ( is_array( $sub ) ) {
					$desc['sub_fields'][] = self::describe_field( $sub, false );
				}
			}
			if ( isset( $field['min'] ) ) {
				$desc['min_rows'] = $field['min'];
			}
			if ( isset( $field['max'] ) ) {
				$desc['max_rows'] = $field['max'];
			}
		}

		return $desc;
	}

	/**
	 * @param array<string|int,string|array<string,mixed>> $choices
	 * @return array<string,string>
	 */
	private static function normalize_choices( array $choices ): array {
		$out = [];
		foreach ( $choices as $k => $v ) {
			if ( is_array( $v ) ) {
				continue;
			}
			$out[ (string) $k ] = (string) $v;
		}
		return $out;
	}

	private static function is_permission_field_blocked( string $field_name ): bool {
		if ( self::permission_fields_patch_allowed() ) {
			return false;
		}
		return in_array( $field_name, self::PERMISSION_FIELD_NAMES, true );
	}

	/**
	 * @param array<string,mixed> $descriptor Output of describe_field for root field.
	 * @return mixed|WP_Error
	 */
	private static function sanitize_value_for_descriptor( array $descriptor, mixed $raw ): mixed {
		$type = $descriptor['type'] ?? '';

		switch ( $type ) {
			case 'text':
				return is_string( $raw ) ? sanitize_text_field( $raw ) : new WP_Error( 'yak_agent_type', 'Expected string.' );

			case 'wysiwyg':
				return is_string( $raw ) ? wp_kses_post( $raw ) : new WP_Error( 'yak_agent_type', 'Expected string.' );

			case 'textarea':
				return is_string( $raw ) ? sanitize_textarea_field( $raw ) : new WP_Error( 'yak_agent_type', 'Expected string.' );

			case 'url':
				return is_string( $raw ) ? esc_url_raw( $raw ) : new WP_Error( 'yak_agent_type', 'Expected string URL.' );

			case 'email':
				return is_string( $raw ) ? sanitize_email( $raw ) : new WP_Error( 'yak_agent_type', 'Expected string email.' );

			case 'number':
			case 'range':
				if ( ! is_numeric( $raw ) ) {
					return new WP_Error( 'yak_agent_type', 'Expected numeric value.' );
				}
				$num = 0 + $raw;
				if ( isset( $descriptor['min'] ) && is_numeric( $descriptor['min'] ) && $num < (float) $descriptor['min'] ) {
					return new WP_Error( 'yak_agent_bounds', 'Value below min.' );
				}
				if ( isset( $descriptor['max'] ) && is_numeric( $descriptor['max'] ) && $num > (float) $descriptor['max'] ) {
					return new WP_Error( 'yak_agent_bounds', 'Value above max.' );
				}
				return $num;

			case 'true_false':
				return self::to_bool( $raw );

			case 'button_group':
			case 'radio':
			case 'select':
				if ( ! is_string( $raw ) && ! is_int( $raw ) ) {
					return new WP_Error( 'yak_agent_type', 'Expected scalar choice value.' );
				}
				$key     = (string) $raw;
				$choices = isset( $descriptor['choices'] ) && is_array( $descriptor['choices'] ) ? $descriptor['choices'] : [];
				if ( ! isset( $choices[ $key ] ) ) {
					return new WP_Error( 'yak_agent_choice', 'Value is not a valid choice key.' );
				}
				return $key;

			case 'color_picker':
				if ( ! is_string( $raw ) ) {
					return new WP_Error( 'yak_agent_type', 'Expected string color.' );
				}
				$hex = sanitize_hex_color( $raw );
				if ( null === $hex ) {
					return new WP_Error( 'yak_agent_color', 'Invalid hex color.' );
				}
				return $hex;

			case 'image':
			case 'file':
				$id = absint( $raw );
				if ( $id < 1 ) {
					return new WP_Error( 'yak_agent_attachment', 'Expected positive attachment ID.' );
				}
				if ( 'attachment' !== get_post_type( $id ) ) {
					return new WP_Error( 'yak_agent_attachment', 'Attachment does not exist.' );
				}
				return $id;

			case 'user':
				if ( self::is_permission_field_blocked( (string) ( $descriptor['name'] ?? '' ) ) ) {
					return new WP_Error( 'yak_agent_blocked', 'Permission field updates are disabled.' );
				}
				$ids = array_map( 'absint', (array) $raw );
				$ids = array_values( array_filter( $ids ) );
				foreach ( $ids as $uid ) {
					if ( ! get_userdata( $uid ) ) {
						return new WP_Error( 'yak_agent_user', sprintf( 'User ID %d does not exist.', $uid ) );
					}
				}
				return $ids;

			case 'repeater':
				return self::sanitize_repeater_value( $descriptor, $raw );

			default:
				return new WP_Error(
					'yak_agent_unsupported_type',
					sprintf( 'Unsupported field type for agent PATCH: %s', $type )
				);
		}
	}

	private static function to_bool( mixed $raw ): bool {
		if ( is_bool( $raw ) ) {
			return $raw;
		}
		if ( is_int( $raw ) ) {
			return $raw !== 0;
		}
		if ( is_string( $raw ) ) {
			$lower = strtolower( $raw );
			if ( in_array( $lower, [ '1', 'true', 'yes', 'on' ], true ) ) {
				return true;
			}
			if ( in_array( $lower, [ '0', 'false', 'no', 'off', '' ], true ) ) {
				return false;
			}
		}
		return (bool) $raw;
	}

	/**
	 * @param array<string,mixed> $descriptor
	 * @return array<int,array<string,mixed>>|WP_Error
	 */
	private static function sanitize_repeater_value( array $descriptor, mixed $raw ): WP_Error|array {
		if ( ! is_array( $raw ) ) {
			return new WP_Error( 'yak_agent_type', 'Repeater value must be a JSON array.' );
		}

		if ( count( $raw ) > self::MAX_REPEATER_ROWS ) {
			return new WP_Error(
				'yak_agent_limits',
				sprintf( 'Too many repeater rows (max %d).', self::MAX_REPEATER_ROWS )
			);
		}

		$sub_defs = [];
		foreach ( (array) ( $descriptor['sub_fields'] ?? [] ) as $sub ) {
			if ( ! empty( $sub['name'] ) ) {
				$sub_defs[ (string) $sub['name'] ] = $sub;
			}
		}

		if ( empty( $sub_defs ) ) {
			return [];
		}

		$rows = [];
		foreach ( $raw as $idx => $row ) {
			if ( ! is_array( $row ) ) {
				return new WP_Error( 'yak_agent_repeater', sprintf( 'Repeater row %s must be an object.', (string) $idx ) );
			}

			$sanitized_row = [];
			foreach ( $sub_defs as $sub_name => $sub_desc ) {
				if ( ! array_key_exists( $sub_name, $row ) ) {
					continue;
				}
				$cell = self::sanitize_value_for_descriptor( $sub_desc, $row[ $sub_name ] );
				if ( is_wp_error( $cell ) ) {
					return new WP_Error(
						$cell->get_error_code(),
						sprintf( '%s (row %s, field %s)', $cell->get_error_message(), (string) $idx, $sub_name ),
						$cell->get_error_data()
					);
				}
				$sanitized_row[ $sub_name ] = $cell;
			}

			$unknown = array_diff_key( $row, $sub_defs );
			if ( ! empty( $unknown ) ) {
				return new WP_Error(
					'yak_agent_repeater',
					sprintf( 'Unknown sub-fields in row %s: %s', (string) $idx, implode( ', ', array_keys( $unknown ) ) )
				);
			}

			$rows[] = $sanitized_row;
		}

		return $rows;
	}
}
