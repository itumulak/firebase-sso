<?php
namespace Itumulak\WpSsoFirebase\Frontend;

use Itumulak\WpSsoFirebase\Models\Admin;
use Itumulak\WpSsoFirebase\Models\Firebase_EmailPass_Auth;
use Itumulak\WpSsoFirebase\Models\Frontend;
use WP_User;

class Controller {
	const LOGIN_STYLE_HANDLE = 'firebase_login';
	private Frontend $frontend_model;
	private Admin $admin_model;
	private Firebase_EmailPass_Auth $email_auth;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->frontend_model = new Frontend();
		$this->admin_model    = new Admin();
		$this->email_auth     = new Firebase_EmailPass_Auth();
	}

	/**
	 * Initialized function.
	 * Hooks/Filter are added here.
	 * Register hooks and filters that modify wp-login.php.
	 *
	 * @since 2.0.0
	 */
	public function init() : void {
		add_action( 'login_enqueue_scripts', array( $this, 'scripts' ) );
		add_filter( 'login_message', array( $this, 'signin_auth_buttons' ) );
		add_filter( 'wp_login_errors', array( $this, 'modify_incorrect_password' ), 10, 2 );
		add_filter( 'authenticate', array( $this, 'email_pass_auth' ), 10, 3 );
	}

	/**
	 * Register Frontend Scripts
	 *
	 * @use Hook/Action
	 * @since 1.0.0
	 */
	public function scripts() : void {
		wp_enqueue_style( 'firebase_login', $this->frontend_model->get_plugin_url() . 'src/Frontend/assets/styles/login.css', array(), $this->frontend_model->get_version() );
	}

	/**
	 * Add Single-on buttons in the login form.
	 *
	 * @use Hook/Filter
	 *
	 * @param string $message
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function signin_auth_buttons( $message ): string {
		$config  = $this->admin_model->get_providers();
		$pattern = '<p class="btn-wrapper"><button id="wp-firebase-%1$s-sign-in" class="btn btn-lg btn-%1$s btn-block text-uppercase" type="submit"><img height="%3$d" src="' . $this->frontend_model->get_plugin_url() . 'src/Frontend/assets/images/%1$s-logo.svg' . '" /> %2$s</button></p>'; // phpcs:ignore

		if ( $config['google']['is_active'] ) {
			$message .= wp_sprintf( $pattern, 'google', __( 'Sign In with Google' ), 18 );
		}

		if ( $config['facebook']['is_active'] ) {
			$message .= wp_sprintf( $pattern, 'facebook', __( 'Log in with Facebook' ), 28 );
		}

		return $message;
	}

	/**
	 * Modify incorrect password text error would display.
	 * Added max attempts to the error text to be inline with Firebase sign-in max attemps.
	 *
	 * @use Hook/Filter
	 *
	 * @param $errors
	 * @param $redirect_to
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function modify_incorrect_password( $errors, $redirect_to ) : mixed {
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

	public function email_pass_firebase_auth( $user, $email_address, $password  ) : false|WP_User {
		return $user;
	}
}
