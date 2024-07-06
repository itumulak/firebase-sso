<?php
/**
 * Gutenburg class controller.
 *
 * @package firebase-sso
 */

namespace Itumulak\WpSsoFirebase\Controller;

use Itumulak\WpSsoFirebase\Models\Base_Model;
use Itumulak\WpSsoFirebase\Models\Frontend_Model;
use Itumulak\WpSsoFirebase\Models\Scripts_Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Gutenburg_Controller
 */
class Gutenburg_Controller {
	/**
	 * Holds the base model class
	 *
	 * @var Base_Model
	 */
	private Base_Model $base;

	/**
	 * Holds the scripts model class
	 *
	 * @var Scripts_Model
	 */
	private Scripts_Model $js;

	/**
	 * Holds the frontend model class
	 *
	 * @var Frontend_Model
	 */
	private Frontend_Model $frontend;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->base     = new Base_Model();
		$this->js       = new Scripts_Model();
		$this->frontend = new Frontend_Model();
	}

	/**
	 * Initialized functions.
	 * Hooks/Filter are added here.
	 *
	 * @since 1.1.0
	 */
	public function init(): void {
		add_action( 'init', array( $this, 'create_block_gutenburg_block_init' ) );
		add_action( 'enqueue_block_assets', array( $this, 'scripts' ) );
	}

	/**
	 * Register Admin Scripts
	 *
	 * @use Hook/Action
	 * @since 1.1.0
	 */
	public function scripts() {
		if ( has_block( 'firebase-sso/gutenburg' ) ) {
			wp_enqueue_style(
				'firebase-login-block',
				$this->base->get_plugin_url() . 'src/View/Frontend/assets/styles/login.css',
				array(),
				$this->base->get_version()
			);

			$this->js->register(
				'firebase-login-block',
				$this->base->get_plugin_url() . 'src/View/Frontend/assets/js/authentication.js',
				array(),
				array(
					'is_module' => true,
				)
			);

			$this->js->register_localization(
				'firebase-login-block',
				$this->frontend::FIREBASE_OBJECT,
				$this->frontend->get_object_data()
			);

			$this->js->enqueue_all();
		}
	}

	/**
	 * Register the block
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function create_block_gutenburg_block_init(): void {
		register_block_type( $this->base->get_plugin_dir() . '/gutenburg/build' );
	}
}
