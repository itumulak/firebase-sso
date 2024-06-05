<?php
/**
 * Script model class.
 *
 * @package firebase-sso
 */

namespace Itumulak\WpSsoFirebase\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Scripts_Model
 */
class Scripts_Model extends Base_Model {
	/**
	 * Holds all js to be enqueued.
	 *
	 * @var array
	 */
	private array $js_handles;

	/**
	 * Holds all js localization.
	 *
	 * @var array
	 */
	private array $js_localization;

	/**
	 * Holds all script js strategies.
	 *
	 * @var array
	 */
	private array $wpjs_strategies;

	/**
	 * Holds the current js handler.
	 *
	 * @var string
	 */
	private string $current_js_handler;

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {
		$this->wpjs_strategies = array(
			'in_footer' => true,
			'strategy'  => 'defer',
			'is_module' => false,
		);
	}

	/**
	 * Enqueue registered scripts.
	 *
	 * @return void
	 */
	public function enqueue_all() : void {
		if ( $this->js_handles ) {
			foreach ( $this->js_handles as $js ) {
				$this->enqueue( $js );
			}
		}
	}

	/**
	 * Register a JavaScript.
	 * Accepts the same parameter as `wp_enqueue_script`.
	 *
	 * @param  string          $handle JS handle.
	 * @param  string          $src Path to the js file.
	 * @param  array           $deps Dependencies.
	 * @param  array|bool      $strategy Strategies.
	 * @param  string|int|null $version JS version.
	 * @return void
	 */
	public function register( string $handle, string $src, array $deps = array(), array|bool $strategy = array(), string|int|null $version = null ) : void {
		if ( is_bool( $strategy ) ) {
			$strategy = wp_parse_args( array( 'in_footer' => (bool) $strategy ), $this->wpjs_strategies );
		}

		$this->js_handles[ $handle ] = array(
			'handle'   => $handle,
			'src'      => $src,
			'deps'     => $deps,
			'strategy' => wp_parse_args( $strategy, $this->wpjs_strategies ),
			'version'  => ! $version ? $version : $this->get_version(),
		);
	}

	/**
	 * Register localized script.
	 * Accepts the same parameter as `wp_localize_script`.
	 *
	 * @param  string $handle JS Handle.
	 * @param  string $object_name Object name.
	 * @param  array  $data Object data.
	 * @return void
	 */
	public function register_localization( string $handle, string $object_name, array $data ) : void {
		$this->js_localization[ $handle ] = array(
			'handle' => $handle,
			'name'   => $object_name,
			'data'   => $data,
		);
	}

	/**
	 * Override the script loader tag to include a module attribute.
	 *
	 * @param  string $tag Script tag.
	 * @param  string $handle JS handle.
	 * @param  string $src Path to the JS file.
	 * @return string
	 */
	public function add_attributes( string $tag, string $handle, string $src ) : string {
		if ( $this->current_js_handler === $handle ) {
			$strategy_attributes = '';
			$module_attribute    = '';

			if ( isset( $this->js_handles[ $handle ]['strategy'] ) ) {
				$strategy            = $this->js_handles[ $handle ]['strategy']['strategy'];
				$strategy_attributes = $strategy . ' data-wp-strategy="' . $strategy . '"';

				if ( $this->js_handles[ $handle ]['strategy']['is_module'] ) {
					$module_attribute = 'type="module"';
				}
			}

			$tag = sprintf( '<script %s %s src="%s"></script>', $module_attribute, $strategy_attributes, $src ); // phpcs:ignore
		}

		return $tag;
	}

	/**
	 * Enqueue a js file.
	 *
	 * @param  array $js Data of the enqueued js file.
	 * @return void
	 */
	private function enqueue( array $js ) {
		$this->current_js_handler = $js['handle'];

		wp_enqueue_script(
			$js['handle'],
			$js['src'],
			$js['deps'],
			$js['version'],
			$js['strategy']
		);

		if ( isset( $this->js_localization[ $js['handle'] ] ) ) {
			wp_localize_script(
				$js['handle'],
				$this->js_localization[ $js['handle'] ]['name'],
				$this->js_localization[ $js['handle'] ]['data']
			);
		}

		// Is there a performance issue when enqueue method is loop together with this filter?
		// the current handle is save in $this->current_js_handler then gets compared later in `add_attributes` method.
		add_filter( 'script_loader_tag', array( $this, 'add_attributes' ), 10, 3 );
	}
}
