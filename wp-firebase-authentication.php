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

class WP_Firebase {
	const slug = 'wp-firebase';
	const JS_MAIN = 'wp_firebase';
	const JS_FIREBASE = 'firebase';
	const JS_FIREBASE_AUTH = 'firebase_authentication';

	function __construct() {

		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );
	}

	public function admin_menu() {
		add_menu_page( 'WP Firebase', 'WP Firebase', 'manage_options', self::slug, [ $this, 'admin_page' ], '', 9 );
	}

	public function admin_scripts() {
		if ( isset( $_GET['page'] ) && $_GET['page'] == self::slug ) {
			wp_enqueue_style( 'wp_firebase', plugin_dir_url( __FILE__ ) . 'styles/admin.css', [], '', 'all' );

			wp_register_script( self::JS_FIREBASE, 'https://www.gstatic.com/firebasejs/7.15.0/firebase-app.js', [], '7.15.0', true );
			wp_register_script( self::JS_FIREBASE_AUTH, 'https://www.gstatic.com/firebasejs/7.15.0/firebase-auth.js', [ self::JS_FIREBASE ], '7.15.0', true );
			wp_enqueue_script( self::JS_MAIN, plugin_dir_url( __FILE__ ) . 'js/admin.js', [
				'jquery',
				self::JS_FIREBASE_AUTH
			], '1.0.0', 'true' );

			$cm_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/css'));
			wp_localize_script(self::JS_MAIN, 'cm_settings', $cm_settings);

			wp_enqueue_script('wp-theme-plugin-editor');
			wp_enqueue_style('wp-codemirror');
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
                <a id="configurations" class="nav-tab" title="Configuration" href="#configurations">Configuration</a>
                <a id="sign-in-providers" class="nav-tab" title="Sign-in Providers"
                   href="#sign-in-providers">Sign-in providers</a>
            </h2>
            <div class="tabs-holder">
                <div id="sign-in-providers-tab" class="group">
                    <div id="sign-in-providers-list">
                        <table class="form-table">
                            <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="email-password">Email/Password</label>
                                </th>
                                <td>
                                    <input type="checkbox" id="email-password" name="sign-in-providers[emailpassword]">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="facebook">Facebook</label>
                                </th>
                                <td>
                                    <input type="checkbox" id="facebook" name="sign-in-providers[facebook]">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="google">Google</label>
                                </th>
                                <td>
                                    <input type="checkbox" id="google" name="sign-in-providers[google]">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <p>
                            <button type="submit" class="button button-primary">Save</button>
                        </p>
                    </div>
                </div>
                <div id="configurations-tab" class="group">
                    <div id="config-textarea-wrapper">
                        <textarea id="fancy-textarea">
var firebaseConfig = {
  apiKey: "api-key",
  authDomain: "project-id.firebaseapp.com",
  databaseURL: "https://project-id.firebaseio.com",
  projectId: "project-id",
  storageBucket: "project-id.appspot.com",
  messagingSenderId: "sender-id",
  appId: "app-id",
  measurementId: "G-measurement-id",
};
                        </textarea>
                    </div>
                    <div id="config-description-wrapper">
                        Get, copy, and paste your <a href="https://firebase.google.com/docs/web/setup?authuser=0#config-object">Firebase config object</a> found at your project settings.
                    </div>
                </div>
            </div>
        </div>
		<?php
	}
}

new namespace\WP_Firebase();

