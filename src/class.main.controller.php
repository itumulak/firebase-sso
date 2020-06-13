<?php
namespace Firebase;
use Kreait\Firebase\Factory;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WP_Firebase_Main extends WP_Firebase_Auth {

	function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ] );
		add_action( 'wp_ajax_firebase_login', [ $this, 'ajax_handle_verification' ] );
		add_action( 'wp_ajax_nopriv_firebase_login', [ $this, 'ajax_handle_verification' ] );
		add_action( 'wp_ajax_firebase_error', [ $this, 'ajax_handle_error' ] );
		add_action( 'wp_ajax_nopriv_firebase_error', [ $this, 'ajax_handle_error' ] );
		
		add_action( 'init', function () {
			if ( $_REQUEST['testing'] ) {
				error_reporting( E_ALL );
				ini_set( "display_errors", "On" );

				$config = WP_Firebase_Admin::get_config();

				echo '<pre>';
				print_r($config);
				echo '</pre>';

				echo '<pre>';
				print_r( json_encode($config) );
				echo '</pre>';

				$user = new WP_Firebase_Auth();

				echo '<pre>';
				print_r($user->auth->signInWithEmailAndPassword('edden87@gmail.com', 'ian052887!!')->data());
				echo '</pre>';

				echo 'hello world';

				wp_die();
			}
		} );
	}

	public function scripts() {
		/** Firebase */
		wp_register_script( self::JS_FIREBASE, 'https://www.gstatic.com/firebasejs/7.15.0/firebase-app.js', [], '7.15.0', true );
		wp_register_script( self::JS_FIREBASE_AUTH, 'https://www.gstatic.com/firebasejs/7.15.0/firebase-auth.js', [ self::JS_FIREBASE ], '7.15.0', true );
		/**  */

		/** Main */
		wp_enqueue_script( self::JS_MAIN, plugin_dir_url( __DIR__) . 'js/main.js', ['jquery', self::JS_FIREBASE_AUTH], '', 'true' );
		wp_localize_script( self::JS_MAIN, 'wp_firebase', WP_Firebase_Admin::get_config() );
		wp_localize_script( self::JS_MAIN, 'firebase_ajaxurl', admin_url( 'admin-ajax.php' ) );
		/**  */
	}

	public function ajax_handle_verification() {
		$response = $_REQUEST;

		if ( $response ) {
			$output = self::handle_verification( $response['user'] );

			if ( $output ) {
				wp_send_json_success( $output );
			} else {
				wp_send_json_error( $output );
			}
		} else {
			wp_send_json_error();
		}
	}

	public function ajax_handle_error() {
		$response = $_REQUEST;

		if ( $response ) {
			$output = self::handle_error( $response );
			wp_send_json_error( $output );
		}
	}
}

new namespace\WP_Firebase_Main();
