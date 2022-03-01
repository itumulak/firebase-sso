<?php

namespace IT\SSO\Firebase;

use IT\SSO\Firebase\Admin_Config as Config;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Admin AJAX callback class.
 *
 * @since 2.0.0
 */
class Admin_Ajax {
	public Config $admin_config;

	/**
	 * Admin Ajax constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		$this->admin_config = new Config();
	}

	/**
	 * Initialized functions.
	 * Hooks/Filter are added here.
	 *
	 * @since 2.0.0
	 */
	public function init() {
		add_action( 'wp_ajax_firebase_config', array( $this, 'ajax_save_config' ) );
		add_action( 'wp_ajax_firebase_providers', array( $this, 'ajax_save_providers' ) );
	}

	/**
	 * Save Firebase Config.
	 * Ajax request callback.
	 *
	 * @use Hook/Action
	 * @return void $data
	 * @since 1.0.0
	 */
	public function ajax_save_config() {
		$config = array_map( 'sanitize_text_field', $_REQUEST );
		unset( $config['action'] );

		if ( $config ) {
			$this->admin_config->save_config( $config );
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
			$this->admin_config->save_providers( $providers );
			wp_send_json_success();

		} else {
			wp_send_json_error();
		}
	}
}

$admin_ajax = new namespace\Admin_Ajax();
$admin_ajax->init();
