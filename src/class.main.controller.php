<?php
namespace Firebase;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WP_Firebase_Main extends WP_Firebase {
	function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ] );
	}

	public function scripts() {
		/** Firebase */
		wp_register_script( self::JS_FIREBASE, 'https://www.gstatic.com/firebasejs/7.15.0/firebase-app.js', [], '7.15.0', true );
		wp_register_script( self::JS_FIREBASE_AUTH, 'https://www.gstatic.com/firebasejs/7.15.0/firebase-auth.js', [ self::JS_FIREBASE ], '7.15.0', true );
		/**  */

		/** Main */
		wp_enqueue_script( self::JS_MAIN, plugin_dir_url( __DIR__) . 'js/main.js', ['jquery', self::JS_FIREBASE_AUTH], '', 'true' );
		wp_localize_script( self::JS_MAIN, 'wp_firebase', stripslashes( WP_Firebase_Admin::get_config() ));
		/**  */
	}
}

new namespace\WP_Firebase_Main();
