<?php
/**
 * WordPress Auth.
 *
 * @since 2.0.0
 */
namespace IT\SSO\Firebase;

use IT\SSO\Firebase\Base as Base;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * WordPress Authentication class.
 *
 * @since 2.0.0
 */
class WP_Auth extends Base {
	/**
	 * Register user.
	 *
	 * If email already exists, skipped the registration.
	 * Otherwise, it will into the process of registering the user.
	 * The newly registered user will undergo and confirm their registration via email.
	 *
	 * If a username is already used, it will generate a random username instead.
	 *
	 * @param $user_email
	 *
	 * @return false|int|WP_Error
	 */
	public function register_user( $user_email ) {
		$email   = sanitize_email( $user_email );
		$user_id = email_exists( $email );

		if ( ! $user_id ) {
			$username = $this->generate_user_login( $email );
			$user_id  = register_new_user( $username, $email );
		}

		return $user_id;
	}

	/**
	 * Insert a user.
	 *
	 * No email verification is sent on this process.
	 * Check whether a user already exists and return the user ID.
	 * Otherwise, create user in WordPress and return the user ID.
	 * User login is the same as the email (before the @).
	 *
	 * @param $email
	 * @param null $password
	 *
	 * @return false|WP_User
	 * @since 1.0.0
	 */
	public function insert_user( $email, $password = null ) {
		$user_id = email_exists( $email );

		if ( ! $user_id ) {
			// Login exist in Firebase but no wp credentials
			// Let's create a new user
			$user_id = wp_insert_user(
				array(
					'user_email' => $email,
					'user_login' => explode( '@', $email )[0],
					'user_pass'  => $password,
				)
			);
		}

		return get_user_by( 'id', $user_id );
	}

	/**
	 * Set authentication cookies.
	 *
	 * @param $user_id
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function login_user( $user_id ) {
		wp_clear_auth_cookie();
		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id );

		// TODO: get redirect url after login
		return get_admin_url();
	}

	/**
	 * Save User Meta upon logged in.
	 *
	 * @param $user_id
	 * @param $sign_in_type
	 * @param null $refresh_token
	 * @param null $oauth_token
	 *
	 * @since 1.0.0
	 */
	public function signin_usermeta( $user_id, $sign_in_type, $refresh_token = null, $oauth_token = null ) {
		$sign_in_types = get_user_meta( $user_id, self::USER_SIGNIN_TYPE, false );

		if ( $sign_in_type ) {
			if ( ! in_array( $sign_in_type, $sign_in_types, true ) ) {
				$sign_in_types[] = $sign_in_type;
				update_user_meta( $user_id, self::USER_SIGNIN_TYPE, $sign_in_types );

				if ( $sign_in_type === self::SIGNIN_GOOGLE || $sign_in_type === self::SIGNIN_FACEBOOK ) {
					update_user_meta( $user_id, self::SIGNIN_OAUTH, $oauth_token );
				}
			}

			update_user_meta( $user_id, self::SIGNIN_REFRESHTOKEN, $refresh_token );
		}
	}

	/**
	 * Set cookie logout.
	 *
	 * @use Hook/Action
	 * @since 1.0.0
	 */
	public function set_cookie_logout() {
		setcookie( self::COOKIE_LOGOUT, 1, time() + 3600, COOKIEPATH, COOKIE_DOMAIN );
	}

	/**
	 * Set cookie deletion
	 *
	 * @since 1.0.0
	 */
	public function delete_cookie() {
		// TODO
	}

	/**
	 * Get the username from the email before the @ symbol.
	 *
	 * If it exists, we then generate a random username.
	 * Repeated verification (username_exists function call) until a non-existing username is generated.
	 *
	 * @param $email
	 * @param $verify_username
	 * @param $length
	 *
	 * @since 2.0.0
	 * @return false|int|string
	 */
	protected function generate_user_login( $email ) {
		$username_from_email = explode( '@', $email )[0];
		$user_exists         = username_exists( $username_from_email );

		do {
			$characters        = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$characters_length = strlen( $characters );
			$random_username   = '';
			for ( $i = 0; $i < strlen( $username_from_email ); $i ++ ) {
				$random_username .= $characters[ rand( 0, $characters_length - 1 ) ];
			}

			$user_exists = username_exists( $random_username );
		} while ( $user_exists > 0 );

		return $random_username;
	}
}
