<?php
namespace Itumulak\WpSsoFirebase\Models;

class Providers implements Data_Management {
	private array $data;
	const PROVIDER_EMAILPASS = 'emailpassword';
	const PROVIDER_FACEBOOK  = 'facebook';
	const PROVIDER_GOOGLE    = 'google';
	const OPTION_KEY_NAME    = 'wp_firebase_signin_providers';

	public function __construct() {
		$this->data = array(
			'emailpassword' => false,
			'google'        => false,
			'facebook'      => false,
		);
	}

	/**
	 * Retrieve a specific configuration value in the database.
	 *
	 * @param [type] $key
	 * @return string|boolean|array
	 */
	public function get( $key ) : string|bool|array {
		return $this->get_all()[ $key ];
	}

	/**
	 * Retrieve the enabled providers in the database.
	 *
	 * @return array
	 */
	public function get_all() : array {
		 return wp_parse_args( get_option( self::OPTION_KEY_NAME ), $this->data );
	}

	/**
	 * Save the enabled providers in the database.
	 *
	 * @param array $data
	 * @return bool
	 */
	public function save( array $data ) : bool {
		if ( $data ) {
			foreach ( array_keys( $data ) as $key ) {
				$this->data[ $key ] = $data[ $key ];
			}
		}

		return update_option( self::OPTION_KEY_NAME, $this->data );
	}
}
