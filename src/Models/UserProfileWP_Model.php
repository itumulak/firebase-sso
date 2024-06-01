<?php
namespace Itumulak\WpSsoFirebase\Models;

class UserProfileWP_Model extends Base_Model {
	private Error_Model $error_model;
	private string $handle;
	private Providers_Model $provider_model;

	public function __construct() {
		 $this->handle         = 'wp_firebase_profile';
		 $this->provider_model = new Providers_Model();
		 $this->error_model = new Error_Model();
	}

	public function get_handle() {
		return $this->handle;
	}

	public function check_token_availability( string $token, string $provider ) : bool|Error_Model {
		if ( $this->provider_model->is_token_available( $token, $provider ) ) {
			return true;
		} else {
			$this->error_model->add( 'firebase_token_in_use', __( 'An error occurred. This provider is already linked to another account.' ) );
		}

		return $this->error_model->get_errors();
	}
}
