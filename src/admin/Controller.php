<?php
namespace Itumulak\WpSsoFirebase\Admin;

use Itumulak\WpSsoFirebase\Models\Admin as AdminModel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Controller {
	private AdminModel $admin_model;
	private CallbackController $callback;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->admin_model = new AdminModel();
		$this->callback    = new CallbackController();
	}

	/**
	 * Initialized functions.
	 * Hooks/Filter are added here.
	 *
	 * @since 2.0.0
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'wp_ajax_' . $this->admin_model::PROVIDER_ACTION, array( $this->callback, $this->callback::SAVE_PROVIDERS_FUNC ) );
		add_action( 'wp_ajax_' . $this->admin_model::CONFIG_ACTION, array( $this->callback, $this->callback::SAVE_CONFIG_FUNC ) );
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
			$this->admin_model->get_plugin_url() . 'src/Admin/assets/images/firebase-logo-menu-icon.svg',
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
			/** Toast */
			wp_enqueue_script( 'toast', $this->admin_model->get_plugin_url() . 'lib/toast/jquery.toast.min.js', array( 'jquery' ), '1.0.0', 'true' );
			wp_enqueue_style( 'toast', $this->admin_model->get_plugin_url() . 'lib/toast/jquery.toast.min.css', array(), '1.0.0' );
			/**  */

			wp_enqueue_style( $this->admin_model::JS_ADMIN_HANDLE, $this->admin_model->get_plugin_url() . 'src/Admin/assets/styles/admin.css', array(), $this->admin_model->get_version() );
			wp_enqueue_script( $this->admin_model::JS_ADMIN_HANDLE, $this->admin_model->get_plugin_url() . 'src/Admin/assets/js/admin.js', array( 'toast', 'jquery' ), $this->admin_model->get_version(), 'true' );
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
		echo $this->admin_model->get_admin_template_part( 'template', 'admin', array( 'admin_model' => $this->admin_model ) );
	}
}
