<?php
/**
 * Plugin Name: Firebase Sign-up/Sign-in Authentication
 * Plugin URI:
 * Description: Utilize Firebase to register and login to your website
 * Version: 1.0.0
 * Author: Ian Tumulak
 * Author URI: https://itumulak.com
 * License: GPLv2 or later
 * Text Domain: wp-firebase-authentication
 */

namespace Firebase;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WP_Firebase_Admin {
	const MENU_SLUG = 'wp-firebase';
	const JS_MAIN = 'wp_firebase';
	const JS_FIREBASE = 'firebase';
	const JS_FIREBASE_AUTH = 'firebase_authentication';
	const OPTION_KEY_CONFIG = 'wp_firebase_config';
	const OPTION_KEY_PROVIDERS = 'wp_firebase_signin_providers';

	function __construct() {

		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );
		add_action( 'wp_ajax_firebase_config', [ $this, 'ajax_save_config' ] );
		add_action( 'wp_ajax_firebase_providers', [ $this, 'ajax_save_providers' ] );
	}

	public function admin_menu() {
		add_menu_page( 'WP Firebase', 'WP Firebase', 'manage_options', self::MENU_SLUG, [
			$this,
			'admin_page'
		], '', 9 );
	}

	public function admin_scripts() {
		if ( isset( $_GET['page'] ) && $_GET['page'] == self::MENU_SLUG ) {
			wp_enqueue_style( 'wp_firebase', plugin_dir_url( __FILE__ ) . 'styles/admin.css', [], '', 'all' );

			/** Firebase */
			wp_register_script( self::JS_FIREBASE, 'https://www.gstatic.com/firebasejs/7.15.0/firebase-app.js', [], '7.15.0', true );
			wp_register_script( self::JS_FIREBASE_AUTH, 'https://www.gstatic.com/firebasejs/7.15.0/firebase-auth.js', [ self::JS_FIREBASE ], '7.15.0', true );
			/**  */

            /** Toast */
			wp_enqueue_script( 'toast', plugin_dir_url( __FILE__ ) . 'js/jquery.toast.min.js', ['jquery'], '', 'true' );
			wp_enqueue_style( 'toast', plugin_dir_url(__FILE__) . 'styles/jquery.toast.min.css', [], '', 'all' );
			/**  */

			/** Admin main */
			wp_enqueue_script( self::JS_MAIN, plugin_dir_url( __FILE__ ) . 'js/admin.js', [
				'jquery',
				'toast',
				self::JS_FIREBASE_AUTH
			], '1.0.0', 'true' );
			/**  */

            /** Codemirror */
			$cm_settings['codeEditor'] = wp_enqueue_code_editor( [ 'type' => 'text/javascript' ] );
			wp_localize_script( self::JS_MAIN, 'cm_settings', $cm_settings );

			wp_enqueue_script( 'wp-theme-plugin-editor' );
			wp_enqueue_style( 'wp-codemirror' );
			/**  */
		}
	}

	public function admin_page() {
		?>
        <!-- Our admin page content should all be inside .wrap -->
        <div class="wrap">
            <!-- Print the page title -->
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <!-- Here are our tabs -->
            <h2 class="nav-tab-wrapper hide-if-js" style="display: block;">
                <a id="configurations" class="nav-tab" title="Configuration"
                   href="#configurations">Configuration</a>
                <a id="sign-in-providers" class="nav-tab" title="Sign-in Providers"
                   href="#sign-in-providers">Sign-in providers</a>
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
                                        <label for="email-password">Email/Password</label>
                                    </th>
                                    <td>
                                        <input type="checkbox" id="email-password"
                                               name="sign-in-providers[emailpassword]"
	                                        <?= (in_array('email-password', $enabledProviders) ? 'checked' : '') ?>
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="facebook">Facebook</label>
                                    </th>
                                    <td>
                                        <input type="checkbox" id="facebook" name="sign-in-providers[facebook]"
	                                        <?= (in_array('facebook', $enabledProviders) ? 'checked' : '') ?>
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="google">Google</label>
                                    </th>
                                    <td>
                                        <input type="checkbox" id="google" name="sign-in-providers[google]"
	                                        <?= (in_array('google', $enabledProviders) ? 'checked' : '')?>
                                        >
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <p>
                                <button type="submit" class="button button-primary">Save</button>
                            </p>
                        </form>
                    </div>
                </div>
                <div id="configurations-tab" class="group">
                    <div id="config-textarea-wrapper">
                        <form id="configuration-code">
                            <textarea id="configuration-textarea">
                                <?php if ( $this->get_config() ) :
	                                echo esc_textarea( $this->get_config() );
                                else : ?>
                                    var firebaseConfig = {
                                    apiKey: "api-key",
                                    authDomain: "project-id.firebaseapp.com",
                                    databaseURL: "https://project-id.firebaseio.com",
                                    projectId: "project-id",
                                    storageBucket: "project-id.appspot.com",
                                    messagingSenderId: "sender-id",
                                    appId: "app-id",
                                    measurementId: "G-measurement-id"
                                    };
                                <?php endif; ?>
                            </textarea>
                            <p>
                                <button type="submit" class="button button-primary">Save Config</button>
                            </p>
                        </form>
                    </div>
                    <div id="config-description-wrapper">
                        Get a copy, and paste your <a
                                href="https://firebase.google.com/docs/web/setup?authuser=0#config-object">Firebase
                            config object</a> found at your project settings.
                    </div>
                </div>
            </div>
        </div>
		<?php
	}

	public function ajax_save_config() {
		$config = $_REQUEST['config'];

		if ( $config ) {
			$this->save_config( $config );
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}

	private function save_config( $config ) {
		update_option( self::OPTION_KEY_CONFIG, sanitize_textarea_field( $config ) );
	}

	public function get_config() {
		return stripslashes( get_option( self::OPTION_KEY_CONFIG ) );
	}

	public function ajax_save_providers() {
		$providers = $_REQUEST['enabled_providers'];

		if ( $providers ) {
			$this->save_providers( $providers );
			wp_send_json_success();

		} else {
			wp_send_json_error();
		}
	}

	private function save_providers( $providers ) {
		update_option( self::OPTION_KEY_PROVIDERS, $providers );
	}

	public function get_providers() {
		return get_option( self::OPTION_KEY_PROVIDERS );
	}
}

new namespace\WP_Firebase_Admin();

