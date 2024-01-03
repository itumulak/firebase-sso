<?php
namespace Itumulak\WpSsoFirebase\Models;

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
			if ( $provider !== $this->providers::PROVIDER_EMAILPASS && $is_enabled ) {
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
		return array( 'ajaxurl' => admin_url('admin-ajax.php'), 'config' => $this->configs->get_all(), 'providers' => $this->get_enabled_providers() );
	}

	/**
	 * Return the asset URL for the frontend.
	 *
	 * @return string
	 */
	public function get_asset_path_url() : string {
		return $this->get_plugin_url() . 'src/View/Frontend/assets/';
	}
}
