<?php
/**
 * Displays the Firebase provider that are available.
 *
 * @since 1.0.0
 * @package firebase-sso
 */

$enabled_providers = $args['providers'];
$admin             = $args['admin_model'];
?>
<form id="sign-in-providers-form" class="provider__wrapper">
	<?php foreach ( $enabled_providers as $provider ) { ?>
	<div class="provider">
			<label	label class="provider__label" for="<?php echo esc_attr( $provider['id'] ); ?>">
				<img height="24" src="<?php echo esc_url( $provider['icon'] ); ?>"/>
				<?php echo esc_html( $provider['label'] ); ?>
			</label>
		<div>
			<label class="switch" for="<?php echo esc_attr( $provider['id'] ); ?>">
				<input type="checkbox" id="<?php echo esc_attr( $provider['id'] ); ?>" name="sign-in-providers[<?php echo esc_attr( $provider['id'] ); ?>]" <?php echo $provider['is_active'] ? 'checked' : ''; ?>>
				<span class="slider round"></span>
			</label>
		</div>
	</div>
	<?php } ?>
	<button type="submit" class="button button-primary"><?php esc_html_e( 'Save' ); ?></button>
</form>
