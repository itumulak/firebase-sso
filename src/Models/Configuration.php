<?php
namespace Itumulak\WpSsoFirebase\Models;

class Configuration implements Data_Management {
	private array $data;
	const OPTION_KEY_NAME = 'wp_firebase_config';

	public function __construct() {
		 $this->data = array(
			 'apiKey'     => '',
			 'authDomain' => '',
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
	 * Retrieve the configuration in the databse.
	 *
	 * @return array
	 */
	public function get_all() : array {
		return wp_parse_args( get_option( self::OPTION_KEY_NAME ), $this->data );
	}

	/**
	 * Save the configuaration in the database.
	 *
	 * @param array $data
	 * @return bool
	 */
	public function save( array $data ) : bool {
		foreach ( array_keys( $this->data ) as $key ) {
			$this->data[ $key ] = $data[ $key ];
		}

		return update_option( self::OPTION_KEY_NAME, $this->data );
	}
}
