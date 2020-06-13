<?php
namespace Firebase;
use Kreait\Firebase\Factory;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WP_Firebase_Main extends WP_Firebase {
	private $firebase;
	private $auth;
	private $config;

	function __construct() {
		error_reporting( E_ALL );
		ini_set( "display_errors", "On" );


		add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ] );
		add_action( 'wp_ajax_firebase_login', [ $this, 'ajax_handle_verification' ] );
		add_action( 'wp_ajax_nopriv_firebase_login', [ $this, 'ajax_handle_verification' ] );
		add_action( 'wp_ajax_firebase_error', [ $this, 'ajax_handle_error' ] );
		add_action( 'wp_ajax_nopriv_firebase_error', [ $this, 'ajax_handle_error' ] );

//		$this->config =

		if ( $this->config ) { // TODO
			$this->firebase =  (new Factory)->withServiceAccount( (object) $this->config );
			$this->auth = $this->firebase->createAuth();
		}
	}

	public function scripts() {
		/** Firebase */
		wp_register_script( self::JS_FIREBASE, 'https://www.gstatic.com/firebasejs/7.15.0/firebase-app.js', [], '7.15.0', true );
		wp_register_script( self::JS_FIREBASE_AUTH, 'https://www.gstatic.com/firebasejs/7.15.0/firebase-auth.js', [ self::JS_FIREBASE ], '7.15.0', true );
		/**  */

		/** Main */
		wp_enqueue_script( self::JS_MAIN, plugin_dir_url( __DIR__) . 'js/main.js', ['jquery', self::JS_FIREBASE_AUTH], '', 'true' );
		wp_localize_script( self::JS_MAIN, 'wp_firebase', WP_Firebase_Admin::get_config() );
		wp_localize_script( self::JS_MAIN, 'firebase_ajaxurl', admin_url( 'admin-ajax.php' ) );
		/**  */
	}

	public function ajax_handle_verification() {
		$response = $_REQUEST;

		if ( $response ) {
			$output = $this->handle_verification( $response['user'] );

			if ( $output ) {
				wp_send_json_success( $output );
			} else {
				wp_send_json_error( $output );
			}
		} else {
			wp_send_json_error();
		}
	}

	public function ajax_handle_error() {
		$response = $_REQUEST;

		if ( $response ) {
			$output = $this->handle_error( $response );
			wp_send_json_error( $output );
		}
	}

	/**
	 * Handles sign-in method
	 * @param $response
	 */
	public function handle_verification( $response ) {
		if ( $response['operationType'] ) {
			switch ( $response['operationType'] ) {
				case 'signIn':
					$response = $this->email_pass_auth( $response );
					break;
			}
		}

		return ['message' => $response['message'], 'status' => $response['code']];
	}

	/**
	 * Handles error response
	 * @param $response
	 *
	 * @return array
	 */
	public function handle_error( $response ) {
		$message = '';
		$status = 400;

		if ( $response['code'] ) {
			switch ( $response['code'] ) {
				case 'auth/user-disabled':
				case 'auth/user-not-found':
				case 'auth/wrong-password':
				case 'auth/invalid-email':
					$message = $response['message'];
					break;
				default:
					$message = 'An error has occured. Please contact admin.';
			}
		}

		return ['message' => $message, 'status' => $status];
	}

	/**
	 * Verify if email exist
	 *
	 * @param $email
	 *
	 * @return mixed
	 */
	public function verify_user( $email ) {
		return email_exists( $email );
	}

	/**
	 * Sign-in user when verifying email
	 * @param $user
	 *
	 * @return array
	 */
	private function email_pass_auth( $user ) {

		$userId = $this->verify_user( $user['email'] );

		if ( ! $userId ) {
			// Login exist in Firebase but no wp credentials
			// Let's create a new user
			$userId = wp_insert_user(
				[
					'user_email' => $user['email'],
					'user_login' => explode( '@', $user['email'] )[0]
				]
			);
		}

		// Proceed logging in...
		wp_set_auth_cookie( $userId );

		return [ 'code' => 200, 'message' => 'success' ];
	}

	private function google_auth( $response ) {

	}

	private function facebook_auth( $response ) {

	}
}

new namespace\WP_Firebase_Main();
