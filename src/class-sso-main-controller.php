<?php

namespace IT\SSO\Firebase;

use IT\SSO\Firebase\SSO_Authentication as Auth;
use WP_User;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class SSO_Main_Controller extends Auth {

	const USER_SIGNIN_TYPE    = 'wp_firebase_signin';
	const SIGNIN_REFRESHTOKEN = 'wp_firebase_refresh_token';
	const SIGNIN_OAUTH        = 'wp_firebase_oauth';
	const SIGNIN_EMAILPASS    = 'emailpass';
	const SIGNIN_GOOGLE       = 'google';
	const SIGNIN_FACEBOOK     = 'facebook';

	/**
	 * Frontend constructor.
	 *
	 * Register AJAX request handling of Firebase Providers.
	 * Hook single sign-on buttons in the login form.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		parent::__construct();

		/** Email Sign-in */
		add_action( 'login_enqueue_scripts', array( $this, 'scripts' ) );
		add_filter( 'login_message', array( $this, 'signin_auth_buttons' ) );
		/**  */

		/** Google */
		add_action( 'wp_ajax_firebase_google_login', array( $this, 'google_auth_ajax' ) );
		add_action( 'wp_ajax_nopriv_firebase_google_login', array( $this, 'google_auth_ajax' ) );
		/**  */

		/** Facebook */
		add_action( 'wp_ajax_firebase_facebook_login', array( $this, 'facebook_auth_ajax' ) );
		add_action( 'wp_ajax_nopriv_firebase_facebook_login', array( $this, 'facebook_auth_ajax' ) );
		/**  */

		/** Firebase error handling */
		add_action( 'wp_ajax_firebase_handle_error', array( $this, 'firebase_auth_error_ajax' ) );
		add_action( 'wp_ajax_nopriv_firebase_handle_error', array( $this, 'firebase_auth_error_ajax' ) );
		/**  */

		/** Login */
		add_filter( 'authenticate', array( $this, 'email_pass_auth' ), 10, 3 );
		add_filter( 'wp_login_errors', array( $this, 'modify_incorrect_password' ), 10, 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'wp_logout', array( $this, 'set_cookie_logout' ) );
		/**  */

		/** Sign-Up */
		add_action( 'register_post', array( $this, 'verify_email_registration_to_firebase' ), 10, 3 );
		add_filter( 'wp_pre_insert_user_data', array( $this, 'register_email_to_firebase' ), 10, 3 );
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
		wp_register_script( self::JS_FIREBASE, 'https://www.gstatic.com/firebasejs/7.15.0/firebase-app.js', array(), '7.15.0', true );
		wp_register_script( self::JS_FIREBASE_AUTH, 'https://www.gstatic.com/firebasejs/7.15.0/firebase-auth.js', array( self::JS_FIREBASE ), '7.15.0', true );
		/**  */

		/** Main */
		wp_enqueue_script( self::JS_MAIN, plugin_dir_url( __DIR__ ) . 'js/main.js', array( 'jquery', self::JS_FIREBASE_AUTH ), '', 'true' );
		wp_localize_script( self::JS_MAIN, 'wp_firebase', SSO_Admin::get_config() );
		wp_localize_script( self::JS_MAIN, 'firebase_ajaxurl', (array) admin_url( 'admin-ajax.php' ) );

		wp_enqueue_style( 'firebase_login', plugin_dir_url( __DIR__ ) . 'styles/login.css', array(), '' );
		/**  */

	}

	/**
	 * Email/Pass Authentication callback.
	 *
	 * @use Hook/Filter
	 *
	 * @param $user
	 * @param $email_address
	 * @param $password
	 *
	 * @return false|WP_User
	 * @since 1.0.0
	 */
	public function email_pass_auth( $user, $email_address, $password ) {
		if ( $email_address && is_email( $email_address ) && ! email_exists( $email_address ) ) { // Firebase only accepts email address to auth
			$auth      = new Auth();
			$user_info = $auth->signInWithEmailAndPassword( $email_address, $password );

			if ( ! isset( $user_info['error'] ) ) {
				$user = self::auth_user( $user_info['email'], $password );
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
		$oauth_token    = sanitize_key( $_REQUEST['oauth_token'] );
		$refresh_token  = sanitize_key( $_REQUEST['refresh_token'] );
		$sanitize_email = sanitize_email( $_REQUEST['email'] );

		if ( $sanitize_email ) {
			$user = self::auth_user( $sanitize_email );

			if ( ! is_wp_error( $user ) ) {
				self::signin_usermeta( $user->ID, self::SIGNIN_GOOGLE, $refresh_token, $oauth_token );
				$login_user_url = self::login_user( $user->ID );

				wp_send_json_success( array( 'url' => $login_user_url ) );
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
		$oauth_token   = sanitize_key( $_REQUEST['oauth_token'] );
		$refresh_token = sanitize_key( $_REQUEST['refresh_token'] );
		$user_email    = sanitize_email( $_REQUEST['email'] );

		if ( $user_email ) {
			$user = self::auth_user( $user_email );

			if ( ! is_wp_error( $user ) ) {
				self::signin_usermeta( $user->ID, self::SIGNIN_FACEBOOK, $refresh_token, $oauth_token );
				$redlogin_user_url = self::login_user( $user->ID );

				wp_send_json_success( array( 'url' => $redlogin_user_url ) );
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
		$error_code = sanitize_key( $_REQUEST['code'] );

		if ( $error_code === 'auth/account-exists-with-different-credential' ) {
			wp_send_json_success( array( 'message' => 'Account already in use.' ) );
		} elseif ( $error_code === 'auth/network-request-failed' ) {
			wp_send_json_success( array( 'message' => 'Sign-in failed. Please try again.' ) );
		} else {
			wp_send_json_error();
		}
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
	public static function signin_auth_buttons( $message ): string {
		$config = SSO_Admin::get_providers();

		if ( in_array( 'google', $config, true ) ) {
			$message .= '<p class="btn-wrapper"><button id="wp-firebase-google-sign-in" class="btn btn-lg btn-google btn-block text-uppercase" type="submit"><i class="fab fa-google mr-2"></i> Sign in with Google</button></p>';
		}

		if ( in_array( 'facebook', $config, true ) ) {
			$message .= '<p class="btn-wrapper"><button id="wp-firebase-facebook-sign-in" class="btn btn-lg btn-facebook btn-block text-uppercase" type="submit"><i class="fab fa-facebook-f mr-2"></i> Sign in with Facebook</button></p>';
		}

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

			foreach ( $tmp['incorrect_password'] as $index => $msg ) {
				$tmp['incorrect_password'][ $index ] = __( '<strong>Error</strong>: The password you entered is incorrect or too many attempts.' );
			}

			$errors->errors = $tmp;

			unset( $tmp );
		}

		return $errors;
	}

	/**
	 * Check whether user already exists, otherwise create user in WordPress.
	 *
	 * @param $email
	 * @param null $password
	 *
	 * @return false|WP_User
	 * @since 1.0.0
	 */
	public static function auth_user( $email, $password = null ) {
		$user_id = email_exists( $email );

		if ( ! $user_id ) {
			// Login exist in Firebase but no wp credentials
			// Let's create a new user
			$user_id = wp_insert_user(
				array(
					'user_email' => $email,
					'user_login' => explode( '@', $email )[0],
					'user_pass'  => $password,
				)
			);
		}

		return get_user_by( 'id', $user_id );
	}

	/**
	 * Set authentication cookies.
	 *
	 * @param $user_id
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private static function login_user( $user_id ) {
		wp_clear_auth_cookie();
		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id );

		// TODO: get redirect url after login
		return get_admin_url();
	}

	/**
	 * Save User Meta upon logged in.
	 *
	 * @param $user_id
	 * @param $sign_in_type
	 * @param null $refresh_token
	 * @param null $oauth_token
	 *
	 * @since 1.0.0
	 */
	private static function signin_usermeta( $user_id, $sign_in_type, $refresh_token = null, $oauth_token = null ) {
		$sign_in_types = get_user_meta( $user_id, self::USER_SIGNIN_TYPE, false );

		if ( $sign_in_type ) {
			if ( ! in_array( $sign_in_type, $sign_in_types, true ) ) {
				$sign_in_types[] = $sign_in_type;
				update_user_meta( $user_id, self::USER_SIGNIN_TYPE, $sign_in_types );

				if ( $sign_in_type === self::SIGNIN_GOOGLE || $sign_in_type === self::SIGNIN_FACEBOOK ) {
					update_user_meta( $user_id, self::SIGNIN_OAUTH, $oauth_token );
				}
			}

			update_user_meta( $user_id, self::SIGNIN_REFRESHTOKEN, $refresh_token );
		}
	}

	/**
	 * Set cookie logout.
	 *
	 * @use Hook/Action
	 * @since 1.0.0
	 */
	public static function set_cookie_logout() {
		setcookie( self::COOKIE_LOGOUT, 1, time() + 3600, COOKIEPATH, COOKIE_DOMAIN );
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
		$auth     = new Auth();
		$response = $auth->fetchProvidersForEmail( $user_email );

		if ( $response['registered'] ) {
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
	 * @return false
	 * @since 1.1.0
	 */
	public static function register_email_to_firebase( $data, $update, $id ) {
		$auth     = new Auth();
		$response = $auth->createUserWithEmailAndPassword( $data['user_email'], $data['user_pass'] );

		if ( $response ) {
			return $data;
		}

		return false;
	}
}

new namespace\SSO_Main_Controller();
