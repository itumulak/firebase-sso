<?php
namespace Firebase;
use Kreait\Firebase\Factory;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WP_Firebase_Auth extends WP_Firebase {
	public $firebase;
	public $auth;
	public $config;

	public $apiKey;
	public $endPoint;
	public $data;
	const baseUri = 'https://identitytoolkit.googleapis.com/v1/accounts';
	const signInEmailPassword = ':signInWithPassword';
	const cookieLogout = 'wp_firebase_logout';

	public function __construct() {
		$this->config = WP_Firebase_Admin::get_config();
		$this->apiKey = $this->config['apiKey'];
	}

	public function signInWithEmailAndPassword( $emailAddress, $password ) {
		$this->data = [
			'email' => $emailAddress,
			'password' => $password,
			'returnSecureToken' => true
		];

		return  $this->handle_request( self::signInEmailPassword,  $this->data );
	}

	public function GoogleAuthProvider( $token, $emailAddress ) {

	}

	/**
	 * Handles sign-in method
	 * @param $response
	 */
	public static function handle_verification( $response ) {
		if ( $response['operationType'] ) {
			switch ( $response['operationType'] ) {
				case 'signIn':
					$response = self::email_pass_auth( $response );
					break;
			}
		}

		return ['message' => $response['message'], 'status' => $response['code']];
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

	private static function google_auth( $response ) {

	}

	private static function facebook_auth( $response ) {

	}

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
