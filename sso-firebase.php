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

require_once 'src/class.default.php';
require_once 'src/class.authentication.controller.php';
require_once 'src/class.admin.controller.php';
require_once 'src/class-main-controller.php';