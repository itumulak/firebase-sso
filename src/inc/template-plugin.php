<?php
namespace IT\SSO\Firebase;

use IT\SSO\Firebase\Base as Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Load our admin template parts.
 *
 * @param $slug
 * @param $name
 * @param $args
 * @param $require_once
 *
 * @since 2.0.0
 * @return void
 */
function get_admin_template_part( $slug, $name = '', $args = array(), $require_once = false  ) {
	$template = '';
	$base     = new Base();

	if ( ! $template && $name && $base->get_plugin_dir() . "template-part/{$slug}-{$name}.php" ) {
		$template = $base->get_plugin_dir() . "src/admin/template-parts/{$slug}-{$name}.php";
	}

	if ( ! $template && $name && $base->get_plugin_dir() . "templates/{$slug}-{$name}.php" ) {
		$template = $base->get_plugin_dir() . "src/admin/templates/{$slug}-{$name}.php";
	}

	if ( $template ) {
		load_template( $template, $require_once, $args );
	}
}
