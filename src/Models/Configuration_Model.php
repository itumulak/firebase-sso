<?php
/**
 * Configuration model class.
 *
 * @package firebase-sso
 */

namespace Itumulak\WpSsoFirebase\Models;

use Itumulak\WpSsoFirebase\Models\Interface\Data_Management_Interface;

/**
 * Configuration_Model
 */
class Configuration_Model implements Data_Management_Interface {
	private array $data;
	const OPTION_KEY_NAME = 'wp_firebase_config';
	
	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {
		$this->data = array(
			'apiKey'     => '',
			'authDomain' => '',
		);
	}

	/**
	 * Retrieve a specific configuration value in the database.
	 *
	 * @param string $key
	 * @return string|boolean|array
	 */
	public function get( string $key ) : string|bool|array {
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
