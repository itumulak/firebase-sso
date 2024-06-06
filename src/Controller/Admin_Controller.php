<?php
/**
 * Admin class controller.
 *
 * @package firebase-sso
 */

namespace Itumulak\WpSsoFirebase\Controller;

use Itumulak\WpSsoFirebase\Models\Admin_Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Admin_Controller
 */
class Admin_Controller {
	/**
	 * Holds admin model class.
	 *
	 * @var Admin_Model
	 */
	private Admin_Model $admin_model;
	const SAVE_CONFIG_FUNC    = 'save_config_callback';
	const SAVE_PROVIDERS_FUNC = 'save_providers_callback';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->admin_model = new Admin_Model();
	}

	/**
	 * Initialized functions.
	 * Hooks/Filter are added here.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'wp_ajax_' . $this->admin_model::PROVIDER_ACTION, array( $this, self::SAVE_PROVIDERS_FUNC ) );
		add_action( 'wp_ajax_' . $this->admin_model::CONFIG_ACTION, array( $this, self::SAVE_CONFIG_FUNC ) );
	}

	/**
	 * Register Admin Menu
	 *
	 * @use Hook/Action
	 * @since 1.0.0
	 */
	public function admin_menu() {
		add_menu_page(
			'WP Firebase',
			'WP Firebase',
			'manage_options',
			$this->admin_model::MENU_SLUG,
			array(
				$this,
				'admin_page',
			),
			$this->admin_model->get_plugin_url() . 'src/View/Admin/assets/images/firebase-logo-menu-icon.svg',
			9
		);
	}

	/**
	 * Register Admin Scripts
	 *
	 * @use Hook/Action
	 * @since 1.0.0
	 */
	public function admin_scripts() {
		if ( isset( $_GET['page'] ) && $this->admin_model::MENU_SLUG === $_GET['page'] ) { // phpcs:ignore
			wp_enqueue_script( 'toast', $this->admin_model->get_plugin_url() . 'lib/toast/jquery.toast.min.js', array( 'jquery' ), '1.0.0', 'true' );
			wp_enqueue_style( 'toast', $this->admin_model->get_plugin_url() . 'lib/toast/jquery.toast.min.css', array(), '1.0.0' );
			wp_enqueue_style( $this->admin_model::JS_ADMIN_HANDLE, $this->admin_model->get_plugin_url() . 'src/View/Admin/assets/styles/admin.css', array(), $this->admin_model->get_version() );
			wp_enqueue_script( $this->admin_model::JS_ADMIN_HANDLE, $this->admin_model->get_plugin_url() . 'src/View/Admin/assets/js/admin.js', array( 'toast', 'jquery' ), $this->admin_model->get_version(), 'true' );
			wp_localize_script(
				$this->admin_model::JS_ADMIN_HANDLE,
				$this->admin_model::JS_ADMIN_OBJECT_NAME,
				array(
					'ajaxurl'         => admin_url( 'admin-ajax.php' ),
					'config_action'   => $this->admin_model::CONFIG_ACTION,
					'provider_action' => $this->admin_model::PROVIDER_ACTION,
					'nonce'           => wp_create_nonce( $this->admin_model::AJAX_NONCE ),
				)
			);
		}
	}

	/**
	 * Render Admin Page
	 *
	 * @use Hook/Action
	 * @since 1.0.0
	 */
	public function admin_page() {
		echo esc_html( $this->admin_model->get_template( 'Admin', 'template-admin', array( 'admin_model' => $this->admin_model ) ) );
	}

	/**
	 * Save Firebase Config.
	 * Ajax request callback.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function save_config_callback() : void {
		if ( ! isset( $_REQUEST['nonce'] ) && ! $this->admin_model->verify_nonce( $_REQUEST['nonce'], $this->admin_model::JS_ADMIN_NONCE ) ) { // phpcs:ignore
			$this->handle_callback( false );
		}

		$this->handle_callback( $this->admin_model->save_config( $_REQUEST ) ); // phpcs:ignore
	}

	/**
	 * Save Firebase Sign-in Providers
	 * Ajax request callback
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function save_providers_callback() : void {
		$post = wp_unslash( $_POST );

		if (
			! isset( $post['enabled_providers'] ) ||
			! isset( $post['nonce'] ) ||
			! wp_verify_nonce( $post['nonce'], $this->admin_model::AJAX_NONCE )
		) {
			$this->handle_callback( false );
			wp_die();
		}

		if ( isset( $post['enabled_providers'] ) ) {
			$providers = array_map( 'sanitize_key', $post['enabled_providers'] ); // phpcs:ignore
			$providers = array_fill_keys( $providers, true );

			$this->handle_callback( $this->admin_model->save_providers( $providers ) );
		} else {
			$this->handle_callback( $this->admin_model->save_providers( array() ) );
		}
	}

	/**
	 * Handle WordPress callbacks.
	 *
	 * @param mixed $callback Callback function.
	 * @return void
	 * @since 1.0.0
	 */
	private function handle_callback( mixed $callback ) : void {
		if ( $callback ) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}

		wp_die();
	}
}
