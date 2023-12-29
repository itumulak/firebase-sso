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

use Itumulak\WpSsoFirebase\Admin\Controller as AdminController;
use Itumulak\WpSsoFirebase\Frontend\Controller as FrontendController;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// /** Base */
// require_once 'src/class-base.php';
// require_once 'src/class-wp-auth.php';
// /**  */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// @todo Configure composer autoloader.
// @todo Redo the plugin structure to comply with PSR-4 autoloading.
require 'vendor/autoload.php';

$admin = new AdminController();
$admin->init();

$frontend = new FrontendController();
$frontend->init();

// /** Inc */
// require_once 'src/inc/class-email-password-auth.php';
// require_once 'src/inc/template-plugin.php';
// /**  */

// /** Callbacks */
// require_once 'src/public/callback/class-callback-factory.php';
// require_once 'src/public/callback/class-email-password.php';
// require_once 'src/public/callback/class-google.php';
// require_once 'src/public/callback/class-facebook.php';
// /**  */

// /** Admin */
// require_once 'src/admin/class-admin-config.php';
// require_once 'src/admin/callback/class-admin-ajax.php';
// require_once 'src/admin/class-admin.php';
// /**  */

// /** Frontend */
// require_once 'src/public/class-wp-login.php';
// /**  */
