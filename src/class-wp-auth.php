<?php
/**
 * WordPress Auth.
 *
 * @since 2.0.0
 */
namespace IT\SSO\Firebase;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * WordPress Authentication class.
 *
 * @since 2.0.0
 */
class WP_Auth {
	/**
	 * Authenticate user.
	 *
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
	public static function auth_user( $email, $password = null ) {
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
	public static function login_user( $user_id ) {
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
	public static function signin_usermeta( $user_id, $sign_in_type, $refresh_token = null, $oauth_token = null ) {
		$sign_in_types = get_user_meta( $user_id, Default_Vars::USER_SIGNIN_TYPE, false );

		if ( $sign_in_type ) {
			if ( ! in_array( $sign_in_type, $sign_in_types, true ) ) {
				$sign_in_types[] = $sign_in_type;
				update_user_meta( $user_id, Default_Vars::USER_SIGNIN_TYPE, $sign_in_types );

				if ( $sign_in_type === Default_Vars::SIGNIN_GOOGLE || $sign_in_type === Default_Vars::SIGNIN_FACEBOOK ) {
					update_user_meta( $user_id, Default_Vars::SIGNIN_OAUTH, $oauth_token );
				}
			}

			update_user_meta( $user_id, Default_Vars::SIGNIN_REFRESHTOKEN, $refresh_token );
		}
	}

	/**
	 * Set cookie logout.
	 *
	 * @use Hook/Action
	 * @since 1.0.0
	 */
	public static function set_cookie_logout() {
		setcookie( Default_Vars::COOKIE_LOGOUT, 1, time() + 3600, COOKIEPATH, COOKIE_DOMAIN );
	}

	/**
	 * Set cookie deletion
	 *
	 * @since 1.0.0
	 */
	public static function delete_cookie() {
		// TODO
	}
}