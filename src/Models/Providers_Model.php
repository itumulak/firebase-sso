<?php
namespace Itumulak\WpSsoFirebase\Models;

use Itumulak\WpSsoFirebase\Models\Interface\Data_Management_Interface;

class Providers_Model implements Data_Management_Interface {
	private array $providers;
	const PROVIDER_FACEBOOK = 'facebook';
	const PROVIDER_GOOGLE   = 'google';
	const OPTION_KEY_NAME   = 'wp_firebase_signin_providers';

	public function __construct() {
		$this->providers = array(
			self::PROVIDER_GOOGLE   => false,
			self::PROVIDER_FACEBOOK => false,
		);
	}

	public function get_providers() : array {
		return $this->providers;
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
		 return wp_parse_args( get_option( self::OPTION_KEY_NAME ), $this->providers );
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
				$this->providers[ $key ] = $data[ $key ];
			}
		}

		return update_option( self::OPTION_KEY_NAME, $this->providers );
	}

	public function is_uid_available( string $id, $provider ) : bool {
		global $wpdb;

		$meta_key            = $this->get_provider_meta_key( $provider );
		$uid_used_by_user_id = $wpdb->get_var( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '$meta_key' AND meta_value = '$id'" );

		if ( $uid_used_by_user_id ) {
			return false;
		}

		return true;
	}

	public function get_provider_meta( int $user_id, $provider ) : mixed {
		return get_user_meta( $user_id, $this->get_provider_meta_key( $provider ), true );
	}

	public function save_provider_meta( int $user_id, string $uid, string $provider ) : int|bool {
		return update_user_meta( $user_id, $this->get_provider_meta_key( $provider ), $uid );
	}

	public function delete_provider_meta( int $user_id, string $provider ) : int|bool {
		return delete_user_meta( $user_id, $this->get_provider_meta_key( $provider ) );
	}

	private function get_provider_meta_key( string $provider ) : string {
		return sprintf( 'firebase_%s_uid', $provider );
	}
}
