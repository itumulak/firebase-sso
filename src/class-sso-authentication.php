<?php

namespace IT\SSO\Firebase;

use IT\SSO\Firebase\SSO_Default as Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class SSO_Authentication extends Main {
	public $config;
	public $api_key;
	public $end_point;
	public $data;
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
		$this->config  = SSO_Admin::get_config();
		$this->api_key = $this->config['apiKey'];
	}

	/**
	 * Prepare data for HTTP request for Email/Password Sign-in Method.
	 *
	 * @param $email_address
	 * @param $password
	 *
	 * @return array JSON
	 */
	public function signInWithEmailAndPassword( $email_address, $password ) {
		$this->data = array(
			'email'             => $email_address,
			'password'          => $password,
			'returnSecureToken' => true,
		);

		return  $this->handle_request( self::SINGIN_EMAIL_PASSWORD, $this->data );
	}

	public function fetchProvidersForEmail( $email_address, $redirect_url = null ) {
		$this->data = array(
			'identifier'  => $email_address,
			'continueUri' => ! $redirect_url ? get_admin_url() : $redirect_url,
		);

		return $this->handle_request( self::FETCH_PROVIDERS_EMAIL, $this->data );
	}

	public function createUserWithEmailAndPassword( $email_address, $password ) {
		$this->data = array(
			'email'             => $email_address,
			'password'          => $password,
			'returnSecureToken' => true,
		);

		return $this->handle_request( self::SINGUP_EMAIL_PASSWORD, $this->data );
	}

	/**
	 * Handles error response
	 * @param $response
	 *
	 * @return array
	 */
	public static function handle_error( $response ) {
		$message = '';
		$status  = 400;

		if ( $response['code'] ) {
			switch ( $response['code'] ) {
				case 'auth/user-disabled':
				case 'auth/user-not-found':
				case 'auth/wrong-password':
				case 'auth/invalid-email':
					$message = $response['message'];
					break;
				default:
					$message = 'An error has occured. Please contact admin.';
			}
		}

		return array(
			'message' => $message,
			'status'  => $status,
		);
	}

	/**
	 * Verify if email exist
	 *
	 * @param $email
	 *
	 * @return mixed
	 */
	public static function verify_user( $email ) {
		return email_exists( $email );
	}

	/**
	 * Perform HTTP Request from Firebase
	 *
	 * @param $auth
	 * @param array $data
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
