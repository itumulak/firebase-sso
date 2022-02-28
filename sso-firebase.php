<?php
/**
 * Plugin Name: Single Sign-On with Firebase
 * Plugin URI:
 * Description: Utilize Firebase to sign-in to your website.
 * Version: 2.0.0
 * Author: Ian Tumulak
 * Author URI: https://itumulak.com
 * License: GPLv2 or later
 * Text Domain: sso-firebase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// @todo Configure composer autoloader.

/** Base */
require_once 'src/class-default-vars.php';
require_once 'src/class-wp-auth.php';
/**  */

/** API */
require_once 'src/public/api/class-email-password-auth.php';
/**  */

/** Callbacks */
require_once 'src/public/callback/class-email-password.php';
require_once 'src/public/callback/class-google.php';
require_once 'src/public/callback/class-facebook.php';
/**  */

/** Admin */
require_once 'src/admin/callback/class-admin-ajax.php';
require_once 'src/admin/class-admin.php';
/**  */

/** Frontend */
require_once 'src/class-wp-auth.php';
require_once 'src/class-wp-login.php';
/**  */
