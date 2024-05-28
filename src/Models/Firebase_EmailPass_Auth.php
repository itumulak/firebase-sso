<?php
namespace Itumulak\WpSsoFirebase\Models;

class Firebase_EmailPass_Auth {
	const BASE_URI      = 'https://identitytoolkit.googleapis.com/v1/accounts';
	const COOKIE_LOGOUT = 'wp_firebase_logout';
	private string $api_key;

	public function __construct() {
		$admin_model   = new Admin_Model();
		$config        = $admin_model->get_config();
		$this->api_key = $config['apiKey']['value'] && '';
	}

	/**
	 * Sign-in Method.
	 *
	 * @param $email_address
	 * @param $password
	 * @since 1.0.0
	 *
	 * @return array JSON
	 */
	public function signin_from_email_password( $email_address, $password ) : array {
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
	 * @param $email_address
	 * @param $password
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function signup_from_email_password( $email_address, $password ) : array {
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
	 * @param $email_address
	 * @param $continue_uri
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_providers_from_email( $email_address, $continue_uri ) : array {
		$data = array(
			'identifier'  => $email_address,
			'continueUri' => $continue_uri,
		);

		return $this->handle_request( ':createAuthUri', $data );
	}

	/**
	 * Perform HTTP Request from Firebase.
	 *
	 * @param $auth
	 * @param array $data
	 * @since 1.0.0
	 *
	 * @return array JSON
	 */
	protected function handle_request( $auth, $data = array() ) : array {
		$args = array(
			'method'    => 'POST',
			'headers'   => array( 'Content-Type' => 'application/json' ),
			'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
			'timeout'   => apply_filters( 'http_request_timeout', 600 ),
			'body'      => json_encode( $data ),
		);

		$end_point = self::BASE_URI . $auth;
		$end_point = add_query_arg( 'key', $this->api_key, $end_point );

		// Get request response.
		$response = wp_remote_request( $end_point, $args );

		return json_decode( $response['body'], true );
	}
}
