<?php

namespace IT\SSO\Firebase;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Base {
	/**
	 * Default Firebase slugs for WordPress.
	 */
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
}
