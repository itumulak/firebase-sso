<?php
namespace Itumulak\WpSsoFirebase\Controller;

use Itumulak\WpSsoFirebase\Models\Admin_Model;
use Itumulak\WpSsoFirebase\Models\Firebase_EmailPass_Auth;
use Itumulak\WpSsoFirebase\Models\Frontend_Model;
use WP_User;

class Frontend_Controller {
    const FIREBASE_GOOGLE_AJAX_HOOK   = 'firebase_google_login';
	const FIREBASE_FACEBOOK_AJAX_HOOK = 'firebase_facebook_login';
	private Frontend_Model $frontend_model;
	private Admin_Model $admin_model;
	private Firebase_EmailPass_Auth $email_auth;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->frontend_model = new Frontend_Model();
		$this->admin_model    = new Admin_Model();
		$this->email_auth     = new Firebase_EmailPass_Auth();
	}

	/**
	 * Initialized function.
	 * Hooks/Filter are added here.
	 * Register hooks and filters that modify wp-login.php.
	 *
	 * @since 1.0.0
	 */
	public function init() : void {
		add_action( 'login_enqueue_scripts', array( $this, 'scripts' ) );
		add_filter( 'login_message', array( $this, 'signin_auth_buttons' ) );
		add_filter( 'wp_login_errors', array( $this, 'modify_incorrect_password' ), 10, 2 );
		add_filter( 'script_loader_tag', array($this, 'add_module_attribute'), 10, 3);
		// add_filter( 'authenticate', array( $this, 'email_pass_auth' ), 10, 3 );

		// add_action( 'wp_ajax_' . self::FIREBASE_AJAX_HANDLE, array( $this, 'get_firebase_config_callback' ), 10);
		// add_action( 'wp_ajax_nopriv_' . self::FIREBASE_AJAX_HANDLE, array( $this, 'get_firebase_config_callback' ), 10);
	}

	/**
	 * Register Frontend Scripts
	 *
	 * @use Hook/Action
	 * @since 1.0.0
	 */
	public function scripts() : void {
		wp_enqueue_style( $this->frontend_model::FIREBASE_HANDLE, $this->frontend_model->get_asset_path_url() . 'styles/login.css', array(), $this->frontend_model->get_version() );
		wp_enqueue_script( $this->frontend_model::FIREBASE_HANDLE, $this->frontend_model->get_asset_path_url() . 'js/firebase-auth.js', array(), $this->frontend_model->get_version(), true );
		wp_localize_script( $this->frontend_model::FIREBASE_HANDLE, $this->frontend_model::FIREBASE_OBJECT, $this->frontend_model->get_object_data() );

		// foreach ( $this->frontend_model->get_enabled_providers() as $provider_name ) {
		// 	wp_enqueue_script( 'provider_' . $provider_name, $this->frontend_model->get_asset_path_url() . 'js/' . $provider_name . '-firebase-auth.js', array('firebase_login'), $this->frontend_model->get_version(), true );
		// }
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

		if ( $config['google']['is_active'] ) {
			$message .= $this->frontend_model->get_template( 'Frontend', 'provider-design-1', array('provider_key' => 'google', 'label' => __( 'Sign In with Google' ), 'img_size' => 18, 'frontend_model' => $this->frontend_model) );
		}

		if ( $config['facebook']['is_active'] ) {
			$message .= $this->frontend_model->get_template( 'Frontend', 'provider-design-1', array('provider_key' => 'facebook', 'label' => __( 'Log in with Facebook' ), 'img_size' => 28, 'frontend_model' => $this->frontend_model) );
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

	public function email_pass_firebase_auth( $user, $email_address, $password ) : false|WP_User {
		return $user;
	}

	public function add_module_attribute($tag, $handle, $src) {
		if ( $this->frontend_model::FIREBASE_HANDLE === $handle ) {
			$tag = '<script type="module" src=" '. $src .' "></script>';
		}

		return $tag;
	}

	/**
	 * Return the Firebase configs AJAX callback.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function get_firebase_config_callback() : void {
		wp_send_json_success( array( 'config' => $this->admin_model->get_config(), 'providers' => $this->frontend_model->get_enabled_providers() ) );
		wp_die();
	}

	public function firebase_login_callback() : void {
		if (
			isset($_POST) && 
			isset($_POST['nonce']) && 
			$this->frontend_model->verify_nonce( $_POST['nonce'], $this->frontend_model::FIREBASE_HANDLE )
		) {
			$email = $_POST['email'];
			$provider = $_POST['provider'];
			$oauth_token = $_POST['oauth_token'];
			$refresh_token = $_POST['refresh_token'];
		}
	}

	public function handle_callback() {
		
	}
}
