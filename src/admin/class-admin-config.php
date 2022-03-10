<?php

namespace IT\SSO\Firebase;

use IT\SSO\Firebase\Base as Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Admin_Config extends Base {
	/**
	 * Save Firebase Config
	 *
	 * @param $config
	 *
	 * @since 1.0.0
	 */
	public function save_config( $config ) {
		update_option( self::OPTION_KEY_CONFIG, $config );
	}

	/**
	 * Fetch saved Firebase Config
	 *
	 * @return false|mixed|void
	 * @since 1.0.0
	 */
	public function get_config() {
		return get_option( self::OPTION_KEY_CONFIG );
	}

	/**
	 * Save Firebase Sign-in Providers
	 *
	 * @param $providers
	 *
	 * @since 1.0.0
	 */
	public function save_providers( $providers ) {
		update_option( self::OPTION_KEY_PROVIDERS, $providers );
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
