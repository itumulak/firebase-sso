<?php
/**
 * Base model class.
 *
 * @package firebase-sso
 */

namespace Itumulak\WpSsoFirebase\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Base_Model
 */
class Base_Model {
	/**
	 * Default Firebase slugs for WordPress.
	 */
	const PLUGIN_VERSION       = '1.0.0';
	const MENU_SLUG            = 'wp-firebase';
	const JS_MAIN              = 'SSO_Firebase_Main';
	const JS_ADMIN_HANDLE      = 'wp_firebase_admin';
	const JS_ADMIN_OBJECT_NAME = 'sso_object';
	const JS_ADMIN_NONCE       = 'sso_admin_nonce';
	const JS_FIREBASE          = 'firebase';
	const JS_FIREBASE_AUTH     = 'firebase_authentication';
	const USER_SIGNIN_TYPE     = 'wp_firebase_signin';
	const SIGNIN_REFRESHTOKEN  = 'wp_firebase_refresh_token';
	const SIGNIN_OAUTH         = 'wp_firebase_oauth';
	const COOKIE_LOGOUT        = 'wp_firebase_logout';
	const AJAX_NONCE           = 'sso-firebase';

	/**
	 * Return the plugin's root url.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_plugin_url(): string {
		return PLUGIN_URL;
	}


	/**
	 * Return the plugin's root directory.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_plugin_dir(): string {
		return PLUGIN_DIR;
	}

	/**
	 * Get plugin's version.
	 *
	 * If plugin is on development, it will return time() to bypass the caching from enqueue versioning.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_version(): string {
		if ( $this->is_development() ) {
			return (string) time();
		}

		return self::PLUGIN_VERSION;
	}

	/**
	 *  Verify if plugin is on development mode.
	 *
	 * It will check for config.dev.php, then check the file variable 'development' set to true.
	 * The file is generated via `npm run dev` and take down on `npm run build`.
	 *
	 * @since 1.0.0
	 * @return false|mixed
	 */
	public function is_development(): mixed {
		if ( file_exists( self::get_plugin_dir() . 'config.dev.php' ) ) {
			$config_dev = include self::get_plugin_dir() . 'config.dev.php';

			return $config_dev['development'];
		}

		return false;
	}

	/**
	 * Load our admin template parts.
	 *
	 * @param string $path Path.
	 * @param string $file_name File name.
	 * @param array  $args Pass additional data as argument.
	 * @param bool   $require_once Load the file as required once or not.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function get_template( string $path, string $file_name, array $args = array(), bool $require_once = false ) {
		load_template( $this->get_plugin_dir() . "src/View/{$path}/{$file_name}.php", $require_once, $args );
	}

	/**
	 * Verify our AJAX nonce.
	 *
	 * @param string $nonce Unique nonce key.
	 * @param string $action The action handler to associate to the nonce variable.
	 * @return boolean
	 * @since 1.0.0
	 */
	public function verify_nonce( string $nonce, string $action ): bool {
		if ( ! wp_verify_nonce( $nonce, $action ) ) {
			return false;
		}

		return true;
	}
}
