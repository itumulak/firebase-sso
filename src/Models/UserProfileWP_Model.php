<?php
/**
 * WordPress user profile class.
 *
 * @package firebase-sso
 */

namespace Itumulak\WpSsoFirebase\Models;

use WP_Error;

/**
 * UserProfileWP_Model
 */
class UserProfileWP_Model extends Base_Model {
	private Error_Model $error_model;
	private string $handle;
	private string $handle_object;
	private Providers_Model $provider_model;
	private Configuration_Model $configs;
	const AJAX_HANDLE        = 'firebase_link_provider';
	const AJAX_UNLINK_HANDLE = 'firebase_unlink_provider';

	/**
	 * Initialize variables.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->configs        = new Configuration_Model();
		$this->handle         = 'wp_firebase_profile';
		$this->handle_object  = 'firebase_sso_object';
		$this->provider_model = new Providers_Model();
		$this->error_model    = new Error_Model();
	}

	/**
	 * Return the name of the script to handle.
	 *
	 * @return string
	 */
	public function get_handle() : string {
		return $this->handle;
	}

	/**
	 * Return the script's object name.
	 *
	 * @return string
	 */
	public function get_handle_object() : string {
		return $this->handle_object;
	}

	/**
	 * Return the script's object data.
	 *
	 * @return array
	 */
	public function get_object_data() : array {
		return array(
			'ajaxurl'       => admin_url( 'admin-ajax.php' ),
			'config'        => $this->configs->get_all(),
			'providers'     => $this->provider_model->get_all(),
			'user_id'       => get_current_user_id(),
			'action'        => self::AJAX_HANDLE,
			'unlink_action' => self::AJAX_UNLINK_HANDLE,
			'nonce'         => wp_create_nonce( self::AJAX_NONCE ),
		);
	}

	/**
	 * Verify UID and its associated provider to be available.
	 * Throw an error message if the UID is already in use by another wp account.
	 *
	 * @param  string $uid
	 * @param  string $provider
	 * @return bool|WP_Error
	 */
	public function check_uid_availability( string $uid, $provider ) : bool|WP_Error {
		if ( $this->provider_model->is_uid_available( $uid, $provider ) ) {
			return true;
		} else {
			$this->error_model->add( $this->error_model::TOKEN_IN_USE );
		}

		return $this->error_model->get_errors();
	}

	/**
	 * Link the provider to the wp account.
	 *
	 * @param  int    $user_id
	 * @param  string $uid
	 * @param  string $provider
	 * @return bool|WP_Error
	 */
	public function link_provider( int $user_id, string $uid, string $provider ) : bool|Error_Model {
		$linked_status = $this->provider_model->save_provider_meta( $user_id, $uid, $provider );

		if ( $linked_status > 0 ) {
			return true;
		} else {
			$this->error_model->add( $this->error_model::WP_ERROR );
		}

		return $this->error_model->get_errors();
	}

	/**
	 * Unlink the provider to the wp account.
	 *
	 * @param  int    $user_id
	 * @param  string $provider
	 * @return bool|WP_Error
	 */
	public function unlink_provider( int $user_id, string $provider ) : bool|WP_Error {
		$unlink_status = $this->provider_model->delete_provider_meta( $user_id, $provider );

		if ( $unlink_status > 0 ) {
			return true;
		} else {
			$this->error_model->add( $this->error_model::WP_ERROR );
		}

		return $this->error_model->get_errors();
	}

	/**
	 * Return the linked providers for this wp account.
	 *
	 * @param  int $user_id
	 * @return array
	 */
	public function get_linked_providers( int $user_id ) : array {
		$active_providers = $this->provider_model->get_all();
		$linked_providers = $this->provider_model->get_providers();

		foreach ( array_keys( $active_providers ) as $provider ) {
			if ( $this->provider_model->get_provider_meta( $user_id, $provider ) ) {
				$linked_providers[ $provider ] = true;
			}
		}

		return $linked_providers;
	}
}
