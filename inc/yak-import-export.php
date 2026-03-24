<?php
/**
 * Yak Theme Settings Import/Export Tool
 * 
 * Allows exporting and importing ALL Yak theme settings via JSON
 * Works between Yak themes only
 * 
 * @package Yak
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Option keys (options_* row names) that must never be changed via Yak JSON import.
 *
 * @return string[]
 */
function yak_import_get_denied_option_names() {
	$denied = [
		'options_active_plugins',
		'options_blog_public',
		'options_siteurl',
		'options_home',
		'options_admin_email',
		'options_users_can_register',
		'options_uninstall_plugins',
		'options_rewrite_rules',
		'options_user_roles',
	];
	return (array) apply_filters( 'yak_import_denied_option_names', $denied );
}

/////////////////////////////////////////////////////////////////////////////////
// Performance & Tools — import/export UI (styles in clb-custom-yak-admin-styles.css)
/////////////////////////////////////////////////////////////////////////////////

add_action( 'admin_footer', 'yak_import_export_scripts' );
function yak_import_export_scripts() {
	// Check if we're on the performance page
	if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'yak-options-performance' ) {
		return;
	}
	?>
	<script>
		jQuery(document).ready(function($) {
		var ajaxurl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';

		// Export button
		$('#yak-export-btn').on('click', function(e) {
			e.preventDefault();

			$('#yak-export-notice').html('<div class="yak-notice yak-notice-success">⏳ Exporting settings...</div>');
			
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'yak_export_settings',
					nonce: '<?php echo wp_create_nonce( 'yak_export_settings' ); ?>'
				},
				success: function(response) {
					if (response.success) {
						$('#yak-export-output').val(JSON.stringify(response.data, null, 2));
						$('#yak-export-notice').html('<div class="yak-notice yak-notice-success">✓ Settings exported successfully! Copy the JSON below.</div>');
					} else {
						$('#yak-export-notice').html('<div class="yak-notice yak-notice-error">✗ Export failed: ' + (response.data || 'Unknown error') + '</div>');
					}
				},
				error: function() {
					$('#yak-export-notice').html('<div class="yak-notice yak-notice-error">✗ Export failed due to server error.</div>');
				}
			});
		});

		// Import button
		$('#yak-import-btn').on('click', function(e) {
			e.preventDefault();
			
			var jsonData = $('#yak-import-input').val().trim();
			
			if (!jsonData) {
				$('#yak-import-notice').html('<div class="yak-notice yak-notice-error">✗ Please paste JSON data first.</div>');
				return;
			}

			// Confirm before importing
			if (!confirm('⚠️ This will OVERWRITE all existing theme settings. Are you sure?\n\nRecommendation: Export your current settings first as backup.')) {
				return;
			}

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'yak_import_settings',
					nonce: '<?php echo wp_create_nonce( 'yak_import_settings' ); ?>',
					json_data: jsonData
				},
				success: function(response) {
					if (response.success) {
						var message = '<div class="yak-notice yak-notice-success">✓ Settings imported successfully!</div>';
						if (response.data.warnings && response.data.warnings.length > 0) {
							message += '<div class="yak-notice yak-notice-warning">';
							message += '<strong>⚠️ Warnings:</strong><ul class="yak-import-warning-list">';
							response.data.warnings.forEach(function(warning) {
								message += '<li>' + warning + '</li>';
							});
							message += '</ul></div>';
						}
						$('#yak-import-notice').html(message);
						
						// Reload page after 2 seconds
						setTimeout(function() {
							location.reload();
						}, 2000);
					} else {
						$('#yak-import-notice').html('<div class="yak-notice yak-notice-error">✗ Import failed: ' + (response.data || 'Unknown error') + '</div>');
					}
				},
				error: function() {
					$('#yak-import-notice').html('<div class="yak-notice yak-notice-error">✗ Import failed due to server error.</div>');
				}
			});
		});

		// Copy to clipboard button
		$('#yak-copy-btn').on('click', function(e) {
			e.preventDefault();
			var textarea = $('#yak-export-output')[0];
			textarea.select();
			document.execCommand('copy');
			$(this).text('✓ Copied!').prop('disabled', true);
			setTimeout(function() {
				$('#yak-copy-btn').text('Copy to clipboard').prop('disabled', false);
			}, 2000);
		});

		// Download JSON button
		$('#yak-download-btn').on('click', function(e) {
			e.preventDefault();
			var json = $('#yak-export-output').val();
			if (!json) return;
			
			var date = new Date();
			var filename = 'yak-settings-' + 
				date.getFullYear() + '-' + 
				String(date.getMonth() + 1).padStart(2, '0') + '-' + 
				String(date.getDate()).padStart(2, '0') + '-' +
				String(date.getHours()).padStart(2, '0') + 
				String(date.getMinutes()).padStart(2, '0') + 
				String(date.getSeconds()).padStart(2, '0') + '.json';
			
			var blob = new Blob([json], { type: 'application/json' });
			var url = URL.createObjectURL(blob);
			var a = document.createElement('a');
			a.href = url;
			a.download = filename;
			a.click();
			URL.revokeObjectURL(url);
		});
	});
	</script>
	<?php
}

