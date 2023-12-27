<?php
namespace Itumulak\WpSsoFirebase\Models;

class Admin extends Factory {
	private array $configs;
	private array $providers;
	const PROVIDER_SLUG_EMAILPASS = 'email-password';
	const PROVIDER_SLUG_FB        = 'facebook';
	const PROVIDER_SLUG_GOOGLE    = 'google';
	const PROVIDER_ACTION         = 'provider_action';
	const CONFIG_ACTION           = 'config_action';

	public function __construct() {
		$this->configs = array(
			'apiKey'     => array(
				'label' => 'API Key',
				'value' => '',
			),
			'authDomain' => array(
				'label' => 'Authorized Domain',
				'value' => '',
			),
		);

		$this->providers = array(
			self::PROVIDER_SLUG_EMAILPASS => false,
			self::PROVIDER_SLUG_FB        => false,
			self::PROVIDER_SLUG_GOOGLE    => false,
		);
	}

	/**
	 * Save Firebase Config
	 *
	 * @param array $configs
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function save_config( array $configs ): bool {
		foreach ( array_keys( $this->configs ) as $key ) {
			$this->configs[ $key ]['value'] = $configs[ $key ];
		}

		return update_option( self::OPTION_KEY_CONFIG, $this->configs );
	}

	/**
	 * Fetch saved Firebase Config
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function get_config(): array {
		return wp_parse_args( get_option( self::OPTION_KEY_CONFIG ), $this->configs );
	}

	/**
	 * Save Firebase Sign-in Providers
	 *
	 * @param array $providers
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function save_providers( array $providers ): bool {
		foreach ( $providers as $provider ) {
			$enabled[ $provider ] = true;
		}

		return update_option( self::OPTION_KEY_PROVIDERS, wp_parse_args( $enabled, $this->providers ) );
	}

	/**
	 * Fetch saved Sign-in Providers
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function get_providers(): mixed {
		return get_option( self::OPTION_KEY_PROVIDERS );
	}

	/**
	 * Save Firebase Config.
	 * Ajax request callback.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function save_config_callback() : void {
		if ( ! $this->verify_nonce( $_REQUEST['nonce'], self::CONFIG_ACTION ) ) {
			$this->handle_callback( false );
		}

		$this->handle_callback( $this->save_config( $_REQUEST ) );
	}

	/**
	 * Save Firebase Sign-in Providers
	 * Ajax request callback
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function save_providers_callback() : void {
		if ( ! $this->verify_nonce( $_POST['nonce'], self::PROVIDER_ACTION ) ) {
			$this->handle_callback( false );
		}

		$providers = array_map( 'sanitize_key', $_REQUEST['enabled_providers'] );
		$this->handle_callback( $this->save_providers( $providers ) );
	}

	/**
	 * Handle WordPress callbacks.
	 *
	 * @param Function|bool $callback
	 * @return void
	 * @since 2.0.0
	 */
	private function handle_callback( $callback ) : void {
		if ( $callback ) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}

		wp_die();
	}
}
