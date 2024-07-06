<?php
/**
 * WP user profile class controller.
 *
 * @package firebase-sso
 */

namespace Itumulak\WpSsoFirebase\Controller;

use Itumulak\WpSsoFirebase\Models\Providers_Model;
use Itumulak\WpSsoFirebase\Models\Scripts_Model;
use Itumulak\WpSsoFirebase\Models\UserProfileWP_Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * UserProfileWP_Controller
 */
class UserProfileWP_Controller extends Base_Controller {
	/**
	 * Holds the script model class.
	 *
	 * @var Scripts_Model
	 */
	private Scripts_Model $js;

	/**
	 * Holds the WP user profile model class.
	 *
	 * @var UserProfileWP_Model
	 */
	private UserProfileWP_Model $user_profile_model;

	/**
	 * Holds the providers model class.
	 *
	 * @var Providers_Model
	 */
	private Providers_Model $providers_model;

	/**
	 * Holds the provider list.
	 *
	 * @var array
	 */
	public array $providers;

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {
		$this->user_profile_model = new UserProfileWP_Model();
		$this->providers_model    = new Providers_Model();
		$this->providers          = $this->providers_model->get_all();
		$this->js                 = new Scripts_Model();
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'show_user_profile', array( $this, 'provider_user_profile_links' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'wp_ajax_' . $this->user_profile_model::AJAX_HANDLE, array( $this, 'provider_link_callback' ) );
		add_action( 'wp_ajax_' . $this->user_profile_model::AJAX_UNLINK_HANDLE, array( $this, 'provider_unlink_callback' ) );
	}

	/**
	 * Register scripts.
	 *
	 * @return void
	 */
	public function scripts(): void {
		global $pagenow;

		if ( 'profile.php' === $pagenow ) {
			wp_enqueue_style(
				$this->user_profile_model->get_handle(),
				$this->user_profile_model->get_plugin_url() . 'src/View/UserProfileWP/assets/styles/linked-providers.css',
				array(),
				$this->user_profile_model->get_version(),
			);

			wp_enqueue_script(
				$this->user_profile_model->get_handle(),
				$this->user_profile_model->get_plugin_url() . 'dist/profile.bundle.js',
				array(),
				$this->user_profile_model->get_version(),
				array(
					'in_footer' => true,
					'strategy'  => 'defer',
				)
			);

			wp_localize_script(
				$this->user_profile_model->get_handle(),
				$this->user_profile_model->get_handle_object(),
				$this->user_profile_model->get_object_data()
			);
		}
	}

	/**
	 * Output the provider management row for the user profile.
	 *
	 * @return void
	 */
	public function provider_user_profile_links(): void {
		echo esc_html(
			$this->user_profile_model->get_template(
				'UserProfileWP',
				'template-linked-providers',
				array(
					'providers' => $this->providers,
					'linked'    => $this->user_profile_model->get_linked_providers( get_current_user_id() ),
					'model'     => $this->user_profile_model,
				)
			)
		);
	}

	/**
	 * AJAX callback for linking provider.
	 *
	 * @return void
	 */
	public function provider_link_callback(): void {
		$post = wp_unslash( $_POST );

		if ( ! isset( $post['user_id'] ) ||
			! isset( $post['uid'] ) ||
			! isset( $post['provider'] ) ||
			! isset( $post['nonce'] ) ||
			! wp_verify_nonce( $post['nonce'], $this->user_profile_model::AJAX_NONCE )
		) {
			wp_die();
		}

		$user_id  = esc_attr( $post['user_id'] );
		$uid      = esc_attr( $post['uid'] );
		$provider = esc_attr( $post['provider'] );

		$process_linking = $this->user_profile_model->check_uid_availability( $uid, $provider );

		if ( is_bool( $process_linking ) && true === $process_linking ) {
			$meta = $this->user_profile_model->link_provider( $user_id, $uid, $provider );

			if ( is_bool( $meta ) && true === $meta ) {
				wp_send_json_success(
					array(
						'linked' => true,
						'meta'   => $meta,
					)
				);
			} else {
				wp_send_json_error(
					array(
						'linked' => false,
						'errors' => $meta->get_error_messages(),
					)
				);
			}
		} else {
			wp_send_json_error(
				array(
					'linked' => false,
					'errors' => $process_linking->get_error_messages(),
				)
			);
		}

		wp_die();
	}

	/**
	 * AJAX callback for unlinking provider.
	 *
	 * @return void
	 */
	public function provider_unlink_callback(): void {
		$post = wp_unslash( $_POST );

		if (
			! isset( $post['user_id'] ) ||
			! isset( $post['provider'] ) ||
			! isset( $post['nonce'] ) ||
			! wp_verify_nonce( $post['nonce'], $this->user_profile_model::AJAX_NONCE )
		) {
			wp_die();
		}

		$user_id  = esc_attr( $post['user_id'] );
		$provider = esc_attr( $post['provider'] );

		$meta = $this->user_profile_model->unlink_provider( $user_id, $provider );

		if ( is_bool( $meta ) && true === $meta ) {
			wp_send_json_success(
				array(
					'unlinked' => true,
					'meta'     => $meta,
				)
			);
		} else {
			wp_send_json_error(
				array(
					'unlinked' => false,
					'errors'   => $meta->get_error_messages(),
				)
			);
		}

		wp_die();
	}
}
