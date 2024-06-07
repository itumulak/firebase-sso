<?php
/**
 * Frontend class controller.
 *
 * @package firebase-sso
 */

namespace Itumulak\WpSsoFirebase\Controller;

use Itumulak\WpSsoFirebase\Models\Admin_Model;
use Itumulak\WpSsoFirebase\Models\Frontend_Model;
use Itumulak\WpSsoFirebase\Models\Providers_Model;
use Itumulak\WpSsoFirebase\Models\Scripts_Model;
use WP_Error;

/**
 * Frontend_Controller
 */
class Frontend_Controller extends Base_Controller {
	const FIREBASE_GOOGLE_AJAX_HOOK   = 'firebase_google_login';
	const FIREBASE_FACEBOOK_AJAX_HOOK = 'firebase_facebook_login';

	/**
	 * Holds the frontend model class.
	 *
	 * @var Frontend_Model
	 */
	private Frontend_Model $frontend_model;

	/**
	 * Holds the admin model class.
	 *
	 * @var Admin_Model
	 */
	private Admin_Model $admin_model;

	/**
	 * Holds the scripts model class.
	 *
	 * @var Scripts_Model;
	 */
	private Scripts_Model $js;

	/**
	 * Holds the providers model class.
	 *
	 * @var Providers_Model
	 */
	private Providers_Model $provider_model;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->js             = new Scripts_Model();
		$this->frontend_model = new Frontend_Model();
		$this->admin_model    = new Admin_Model();
		$this->provider_model = new Providers_Model();
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

		add_action( 'wp_ajax_' . $this->frontend_model::FIREBASE_LOGIN_HANDLE, array( $this, 'firebase_login_callback' ) );
		add_action( 'wp_ajax_nopriv_' . $this->frontend_model::FIREBASE_LOGIN_HANDLE, array( $this, 'firebase_login_callback' ) );
		add_action( 'wp_ajax_' . $this->frontend_model::FIREBASE_RELOG_HANDLE, array( $this, 'firebase_relogin_callback' ), 10 );
		add_action( 'wp_ajax_nopriv_' . $this->frontend_model::FIREBASE_RELOG_HANDLE, array( $this, 'firebase_relogin_callback' ), 10 );
	}

	/**
	 * Register Frontend Scripts
	 *
	 * @use Hook/Action
	 * @since 1.0.0
	 */
	public function scripts() : void {
		wp_enqueue_script( 'toast', $this->admin_model->get_plugin_url() . 'lib/toast/jquery.toast.min.js', array( 'jquery' ), '1.0.0', 'true' );
		wp_enqueue_style( 'toast', $this->admin_model->get_plugin_url() . 'lib/toast/jquery.toast.min.css', array(), '1.0.0' );
		wp_enqueue_style( $this->frontend_model::FIREBASE_LOGIN_HANDLE, $this->frontend_model->get_asset_path_url() . 'styles/login.css', array(), $this->frontend_model->get_version() );

		$this->js->register(
			$this->frontend_model::FIREBASE_LOGIN_HANDLE,
			$this->frontend_model->get_asset_path_url() . 'js/authentication.js',
			array('toast'),
			array(
				'is_module' => true,
			)
		);

		$this->js->register_localization(
			$this->frontend_model::FIREBASE_LOGIN_HANDLE,
			$this->frontend_model::FIREBASE_OBJECT,
			$this->frontend_model->get_object_data()
		);

		$this->js->enqueue_all();
	}

	/**
	 * Add Single-on buttons in the login form.
	 *
	 * @use Hook/Filter
	 *
	 * @param string $message Holds the HTML message output. We will append our provider buttons here.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function signin_auth_buttons( string $message ): string {
		$config = $this->admin_model->get_providers();

		if ( $config['google']['is_active'] ) {
			$message .= $this->frontend_model->get_template(
				'Frontend',
				'provider-design-1',
				array(
					'provider_key'   => 'google',
					'label'          => __( 'Sign In with Google', 'firebase-sso' ),
					'img_size'       => 18,
					'frontend_model' => $this->frontend_model,
				)
			);
		}

		if ( $config['facebook']['is_active'] ) {
			$message .= $this->frontend_model->get_template(
				'Frontend',
				'provider-design-1',
				array(
					'provider_key'   => 'facebook',
					'label'          => __( 'Log in with Facebook', 'firebase-sso' ),
					'img_size'       => 28,
					'frontend_model' => $this->frontend_model,
				)
			);
		}

		return $message;
	}

	/**
	 * Modify incorrect password text error would display.
	 * Added max attempts to the error text to be inline with Firebase sign-in max attemps.
	 *
	 * @use Hook/Filter
	 *
	 * @param WP_Error $errors We will append our wp errors here.
	 * @param string   $redirect_to // phpcs:ignore.
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function modify_incorrect_password( // phpcs:ignore.
		WP_Error $errors,
		string $redirect_to
	) : mixed {
		if ( isset( $errors->errors['incorrect_password'] ) ) {
			$tmp = $errors->errors;

			foreach ( $tmp['incorrect_password'] as $index => $msg ) {
				$tmp['incorrect_password'][ $index ] = __( '<strong>Error</strong>: The password you entered is incorrect or too many attempts.', 'firebase-sso' );
			}

			$errors->errors = $tmp;

			unset( $tmp );
		}

		return $errors;
	}

	/**
	 * Return the Firebase configs AJAX callback.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function get_firebase_config_callback() : void {
		wp_send_json_success(
			array(
				'config'    => $this->admin_model->get_config(),
				'providers' => $this->frontend_model->get_enabled_providers(),
			)
		);

		wp_die();
	}

	/**
	 * AJAX callback for logging in with firebase provider.
	 *
	 * @return void
	 */
	public function firebase_login_callback() : void {
		$post = wp_unslash( $_POST );

		if (
			! isset( $post['provider'] ) ||
			! isset( $post['uid'] ) ||
			! isset( $post['nonce'] ) ||
			! wp_verify_nonce( $post['nonce'], $this->frontend_model::AJAX_NONCE )
		) {
			wp_die();
		}

		$provider = esc_attr( $post['provider'] );
		$uid      = esc_attr( $post['uid'] );

		$prosessed_user = $this->frontend_model->process_user( $uid, $provider );

		if ( is_bool( $prosessed_user ) && true === $prosessed_user ) {
			wp_send_json_success(
				array(
					'login' => true,
					'meta'  => $this->provider_model->save_provider_meta( get_current_user_id(), $uid, $provider ),
					'url'   => get_home_url(),
				)
			);
		} elseif ( is_wp_error( $prosessed_user ) ) {
			wp_send_json_error(
				array(
					'error_messages' => $prosessed_user->get_error_messages(),
					'error_codes'    => $prosessed_user->get_error_codes(),
				)
			);
		}

		wp_die();
	}
}
