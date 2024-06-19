<?php
/**
 * Firebase email/password authentication class.
 *
 * @package firebase-sso
 */

namespace Itumulak\WpSsoFirebase\Models;

/**
 * Firebase_EmailPass_Auth
 */
class Firebase_EmailPass_Auth {
	const BASE_URI      = 'https://identitytoolkit.googleapis.com/v1/accounts';
	const COOKIE_LOGOUT = 'wp_firebase_logout';

	/**
	 * Holds the API key.
	 *
	 * @var string
	 */
	private string $api_key;

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {
		$admin_model   = new Admin_Model();
		$config        = $admin_model->get_config();
		$this->api_key = $config['apiKey']['value'] && '';
	}

	/**
	 * Sign-in Method.
	 *
	 * @param string $email_address Email address.
	 * @param string $password Password.
	 * @since 1.0.0
	 *
	 * @return array JSON
	 */
	public function signin_from_email_password( string $email_address, string $password ): array {
		$data = array(
			'email'             => $email_address,
			'password'          => $password,
			'returnSecureToken' => true,
		);

		return $this->handle_request( ':signInWithPassword', $data );
	}

	/**
	 * Sign-up method.
	 *
	 * @param string $email_address Email address.
	 * @param string $password Password.
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function signup_from_email_password( string $email_address, string $password ): array {
		$data = array(
			'email'             => $email_address,
			'password'          => $password,
			'returnSecureToken' => true,
		);

		return $this->handle_request( ':signUp', $data );
	}

	/**
	 * Fetch providers from User's email.
	 *
	 * @param string $email_address Email address.
	 * @param string $continue_uri Continue URL.
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_providers_from_email( string $email_address, string $continue_uri ): array {
		$data = array(
			'identifier'  => $email_address,
			'continueUri' => $continue_uri,
		);

		return $this->handle_request( ':createAuthUri', $data );
	}

	/**
	 * Perform HTTP Request from Firebase.
	 *
	 * @param string $auth Authentication key.
	 * @param array  $data Data.
	 * @since 1.0.0
	 *
	 * @return array JSON
	 */
	protected function handle_request( string $auth, array $data = array() ): array {
		$args = array(
			'method'    => 'POST',
			'headers'   => array( 'Content-Type' => 'application/json' ),
			'sslverify' => apply_filters( 'https_local_ssl_verify', false ), //phpcs:ignore.
			'timeout'   => apply_filters( 'http_request_timeout', 600 ), //phpcs:ignore.
			'body'      => wp_json_encode( $data ),
		);

		$end_point = self::BASE_URI . $auth;
		$end_point = add_query_arg( 'key', $this->api_key, $end_point );

		// Get request response.
		$response = wp_remote_request( $end_point, $args );

		return json_decode( $response['body'], true );
	}
}
