<?php

namespace IT\SSO\Firebase;

use IT\SSO\Firebase\Admin_Config as Admin_Config;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Admin class.
 *
 * @since 1.0.0
 */
class Admin extends Admin_Config {
	/**
	 * Initialized functions.
	 * Hooks/Filter are added here.
	 *
	 * @since 2.0.0
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
    }

	/**
	 * Register Admin Menu
	 *
	 * @use Hook/Action
	 * @since 1.0.0
	 */
	public function admin_menu() {
		add_menu_page( 'WP Firebase', 'WP Firebase', 'manage_options', self::MENU_SLUG, array(
			$this,
			'admin_page'
		), $this->get_plugin_url() . 'assets/firebase-logo-menu-icon.svg', 9 );
	}

	/**
	 * Register Admin Scripts
	 *
	 * @use Hook/Action
	 * @since 1.0.0
	 */
	public function admin_scripts() {
		if ( isset( $_GET['page'] ) && $_GET['page'] === self::MENU_SLUG ) {
			/** Toast */
			wp_enqueue_script( 'toast', $this->get_plugin_url() . 'lib/toast/jquery.toast.min.js', array( 'jquery' ), '', 'true' );
			wp_enqueue_style( 'toast', $this->get_plugin_url() . 'lib/toast/jquery.toast.min.css', array(), '' );
			/**  */

			/** Admin main */
			wp_enqueue_style( self::JS_ADMIN, $this->get_plugin_url() . 'src/admin/styles/admin.css', array(), self::get_version() );
			wp_enqueue_script( self::JS_ADMIN, $this->get_plugin_url() . 'src/admin/js/admin.js', array( 'toast', 'jquery' ), self::get_version(), 'true' );
			/**  */
		}
	}

	/**
	 * Render Admin Page
	 *
	 * @use Hook/Action
	 * @since 1.0.0
	 */
	public function admin_page() {
		echo get_admin_template_part( 'template', 'admin' );
	}
}

$admin = new namespace\Admin();
$admin->init();
