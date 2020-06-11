<?php
namespace Firebase;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WP_Firebase_Main extends WP_Firebase {
	function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ] );
	}

	public function scripts() {
		/** Firebase */
		wp_register_script( self::JS_FIREBASE, 'https://www.gstatic.com/firebasejs/7.15.0/firebase-app.js', [], '7.15.0', true );
		wp_register_script( self::JS_FIREBASE_AUTH, 'https://www.gstatic.com/firebasejs/7.15.0/firebase-auth.js', [ self::JS_FIREBASE ], '7.15.0', true );
		/**  */

		/** Main */
		wp_enqueue_script( self::JS_MAIN, plugin_dir_url( __DIR__) . 'js/main.js', ['jquery', self::JS_FIREBASE_AUTH], '', 'true' );
		wp_localize_script( self::JS_MAIN, 'wp_firebase', WP_Firebase_Admin::get_config() );
		/**  */
	}

	public function handle_verification( $response ) {
		$response = json_decode( $response, true );
		$message = '';

		if ( $response['code'] ) {
			switch ( $response['code'] ) {
				case 'auth/user-disabled':
				case 'auth/user-not-found':
				case 'auth/wrong-password':
				case 'auth/invalid-email':
					$message = $response['message'];
				default:
					$message = 'An error has occured. Please contact admin.';
			}
		}
		else if ( $response['operationType'] ) {
			switch ( $response['operationType'] ) {
				case 'signIn':
					$this->email_pass_auth( $response['user'] );
			}
		}
	}

	public function verify_user( $email ) {
		return email_exists( $email );
	}

	private function email_pass_auth( $user ) {
		if ( $this->verify_user( $user['email'] ) )
		{
			// Proceed logging in...
		}
		else {
			// Login exist in Firebase but no wp credentials
			// Let's create a new user
		}
	}

	private function google_auth( $response ) {

	}

	private function facebook_auth( $response ) {

	}
}

new namespace\WP_Firebase_Main();
