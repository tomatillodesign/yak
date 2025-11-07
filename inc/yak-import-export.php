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

/////////////////////////////////////////////////////////////////////////////////
// Add Tabs to Performance & Tools Page
/////////////////////////////////////////////////////////////////////////////////

add_action( 'admin_head', 'yak_import_export_styles' );
function yak_import_export_styles() {
	// Check if we're on the performance page
	if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'yak-options-performance' ) {
		return;
	}
	?>
	<style>
		.yak-tools-tabs {
			margin: 20px 0 0 0;
			border-bottom: 1px solid #ccc;
			padding-left: 0;
		}
		.yak-tools-tabs li {
			display: inline-block;
			margin: 0;
			padding: 0;
		}
		.yak-tools-tabs a {
			display: inline-block;
			padding: 10px 20px;
			text-decoration: none;
			border: 1px solid transparent;
			border-bottom: none;
			background: #f0f0f1;
			color: #2271b1;
			margin-right: 5px;
		}
		.yak-tools-tabs a:hover {
			background: #fff;
		}
		.yak-tools-tabs a.active {
			background: #fff;
			border-color: #ccc #ccc #fff;
			color: #000;
			font-weight: 600;
		}
		.yak-tools-tab-content {
			display: none;
			padding: 20px 0;
		}
		.yak-tools-tab-content.active {
			display: block;
		}
		.yak-import-export-section {
			background: #fff;
			border: 1px solid #ccd0d4;
			padding: 20px;
			max-width: 800px;
		}
		.yak-json-textarea {
			width: 100%;
			min-height: 300px;
			font-family: monospace;
			font-size: 12px;
		}
		.yak-button-group {
			margin-top: 15px;
		}
		.yak-notice {
			padding: 12px;
			margin: 15px 0;
			border-left: 4px solid;
		}
		.yak-notice-success {
			background: #edfaed;
			border-color: #00a32a;
		}
		.yak-notice-error {
			background: #fcf0f1;
			border-color: #d63638;
		}
		.yak-notice-warning {
			background: #fcf9e8;
			border-color: #dba617;
		}
	</style>
	<?php
}

add_action( 'admin_footer', 'yak_import_export_scripts' );
function yak_import_export_scripts() {
	// Check if we're on the performance page
	if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'yak-options-performance' ) {
		return;
	}
	?>
	<script>
	jQuery(document).ready(function($) {
		var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
		
		console.log('Yak Import/Export: Script loaded');
		console.log('Export button:', $('#yak-export-btn').length);
		console.log('Import button:', $('#yak-import-btn').length);
		
		// Tab switching
		$('.yak-tools-tabs a').on('click', function(e) {
			e.preventDefault();
			var target = $(this).data('tab');
			
			// Update active states
			$('.yak-tools-tabs a').removeClass('active');
			$(this).addClass('active');
			
			$('.yak-tools-tab-content').removeClass('active');
			$('#' + target).addClass('active');
		});

		// Export button
		$('#yak-export-btn').on('click', function(e) {
			e.preventDefault();
			console.log('Export button clicked!');
			console.log('AJAX URL:', ajaxurl);
			
			$('#yak-export-notice').html('<div class="yak-notice yak-notice-success">⏳ Exporting settings...</div>');
			
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'yak_export_settings',
					nonce: '<?php echo wp_create_nonce( 'yak_export_settings' ); ?>'
				},
				success: function(response) {
					console.log('Export AJAX response:', response);
					if (response.success) {
						$('#yak-export-output').val(JSON.stringify(response.data, null, 2));
						$('#yak-export-notice').html('<div class="yak-notice yak-notice-success">✓ Settings exported successfully! Copy the JSON below.</div>');
					} else {
						$('#yak-export-notice').html('<div class="yak-notice yak-notice-error">✗ Export failed: ' + (response.data || 'Unknown error') + '</div>');
					}
				},
				error: function(xhr, status, error) {
					console.log('Export AJAX error:', xhr, status, error);
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
							message += '<strong>⚠️ Warnings:</strong><ul style="margin: 10px 0 0 20px;">';
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
				$('#yak-copy-btn').text('Copy to Clipboard').prop('disabled', false);
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
					<div class="yak-import-export-section">
						<h2>📦 Export Settings</h2>
						<p>Export all Yak theme settings to JSON. You can save this as a backup or import it on another Yak theme installation.</p>
						
						<div class="yak-button-group">
							<button type="button" id="yak-export-btn" class="button button-primary">Export All Settings</button>
						</div>
						
						<div id="yak-export-notice"></div>
						
						<div style="margin-top: 15px;">
							<textarea id="yak-export-output" class="yak-json-textarea" placeholder="Exported JSON will appear here..." readonly></textarea>
						</div>
						
						<div class="yak-button-group">
							<button type="button" id="yak-copy-btn" class="button">Copy to Clipboard</button>
							<button type="button" id="yak-download-btn" class="button">Download JSON File</button>
						</div>
					</div>

					<hr style="margin: 40px 0; border: none; border-top: 1px solid #ddd;">

					<div class="yak-import-export-section">
						<h2>📥 Import Settings</h2>
						<p><strong>⚠️ Warning:</strong> This will <strong>OVERWRITE</strong> all existing theme settings. Export your current settings first as backup.</p>
						
						<div style="margin-top: 15px;">
							<textarea id="yak-import-input" class="yak-json-textarea" placeholder="Paste exported JSON here..."></textarea>
						</div>
						
						<div class="yak-button-group">
							<button type="button" id="yak-import-btn" class="button button-primary">Import Settings</button>
						</div>
						
						<div id="yak-import-notice"></div>
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
	
	if ( ! current_user_can( 'manage_options' ) ) {
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
	
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Insufficient permissions' );
	}

	$json_data = isset( $_POST['json_data'] ) ? stripslashes( $_POST['json_data'] ) : '';
	
	if ( empty( $json_data ) ) {
		wp_send_json_error( 'No data provided' );
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

	// Import each setting
	foreach ( $data['settings'] as $option_name => $option_value ) {
		// Skip if not an ACF option
		if ( strpos( $option_name, 'options_' ) !== 0 ) {
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

