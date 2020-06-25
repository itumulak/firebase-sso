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

error_reporting( E_ALL );
ini_set( "display_errors", "On" );

require_once 'src/class.default.php';
require_once 'src/class.authentication.controller.php';
require_once 'src/class.admin.controller.php';
require_once 'src/class.main.controller.php';

//if ( $_REQUEST['testing'] === 'true' ) {
//	$request = new WP_Firebase_Auth();
//
//	echo '<pre>';
//	print_r($request->signInWithEmailAndPassword('test@mail.com', 'ian052887!!'));
//	echo '</pre>';
//
//	wp_die();
//}