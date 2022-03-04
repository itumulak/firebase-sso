<?php
namespace IT\SSO\Firebase;

use IT\SSO\Firebase\Base as Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * The below function will help to load template file from plugin directory of WordPress.
 *
 * @param $slug
 * @param null $name
 * @param array $args
 *
 * @return void
 */
function it_get_admin_template_part( $slug, $name = null, $args = array() ) {
	do_action( "it_get_admin_template_path_$slug", $slug, $name );

	$templates = array();
	if ( isset( $name ) ) {
		$templates[] = "{$slug}-{$name}.php";
	}

	$templates[] = "$slug.php";

	it_get_admin_template_path( $templates, true, false, $args );
}

/**
 * Extend locate_template from WP Core and used it for this plugin.
 *
 * @param $template_names
 * @param bool $load
 * @param bool $require_once
 * @param array $args
 *
 * @return string
 */

function it_get_admin_template_path( $template_names, $load = false, $require_once = true, $args = array() ) {
	$base    = new Base();
	$located = '';

	foreach ( (array) $template_names as $template_name ) {
		if ( ! $template_name ) {
			continue;
		}

		if ( file_exists( $base->get_plugin_dir() . 'src/admin/template-parts/' . $template_name ) ) {
			$located = $base->get_plugin_dir() . 'src/admin/template-parts/' . $template_name;
			break;
		} elseif ( file_exists( $base->get_plugin_dir() . 'src/admin/templates/' . $template_name ) ) {
			$located = $base->get_plugin_dir() . 'src/admin/templates/' . $template_name;
			break;
		}
	}

	if ( $load && '' !== $located ) {
		load_template( $located, $require_once, $args );
	}

	return $located;
}
