<?php
/**
 * Display the providers that are enabled.
 *
 * @since 1.0.0
 * @package firebase-sso
 */

$provider_key   = $args['provider_key'];
$label          = $args['label'];
$img_size       = $args['img_size'];
$frontend_model = $args['frontend_model'];

echo wp_sprintf( '<p class="btn-wrapper"><button id="wp-firebase-%1$s-sign-in" class="btn btn-lg btn-%1$s btn-block text-uppercase" type="submit"><img height="%3$d" src="' . esc_url( $frontend_model->get_plugin_url() . 'src/assets/images/%1$s-logo.svg' ) . '" /> %2$s</button></p>', esc_attr( $provider_key ), esc_attr( $label ), esc_attr( $img_size ) );
