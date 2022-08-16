<?php
/**
 * Plugin Name: AGCDN Management
 * Plugin URI: https://pantheon.io
 * Description: Manage AGCDN Options
 * Version: 1.2
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
	if ( ! empty( pantheon_agcdn_get_api_key_constant() ) ) {
		return;
	}

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
	add_options_page(
		__( 'AGCDN Management' ),
		__( 'AGCDN Management' ),
		apply_filters( 'pantheon_agcdn_management_user_role', 'manage_options' ),
		'pantheon-agcdn-management',
		'pantheon_agcdn_view_settings_page'
	);
}
add_action( 'admin_menu', 'pantheon_agcdn_management_admin_menu' );

/**
 * Show Settings Page
 *
 * @return void
 */
function pantheon_agcdn_view_settings_page() {
	$api_key_constant = pantheon_agcdn_get_api_key_constant();
	if ( ! empty( $api_key_constant ) ) {
		$api_key = $api_key_constant;
	} else {
		$api_key = get_option( 'pantheon_agcdn_management_api_key' );
	}

	wp_enqueue_script( 'vue', 'https://cdn.jsdelivr.net/npm/vue@2.6.12', array(), '1', true );
	wp_enqueue_script( 'pantheon-agcdn-management', 'https://cdn.jsdelivr.net/gh/pantheon-systems/vue-agcdn-mgmt@0.9.5/dist/main.js', array( 'vue' ), '0.9.5', true );
	wp_localize_script(
		'pantheon-agcdn-management',
		'WP_OPTIONS',
		array(
			'nonce'                             => wp_create_nonce( 'agcdn_mgmt' ),
			'pantheon_agcdn_management_api_key' => $api_key,
		)
	);

	?>
	<div class="wrap">
		<h2>AGCDN Management Options</h2>

		<?php if ( empty( $api_key_constant ) ) : ?>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'pantheon_agcdn_management_settings' );
				do_settings_sections( 'pantheon-agcdn-management' );
				submit_button();
				?>
			</form>
		<?php endif; ?>

		<div id="app">
			<p>Loading...</p>
		</div>
	</div>
	<?php
}

/**
 * Check if API key constant is defined.
 *
 * @return string
 */
function pantheon_agcdn_get_api_key_constant() {
	if ( defined( 'PANTHEON_AGCDN_MANAGEMENT_API_KEY' ) && ! empty( PANTHEON_AGCDN_MANAGEMENT_API_KEY ) ) {
		return PANTHEON_AGCDN_MANAGEMENT_API_KEY;
	}
	return '';
}
