<?php
/**
 * Plugin Name: Firebase Sign-up/Sign-in Authentication
 * Plugin URI:
 * Description: Utilize Firebase to register and login to your website
 * Version: 1.0.0
 * Author: Ian Tumulak
 * Author URI: https://itumulak.com
 * License: GPLv2 or later
 * Text Domain: wp-firebase-authentication
 */

namespace Firebase;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

require_once 'src/class.default.php';
require_once 'src/class.admin.controller.php';
require_once 'src/class.main.controller.php';

