<?php

namespace IT\SSO\Firebase;

use IT\SSO\Firebase\SSO_Default as Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class SSO_Admin extends Main {

	/**
	 * Admin constructor.
	 *
	 * Build WP Admin.
	 *
	 * @param void
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'wp_ajax_firebase_config', array( $this, 'ajax_save_config' ) );
		add_action( 'wp_ajax_firebase_providers', array( $this, 'ajax_save_providers' ) );
	}

	/**
	 * Register Admin Menu
	 *
	 * @use Hook/Action
	 * @since 1.0.0
	 */
	public function admin_menu() {
		add_menu_page( 'WP Firebase', 'WP Firebase', 'manage_options', self::MENU_SLUG, array(
			$this,
			'admin_page'
		), '', 9 );
	}

	/**
	 * Register Admin Scripts
	 *
	 * @use Hook/Action
	 * @since 1.0.0
	 */
	public function admin_scripts() {
		if ( isset( $_GET['page'] ) && $_GET['page'] === self::MENU_SLUG ) {
			/** Toast */
			wp_enqueue_script( 'toast', plugin_dir_url( __DIR__ ) . 'dist/jquery.toast.min.js', array( 'jquery' ), '', 'true' );
			wp_enqueue_style( 'toast', plugin_dir_url( __DIR__ ) . 'dist/jquery.toast.min.css', array(), '' );
			/**  */

			/** Admin main */
			wp_enqueue_style( self::JS_ADMIN, plugin_dir_url( __DIR__ ) . 'dist/admin.css', array(), '' );
			wp_enqueue_script( self::JS_ADMIN, plugin_dir_url( __DIR__ ) . 'dist/admin.js', array(
				'jquery',
				'toast'
			), '1.0.0', 'true' );
			/**  */
		}
	}

	/**
	 * Render Admin Page
	 *
	 * @use Hook/Action
	 * @since 1.0.0
	 */
	public function admin_page() {
		?>
        <!-- Our admin page content should all be inside .wrap -->
        <div class="wrap">
            <!-- Print the page title -->
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <!-- Here are our tabs -->
            <h2 class="nav-tab-wrapper hide-if-js" style="display: block;">
                <a class="nav-tab" href="#configurations" id="configurations"
                   title="<?php esc_attr( 'Configuration' ) ?>"><?php _e( 'Configuration' ) ?></a>
                <a class="nav-tab" href="#sign-in-providers" id="sign-in-providers"
                   title="<?php esc_attr( 'Sign-in Providers' ) ?>"><?php _e( 'Sign-in providers' ) ?></a>
            </h2>
            <div class="tabs-holder">
                <div id="sign-in-providers-tab" class="group">
                    <div id="sign-in-providers-list">
                        <form id="sign-in-providers-form">
							<?php $enabledProviders = self::get_providers(); ?>
                            <table class="form-table">
                                <tbody>
                                <tr>
                                    <th scope="row">
                                        <label for="email-password"><?php _e( 'Email/Password' ) ?></label>
                                    </th>
                                    <td>
                                        <input type="checkbox" id="email-password"
                                               name="sign-in-providers[emailpassword]" <?= ( in_array( 'email-password', $enabledProviders, true ) ? 'checked' : '' ) ?>>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="facebook"><?php _e( 'Facebook' ) ?></label>
                                    </th>
                                    <td>
                                        <input type="checkbox" id="facebook"
                                               name="sign-in-providers[facebook]" <?= ( in_array( 'facebook', $enabledProviders, true ) ? 'checked' : '' ) ?>>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="google"><?php _e( 'Google' ) ?></label>
                                    </th>
                                    <td>
                                        <input id="google" name="sign-in-providers[google]"
                                               type="checkbox" <?= ( in_array( 'google', $enabledProviders, true ) ? 'checked' : '' ) ?>>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <p>
                                <button type="submit" class="button button-primary"><?php _e( 'Save' ); ?></button>
                            </p>
                        </form>
                    </div>
                </div>
                <div id="configurations-tab" class="group">
                    <div id="config-textarea-wrapper">
                        <form id="configuration-fields">
                            <h1><?php _e( 'Firebase Configurations' ); ?></h1>
                            <p><?php echo wp_sprintf(
									'%s <a target="_blank" href="%s">%s</a> %s',
									__( 'Get a copy, and paste your' ),
									esc_url( 'https://firebase.google.com/docs/web/setup?authuser=0#config-object' ),
									__( 'Firebase config object' ),
									__( 'found at your project settings.' ),
								);
								?>
                            </p>
							<?php
							$config = $this->get_config();

							if ( array_key_exists( 'apiKey', $config ) ) {
								echo 'hello';
							}

							$config_fields = array(
								'apiKey'     => 'API Key',
								'authDomain' => 'Authorized Domain',
							);
							?>
                            <table class="form-table">
                                <tbody>
								<?php foreach ( $config_fields as $key => $label ) :
									$field_value = '';

									if ( array_key_exists( 'apiKey', $config ) ) {
										$field_value =  $config[ $key ];
									}
									?>
                                    <tr>
                                        <th scope="row">
                                            <label for="<?php echo esc_attr( $key ) ?>"><?php echo esc_attr( $label ) ?></label>
                                        </th>
                                        <td>
                                            <input class="regular-text" id="<?php echo esc_attr( $key ) ?>"
                                                   name="<?php echo esc_attr( $key ) ?>"
                                                   type="text" value="<?php echo esc_attr( $field_value ); ?>">
                                        </td>
                                    </tr>
								<?php endforeach; ?>

                                </tbody>
                            </table>
                            <p>
                                <button type="submit"
                                        class="button button-primary"><?php _e( 'Save Config' ) ?></button>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}

	/**
	 * Save Firebase Config
	 * Ajax request callback
	 *
	 * @use Hook/Action
	 * @return void $data
	 * @since 1.0.0
	 */
	public function ajax_save_config() {
		$config = array_map( 'sanitize_text_field', $_REQUEST );
		unset( $config['action'] );

		if ( $config ) {
			self::save_config( $config );
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Save Firebase Config
	 *
	 * @param $config
	 *
	 * @since 1.0.0
	 */
	private static function save_config( $config ) {
		update_option( self::OPTION_KEY_CONFIG, $config );
	}

	/**
	 * Fetch saved Firebase Config
	 *
	 * @return false|mixed|void
	 * @since 1.0.0
	 */
	public static function get_config() {
		return get_option( self::OPTION_KEY_CONFIG );
	}

	/**
	 * Save Firebase Sign-in Providers
	 * Ajax request callback
	 *
	 * @return void $data
	 * @since 1.0.0
	 */
	public function ajax_save_providers() {
		$providers = array_map( 'sanitize_key', $_REQUEST['enabled_providers'] );

		if ( $providers ) {
			self::save_providers( $providers );
			wp_send_json_success();

		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Save Firebase Sign-in Providers
	 *
	 * @param $providers
	 *
	 * @since 1.0.0
	 */
	private static function save_providers( $providers ) {
		update_option( self::OPTION_KEY_PROVIDERS, $providers );
	}

	/**
	 * Fetch saved Sign-in Providers
	 *
	 * @return false|mixed|void
	 * @since 1.0.0
	 */
	public static function get_providers() {
		return get_option( self::OPTION_KEY_PROVIDERS );
	}
}

new namespace\SSO_Admin();
