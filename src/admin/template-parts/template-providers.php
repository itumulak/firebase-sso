<?php
/**
 * Displays the Firebase provider that are available.
 *
 * @type template-type
 * @since 2.0.0
 */


?>
<form id="sign-in-providers-form">
	<?php $enabledProviders = $args['providers']; ?>
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row">
                <label for="email-password"><img height="24"
                                                 src="<?php echo $args['plugin_url'] . 'assets/mail-logo.svg'; ?>"/>
                    <span class="mail"><?php _e( 'Email/Password', 'sso-firebase' ) ?></span></label>
            </th>
            <td>
                <input type="checkbox" id="email-password"
                       name="sign-in-providers[emailpassword]" <?= ( in_array( 'email-password', $enabledProviders, true ) ? 'checked' : '' ) ?>>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="facebook"><img height="24"
                                           src="<?php echo $args['plugin_url'] . 'assets/facebook-logo.svg'; ?>"/> <span
                            class="facebook"><?php _e( 'Facebook', 'sso-firebase' ) ?></span></label>
            </th>
            <td>
                <input type="checkbox" id="facebook"
                       name="sign-in-providers[facebook]" <?= ( in_array( 'facebook', $enabledProviders, true ) ? 'checked' : '' ) ?>>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="google"><img height="24"
                                         src="<?php echo $args['plugin_url'] . 'assets/google-logo.svg'; ?>"/> <span
                            class="google"><?php _e( 'Google' ) ?></span></label>
            </th>
            <td>
                <input id="google" name="sign-in-providers[google]"
                       type="checkbox" <?= ( in_array( 'google', $enabledProviders, true ) ? 'checked' : '' ) ?>>
            </td>
        </tr>
        </tbody>
    </table>
    <p>
        <button type="submit" class="button button-primary"><?php _e( 'Save' ); ?></button>
    </p>
</form>
