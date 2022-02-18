<?php

namespace IT\SSO\Firebase;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class SSO_Default {
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
}