<?php
namespace Itumulak\WpSsoFirebase\Models;

class AdminCallback {
	private Admin $admin_model;
	const SAVE_CONFIG_FUNC    = 'save_config_callback';
	const SAVE_PROVIDERS_FUNC = 'save_providers_callback';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->admin_model = new Admin();
	}

	/**
	 * Save Firebase Config.
	 * Ajax request callback.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function save_config_callback() : void {
		if ( !isset($_REQUEST['nonce']) && ! $this->admin_model->verify_nonce( $_REQUEST['nonce'], $this->admin_model::JS_ADMIN_NONCE ) ) {
		    $this->handle_callback( false );
		}

		$this->handle_callback( $this->admin_model->save_config( $_REQUEST ) );
	}

	/**
	 * Save Firebase Sign-in Providers
	 * Ajax request callback
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function save_providers_callback() : void {
		if ( ! isset($_REQUEST['nonce']) && ! $this->admin_model->verify_nonce( $_REQUEST['nonce'], $this->admin_model::JS_ADMIN_NONCE ) ) {
		    $this->handle_callback( false );
		}

		$providers = array_map( 'sanitize_key', $_POST['enabled_providers'] );
		// $providers = $_REQUEST;
		$this->handle_callback( $this->admin_model->save_providers( $providers ) );
	}

	/**
	 * Handle WordPress callbacks.
	 *
	 * @param Function|bool $callback
	 * @return void
	 * @since 2.0.0
	 */
	private function handle_callback( $callback ) : void {
		if ( $callback ) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}

		wp_die();
	}
}
