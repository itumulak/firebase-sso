<?php
/**
 * Displays the linked firebase providers.
 * Users can also link/unlinked providers here.
 *
 * @since 1.0.0
 * @package firebase-sso
 */

$providers          = $args['providers'];
$user_profile_model = $args['model'];
$linked             = $args['linked'];
?>
<div class="providers">
	<h3 class="providers__headline"><?php esc_html_e( 'Linked Providers', 'firebase-sso' ); ?></h3>
	<div class="providers__list">
	<?php foreach ( $providers as $provider => $set ) : ?>
		<button
			id="provider-<?php echo esc_attr( $provider ); ?>" 
			data-provider="<?php echo esc_attr( $provider ); ?>" 
			data-action="<?php echo esc_attr( $linked[ $provider ] ? 'disconnect' : 'connect' ); ?>" 
			class="providers__list-btn btn btn-<?php echo esc_attr( $provider ); ?>">
			<img height="18" src="<?php echo esc_url( $user_profile_model->get_plugin_url() . 'src/assets/images/' . $provider . '-logo.svg' ); ?>"/> 
			<span>
				<?php echo $linked[ $provider ] ? 'Disconnect' : 'Connect'; ?>
			</span>
		</button>
	<?php endforeach; ?>
	</div>
</div>
