<?php
/**
 * Displays the Firebase provider that are available.
 *
 * @type template-type
 * @since 2.0.0
 */

 $enabled_providers = $args['providers'];
 $admin             = $args['admin_model'];
?>
<form id="sign-in-providers-form">
	<table class="form-table">
		<tbody>
		<tr>
			<th scope="row">
				<label for="email-password"><img height="24" src="<?php echo esc_attr( $args['plugin_url'] . 'assets/mail-logo.svg' ); ?>"/>
					<span class="mail"><?php esc_html_e( 'Email/Password', 'sso-firebase' ); ?></span></label>
			</th>
			<td>
				<input type="checkbox" id="email-password" name="sign-in-providers[emailpassword]" <?php echo $enabled_providers[ $admin::PROVIDER_SLUG_EMAILPASS ] ? 'checked' : ''; ?>>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="facebook"><img height="24" src="<?php echo esc_attr( $args['plugin_url'] . 'assets/facebook-logo.svg' ); ?>"/> <span class="facebook"><?php esc_html_e( 'Facebook', 'sso-firebase' ); ?></span></label>
			</th>
			<td>
				<input type="checkbox" id="facebook" name="sign-in-providers[facebook]" <?php echo $enabled_providers[ $admin::PROVIDER_SLUG_FB ] ? 'checked' : ''; ?>>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="google"><img height="24" src="<?php echo esc_attr( $args['plugin_url'] . 'assets/google-logo.svg' ); ?>"/> <span class="google"><?php esc_html_e( 'Google' ); ?></span></label>
			</th>
			<td>
				<input id="google" name="sign-in-providers[google]" type="checkbox" <?php echo $enabled_providers[ $admin::PROVIDER_SLUG_GOOGLE ] ? 'checked' : ''; ?>>
			</td>
		</tr>
		</tbody>
	</table>
	<p>
		<button type="submit" class="button button-primary"><?php esc_html_e( 'Save' ); ?></button>
	</p>
</form>
