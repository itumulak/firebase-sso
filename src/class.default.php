<?php
namespace Firebase;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WP_Firebase
{
	/**
	 * Default Firebase keys.
	 */
	const MENU_SLUG = 'wp-firebase';
	const JS_MAIN = 'wp_firebase_main';
	const JS_ADMIN = 'wp_firebase_admin';
	const JS_FIREBASE = 'firebase';
	const JS_FIREBASE_AUTH = 'firebase_authentication';
	const OPTION_KEY_CONFIG = 'wp_firebase_config';
	const OPTION_KEY_PROVIDERS = 'wp_firebase_signin_providers';
}