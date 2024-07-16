<?php
/**
 * Render file.
 *
 * @package firebase-sso
 */

?>
<div <?php echo get_block_wrapper_attributes(); //phpcs:ignore ?>>
	<?php if ( $attributes['showGoogle'] ) : ?>
		<p class="btn-wrapper">
			<button
				id="wp-firebase-google-sign-in"
				class="btn btn-lg btn-google btn-block text-uppercase"
				type="submit"
			>
				<img width="24" src="<?php echo esc_attr( FIREBASE_SSO_URL . 'src/assets/images/google-logo.svg' ); ?>" /> Google
			</button>
		</p>
	<?php endif; ?>
	<?php if ( $attributes['showFacebook'] ) : ?>
		<p class="btn-wrapper">
			<button
				id="wp-firebase-facebook-sign-in"
				class="btn btn-lg btn-facebook btn-block text-uppercase"
				type="submit"
			>
				<img width="48" src="<?php echo esc_attr( FIREBASE_SSO_URL . 'src/assets/images/facebook-logo.svg' ); ?>" /> Facebook
			</button>
		</p>
	<?php endif; ?>
	<?php wp_login_form(); ?>
</div>
