<?php

namespace IT\SSO\Firebase;

use IT\SSO\Firebase\Callback_Factory as Callback;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Google AJAX callback class.
 *
 * @since 2.0.0
 */
class Google extends Callback {
	public function __construct() {
		$this->sign_in_type = self::SIGNIN_GOOGLE;
	}

	/**
	 * Initialized functions.
	 * Hooks/Filter are added here.
	 *
	 * @since 2.0.0
	 */
	function init() {
		add_action( 'wp_ajax_firebase_google_login', array( $this, 'callback' ) );
		add_action( 'wp_ajax_nopriv_firebase_google_login', array( $this, 'callback' ) );
	}
}

$google_callback = new namespace\Google();
$google_callback->init();
