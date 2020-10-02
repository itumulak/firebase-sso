<?php
namespace IT\SSO\Firebase;
use IT\SSO\Firebase\Authentication as Auth;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Frontend extends Auth {

	const USER_SIGNIN_TYPE = 'wp_firebase_signin';
	const SIGNIN_REFRESHTOKEN = 'wp_firebase_refresh_token';
	const SIGNIN_OAUTH = 'wp_firebase_oauth';
	const SIGNIN_EMAILPASS = 'emailpass';
	const SIGNIN_GOOGLE = 'google';
	const SIGNIN_FACEBOOK = 'facebook';

	/**
	 * Frontend constructor.
	 *
	 * Register AJAX request handling of Firebase Providers.
	 * Hook single sign-on buttons in the login form.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		/** Email Sign-in */
		add_action( 'login_enqueue_scripts', [ $this, 'scripts' ] );
		add_filter('login_message', [$this, 'signin_auth_buttons']);
		/**  */

		/** Google */
		add_action( 'wp_ajax_firebase_google_login', [ $this, 'google_auth_ajax' ] );
		add_action( 'wp_ajax_nopriv_firebase_google_login', [ $this, 'google_auth_ajax' ] );
		/**  */

		/** Facebook */
		add_action( 'wp_ajax_firebase_facebook_login', [ $this, 'facebook_auth_ajax' ] );
		add_action( 'wp_ajax_nopriv_firebase_facebook_login', [ $this, 'facebook_auth_ajax' ] );
		/**  */

		/** Firebase error handling */
		add_action( 'wp_ajax_firebase_handle_error', [$this, 'firebase_auth_error_ajax'] );
		add_action( 'wp_ajax_nopriv_firebase_handle_error', [$this, 'firebase_auth_error_ajax'] );
		/**  */

		/** Login */
		add_filter( 'authenticate', [$this, 'email_pass_auth'], 10, 3 );
		add_filter( 'wp_login_errors', [$this, 'modify_incorrect_password'], 10, 2);
		add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ] );
		add_action( 'wp_logout', [$this, 'set_cookie_logout'] );
		/**  */

		/** Sign-Up */
		add_action( 'register_post', [$this, 'verify_email_registration_to_firebase'], 10, 3 );
		add_filter( 'wp_pre_insert_user_data', [$this, 'register_email_to_firebase'], 10, 3 );
		/**  */
	}

	/**
	 * Register Frontend Scripts
	 *
	 * @use Hook/Action
	 * @since 1.0.0
	 */
	public function scripts() {
		/** Firebase */
		wp_register_script( self::JS_FIREBASE, 'https://www.gstatic.com/firebasejs/7.15.0/firebase-app.js', [], '7.15.0', true );
		wp_register_script( self::JS_FIREBASE_AUTH, 'https://www.gstatic.com/firebasejs/7.15.0/firebase-auth.js', [ self::JS_FIREBASE ], '7.15.0', true );
		/**  */

		/** Main */
		wp_enqueue_script( self::JS_MAIN, plugin_dir_url( __DIR__) . 'js/main.js', ['jquery', self::JS_FIREBASE_AUTH], '', 'true' );
		wp_localize_script( self::JS_MAIN, 'wp_firebase', Admin::get_config() );
		wp_localize_script( self::JS_MAIN, 'firebase_ajaxurl', admin_url( 'admin-ajax.php' ) );

		wp_enqueue_style( 'firebase_login', plugin_dir_url( __DIR__ ) . 'styles/login.css', [], '', 'all' );
		/**  */

	}

	/**
	 * Email/Pass Authentication callback.
	 *
	 * @use Hook/Filter
	 *
	 * @param $user
	 * @param $emailAddress
	 * @param $password
	 *
	 * @return false|\WP_User
	 * @since 1.0.0
	 */
	public function email_pass_auth( $user, $emailAddress, $password ) {
		if ( $emailAddress && is_email( $emailAddress ) && ! email_exists( $emailAddress ) ) { // Firebase only accepts email address to auth
			$auth = new Auth();
			$userInfo = $auth->signInWithEmailAndPassword( $emailAddress, $password );

			if ( ! isset( $userInfo['error'] ) )
			{
				$user = self::auth_user( $userInfo['email'], $password );
				self::signin_usermeta( $user->ID, self::SIGNIN_EMAILPASS );
			}
		}

		return $user;
	}

	/**
	 * Google Authentication AJAX callback
	 *
	 * @use Hook/Action
	 * @return void $data
	 * @since  1.0.0
	 */
	public function google_auth_ajax() {
		$oAuthToken = sanitize_key( $_REQUEST['oauth_token'] );
		$refreshToken = sanitize_key( $_REQUEST['refresh_token'] );
		$userEmail = sanitize_email( $_REQUEST['email'] );

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

	/**
	 * Facebook Authentication AJAX callback
	 *
	 * @use Hook/Action
	 * @return void $data
	 * @since 1.0.0
	 */
	public function facebook_auth_ajax() {
		$oAuthToken = sanitize_key( $_REQUEST['oauth_token'] );
		$refreshToken = sanitize_key( $_REQUEST['refresh_token'] );
		$userEmail = sanitize_email( $_REQUEST['email'] );

		if ( $userEmail ) {
			$user = self::auth_user( $userEmail );

			if ( ! is_wp_error( $user )) {
				self::signin_usermeta( $user->ID, self::SIGNIN_FACEBOOK, $refreshToken, $oAuthToken );
				$redirectUrl = self::login_user( $user->ID );

				wp_send_json_success( [ 'url' => $redirectUrl ] );
			}
		}

		wp_send_json_error();
	}

	/**
	 * Firebase Authentication Error AJAX callback
	 *
	 * @use Hook/Action
	 * @return void $data
	 * @since 1.0.0
	 */
	public function firebase_auth_error_ajax() {
		$errorCode = sanitize_key( $_REQUEST['code'] );

		if ( $errorCode == 'auth/account-exists-with-different-credential' )
			wp_send_json_success( [ 'message' => 'Account already in use.' ] );
		else if ( $errorCode == 'auth/network-request-failed' )
			wp_send_json_success( [ 'message' => 'Sign-in failed. Please try again.' ] );
		else
			wp_send_json_error();
	}

	/**
	 * Add Single-on buttons in the login form.
	 *
	 * @use Hook/Action
	 *
	 * @param $message
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function signin_auth_buttons( $message ) {
		$config = Admin::get_providers();

		if (in_array('google', $config))
			$message .= '<p class="btn-wrapper"><button id="wp-firebase-google-sign-in" class="btn btn-lg btn-google btn-block text-uppercase" type="submit"><i class="fab fa-google mr-2"></i> Sign in with Google</button></p>';

		if (in_array('facebook', $config))
			$message .= '<p class="btn-wrapper"><button id="wp-firebase-facebook-sign-in" class="btn btn-lg btn-facebook btn-block text-uppercase" type="submit"><i class="fab fa-facebook-f mr-2"></i> Sign in with Facebook</button></p>';

		return $message;
	}

	/**
	 * Modify how the incorrect password would display.
	 * To be inline with firebase max attempts.
	 *
	 * @use Hook/Filter
	 *
	 * @param $errors
	 * @param $redirect_to
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
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

	/**
	 * Check whether user already exists, otherwise create user in WordPress.
	 *
	 * @param $emailAddress
	 * @param null $password
	 *
	 * @return false|\WP_User
	 * @since 1.0.0
	 */
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

	/**
	 * Set authentication cookies.
	 *
	 * @param $userId
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private static function login_user( $userId ) {
		wp_clear_auth_cookie();
		wp_set_current_user( $userId );
		wp_set_auth_cookie( $userId );

		// TODO: get redirect url after login
		return get_admin_url();
	}

	/**
	 * Save User Meta upon logged in.
	 *
	 * @param $userId
	 * @param $signInType
	 * @param null $refreshToken
	 * @param null $oAuthToken
	 *
	 * @since 1.0.0
	 */
	private static function signin_usermeta( $userId, $signInType, $refreshToken = null, $oAuthToken = null ) {
		$signInTypes = get_user_meta( $userId, self::USER_SIGNIN_TYPE, false );

		if ( $signInType ) {
			if ( ! in_array( $signInType, $signInTypes ) ) {
				$signInTypes[] = $signInType;
				update_user_meta( $userId, self::USER_SIGNIN_TYPE, $signInTypes );

				if ( $signInType == self::SIGNIN_GOOGLE || $signInType == self::SIGNIN_FACEBOOK ) {
					update_user_meta( $userId, self::SIGNIN_OAUTH, $oAuthToken );
				}
			}

			update_user_meta( $userId, self::SIGNIN_REFRESHTOKEN, $refreshToken );
		}
	}

	/**
	 * Set cookie logout.
	 *
	 * @use Hook/Action
	 * @since 1.0.0
	 */
	public static function set_cookie_logout() {
		setcookie( self::cookieLogout, 1, time() + 3600, COOKIEPATH, COOKIE_DOMAIN );
	}

	/**
	 * Set cookie deletion
	 *
	 * @since 1.0.0
	 */
	public static function delete_cookie() {
		// TODO
	}

	/**
	 * Verify if email already exists when signing up.
	 *
	 * @use Hook/Action
	 *
	 * @param $user_login
	 * @param $user_email
	 * @param $errors
	 *
	 * @since 1.1.0
	 */
	public static function verify_email_registration_to_firebase( $user_login, $user_email, $errors ) {
		$auth = new Auth();
		$response = $auth->fetchProvidersForEmail( $user_email );

		if ( $response['registered']  === true )
		{
			$errors->add( 'firebase_user_already_registered', '<strong>Error</strong>: Email Address already in use.' );
		}
	}

	/**
	 * Register email and password to Firebase before it is created.
	 *
	 * @use Hook/Filter
	 *
	 * @param $data
	 * @param $update
	 * @param $id
	 *
	 * @since 1.1.0
	 */
	public static function register_email_to_firebase( $data, $update, $id ) {
		$auth = new Auth();
		$response = $auth->createUserWithEmailAndPassword( $data['user_email'], $data['user_pass'] );

		if ( $response )
			return $data;
	}
}

new namespace\Frontend();
