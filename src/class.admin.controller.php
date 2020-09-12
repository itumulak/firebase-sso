<?php
namespace IT\SSO\Firebase;
use IT\SSO\Firebase\WP as Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Admin extends Main {

	/**
	 * Admin constructor.
     *
     * Build WP Admin.
     *
     * @since 1.0.0
     *
     * @param void
     * @return void
	 */
	function __construct() {
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );
		add_action( 'wp_ajax_firebase_config', [ $this, 'ajax_save_config' ] );
		add_action( 'wp_ajax_firebase_providers', [ $this, 'ajax_save_providers' ] );
	}

	/**
	 * Register Admin Menu
     *
     * @since 1.0.0
	 */
	public function admin_menu() {
		add_menu_page( 'WP Firebase', 'WP Firebase', 'manage_options', self::MENU_SLUG, [$this, 'admin_page'], '', 9 );
	}

	/**
	 * Register Admin Scripts
     *
     * @since 1.0.0
	 */
	public function admin_scripts() {
		if ( isset( $_GET['page'] ) && $_GET['page'] == self::MENU_SLUG ) {
			/** Toast */
			wp_enqueue_script( 'toast', plugin_dir_url( __DIR__ ) . 'js/jquery.toast.min.js', [ 'jquery' ], '', 'true' );
			wp_enqueue_style( 'toast', plugin_dir_url( __DIR__ ) . 'styles/jquery.toast.min.css', [], '', 'all' );
			/**  */

			/** Admin main */
			wp_enqueue_style( self::JS_ADMIN, plugin_dir_url( __DIR__ ) . 'styles/admin.css', [], '', 'all' );
			wp_enqueue_script( self::JS_ADMIN, plugin_dir_url( __DIR__ ) . 'js/admin.js', [
				'jquery',
				'toast'
			], '1.0.0', 'true' );
			/**  */
		}
	}

	/**
	 * Render Admin Page
     *
     * @since 1.0.0
	 */
	public function admin_page() {
		?>
        <!-- Our admin page content should all be inside .wrap -->
        <div class="wrap">
            <!-- Print the page title -->
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <!-- Here are our tabs -->
            <h2 class="nav-tab-wrapper hide-if-js" style="display: block;">
                <a id="configurations" class="nav-tab" title="Configuration"
                   href="#configurations">Configuration</a>
                <a id="sign-in-providers" class="nav-tab" title="Sign-in Providers"
                   href="#sign-in-providers">Sign-in providers</a>
            </h2>
            <div class="tabs-holder">
                <div id="sign-in-providers-tab" class="group">
                    <div id="sign-in-providers-list">
                        <form id="sign-in-providers-form">
							<?php $enabledProviders = self::get_providers(); ?>
                            <table class="form-table">
                                <tbody>
                                <tr>
                                    <th scope="row">
                                        <label for="email-password">Email/Password</label>
                                    </th>
                                    <td>
                                        <input type="checkbox" id="email-password"
                                               name="sign-in-providers[emailpassword]"
											<?= ( in_array( 'email-password', $enabledProviders ) ? 'checked' : '' ) ?>
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="facebook">Facebook</label>
                                    </th>
                                    <td>
                                        <input type="checkbox" id="facebook" name="sign-in-providers[facebook]"
											<?= ( in_array( 'facebook', $enabledProviders ) ? 'checked' : '' ) ?>
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="google">Google</label>
                                    </th>
                                    <td>
                                        <input type="checkbox" id="google" name="sign-in-providers[google]"
											<?= ( in_array( 'google', $enabledProviders ) ? 'checked' : '' ) ?>
                                        >
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <p>
                                <button type="submit" class="button button-primary">Save</button>
                            </p>
                        </form>
                    </div>
                </div>
                <div id="configurations-tab" class="group">
                    <div id="config-textarea-wrapper">
                        <form id="configuration-fields">
                            <h1>Firebase Configurations</h1>
                            <p>Get a copy, and paste your <a target="_blank" href="https://firebase.google.com/docs/web/setup?authuser=0#config-object">Firebase config object</a> found at your project settings.</p>
		                    <?php
		                    $config       = $this->get_config();
		                    $configFields = [
			                    'apiKey'             => 'API Key',
			                    'authDomain'         => 'Authorized Domain'
		                    ];
		                    ?>
                            <table class="form-table">
                                <tbody>
			                    <?php foreach ( $configFields as $key => $label ) : ?>
                                    <tr>
                                        <th scope="row">
                                            <label for="<?= $key ?>"><?= $label ?></label>
                                        </th>
                                        <td>
                                            <input name="<?= $key ?>" type="text" id="<?= $key ?>" class="regular-text"
							                    <?= array_key_exists($key, $config) ? 'value="'.$config[$key].'"' : '' ?>>
                                        </td>
                                    </tr>
			                    <?php endforeach; ?>

                                </tbody>
                            </table>
                            <p>
                                <button type="submit" class="button button-primary">Save Config</button>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}

	/**
	 * Save Firebase Config
     * Ajax request callback
	 *
	 * @return void $data
	 * @since 1.0.0
	 */
	public function ajax_save_config() {
		$config = $_REQUEST;
		unset( $config['action'] );

		if ( $config ) {
			self::save_config( $config );
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}

	/**
     * Save Firebase Config
     *
	 * @param $config
     * @since 1.0.0
	 */
	private static function save_config( $config ) {
		update_option( self::OPTION_KEY_CONFIG, $config );
	}

	/**
     * Fetch saved Firebase Config
     *
	 * @return false|mixed|void
     * @since 1.0.0
	 */
	public static function get_config() {
		return get_option( self::OPTION_KEY_CONFIG );
	}

	/**
	 * Save Firebase Sign-in Providers
     * Ajax request callback
     *
     * @return void $data
     * @since 1.0.0
	 */
	public function ajax_save_providers() {
		$providers = $_REQUEST['enabled_providers'];

		if ( $providers ) {
			self::save_providers( $providers );
			wp_send_json_success();

		} else {
			wp_send_json_error();
		}
	}

	/**
     * Save Firebase Sign-in Providers
     *
	 * @param $providers
     * @since 1.0.0
	 */
	private static function save_providers( $providers ) {
		update_option( self::OPTION_KEY_PROVIDERS, $providers );
	}

	/**
     * Fetch saved Sign-in Providers
     *
	 * @return false|mixed|void
     * @since 1.0.0
	 */
	public static function get_providers() {
		return get_option( self::OPTION_KEY_PROVIDERS );
	}
}

new namespace\Admin();