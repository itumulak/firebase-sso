<?php
namespace Itumulak\WpSsoFirebase\Models;

class UserProfileWP_Model extends Base_Model {
	private Error_Model $error_model;
	private string $handle;
	private string $handle_object;
	private Providers_Model $provider_model;
	private Configuration_Model $configs;

	public function __construct() {

		 $this->configs        = new Configuration_Model();
		 $this->handle         = 'wp_firebase_profile';
		 $this->handle_object  = 'sso_admin_object';
		 $this->provider_model = new Providers_Model();
		 $this->error_model    = new Error_Model();
	}

	public function get_handle() {
		return $this->handle;
	}

	public function get_handle_object() {
		return $this->handle_object;
	}

	public function get_object_data() : array {
		return array(
			'ajaxurl'   => admin_url( 'admin-ajax.php' ),
			'config'    => $this->configs->get_all(),
			'providers' => $this->provider_model->get_all(),
		);
	}

	public function check_token_availability( string $token, string $provider ) : bool|Error_Model {
		if ( $this->provider_model->is_token_available( $token, $provider ) ) {
			return true;
		} else {
			$this->error_model->add( $this->error_model::TOKEN_IN_USE );
		}

		return $this->error_model->get_errors();
	}

	public function link_provider( int $user_id, string $token, string $provider ) : bool|Error_Model {
		$linked_status = $this->provider_model->save_provider_meta( $user_id, $token, $provider );

		if ( $linked_status > 0 ) {
			return true;
		} else {
			$this->error_model->add( $this->error_model::WP_ERROR );
		}

		return $this->error_model->get_errors();
	}

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
