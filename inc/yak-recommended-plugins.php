<?php

/**
 * Yak Recommended Plugins Page
 * Defines rendering and helper functions only
 */

if ( ! defined( 'ABSPATH' ) ) exit;



// Output callback for your existing submenu registration
function yak_render_plugins_page() {
	?>
	<div class="wrap">
		<h1>Yak Recommended Plugins</h1>
		<p>Install and activate plugins useful for your theme workflow.</p>
		<ul>
			<?php foreach ( yak_get_recommended_plugins() as $plugin ) : ?>
				<li>
					<strong><?php echo esc_html( $plugin['name'] ); ?></strong> —
					<em><?php echo esc_html( $plugin['description'] ); ?></em><br>
					<?php echo yak_plugin_install_button( $plugin ); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
}





function yak_get_recommended_plugins() {
	return [
		[
			'name'        => 'Advanced Custom Fields',
			'slug'        => 'advanced-custom-fields',
			'description' => 'Flexible fields and layouts.',
		],
		[
			'name'        => 'Safe SVG',
			'slug'        => 'safe-svg',
			'description' => 'Securely upload SVG files.',
		],
		[
			'name'        => 'GitHub Plugin Example',
			'slug'        => 'github-plugin-example',
			'description' => 'Example plugin hosted on GitHub.',
			'github_url'  => 'https://github.com/example/example-plugin/archive/refs/heads/main.zip',
		],
	];
}

function yak_plugin_install_button( $plugin ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';

	$slug = $plugin['slug'];
	$installed = false;
	$path = '';

	foreach ( get_plugins() as $plugin_path => $details ) {
		if ( strpos( $plugin_path, $slug ) !== false ) {
			$installed = true;
			$path = $plugin_path;
			break;
		}
	}

	if ( $installed ) {
		if ( is_plugin_active( $path ) ) {
			return '<span style="color:green;">Installed & Active</span>';
		} else {
			$activate_url = wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . $path ), 'activate-plugin_' . $path );
			return '<a href="' . esc_url( $activate_url ) . '" class="button button-secondary">Activate</a>';
		}
	}

	if ( ! empty( $plugin['github_url'] ) ) {
		$install_url = wp_nonce_url(
			admin_url( 'admin-post.php?action=yak_install_github_plugin&github_url=' . urlencode( $plugin['github_url'] ) ),
			'yak_install_github_plugin'
		);
		return '<a href="' . esc_url( $install_url ) . '" class="button button-primary">Install from GitHub</a>';
	}

	return '<a href="' . esc_url(
		wp_nonce_url(
			self_admin_url( 'update.php?action=install-plugin&plugin=' . $slug ),
			'install-plugin_' . $slug
		)
	) . '" class="button button-primary">Install</a>';
}

function yak_handle_github_plugin_install() {
	if ( ! current_user_can( 'install_plugins' ) || ! check_admin_referer( 'yak_install_github_plugin' ) ) {
		wp_die( 'Not allowed.' );
	}

	$zip_url = esc_url_raw( $_GET['github_url'] );

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/misc.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/plugin-install.php';

	WP_Filesystem();
	$tmp = download_url( $zip_url );

	if ( is_wp_error( $tmp ) ) {
		wp_die( 'Failed to download ZIP.' );
	}

	$upgrader = new Plugin_Upgrader();
	$result   = $upgrader->install( $tmp );

	if ( is_wp_error( $result ) || ! $result ) {
		wp_die( 'Install failed.' );
	}

	wp_safe_redirect( admin_url( 'admin.php?page=yak-theme-plugins&installed=1' ) );
	exit;
}

