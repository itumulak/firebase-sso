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
	private array $js_modules;
    private array $js_localization;
	private array $wpjs_strategies;
	private string $current_js_handler;

	public function __construct() {
		$this->wpjs_strategies = array(
			'in_footer' => true,
			'strategy'  => 'async',
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
			'strategy' => $this->get_wpjs_strategy( $strategy ),
			'version'  => ! $version ? $version : $this->get_version(),
		);

		if ( isset( $strategy['is_module'] ) && is_bool( $strategy['is_module'] ) ) {
			if ( true === $strategy['is_module'] ) {
				$this->js_modules[] = $handle;
			}
		}
	}

    public function register_localization( string $handle, string $object_name, array $data ) : void {
        $this->js_localization[$handle] = array(
            'handle' => $handle,
            'name' => $object_name,
            'data' => $data
        );
    }

	public function add_attribute_module( $tag, $handle, $src ) {
		if ( $this->current_js_handler === $handle ) {
			$strategy_output = '';

			if ( isset( $this->js_handles[ $handle ]['strategy'] ) ) {
				$strategy        = $this->js_handles[ $handle ]['strategy']['strategy'];
				$strategy_output = $strategy . ' data-wp-strategy="' . $strategy . '"';
			}

			$tag = '<script type="module" ' . $strategy_output . ' src="' . $src . '"></script>';
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

        if ( isset($this->js_localization[$js['handle']]) ) {
            wp_localize_script(
                $js['handle'],
                $this->js_localization[$js['handle']]['name'],
                $this->js_localization[$js['handle']]['data']
            );
        }

		if ( $this->js_modules ) {
			// Is there a performance issue when enqueue method is loop together with this filter?
			// the current handle is save in $this->current_js_handler then gets compared later in `add_attribute_module` method.
			add_filter( 'script_loader_tag', array( $this, 'add_attribute_module' ), 10, 3 );
		}
    }

	private function get_wpjs_strategy( array $strategies ) : array {
		$wpjs_strategies = $this->wpjs_strategies;
		unset( $wpjs_strategies['in_module'] );

		if ( isset( $strategies['in_footer'] ) && is_bool( $strategies['in_footer'] ) ) {
			$wpjs_strategies['in_footer'] = $strategies['in_footer'];
		}

		if ( isset( $strategies['strategy'] ) && in_array( $strategies['strategy'], array( 'async', 'defer' ), true ) ) {
			$wpjs_strategies['strategy'] = $strategies['strategy'];
		}

		return $wpjs_strategies;
	}
}
