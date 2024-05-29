<?php
/**
 * Displays the configuration form.
 *
 * @type template-part
 * @since 1.0.0
 */

$configs = $args['config'];
?>
<form id="configuration-fields">
	<h1><?php esc_html_e( 'Firebase Configurations' ); ?></h1>
	<p>
	<?php
	echo wp_sprintf(
		'%s <a target="_blank" href="%s">%s</a> %s',
		esc_html__( 'Get a copy, and paste your' ),
		esc_url( 'https://firebase.google.com/docs/web/setup?authuser=0#config-object' ),
		esc_html__( 'Firebase config object' ),
		esc_html__( 'found at your project settings.' ),
	);
	?>
	</p>
	<table class="form-table">
		<tbody>
		<?php
		foreach ( $configs as $key => $config ) :
			?>
			<tr>
				<th scope="row">
					<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $config['label'] ); ?></label>
				</th>
				<td>
					<input class="regular-text" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>[value]" type="text" value="<?php echo esc_attr( $config['value'] ); ?>">
				</td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<th scope="row">
				<label for="">Sync Email/Password in Firebase</label>
			</th>
			<td>
				<input type="checkbox">
			</td>
		</tr>

		</tbody>
	</table>
	<p>
		<button type="submit"
				class="button button-primary"><?php esc_html_e( 'Save Config' ); ?></button>
	</p>
</form>

