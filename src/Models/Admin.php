<?php
namespace Itumulak\WpSsoFirebase\Models;

class Admin extends Factory {
	private array $configs;
	private array $providers;
	const PROVIDER_SLUG_EMAILPASS = 'emailpassword';
	const PROVIDER_SLUG_FB        = 'facebook';
	const PROVIDER_SLUG_GOOGLE    = 'google';
	const PROVIDER_ACTION         = 'provider_action';
	const CONFIG_ACTION           = 'config_action';

	/**
	 * Constructor.
	 * Initialize default data.
	 *
	 * @since 2.0.0
	 */
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
			self::PROVIDER_SLUG_EMAILPASS => array(
				'id'        => self::PROVIDER_SLUG_EMAILPASS,
				'icon'      => $this->get_plugin_url() . 'src/Admin/assets/images/mail-logo.svg',
				'label'     => 'Email/Password',
				'is_active' => false,
			),
			self::PROVIDER_SLUG_GOOGLE    => array(
				'id'        => self::PROVIDER_SLUG_GOOGLE,
				'icon'      => $this->get_plugin_url() . 'src/Admin/assets/images/google-logo.svg',
				'label'     => 'Google',
				'is_active' => false,
			),
			self::PROVIDER_SLUG_FB        => array(
				'id'        => self::PROVIDER_SLUG_FB,
				'icon'      => $this->get_plugin_url() . 'src/Admin/assets/images/facebook-logo.svg',
				'label'     => 'Facebook',
				'is_active' => false,
			),
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
		$_configs       = $this->configs;
		$current_config = $this->get_config();

		foreach ( array_keys( $this->configs ) as $key ) {
			// @todo Improve saving of configs.
			if ( $this->spoof_datum() !== $configs[ $key ] ) {
				$_configs[ $key ]['value'] = $configs[ $key ];
			} else {
				if ( $configs[ $key ] === $current_config[ $key ]['value'] ) {
					$_configs[ $key ]['value'] = $current_config[ $key ]['value'];
				} else {
					$_configs[ $key ]['value'] = $configs[ $key ];
				}
			}
		}

		return update_option( self::OPTION_KEY_CONFIG, $_configs );
	}

	/**
	 * Fetch saved Firebase Config
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function get_config(): array {
		$_configs = wp_parse_args( get_option( self::OPTION_KEY_CONFIG ), $this->configs );

		foreach ( array_keys( $this->configs ) as $key ) {
			$_configs[ $key ]['value'] = strlen( $_configs[ $key ]['value'] ) ? $this->spoof_datum() : '';
		}

		return $_configs;
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

		return update_option( self::OPTION_KEY_PROVIDERS, $enabled );
	}

	/**
	 * Fetch saved Sign-in Providers
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function get_providers(): mixed {
		$saved_providers = get_option( self::OPTION_KEY_PROVIDERS );
		$providers       = $this->providers;

		foreach ( array_keys( $saved_providers ) as $key ) {
			$providers[ $key ]['is_active'] = true;
		}

		return $providers;
	}

	/**
	 * Return an imitating "•" to prevent revealing sensitive datum.
	 *
	 * @return string
	 */
	private function spoof_datum() : string {
		return str_repeat( '•', 30 );
	}
}
