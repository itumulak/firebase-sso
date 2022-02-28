<?php

namespace IT\SSO\Firebase;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Admin AJAX callback class.
 *
 * @since 2.0.0
 */
class Admin_Ajax {
	public function __construct() {
		add_action( 'wp_ajax_firebase_config', array( $this, 'ajax_save_config' ) );
		add_action( 'wp_ajax_firebase_providers', array( $this, 'ajax_save_providers' ) );
	}

	/**
	 * Save Firebase Config
	 * Ajax request callback
	 *
	 * @use Hook/Action
	 * @return void $data
	 * @since 1.0.0
	 */
	public function ajax_save_config() {
		$config = array_map( 'sanitize_text_field', $_REQUEST );
		unset( $config['action'] );

		if ( $config ) {
			Admin::save_config( $config );
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Save Firebase Sign-in Providers
	 * Ajax request callback
	 *
	 * @return void $data
	 * @since 1.0.0
	 */
	public function ajax_save_providers() {
		$providers = array_map( 'sanitize_key', $_REQUEST['enabled_providers'] );

		if ( $providers ) {
			Admin::save_providers( $providers );
			wp_send_json_success();

		} else {
			wp_send_json_error();
		}
	}
}

new namespace\Admin_Ajax();
