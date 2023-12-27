<?php

namespace IT\SSO\Firebase;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// @todo refactor this file as the base model.

class Base {
	/**
	 * Default Firebase slugs for WordPress.
	 */
	const PLUGIN_VERSION       = '2.0.0';
	const MENU_SLUG            = 'wp-firebase';
	const JS_MAIN              = 'SSO_Firebase_Main';
	const JS_ADMIN             = 'wp_firebase_admin';
	const JS_FIREBASE          = 'firebase';
	const JS_FIREBASE_AUTH     = 'firebase_authentication';
	const OPTION_KEY_CONFIG    = 'wp_firebase_config';
	const OPTION_KEY_PROVIDERS = 'wp_firebase_signin_providers';
	const USER_SIGNIN_TYPE     = 'wp_firebase_signin';
	const SIGNIN_REFRESHTOKEN  = 'wp_firebase_refresh_token';
	const SIGNIN_OAUTH         = 'wp_firebase_oauth';
	const SIGNIN_EMAILPASS     = 'emailpass';
	const SIGNIN_GOOGLE        = 'google';
	const SIGNIN_FACEBOOK      = 'facebook';
	const COOKIE_LOGOUT        = 'wp_firebase_logout';
	const AJAX_NONCE           = 'sso-firebase';

	/**
	 * Return the plugin's root url.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function get_plugin_url() {
		return WP_PLUGIN_URL . '/wp-sso-firebase/';
	}

	/**
	 * Return the plugin's root directory.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function get_plugin_dir() {
		return WP_PLUGIN_DIR . '/wp-sso-firebase/';
	}

	/**
	 * Get plugin's version.
	 *
	 * If plugin is on development, it will return time() to bypass the caching from enqueue versioning.
	 *
	 * @since 2.0.0
	 * @return int|string
	 */
	public function get_version() {
		if ( $this->is_development() ) {
			return time();
		}

		return self::PLUGIN_VERSION;
	}

	/**
	 *  Verify if plugin is on development mode.
	 *
	 * It will check for config.dev.php, then check the file variable 'development' set to true.
	 * The file is generated via `npm run dev` and take down on `npm run build`.
	 *
	 * @since 2.0.0
	 * @return false|mixed
	 */
	public function is_development() {
		if (file_exists( self::get_plugin_dir() . 'config.dev.php' ) ) {
			$config_dev = include self::get_plugin_dir() . 'config.dev.php';

			return $config_dev['development'];
		}

		return false;
	}
}
