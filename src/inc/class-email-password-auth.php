<?php

namespace IT\SSO\Firebase;

use IT\SSO\Firebase\Admin_Config as Admin_Config;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Email & Password Authentication class.
 * Utilizes Firebase Auth REST API for email and password sign-in or sing-up.
 *
 * @since 2.0.0
 */
class Email_Password_Auth {
	public Admin_Config $admin_config;
	public $config;
	public string $api_key;
	public string $end_point;
	public array $data;
	const BASE_URI              = 'https://identitytoolkit.googleapis.com/v1/accounts';
	const SINGIN_EMAIL_PASSWORD = ':signInWithPassword';
	const SINGUP_EMAIL_PASSWORD = ':signUp';
	const FETCH_PROVIDERS_EMAIL = ':createAuthUri';
	const COOKIE_LOGOUT         = 'wp_firebase_logout';

	/**
	 * Authentication constructor.
	 * Handles PHP related HTTP request.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->admin_config = new Admin_Config();
		$this->config       = $this->admin_config->get_config();
		$this->api_key      = $this->config['apiKey'];
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
	public function signin_from_email_password( $email_address, $password ) {
		$this->data = array(
			'email'             => $email_address,
			'password'          => $password,
			'returnSecureToken' => true,
		);

		return  $this->handle_request( self::SINGIN_EMAIL_PASSWORD, $this->data );
	}


	/**
	 * Sign-up method.
	 *
	 * @param $email_address
	 * @param $password
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function signup_from_email_password( $email_address, $password ) {
		$this->data = array(
			'email'             => $email_address,
			'password'          => $password,
			'returnSecureToken' => true,
		);

		return $this->handle_request( self::SINGUP_EMAIL_PASSWORD, $this->data );
	}

	/**
	 * Fetch providers from User's email.
	 *
	 * @param $email_address
	 * @param $continue_uri
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_providers_from_email( $email_address, $continue_uri ) {
		$this->data = array(
			'identifier'  => $email_address,
			'continueUri' => $continue_uri,
		);

		return $this->handle_request( self::FETCH_PROVIDERS_EMAIL, $this->data );
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
	protected function handle_request( $auth, $data = array() ) {
		$args = array(
			'method'    => 'POST',
			'headers'   => array( 'Content-Type' => 'application/json' ),
			'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
			'timeout'   => apply_filters( 'http_request_timeout', 600 ),
			'body'      => json_encode( $data ),
		);

		$end_point       = self::BASE_URI . $auth;
		$end_point       = add_query_arg( 'key', $this->api_key, $end_point );
		$this->end_point = $end_point;

		// Get request response.
		$response = wp_remote_request( $end_point, $args );

		return json_decode( $response['body'], true );
	}
}
