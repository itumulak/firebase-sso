<?php
namespace Firebase;
use Kreait\Firebase\Factory;
use function Sodium\add;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WP_Firebase_Main extends WP_Firebase_Auth {



	function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ] );

		/** Email Sign-in */
		add_action( 'login_enqueue_scripts', [ $this, 'scripts' ] );
		add_filter('login_message', [$this, 'signin_auth_buttons']);
		/**  */

		/** Google */
		add_action( 'wp_ajax_firebase_google_login', [ $this, 'google_auth_ajax' ] );
		add_action( 'wp_ajax_nopriv_firebase_google_login', [ $this, 'google_auth_ajax' ] );
		/**  */

		add_action( 'wp_logout', [$this, 'delete_cookie'] );

//		add_action( 'wp_ajax_firebase_login', [ $this, 'ajax_handle_verification' ] );
//		add_action( 'wp_ajax_nopriv_firebase_login', [ $this, 'ajax_handle_verification' ] );
//		add_action( 'wp_ajax_firebase_error', [ $this, 'ajax_handle_error' ] );
//		add_action( 'wp_ajax_nopriv_firebase_error', [ $this, 'ajax_handle_error' ] );
		
		add_filter( 'authenticate', [$this, 'email_pass_auth'], 10, 3 );
		add_filter( 'wp_login_errors', [$this, 'modify_incorrect_password'], 10, 2);
//		add_filter( 'shake_error_codes', [$this, 'shake_error_codes'], 10);
//		add_filter( 'wp_authenticate_user', [$this, 'auth_user'], 10, 2);
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

		wp_enqueue_style( 'firebase_login', plugin_dir_url( __DIR__ ) . 'styles/login.css', [], '', 'all' );
		/**  */

	}

	public function email_pass_auth( $user, $emailAddress, $password ) {

		if ( $emailAddress && is_email( $emailAddress ) ) { // Firebase only accepts email address to auth
			$auth = new WP_Firebase_Auth();
			$userInfo = $auth->signInWithEmailAndPassword( $emailAddress, $password );

			if ( ! isset( $userInfo['error'] ) )
			{
				$user = self::auth_user( $userInfo['email'] );
				self::set_cookie();
			}
		}

		return $user;
	}

	public function google_auth_ajax() {
		$token = $_REQUEST['google_token'];
		$userEmail = $_REQUEST['email'];

		if ( $userEmail ) {
			$user = self::auth_user( $userEmail );

			if ( ! is_wp_error( $user )) {
				$redirectUrl = self::login_user( $user->ID );

				wp_send_json_success( [ 'url' => $redirectUrl ] );
			}
		}

		wp_send_json_error();
	}

	public static function signin_auth_buttons( $message ) {
		return $message .
		       '<p><button name="wp-firebase-google-sign-in" id="wp-firebase-google-sign-in"><img src="'. plugin_dir_url( __DIR__) . 'img/btn_google_signin_dark_focus_web.png' .'" /></button></p>';
	}

	public function modify_incorrect_password( $errors, $redirect_to ) {
		if ( isset( $errors->errors['incorrect_password'] ) ) {
			$tmp = $errors->errors;

			foreach( $tmp['incorrect_password'] as $index => $msg )
			{
				$tmp['incorrect_password'][$index] = __( '<strong>Error</strong>: The password you entered is incorrect or too many attempts.' );
			}

			$errors->errors = $tmp;

			unset( $tmp );
		}

		return $errors;
	}

//	public function shake_error_codes( $error_codes ) {
//		$error_codes[] = 'too_many_password_attempt';
//
//		return $error_codes;
//	}
//
//	public function auth_user( $user, $password ) {
//		return new \WP_Error('too_many_password_attempt', __( '<strong>Error</strong>: Too many attempts.'));
//	}
//
//	public function wp_firebase_error_mapping() {
//		$error_codes = [
//			'EMAIL_NOT_FOUND' => 'invalid_email',
//			'INVALID_EMAIL' => 'invalid_email',
//			'INVALID_PASSWORD' => 'incorrect_password',
//			'MISSING_PASSWORD' => 'empty_password',
//			'TOO_MANY_ATTEMPTS_TRY_LATER' => 'too_many_password_attempt'
//		];
//
//		return $error_codes;
//	}
//
//	public function wp_firebase_error_labels() {
//		$labels = [
//			'too_many_password_attempt' => __( '<strong>Error</strong>: The password you entered is incorrect or too many attempts.')
//		];
//	}

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

	public static function auth_user( $emailAddress, $password = null ) {
		$userId = email_exists( $emailAddress );

		if ( ! $userId ) {
			// Login exist in Firebase but no wp credentials
			// Let's create a new user
			$userId = wp_insert_user(
				[
					'user_email' => $emailAddress,
					'user_login' => explode( '@', $emailAddress)[0],
					'user_pass' => $password
				]
			);
		}

		return get_user_by( 'id', $userId );
	}

	private static function login_user( $userId ) {
		wp_clear_auth_cookie();
		wp_set_current_user( $userId );
		wp_set_auth_cookie( $userId );
		self::set_cookie();

		// TODO: get redirect url after login
		return get_admin_url();
	}

	private static function set_cookie() {
		setcookie( self::COOKIE, 1, time() + 3600, COOKIEPATH, COOKIE_DOMAIN );
	}

	public static function delete_cookie() {
		setcookie( self::COOKIE, ' ', time() - YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
		setcookie( self::COOKIE, ' ', time() - YEAR_IN_SECONDS, SITECOOKIEPATH, COOKIE_DOMAIN );
	}
}

new namespace\WP_Firebase_Main();
