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
			$this->firebase = ( new Factory )->withServiceAccount( '{
  "type": "service_account",
  "project_id": "vue-axios-b28c2",
  "private_key_id": "2a02651bdfb3e0bfb055703137ba9883d04901ec",
  "private_key": "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCvRM++4wrUQPnT\nOs6nNssALGu0eG/QRBW/FfWEke+zvM339rXdkpa9f5eYvW7p4wzj8hrrS3XQILVP\nYCoJMTHsFso+SUwRe+CmIxKrQ/LEBpDLCesEJva5J6f3TRgPbjvXL5ODZ7puQSck\nGJ1V4YCzgVwE739mwqYQduDkWFCehxFGKMwiFBwHZCTfVPpfGOqRhiFR1EP1CLSG\n+czCGIIbasyxaNaCuA9e/FBw/68qUO6z3OU3hphIA3GTIKsn7AIP5rB5UQyewx9O\nwSQmUGQwJdYg8JuHwW8v4ts5QjJzk1My99MdnlNjHJwHZJvMWHVmwh6i6pqLG/Dh\nGLkbDNLdAgMBAAECggEAAf8tMnF3mXlPv1JkH1P/jRF0fPXLuyx6QhchBrpEVcU9\nZFcG2hyYWp8L3ir3eJetZv1rDmEONMTioH2FVjkLVFfiaUJAX/bnZ6iTEBbfutAi\n6E5psRnGij1VxwzaA63Y5a6jAYZ2E8cPPuI+6BA22Kb62S/VCj5Pm1BFHlu5N/rV\njmGGFXLJrV+kfhnNNcQQTg5GJNSo4qIuU7KpWAHomh9SILs/GOQhkCxzjxtrwobM\n/qfpLPizX4uYw0/wYF1bNhhRlM7hvwzbp/9OIq951RDU4CnJjtxrX/iu5+tujv0D\nuOS/WG7WwRRoaEyuqZIzmILZTmNULJs5X9JN94xkgQKBgQDWJVfXAG9uVV08fy7A\nuZ6BXWSBtVnTQBIugBJVjJHbxZkKv2VwFs2eRSr6MmyPAIORqbQSj8fkOpod/F0k\nRN8hT9tRkkYnIavgZO/yuC4SS0/CqLnTALXC1sC474wdPU7lMqKEEsEOOlY2fYqk\nyvQIsqWHGCNY0p53xC1Y/bjRQQKBgQDRhkcct722ueB0VEsAXUJzQLeJC0R8p9y/\nnxFtn9OWCdO6oRcseogm/GADQBNUl1PoSJG/0Dila6+bVBrLrL+ViCWKKIsDMcul\nYe6iXAWWHEBP0O+71urGZQQNdc0JupCYvo/bdWJWNUh4cpRA1MSjztNOKRzUd0g2\ndV33RZb+nQKBgBsCdsPNcECQT8Qznmf8gXt7lhOuFfhJGoH28VkM89CuaZErdtXe\namzN+I+6EGsw+2dB6k51CBdHNO0XSYArl6ER+22cb3C8FHum/4SzqkvwZ8z7jwSI\nEGTUxVYOELQXaX4LFuhlnSf4P6t7xiLm3kTCk5IofzzSw94DRlz1E3yBAoGBAMT7\nhvO60wNtcVYswW6QPx734xVWoIbJkkIdHFeCAXx3tLUrKgxJDqQIdYPYw9OtiddC\nErTQ39C1kx1nTuHZgSzmGNTxFPBl6l2L2ryN4zqjSNtBRYHFpmfrJIo7DA9vdO4F\nil361/7QjVef8T2aS1zt378F6/LcSenZIgSNSk4JAoGAPWsRXVKoJOKXyr1kTlXa\nO5kaGSG9ozKjo3q3uBPwCijIVMmkI7jV/z2igQWDM8cvs0uEAVDmUVM+spoh0+KC\nSd9t+5lgGLgbweB31R4VE3VbOI1PKClDbyxaiJGFTJB2hezIu/Ig+mAKRaQCX4f0\nWSCuXrUFWvoekoHhNTTOIrE=\n-----END PRIVATE KEY-----\n",
  "client_email": "firebase-adminsdk-w8saz@vue-axios-b28c2.iam.gserviceaccount.com",
  "client_id": "111935012671951380185",
  "auth_uri": "https://accounts.google.com/o/oauth2/auth",
  "token_uri": "https://oauth2.googleapis.com/token",
  "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
  "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-w8saz%40vue-axios-b28c2.iam.gserviceaccount.com"
}' );
			$this->auth = $this->firebase->createAuth();
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
	private static function email_pass_auth( $user ) {

		$userId = self::verify_user( $user['email'] );

		if ( ! $userId ) {
			// Login exist in Firebase but no wp credentials
			// Let's create a new user
			$userId = wp_insert_user(
				[
					'user_email' => $user['email'],
					'user_login' => explode( '@', $user['email'] )[0]
				]
			);
		}

		// Proceed logging in...
		wp_set_auth_cookie( $userId );

		return [ 'code' => 200, 'message' => 'success' ];
	}

	private static function google_auth( $response ) {

	}

	private static function facebook_auth( $response ) {

	}
}
