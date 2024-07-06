<?php
/**
 * Base controller class.
 *
 * @package firebase-sso
 */

namespace Itumulak\WpSsoFirebase\Controller;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Base_Controller
 */
abstract class Base_Controller {
	/**
	 * Init method.
	 *
	 * @return void
	 */
	abstract public function init(): void;

	/**
	 * Scripts method.
	 *
	 * @return void
	 */
	abstract public function scripts(): void;
}
