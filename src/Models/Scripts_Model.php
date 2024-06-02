<?php
namespace Itumulak\WpSsoFirebase\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// TODO: Implement Script Module.
	// Drawback: Script Module does not support Localize Script.
	// Bug: Script Module cannot be registered in wp admin. Fix in v6.6.
class Scripts_Model extends Base_Model {
	private array $js_handles;
	private array $js_localization;
	private array $wpjs_strategies;
	private string $current_js_handler;

	public function __construct() {
		$this->wpjs_strategies = array(
			'in_footer' => true,
			'strategy'  => 'defer',
			'is_module' => false,
		);
	}

	public function enqueue_all() : void {
		if ( $this->js_handles ) {
			foreach ( $this->js_handles as $js ) {
				$this->enqueue( $js );
			}
		}
	}

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

	public function register_localization( string $handle, string $object_name, array $data ) : void {
		$this->js_localization[ $handle ] = array(
			'handle' => $handle,
			'name'   => $object_name,
			'data'   => $data,
		);
	}

	public function add_attributes( $tag, $handle, $src ) {
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

			$tag = sprintf('<script %s %s src="%s"></script>', $module_attribute, $strategy_attributes, $src);
		}

		return $tag;
	}

	private function enqueue( $js ) {
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
