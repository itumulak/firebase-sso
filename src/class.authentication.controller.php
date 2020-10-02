<?php
namespace IT\SSO\Firebase;
use IT\SSO\Firebase\WP as Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Authentication extends Main {
	public $config;
	public $apiKey;
	public $endPoint;
	public $data;
	const baseUri = 'https://identitytoolkit.googleapis.com/v1/accounts';
	const signInEmailPassword = ':signInWithPassword';
	const signUpEmailPassword = ':signUp';
	const cookieLogout = 'wp_firebase_logout';

	/**
	 * Authentication constructor.
	 * Handles PHP related HTTP request.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->config = Admin::get_config();
		$this->apiKey = $this->config['apiKey'];
	}

	/**
	 * Prepare data for HTTP request for Email/Password Sign-in Method.
	 *
	 * @param $emailAddress
	 * @param $password
	 *
	 * @return JSON
	 */
	public function signInWithEmailAndPassword( $emailAddress, $password ) {
		$this->data = [
			'email' => $emailAddress,
			'password' => $password,
			'returnSecureToken' => true
		];

		return  $this->handle_request( self::signInEmailPassword,  $this->data );
	}

	public function createUserWithEmailAndPassword( $emailAddress, $password ) {
		$this->data = [
			'email' => $emailAddress,
			'password' => $password,
			'returnSecureToken' => true
		];

		return $this->handle_request( self::signUpEmailPassword, $this->data );
	}

	/**
	 * Handles error response
	 * @param $response
	 *
	 * @return array
	 */
	public static function handle_error( $response ) {
		$message = '';
		$status = 400;

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

		return ['message' => $message, 'status' => $status];
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
	 * @return JSON
	 */
	protected function handle_request( $auth, $data = [] ) {
		$args = [
			'method' => 'POST',
			'headers' => [
				'Content-Type' => 'application/json'
			],
			'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
			'timeout'   => apply_filters( 'http_request_timeout', 600 ),
			'body' => json_encode( $data )
		];

		$endPoint = self::baseUri . $auth;
		$endPoint = add_query_arg( 'key', $this->apiKey, $endPoint );
		$this->endPoint = $endPoint;

		// Get request response.
		$response = wp_remote_request( $endPoint, $args );

		return json_decode( $response['body'], true );
	}
}
