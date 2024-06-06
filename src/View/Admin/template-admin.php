<?php
/**
 * Output the Admin page.
 *
 * @since 1.0.0
 * @package firebase-sso
 */

$admin = $args['admin_model'];

// @todo Fix tab styling in mobile view. phpcs:ignore.
// @todo Implement Material 3? phpcs:ignore.

?>
<div class="wrap">
	<!-- Print the page title -->
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<!-- Here are our tabs -->
	<h2 class="nav-tab-wrapper hide-if-js" style="display: block;">
		<a class="nav-tab" href="#configurations" id="configurations" title="<?php esc_attr__( 'Configuration', 'firebase-sso' ); ?>"><?php esc_html_e( 'Configuration', 'firebase-sso' ); ?></a>
		<a class="nav-tab" href="#sign-in-providers" id="sign-in-providers" title="<?php esc_attr__( 'Sign-in Providers', 'firebase-sso' ); ?>"><?php esc_html_e( 'Sign-in providers', 'firebase-sso' ); ?></a>
	</h2>
	<div class="tabs-holder">
		<div id="sign-in-providers-tab" class="group">
			<div id="sign-in-providers-list">
				<?php
				echo esc_html(
					$admin->get_template(
						'Admin',
						'template-providers',
						array(
							'providers'   => $admin->get_providers(),
							'admin_model' => $admin,
							'plugin_url'  => $admin->get_plugin_url(),
						)
					)
				);
				?>
			</div>
		</div>
		<div id="configurations-tab" class="group">
			<div id="config-textarea-wrapper">
				<?php
				echo esc_html(
					$admin->get_template(
						'Admin',
						'template-configuration',
						array(
							'config'      => $admin->get_config(),
							'admin_model' => $admin,
						)
					)
				);
				?>
			</div>
		</div>
	</div>
</div>
