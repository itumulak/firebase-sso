<?php

namespace IT\SSO\Firebase;

use IT\SSO\Firebase\Admin_Config as Admin_Config;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Admin class.
 *
 * @since 1.0.0
 */
class Admin extends Admin_Config {
	/**
	 * Initialized functions.
	 * Hooks/Filter are added here.
	 *
	 * @since 2.0.0
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
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
			wp_enqueue_script( 'toast', self::get_plugin_url() . 'lib/toast/jquery.toast.min.js', array( 'jquery' ), '', 'true' );
			/**  */

			/** Admin main */
			wp_enqueue_style( self::JS_ADMIN, self::get_plugin_url() . 'dist/admin.css', array(), (string) time() );
			wp_enqueue_script( self::JS_ADMIN, self::get_plugin_url() . 'dist/sso-fb-admin.js', array( 'toast', 'jquery' ), (string) time(), 'true' );
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
							<?php $enabledProviders = $this->get_providers(); ?>
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
}

$admin = new namespace\Admin();
$admin->init();
