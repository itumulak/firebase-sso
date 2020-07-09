<?php
namespace Firebase;
use Kreait\Firebase\Factory;
use function Sodium\add;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WP_Firebase_Main extends WP_Firebase_Auth {

	const USER_SIGNIN_TYPE = 'wp_firebase_signin';
	const SIGNIN_REFRESHTOKEN = 'wp_firebase_refresh_token';
	const SIGNIN_OAUTH = 'wp_firebase_oauth';
	const SIGNIN_EMAILPASS = 'emailpass';
	const SIGNIN_GOOGLE = 'google';

	function __construct() {
		/** Email Sign-in */
		add_action( 'login_enqueue_scripts', [ $this, 'scripts' ] );
		add_filter('login_message', [$this, 'signin_auth_buttons']);
		/**  */

		/** Google */
		add_action( 'wp_ajax_firebase_google_login', [ $this, 'google_auth_ajax' ] );
		add_action( 'wp_ajax_nopriv_firebase_google_login', [ $this, 'google_auth_ajax' ] );
		/**  */

		/** General */
		add_filter( 'authenticate', [$this, 'email_pass_auth'], 10, 3 );
		add_filter( 'wp_login_errors', [$this, 'modify_incorrect_password'], 10, 2);
		add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ] );
		add_action( 'wp_logout', [$this, 'set_cookie_logout'] );
		/**  */
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
		if ( $emailAddress && is_email( $emailAddress ) && ! email_exists( $emailAddress ) ) { // Firebase only accepts email address to auth
			$auth = new WP_Firebase_Auth();
			$userInfo = $auth->signInWithEmailAndPassword( $emailAddress, $password );

			if ( ! isset( $userInfo['error'] ) )
			{
				$user = self::auth_user( $userInfo['email'], $password );
				self::signin_usermeta( $user->ID, self::SIGNIN_EMAILPASS );
			}
		}

		return $user;
	}

	public function google_auth_ajax() {
		$oAuthToken = $_REQUEST['oauth_token'];
		$refreshToken = $_REQUEST['refresh_token'];
		$userEmail = $_REQUEST['email'];

		if ( $userEmail ) {
			$user = self::auth_user( $userEmail );

			if ( ! is_wp_error( $user )) {
				self::signin_usermeta( $user->ID, self::SIGNIN_GOOGLE, $refreshToken, $oAuthToken );
				$redirectUrl = self::login_user( $user->ID );

				wp_send_json_success( [ 'url' => $redirectUrl ] );
			}
		}

		wp_send_json_error();
	}

	public static function signin_auth_buttons( $message ) {
		return $message .
		       '<p class="btn-wrapper"><button id="wp-firebase-google-sign-in" class="btn btn-lg btn-google btn-block text-uppercase" type="submit"><i class="fab fa-google mr-2"></i> Sign in with Google</button></p>
			    <p class="btn-wrapper"><button id="wp-firebase-facebook-sign-in" class="btn btn-lg btn-facebook btn-block text-uppercase" type="submit"><i class="fab fa-facebook-f mr-2"></i> Sign in with Facebook</button></p>';
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

		// TODO: get redirect url after login
		return get_admin_url();
	}

	private static function signin_usermeta( $userId, $signInType, $refreshToken = null, $oAuthToken = null ) {
		$signInTypes = get_user_meta( $userId, self::USER_SIGNIN_TYPE, false );

		if ( $signInType ) {
			if ( ! in_array( $signInType, $signInTypes ) ) {
				$signInTypes[] = $signInType;
				update_user_meta( $userId, self::USER_SIGNIN_TYPE, $signInTypes );

				if ( $signInType == self::SIGNIN_GOOGLE ) {
					update_user_meta( $userId, self::SIGNIN_OAUTH, $oAuthToken );
				}
			}

			update_user_meta( $userId, self::SIGNIN_REFRESHTOKEN, $refreshToken );
		}
	}

	public static function set_cookie_logout() {
		setcookie( self::cookieLogout, 1, time() + 3600, COOKIEPATH, COOKIE_DOMAIN );
	}

	public static function delete_cookie() {

	}
}

new namespace\WP_Firebase_Main();
