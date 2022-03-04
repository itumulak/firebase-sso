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
require_once 'src/class-base.php';
require_once 'src/class-wp-auth.php';
/**  */

/** Inc */
require_once 'src/inc/class-email-password-auth.php';
require_once 'src/inc/template-plugin.php';
/**  */

/** Callbacks */
require_once 'src/public/callback/class-callback-factory.php';
require_once 'src/public/callback/class-email-password.php';
require_once 'src/public/callback/class-google.php';
require_once 'src/public/callback/class-facebook.php';
/**  */

/** Admin */
require_once 'src/admin/class-admin-config.php';
require_once 'src/admin/callback/class-admin-ajax.php';
require_once 'src/admin/class-admin.php';
/**  */

/** Frontend */
require_once 'src/public/class-wp-login.php';
/**  */
