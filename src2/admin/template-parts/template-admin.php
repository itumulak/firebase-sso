<?php
/**
 * Output the Admin page.
 *
 * @type template
 * @since 2.0.0
 */

use function IT\SSO\Firebase\get_admin_template_part;
use IT\SSO\Firebase\Admin_Config;
use IT\SSO\Firebase\Base;

$admin_config = new Admin_Config();
$base = new Base();
?>

<!-- Our admin page content should all be inside .wrap -->
<div class="wrap">
	<!-- Print the page title -->
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<!-- Here are our tabs -->
	<h2 class="nav-tab-wrapper hide-if-js" style="display: block;">
		<a class="nav-tab" href="#configurations" id="configurations" title="<?php esc_attr__( 'Configuration', 'sso-firebase' ) ?>"><?php _e( 'Configuration', 'sso-firebase' ) ?></a>
		<a class="nav-tab" href="#sign-in-providers" id="sign-in-providers" title="<?php esc_attr__( 'Sign-in Providers', 'sso-firebase' ) ?>"><?php _e( 'Sign-in providers', 'sso-firebase' ) ?></a>
	</h2>
	<div class="tabs-holder">
		<div id="sign-in-providers-tab" class="group">
			<div id="sign-in-providers-list">
				<?php echo get_admin_template_part( 'template', 'providers', array( 'providers' => $admin_config->get_providers(), 'admin_instance' => $admin_config, 'plugin_url' => $base->get_plugin_url()  ) ); ?>
			</div>
		</div>
		<div id="configurations-tab" class="group">
			<div id="config-textarea-wrapper">
				<?php echo get_admin_template_part( 'template', 'configuration', array( 'config' => $admin_config->get_config(), 'admin_instance' => $admin_config ) ); ?>
			</div>
		</div>
	</div>
</div>
