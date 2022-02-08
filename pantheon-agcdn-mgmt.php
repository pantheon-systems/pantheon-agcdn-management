<?php
/**
 * Plugin Name: AGCDN Management
 * Plugin URI: https://pantheon.io
 * Description: Manage AGCDN Options
 * Version: 1.0
 * Author: Pantheon
 * Author URI: https://pantheon.io
 *
 * @package AGCDN Management
 */

/**
 * Initialize Options
 *
 * @return void
 */
function pantheon_agcdn_management_init() {
	register_setting(
		'pantheon_agcdn_management_settings',
		'pantheon_agcdn_management_api_key',
		array(
			'type'              => 'string',
			'description'       => __( 'The API key provided by Pantheon' ),
			'default'           => '',
			'sanitize_callback' => 'pantheon_agcdn_management_sanitize',
		)
	);
	add_settings_field( 'pantheon_agcdn_management_api_key', __( 'API Key' ), 'pantheon_agcdn_management_api_key', 'pantheon-agcdn-management', 'pantheon_agcdn_management_settings' );

	add_settings_section( 'pantheon_agcdn_management_settings', __( 'Plugin Settings' ), null, 'pantheon-agcdn-management' );
}
add_action( 'admin_init', 'pantheon_agcdn_management_init' );

/**
 * API Key Input Field
 *
 * @return void
 */
function pantheon_agcdn_management_api_key() {
	$option = get_option( 'pantheon_agcdn_management_api_key' );
	?>
	<input type="text" size="50" id="pantheon_agcdn_management_api_key" name="pantheon_agcdn_management_api_key" value="<?php echo esc_attr( $option ); ?>" />
	<p class="description"><?php echo esc_attr( __( 'The API key provided by Pantheon' ) ); ?></p>
	<?php
}

/**
 * Sanitize Input
 *
 * @param string $input The input to sanitize.
 * @return string
 */
function pantheon_agcdn_management_sanitize( $input ) {
	return $input;
}

/**
 * Create the admin menu item.
 *
 * @return void
 */
function pantheon_agcdn_management_admin_menu() {
	add_options_page( __( 'AGCDN Management' ), __( 'AGCDN Management' ), 'manage_options', 'pantheon-agcdn-management', 'view_settings_page' );
}
add_action( 'admin_menu', 'pantheon_agcdn_management_admin_menu' );

/**
 * Show Settings Page
 *
 * @return void
 */
function view_settings_page() {
	wp_enqueue_script( 'vue', 'https://cdn.jsdelivr.net/npm/vue@2.6.12', array(), '', true );
	wp_enqueue_script( 'pantheon-agcdn-management', 'https://cdn.jsdelivr.net/gh/pantheon-systems/vue-agcdn-mgmt@0.9.5/dist/main.js', array( 'vue' ), '0.9.5', true );
	wp_localize_script(
		'pantheon-agcdn-management',
		'WP_OPTIONS',
		array(
			'nonce'                             => wp_create_nonce( 'agcdn_mgmt' ),
			'pantheon_agcdn_management_api_key' => get_option( 'pantheon_agcdn_management_api_key' ),
		)
	);

	?>
	<div class="wrap">
		<h2>AGCDN Management Options</h2>
		<form action="options.php" method="post">
			<?php
				settings_fields( 'pantheon_agcdn_management_settings' );
				do_settings_sections( 'pantheon-agcdn-management' );
				submit_button();
			?>
		</form>
		<div id="app">
			<p>Loading...</p>
		</div>
	</div>
	<?php
}
