<?php
namespace Itumulak\WpSsoFirebase\Models;

use WP_Error;

class Frontend_Model extends Base_Model {
	private Providers_Model $provider_model;
	private Error_Model $error_model;
	private Configuration_Model $configs;
	public array $enabled_providers;
	const FIREBASE_LOGIN_HANDLE = 'firebase_login';
	const FIREBASE_RELOG_HANDLE = 'firebase_relog';
	const FIREBASE_OBJECT       = 'firebase_sso_object';

	/**
	 * WP login constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$admin_model             = new Admin_Model();
		$this->error_model       = new Error_Model();
		$this->enabled_providers = $admin_model->get_providers();
		$this->configs           = new Configuration_Model();
		$this->provider_model    = new Providers_Model();
	}

	/**
	 * Retrieve enabled provider.
	 *
	 * @return array
	 */
	public function get_enabled_providers() : array {
		$enabled_providers = array();

		foreach ( $this->provider_model->get_all() as $provider => $is_enabled ) {
			$enabled_providers[] = $provider;
		}

		return $enabled_providers;
	}

	/**
	 * Return the firebase config.
	 *
	 * @return array
	 */
	public function get_object_data() : array {
		return array(
			'ajaxurl'        => admin_url( 'admin-ajax.php' ),
			'config'         => $this->configs->get_all(),
			'providers'      => $this->get_enabled_providers(),
			'action_login'   => self::FIREBASE_LOGIN_HANDLE,
			'action_relogin' => self::FIREBASE_RELOG_HANDLE,
			'nonce'          => wp_create_nonce( self::AJAX_NONCE ),
		);
	}

	/**
	 * Return the asset URL for the frontend.
	 *
	 * @return string
	 */
	public function get_asset_path_url() : string {
		return $this->get_plugin_url() . 'src/View/Frontend/assets/';
	}

	public function process_user( string $email, string $uid, string $provider ) : bool|Error_Model {
		if ( email_exists( $email ) ) {
			if ( $this->login_user( $email ) ) {
				return true;
			} else {
				$this->error_model->add( $this->error_model::LOGIN_FAILED );
			}
		} else {
			if ( $this->provider_model->is_uid_available( $uid, $provider ) ) {
				if ( $this->create_user( $email ) ) {
					return $this->process_user( $email, $uid, $provider );
				} else {
					$this->error_model->add( $this->error_model::ACCOUNT_IN_USE );
				}
			} else {
				$this->error_model->add( $this->error_model::TOKEN_IN_USE );
			}
		}

		return $this->error_model->get_errors();
	}

	/**
	 * Login user progmatically.
	 *
	 * @param  string $email
	 * @return bool
	 */
	public function login_user( string $email ) : bool {
		$user = get_user_by( 'email', $email );

		if ( $user && ! is_wp_error( $user ) ) {
			clean_user_cache( $user->ID );
			wp_clear_auth_cookie();
			wp_set_current_user( $user->ID );
			wp_set_auth_cookie( $user->ID );
			update_user_caches( $user );

			return is_user_logged_in();
		}

		return false;
	}

	/**
	 * Create a WP account if the email is not yet registred in the website.
	 *
	 * @param  string $email
	 * @return bool
	 */
	public function create_user( string $email ) : bool {
		if ( $this->is_valid_email( $email ) ) {
			$username = $this->generate_username( array_shift( explode( '@', $email ) ) );
			$password = wp_generate_password();

			$user_id = wp_create_user( $username, $password, $email );

			if ( $user_id && ! is_wp_error( $user_id ) ) {
				return true;
			}
		}

		return false;
	}

	protected function is_valid_email( string $email ) : bool {
		if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			return false;
		}

		$email_host = array_slice( explode( '@', $email ), -1 )[0];

		if ( ! filter_var(
			$email_host,
			FILTER_VALIDATE_IP,
			array(
				'flags' => FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE,
			)
		) ) {
			$email_host = idn_to_ascii( $email_host . '.' );
			if ( ! checkdnsrr( $email_host, 'MX' ) ) {
				return false;
			}
		}

		return true;
	}

	protected function generate_username( string $suggested_username ) {
		$suggested_username = preg_replace( '/[^a-z0-9]/i', '', $suggested_username );

		if ( ! username_exists( $suggested_username ) ) {
			return $suggested_username;
		}

		$suggested_username = $suggested_username . '-' . $this->random_alphanumeric();

		return $this->generate_username( $suggested_username );
	}

	protected function random_alphanumeric( int $length = 5 ) : string {
		$chars     = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ12345689';
		$my_string = '';

		for ( $i = 0; $i < $length; $i++ ) {
			$pos        = random_int( 0, strlen( $chars ) - 1 );
			$my_string .= substr( $chars, $pos, 1 );
		}
		return $my_string;
	}
}
