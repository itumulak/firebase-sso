<?php
/**
 * Gutenburg class controller.
 * 
 * @package firebase-sso
 */

 namespace Itumulak\WpSsoFirebase\Controller;

use Itumulak\WpSsoFirebase\Models\Base_Model;

 if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Gutenburg_Controller
 */
class Gutenburg_Controller {
    private Base_Model $base;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->base = new Base_Model();
    }

    public function init() : void {
        add_action( 'init', array($this, 'create_block_gutenburg_block_init') );
        add_action( 'enqueue_block_assets', array($this, 'scripts') );
    }

    public function scripts() {
        if (has_block('firebase-sso/gutenburg')) {
            wp_enqueue_style(
                'firebase-login-block',
                $this->base->get_plugin_url() . 'src/View/Frontend/assets/styles/login.css',
                array(),
                $this->base->get_version()
            );
        }
    }

    public function create_block_gutenburg_block_init() : void {
        register_block_type( $this->base->get_plugin_dir() . '/gutenburg/build' );
    }
}