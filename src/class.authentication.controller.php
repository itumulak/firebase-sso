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

	public function __construct() {
		$this->config = WP_Firebase_Admin::get_config();

		if ( $this->config ) {
			$this->firebase = ( new Factory )->withServiceAccount( WP_Firebase_Admin::get_config() );
			$this->auth = $this->firebase->createAuth();
		}
	}

	public function signInWithEmailAndPassword( $emailAddress, $password ) {
		try {
			$request = $this->auth->signInWithEmailAndPassword( $emailAddress, $password );
			update_option('wp_firebase_user_data', $request->data());
			return $request->data();
		} catch (\Exception $e) {
			if ( is_array( explode( ':', $e->getMessage() ) ) ) {
				return [ 'error' => trim( explode( ':', $e->getMessage() )[0] ) ];
			}

			return [ 'error' => $e->getMessage() ];
		}
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

	/**
	 * Sign-in user when verifying email
	 * @param $user
	 *
	 * @return array
	 */
//	private static function email_pass_auth( $user ) {
//
//		$userId = self::verify_user( $user['email'] );
//
//		if ( ! $userId ) {
//			// Login exist in Firebase but no wp credentials
//			// Let's create a new user
//			$userId = wp_insert_user(
//				[
//					'user_email' => $user['email'],
//					'user_login' => explode( '@', $user['email'] )[0]
//				]
//			);
//		}
//
//		// Proceed logging in...
//		wp_set_auth_cookie( $userId );
//
//		return [ 'code' => 200, 'message' => 'success' ];
//	}

	private static function google_auth( $response ) {

	}

	private static function facebook_auth( $response ) {

	}
}