/////////////////////////////////////////////////////////////////////////////////
// Add Import/Export Field Group to Performance Page
/////////////////////////////////////////////////////////////////////////////////

if ( function_exists( 'acf_add_local_field_group' ) ) {
	acf_add_local_field_group( [
		'key' => 'group_yak_import_export',
		'title' => 'Import / Export Settings',
		'fields' => [
			[
				'key' => 'field_yak_import_export_ui',
				'label' => '',
				'name' => 'yak_import_export_ui',
				'type' => 'message',
				'message' => '
					<div class="yak-import-export-section" role="region" aria-labelledby="yak-export-heading">
						<h2 id="yak-export-heading" class="yak-tools-section-title">Export settings</h2>
						<p class="yak-tools-lede">Export all Yak theme settings to JSON for backup or migration to another Yak site.</p>
						
						<div class="yak-button-group">
							<button type="button" id="yak-export-btn" class="button button-primary">Export all settings</button>
						</div>
						
						<div id="yak-export-notice" role="status" aria-live="polite"></div>
						
						<div class="yak-tools-field">
							<label class="screen-reader-text" for="yak-export-output">Exported JSON</label>
							<textarea id="yak-export-output" class="yak-json-textarea" placeholder="Exported JSON will appear here…" readonly rows="12"></textarea>
						</div>
						
						<div class="yak-button-group yak-button-group--row">
							<button type="button" id="yak-copy-btn" class="button">Copy to clipboard</button>
							<button type="button" id="yak-download-btn" class="button">Download JSON file</button>
						</div>
					</div>

					<hr class="yak-tools-divider" />

					<div class="yak-import-export-section" role="region" aria-labelledby="yak-import-heading">
						<h2 id="yak-import-heading" class="yak-tools-section-title">Import settings</h2>
						<p class="yak-tools-lede"><strong>Warning:</strong> this overwrites existing theme settings. Export a backup first.</p>
						
						<div class="yak-tools-field">
							<label class="screen-reader-text" for="yak-import-input">JSON to import</label>
							<textarea id="yak-import-input" class="yak-json-textarea" placeholder="Paste exported JSON here…" rows="12"></textarea>
						</div>
						
						<div class="yak-button-group">
							<button type="button" id="yak-import-btn" class="button button-primary">Import settings</button>
						</div>
						
						<div id="yak-import-notice" role="status" aria-live="polite"></div>
					</div>
				',
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
		'menu_order' => 100,
	] );
}

/////////////////////////////////////////////////////////////////////////////////
// AJAX Handler: Export Settings
/////////////////////////////////////////////////////////////////////////////////

add_action( 'wp_ajax_yak_export_settings', 'yak_ajax_export_settings' );
function yak_ajax_export_settings() {
	// Security check
	check_ajax_referer( 'yak_export_settings', 'nonce' );
	
	if ( ! current_user_can( YAK_CAP_THEME_SETTINGS ) ) {
		wp_send_json_error( 'Insufficient permissions' );
	}

	// Get all options
	global $wpdb;
	$options = $wpdb->get_results( 
		"SELECT option_name, option_value 
		FROM {$wpdb->options} 
		WHERE option_name LIKE 'options_%'"
	);

	$export_data = array(
		'yak_theme_version' => wp_get_theme()->get( 'Version' ),
		'export_date' => current_time( 'mysql' ),
		'site_url' => get_site_url(),
		'settings' => array()
	);

	foreach ( $options as $option ) {
		$export_data['settings'][ $option->option_name ] = maybe_unserialize( $option->option_value );
	}

	wp_send_json_success( $export_data );
}

/////////////////////////////////////////////////////////////////////////////////
// AJAX Handler: Import Settings
/////////////////////////////////////////////////////////////////////////////////

add_action( 'wp_ajax_yak_import_settings', 'yak_ajax_import_settings' );
function yak_ajax_import_settings() {
	// Security check
	check_ajax_referer( 'yak_import_settings', 'nonce' );
	
	if ( ! current_user_can( YAK_CAP_THEME_SETTINGS ) ) {
		wp_send_json_error( 'Insufficient permissions' );
	}

	$json_data = isset( $_POST['json_data'] ) ? stripslashes( $_POST['json_data'] ) : '';
	
	if ( empty( $json_data ) ) {
		wp_send_json_error( 'No data provided' );
	}

	$max_import_bytes = (int) apply_filters( 'yak_import_json_max_bytes', 5 * MB_IN_BYTES );
	if ( strlen( $json_data ) > $max_import_bytes ) {
		wp_send_json_error( 'JSON payload exceeds maximum allowed size.' );
	}

	// Decode JSON
	$data = json_decode( $json_data, true );
	
	if ( json_last_error() !== JSON_ERROR_NONE ) {
		wp_send_json_error( 'Invalid JSON: ' . json_last_error_msg() );
	}

	// Validate structure
	if ( ! isset( $data['settings'] ) || ! is_array( $data['settings'] ) ) {
		wp_send_json_error( 'Invalid data structure. This does not appear to be a valid Yak settings export.' );
	}

	$warnings = array();
	$imported_count = 0;
	$denied_names   = array_fill_keys( yak_import_get_denied_option_names(), true );

	// Import each setting
	foreach ( $data['settings'] as $option_name => $option_value ) {
		// Skip if not an ACF option
		if ( strpos( $option_name, 'options_' ) !== 0 ) {
			continue;
		}

		if ( preg_match( '/_(site_)?transient(_timeout)?_/i', $option_name ) ) {
			$warnings[] = 'Skipped transient option: ' . $option_name;
			continue;
		}

		if ( isset( $denied_names[ $option_name ] ) ) {
			$warnings[] = 'Skipped restricted option: ' . $option_name;
			continue;
		}

		// Check for image fields that might be missing
		// Only check actual image field names, not settings that contain these words
		$is_image_field = is_numeric( $option_value ) && (
			$option_name === 'options_yak_logo_image' ||
			$option_name === 'options_yak_favicon' ||
			strpos( $option_name, '_image' ) !== false ||
			strpos( $option_name, 'favicon' ) !== false
		);
		
		if ( $is_image_field ) {
			// Check if attachment exists
			if ( ! get_post( $option_value ) ) {
				$warnings[] = "Image/attachment #{$option_value} not found for setting: {$option_name}";
				continue; // Skip this setting
			}
		}

		// Update the option
		update_option( $option_name, $option_value );
		$imported_count++;
	}

	wp_send_json_success( array(
		'imported_count' => $imported_count,
		'warnings' => $warnings
	) );
}

