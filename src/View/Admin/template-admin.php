<?php
/**
 * Output the Admin page.
 *
 * @type template
 * @since 2.0.0
 */

$admin = $args['admin_model'];

// @todo Fix tab styling in mobile view.
// @todo Implement Material 3?

?>
 <div class="wrap">
	<!-- Print the page title -->
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<!-- Here are our tabs -->
	<h2 class="nav-tab-wrapper hide-if-js" style="display: block;">
		<a class="nav-tab" href="#configurations" id="configurations" title="<?php esc_attr__( 'Configuration', 'sso-firebase' ); ?>"><?php esc_html_e( 'Configuration', 'sso-firebase' ); ?></a>
		<a class="nav-tab" href="#sign-in-providers" id="sign-in-providers" title="<?php esc_attr__( 'Sign-in Providers', 'sso-firebase' ); ?>"><?php esc_html_e( 'Sign-in providers', 'sso-firebase' ); ?></a>
	</h2>
	<div class="tabs-holder">
		<div id="sign-in-providers-tab" class="group">
			<div id="sign-in-providers-list">
				<?php
				echo $admin->get_template(
					'Admin',
					'template-providers',
					array(
						'providers'   => $admin->get_providers(),
						'admin_model' => $admin,
						'plugin_url'  => $admin->get_plugin_url(),
					)
				);
				?>
			</div>
		</div>
		<div id="configurations-tab" class="group">
			<div id="config-textarea-wrapper">
				<?php
				echo $admin->get_template(
					'Admin',
					'template-configuration',
					array(
						'config'      => $admin->get_config(),
						'admin_model' => $admin,
					)
				);
				?>
			</div>
		</div>
	</div>
</div>
