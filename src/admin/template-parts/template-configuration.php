<?php
/**
 * Displays the configuration form.
 *
 * @type template-part
 * @since 2.0.0
 */

?>


<form id="configuration-fields">
	<h1><?php _e( 'Firebase Configurations' ); ?></h1>
	<p><?php echo wp_sprintf(
			'%s <a target="_blank" href="%s">%s</a> %s',
			__( 'Get a copy, and paste your' ),
			esc_url( 'https://firebase.google.com/docs/web/setup?authuser=0#config-object' ),
			__( 'Firebase config object' ),
			__( 'found at your project settings.' ),
		);
		?>
	</p>
	<?php
	$config = wp_parse_args($args['config'], array('apiKey' => '', 'authDomain' => ''));
	$config_fields = array(
		'apiKey'     => 'API Key',
		'authDomain' => 'Authorized Domain',
	);
	?>
	<table class="form-table">
		<tbody>
		<?php foreach ( $config_fields as $key => $label ) :
			$field_value = '';

			if ( array_key_exists( 'apiKey', $config ) ) {
				$field_value =  $config[ $key ];
			}
			?>
			<tr>
				<th scope="row">
					<label for="<?php echo esc_attr( $key ) ?>"><?php echo esc_attr( $label ) ?></label>
				</th>
				<td>
					<input class="regular-text" id="<?php echo esc_attr( $key ) ?>"
					       name="<?php echo esc_attr( $key ) ?>"
					       type="text" value="<?php echo esc_attr( $field_value ); ?>">
				</td>
			</tr>
		<?php endforeach; ?>

		</tbody>
	</table>
	<p>
		<button type="submit"
		        class="button button-primary"><?php _e( 'Save Config' ) ?></button>
	</p>
</form>

