<?php

namespace IT\SSO\Firebase;

use IT\SSO\Firebase\WP_Auth as WP;
use IT\SSO\Firebase\Email_Password_Auth as Firebase_Auth;
use IT\SSO\Firebase\Callback_Factory as Callback;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit

/**
 * Email and password authentication AJAX callback.
 *
 * @since 2.0.0
 */
class Email_Password extends Callback {
	public function __construct() {
		$this->sign_in_type = self::SIGNIN_EMAILPASS;
	}
	/**
	 * Initialized functions.
	 * Hooks/Filter are added here.
	 *
	 * @since 2.0.0
	 */
	function init() {
		add_filter( 'authenticate', array( $this, 'email_pass_auth' ), 10, 3 );
	}

	/**
	 * Email/Pass Authentication callback.
	 *
	 * Proceed to register the user credentials in WordPress if no errors occurred during firebase authentication.
	 *
	 * @use Hook/Filter
	 *
	 * @param $user
	 * @param $email_address
	 * @param $password
	 *
	 * @return false|WP_User
	 * @since 1.0.0
	 */
	public function email_pass_auth( $user, $email_address, $password ) {
		if ( $email_address && is_email( $email_address ) && ! email_exists( $email_address ) ) { // Firebase only accepts email address to auth
			$sanitized_email = sanitize_email( $email_address );
			$firebase_auth   = new Firebase_Auth();
			$wp_auth         = new WP();

			$user_info = $firebase_auth->signin_from_email_password( $sanitized_email, $password );

			if ( ! isset( $user_info['error'] ) ) {
//				$user = $wp_auth->insert_user( $user_info['email'], $password );
				$user = $wp_auth->register_user( $user_info['email'], $password );
				$wp_auth->signin_usermeta( $user->ID, $this->sign_in_type );
			}
		}

		return $user;
	}
}

$email_password = new Email_Password();
$email_password->init();
