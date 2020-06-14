<?php
namespace Firebase;
use Kreait\Firebase\Factory;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WP_Firebase_Main extends WP_Firebase_Auth {

	function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ] );
		add_action( 'wp_ajax_firebase_login', [ $this, 'ajax_handle_verification' ] );
		add_action( 'wp_ajax_nopriv_firebase_login', [ $this, 'ajax_handle_verification' ] );
		add_action( 'wp_ajax_firebase_error', [ $this, 'ajax_handle_error' ] );
		add_action( 'wp_ajax_nopriv_firebase_error', [ $this, 'ajax_handle_error' ] );
		
		add_filter( 'authenticate', [$this, 'email_pass_auth'], 10, 3 );
		add_filter( '$shake_error_codes', 'error_codes', 10);
		
//		add_action( 'init', function () {
//			if ( $_REQUEST['testing'] ) {
//
//				error_reporting( E_ALL );
//				ini_set( "display_errors", "On" );
//
//				$firebase = new WP_Firebase_Auth();
//				$request = $firebase->signInWithEmailAndPassword('edden87@gmail.com', 'sdaaassfsdf');
//
//				echo '<pre>';
//				print_r($request);
//				echo '</pre>';
//
//				echo '<pre>';
//				print_r(wp_get_current_user()->data->user_email);
//				echo '</pre>';
//
//				echo '<pre>';
//				print_r( new \WP_Error()->get_error_messages('invalid_email'));
//				echo '</pre>';
//
//				wp_die();
//			}
//		} );
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

	public function email_pass_auth( $user, $emailAddress, $password ) {

		if ( $emailAddress ) {
			$auth = new WP_Firebase_Auth();
			$userInfo = $auth->signInWithEmailAndPassword( $emailAddress, $password );

			if ( ! isset( $userInfo['error'] ) )
			{
				$userId = email_exists( $userInfo['email'] );

				if ( ! $userId ) {
					// Login exist in Firebase but no wp credentials
					// Let's create a new user
					$userId = wp_insert_user(
						[
							'user_email' => $userInfo['email'],
							'user_login' => explode( '@', $userInfo['email'] )[0],
							'user_pass' => $password
						]
					);
				}

				$user = get_user_by( 'id', $userId );
			}
		}

		return $user;
	}

	public function shake_error_codes( $error_codes ) {
		$error_codes[] = 'firebase_error';

		return $error_codes;
	}

	public function wp_firebase_error_mapping() {
		$error_codes = [
			'EMAIL_NOT_FOUND' => 'invalid_email',
			'INVALID_EMAIL' => 'invalid_email',
			'INVALID_PASSWORD' => 'incorrect_password',
			'MISSING_PASSWORD' => 'empty_password',
			'TOO_MANY_ATTEMPTS_TRY_LATER' => 'too_many_password_attempt'
		];

		return $error_codes;
	}

	public function ajax_handle_verification() {
		$response = $_REQUEST;

		if ( $response ) {
			$output = self::handle_verification( $response['user'] );

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
			$output = self::handle_error( $response );
			wp_send_json_error( $output );
		}
	}

	public function login( $userId ) {

	}
}

new namespace\WP_Firebase_Main();
