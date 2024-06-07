<?php
/**
 * Providers model.
 *
 * @package firebase-sso
 */

namespace Itumulak\WpSsoFirebase\Models;

use Itumulak\WpSsoFirebase\Models\Interface\Data_Management_Interface;
use WP_User;

/**
 * Providers_Model
 */
class Providers_Model implements Data_Management_Interface {
	/**
	 * Holds the available the supported firebase providers.
	 *
	 * @var array
	 */
	private array $providers;
	const PROVIDER_FACEBOOK = 'facebook';
	const PROVIDER_GOOGLE   = 'google';
	const OPTION_KEY_NAME   = 'wp_firebase_signin_providers';

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {
		$this->providers = array(
			self::PROVIDER_GOOGLE   => false,
			self::PROVIDER_FACEBOOK => false,
		);
	}

	/**
	 * Return the available providers.
	 *
	 * @return array
	 */
	public function get_providers() : array {
		return $this->providers;
	}

	/**
	 * Retrieve a specific configuration value in the database.
	 *
	 * @param string $key Provider Key.
	 * @return string|boolean|array
	 */
	public function get( string $key ) : string|bool|array {
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
	 * @param array $data Providers data.
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

	/**
	 * Check if uid is still available.
	 *
	 * @param  string $uid UID.
	 * @param  string $provider Provider type.
	 * @return bool
	 */
	public function is_uid_available( string $uid, string $provider ) : bool {
		global $wpdb;

		$meta_key            = $this->get_provider_meta_key( $provider );
		$uid_used_by_user_id = $wpdb->get_var( 
			$wpdb->prepare( 
					"SELECT user_id 
						FROM $wpdb->usermeta 
						WHERE meta_key = %s 
						AND meta_value = %d", 
					esc_attr( $meta_key ), 
					esc_attr( $uid ) 
				) 
			); // db call ok. no-cache ok.

		if ( $uid_used_by_user_id ) {
			return false;
		}

		return true;
	}

	public function get_account_uid_assoc(string $uid, string $email) : WP_User|false { 
		global $wpdb;

		$user_id  = $wpdb->get_var( 
			$wpdb->prepare( 
				"SELECT $wpdb->users.ID 
				 FROM $wpdb->users 
				 	LEFT JOIN $wpdb->usermeta
				 	ON $wpdb->users.ID = $wpdb->usermeta.user_id
				 		WHERE $wpdb->users.user_email = %s 
				 		AND $wpdb->usermeta.meta_value = %d",
				esc_attr( $email ), 
				esc_attr( $uid ) ) 
			); // db call ok. no-cache ok.

		if ( $user_id ) {
			return get_user_by('id', $user_id);
		} 

		return false;
	}

	/**
	 * Get the user's provider uid.
	 *
	 * @param  int    $user_id User Id.
	 * @param  string $provider Provider type.
	 * @return mixed
	 */
	public function get_provider_meta( int $user_id, string $provider ) : mixed {
		return get_user_meta( $user_id, $this->get_provider_meta_key( $provider ), true );
	}

	/**
	 * Save the user's provider uid.
	 *
	 * @param  int    $user_id User Id.
	 * @param  string $uid UID.
	 * @param  string $provider Provider type.
	 * @return int|bool
	 */
	public function save_provider_meta( int $user_id, string $uid, string $provider ) : int|bool {
		return update_user_meta( $user_id, $this->get_provider_meta_key( $provider ), $uid );
	}

	/**
	 * Delete the user's provider uid.
	 *
	 * @param  int    $user_id User Id.
	 * @param  string $provider Provider type.
	 * @return int|bool
	 */
	public function delete_provider_meta( int $user_id, string $provider ) : int|bool {
		return delete_user_meta( $user_id, $this->get_provider_meta_key( $provider ) );
	}

	/**
	 * Get the provider's meta key.
	 *
	 * @param  string $provider Provider type.
	 * @return string
	 */
	private function get_provider_meta_key( string $provider ) : string {
		return sprintf( 'firebase_%s_uid', $provider );
	}
}
