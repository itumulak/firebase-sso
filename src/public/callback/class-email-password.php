<?php

namespace IT\SSO\Firebase;

use IT\SSO\Firebase\WP_Auth as WP;
use IT\SSO\Firebase\Email_Password_Auth as Firebase_Auth;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit

/**
 * Email and password authentication AJAX callback.
 *
 * @since 2.0.0
 */
class Email_Password {
	/**
	 * Email and password constructor.
	 *
	 * Add a filter hook in authenticating email/password sign-up.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
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
			$auth      = new Firebase_Auth();
			$user_info = $auth->signin_from_email_password( $email_address, $password );

			if ( ! isset( $user_info['error'] ) ) {
				$user = WP::auth_user( $user_info['email'], $password );
				WP::signin_usermeta( $user->ID, Default_Vars::SIGNIN_EMAILPASS );
			}
		}

		return $user;
	}

}

new Email_Password();
