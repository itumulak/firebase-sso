<?php
namespace Itumulak\WpSsoFirebase\Models;

use WP_Error;

class Error_Model {
	const LOGIN_FAILED   = 'login_failed';
	const ACCOUNT_IN_USE = 'account_in_use';
	const TOKEN_IN_USE   = 'token_in_use';
	const FIREBASE_ERROR = 'firebase_error';
	const WP_ERROR       = 'wp_error';
	private WP_Error $wp_errors;
	private array $error_data;

	public function __construct() {
		$this->wp_errors = new WP_Error();

		 $this->error_data = array(
			 self::ACCOUNT_IN_USE => array(
				 'key'     => self::ACCOUNT_IN_USE,
				 'message' => __( 'Login has failed. An internal issue occurred please again.' ),
			 ),
			 self::LOGIN_FAILED   => array(
				 'key'     => self::LOGIN_FAILED,
				 'message' => __( 'Login has failed. An internal issue occurred please again.' ),
			 ),
			 self::TOKEN_IN_USE   => array(
				 'key'     => self::TOKEN_IN_USE,
				 'message' => __( 'An error occurred. This provider is already in used.' ),
			 ),
			 self::FIREBASE_ERROR => array(
				 'key'     => self::FIREBASE_ERROR,
				 'message' => __( 'An error occurred. Please try again.' ),
			 ),
			 self::WP_ERROR       => array(
				 'key'     => self::WP_ERROR,
				 'message' => __( 'An error occurred. Please try again.' ),
			 ),
		 );
	}

	public function add( string $key, string $message = '' ) : void {
		if ( ! $message && isset( $this->error_data[ $key ] ) ) {
			$message = $this->get_message( $key );
		}

		$this->wp_errors->add( $key, $message );
	}

	public function get_message( string $key ) : string {
		return $this->error_data[ $key ]['message'];
	}

	public function get_errors() : WP_Error {
		return $this->wp_errors;
	}

	public function get_error_messages() : array {
		return $this->wp_errors->get_error_messages();
	}

	public function get_error_codes() : array {
		return $this->wp_errors->get_error_codes();
	}
}
