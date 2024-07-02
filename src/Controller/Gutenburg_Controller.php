<?php
/**
 * Gutenburg class controller.
 * 
 * @package firebase-sso
 */

 namespace Itumulak\WpSsoFirebase\Controller;

use Itumulak\WpSsoFirebase\Models\Admin_Model;
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
    }

    public function create_block_gutenburg_block_init() : void {
        register_block_type( $this->base->get_plugin_dir() . '/gutenburg/build' );
    }
}