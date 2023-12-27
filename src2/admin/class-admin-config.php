<?php

namespace IT\SSO\Firebase;

use IT\SSO\Firebase\Base as Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// @todo Refactor this as the model of the Admin page.

class Admin_Config extends Base {
	private array $configs;
	private array $providers;
	const PROVIDER_SLUG_EMAILPASS = 'email-password';
	const PROVIDER_SLUG_FB        = 'facebook';
	const PROVIDER_SLUG_GOOGLE    = 'google';

	public function __construct() {
		$this->configs = array(
			'apiKey'     => array(
				'label' => 'API Key',
				'value' => ''
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
	 *
	 * @since 1.0.0
	 */
	public function save_config( array $configs ): bool {
		foreach (array_keys($this->configs) as $key) {
			$this->configs[$key]['value'] = $configs[$key];
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
	 * @return false|mixed|void
	 * @since 1.0.0
	 */
	public function get_providers() {
		return get_option( self::OPTION_KEY_PROVIDERS );
	}
}

new namespace\Admin_Config();
