<?php
/**
 * Plugin Name: Single Sign-On with Firebase
 * Plugin URI:
 * Description: Utilize Firebase to sign-in to your website.
 * Version: 1.0.0
 * Author: Ian Tumulak
 * Author URI: https://itumulak.com
 * License: GPLv2 or later
 * Text Domain: sso-firebase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

require_once 'src/class-sso-default.php';
require_once 'src/class-sso-authentication.php';
require_once 'src/class-sso-admin.php';
require_once 'src/class-sso-main-controller.php';