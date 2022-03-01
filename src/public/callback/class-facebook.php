<?php

namespace IT\SSO\Firebase;

use IT\SSO\Firebase\Callback_Factory as Callback;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Facebook AJAX callback class.
 *
 * @since 2.0.0
 */
class Facebook extends Callback {
	/**
	 * Initialized functions.
	 * Hooks/Filter are added here.
	 *
	 * @since 2.0.0
	 */
	function init() {
		add_action( 'wp_ajax_firebase_facebook_login', array( $this, 'callback' ) );
		add_action( 'wp_ajax_nopriv_firebase_facebook_login', array( $this, 'callback' ) );
	}
}

$facebook_callback = new namespace\Facebook();
$facebook_callback->init();
