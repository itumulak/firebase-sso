<?php
/**
 * Plugin Name: Single Sign-On with Firebase DEV
 * Plugin URI: https://github.com/itumulak
 * Description: Utilize Firebase to sign-in to your website.
 * Version: 1.0.0
 * Author: Ian Tumulak
 * Author URI: https://github.com/itumulak
 * License: GPLv2 or later
 * Text Domain: firebase-sso
 */

use Itumulak\WpSsoFirebase\Controller\Admin_Controller;
use Itumulak\WpSsoFirebase\Controller\UserProfileWP_Controller;
use Itumulak\WpSsoFirebase\Controller\Frontend_Controller;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

require 'vendor/autoload.php';

$admin = new Admin_Controller();
$admin->init();

$admin_user_profile =  new UserProfileWP_Controller();
$admin_user_profile->init();

$frontend = new Frontend_Controller();
$frontend->init();
