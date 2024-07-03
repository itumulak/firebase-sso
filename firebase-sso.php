<?php
/**
 * Plugin Name: Single Sign-On with Firebase. DEV
 * Plugin URI: https://github.com/itumulak
 * Description: Utilize Firebase to sign-in to your website.
 * Version: 2.0.0
 * Author: Ian Tumulak
 * Author URI: https://www.linkedin.com/in/itumulak
 * License: GPLv2 or later
 * Text Domain: firebase-sso
 */

use Itumulak\WpSsoFirebase\Controller\Admin_Controller;
use Itumulak\WpSsoFirebase\Controller\Gutenburg_Controller;
use Itumulak\WpSsoFirebase\Controller\UserProfileWP_Controller;
use Itumulak\WpSsoFirebase\Controller\Frontend_Controller;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

define('PLUGIN_DIR', plugin_dir_path( __FILE__ ));
define('PLUGIN_URL', plugin_dir_url( __FILE__ ));

require 'vendor/autoload.php';

$admin = new Admin_Controller();
$admin->init();

$gutenburg = new Gutenburg_Controller();
$gutenburg->init();

$admin_user_profile =  new UserProfileWP_Controller();
$admin_user_profile->init();

$frontend = new Frontend_Controller();
$frontend->init();
