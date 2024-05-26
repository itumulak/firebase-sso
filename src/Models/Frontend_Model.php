<?php
namespace Itumulak\WpSsoFirebase\Models;

use WP_User;

class Frontend_Model extends Base_Model {
	private Providers_Model $providers;
	private Configuration_Model $configs;
	public array $enabled_providers;
	const FIREBASE_HANDLE = 'firebase_login';
	const FIREBASE_OBJECT = 'firebase_sso_object';
	
	/**
	 * WP login constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$admin_model             = new Admin_Model();
		$this->enabled_providers = $admin_model->get_providers();
		$this->configs = new Configuration_Model();
		$this->providers = new Providers_Model();
	}

	/**
	 * Retrieve enabled providers.
	 *
	 * @return array
	 */
	public function get_enabled_providers() : array {
		$enabled_providers = array();

		foreach ( $this->providers->get_all() as $provider => $is_enabled ) {
			if ( $provider !== PROVIDER_EMAILPASS && $is_enabled ) {
				$enabled_providers[] = $provider;
			}
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
			'ajaxurl' => admin_url('admin-ajax.php'), 
			'config' => $this->configs->get_all(), 
			'providers' => $this->get_enabled_providers(),
			'action' => self::FIREBASE_HANDLE,
			'nonce' => wp_create_nonce( self::AJAX_NONCE )
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
	
	/**
	 * Login user progmatically.
	 *
	 * @param  mixed $email
	 * @return bool
	 */
	public function login_user( string $email ) : bool {
		$user = get_user_by('email', $email);

		if ( !is_wp_error($user) ) {
			clean_user_cache($user->ID);
			wp_clear_auth_cookie();
			wp_set_current_user ( $user->ID );
			wp_set_auth_cookie  ( $user->ID );
			update_user_caches($user);
		}

		return false;
	}

	public function save_firebase_meta( string $oauth_token, string $refresh_token, string $provider ) : void {
		
	}

	public function verify_account_not_used( string $email )
	{
		
	}

	public function create_account( string $email )
	{
		
	}
}
