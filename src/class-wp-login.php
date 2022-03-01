<?php

namespace IT\SSO\Firebase;

use IT\SSO\Firebase\Email_Password_Auth as Firebase_Auth;
use IT\SSO\Firebase\Admin as Admin;
use IT\SSO\Firebase\Base as Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * WP login class.
 *
 * @since 2.0.0
 */
class WP_Login extends Base {
	public Firebase_Auth $firebase_auth;
	public WP_Auth $wp_auth;
	public Admin $admin;

	/**
	 * WP login constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->firebase_auth = new Firebase_Auth();
		$this->wp_auth       = new WP_Auth();
		$this->admin         = new Admin();
	}

	/**
	 * Initialized function.
	 * Hooks/Filter are added here.
	 * Register hooks and filters that modify wp-login.php.
	 *
	 * @since 2.0.0
	 */
	public function init() {
		add_action( 'login_enqueue_scripts', array( $this, 'scripts' ) );
		add_filter( 'login_message', array( $this, 'signin_auth_buttons' ) );

		add_filter( 'authenticate', array( $this, 'email_pass_auth' ), 10, 3 );
		add_filter( 'wp_login_errors', array( $this, 'modify_incorrect_password' ), 10, 2 );
		add_action( 'wp_logout', array( $this, 'set_cookie_logout' ) );

		add_action( 'register_post', array( $this, 'verify_email_exist_from_firebase' ), 10, 3 );
		add_filter( 'wp_pre_insert_user_data', array( $this, 'register_email_to_firebase' ), 10, 3 );

		add_action( 'wp_ajax_firebase_config', array( $this, 'get_firebase_config_ajax' ) );
		add_action( 'wp_ajax_nopriv_firebase_config', array( $this, 'get_firebase_config_ajax' ) );
	}

	/**
	 * Register Frontend Scripts
	 *
	 * @use Hook/Action
	 * @since 1.0.0
	 */
	public function scripts() {
		wp_enqueue_script( self::JS_ADMIN, self::get_plugin_url() . 'dist/sso-fb.js', array(), (string) time(), 'true' );
		wp_localize_script( self::JS_ADMIN, 'firebase_ajaxurl', (array) admin_url( 'admin-ajax.php' ) );
		wp_localize_script( self::JS_ADMIN, 'sso_firebase_nonce', wp_create_nonce( self::AJAX_NONCE ) );
		wp_enqueue_style( 'firebase_login', self::get_plugin_url() . 'dist/login.css', array(), '' );
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
	public function signin_auth_buttons( $message ): string {
		$config = $this->admin->get_providers();

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
	 * Set cookie logout.
	 *
	 * @use Hook/Action
	 * @since 1.0.0
	 */
	public function set_cookie_logout() {
		setcookie( self::COOKIE_LOGOUT, 1, time() + 3600, COOKIEPATH, COOKIE_DOMAIN );
	}

	/**
	 * Set cookie deletion
	 *
	 * @since 1.0.0
	 */
	public function delete_cookie() {
		// TODO
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
			$user_info = $this->firebase_auth->signin_from_email_password( $email_address, $password );

			if ( ! isset( $user_info['error'] ) ) {
				$user = $this->wp_auth->auth_user( $user_info['email'], $password );
				$this->wp_auth->signin_usermeta( $user->ID, self::SIGNIN_EMAILPASS );
			}
		}

		return $user;
	}

	/**
	 * Verify if email already exists in Firebase when signing up.
	 *
	 * @use Hook/Action
	 *
	 * @param $user_login
	 * @param $user_email
	 * @param $errors
	 *
	 * @since 1.1.0
	 */
	public function verify_email_exist_from_firebase( $user_login, $user_email, $errors ) {
		$response = $this->firebase_auth->get_providers_from_email( $user_email, '' );

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
	public function register_email_to_firebase( $data, $update, $id ) {
		$response = $this->firebase_auth->signup_from_email_password( $data['user_email'], $data['user_pass'] );

		if ( $response ) {
			return $data;
		}

		return false;
	}

	/**
	 * Set authentication cookies.
	 *
	 * @param $user_id
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function login_user( $user_id ) {
		wp_clear_auth_cookie();
		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id );

		// TODO: get redirect url after login
		return get_admin_url();
	}

	/**
	 * Return the Firebase configs AJAX callback.
	 *
	 * @since 2.0.0
	 * @return false|mixed|void
	 */
	public function get_firebase_config_ajax() {
		wp_send_json_success( array( 'config' => $this->admin->get_config() ) );
	}
}

$wp_login = new namespace\WP_Login();
$wp_login->init();
