<?php

namespace IT\SSO\Firebase;

use IT\SSO\Firebase\WP_Auth as WP;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Google AJAX callback class.
 *
 * @since 2.0.0
 */
class Google {
	/**
	 * Google constructor.
	 *
	 * @since 2.0.0
	 */
	function __construct() {
		add_action( 'wp_ajax_firebase_google_login', array( $this, 'callback' ) );
		add_action( 'wp_ajax_nopriv_firebase_google_login', array( $this, 'callback' ) );
	}

	/**
	 * AJAX callback.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function callback() {
		$oauth_token    = sanitize_key( $_REQUEST['oauth_token'] );
		$refresh_token  = sanitize_key( $_REQUEST['refresh_token'] );
		$sanitize_email = sanitize_email( $_REQUEST['email'] );

		if ( $sanitize_email ) {
			$user = WP::auth_user( $sanitize_email );

			if ( ! is_wp_error( $user ) ) {
				WP::signin_usermeta( $user->ID, Default_Vars::SIGNIN_GOOGLE, $refresh_token, $oauth_token );
				$login_user_url = WP::login_user( $user->ID );

				wp_send_json_success( array( 'url' => $login_user_url ) );
			}
		}

		wp_send_json_error();
	}
}

new Google();

