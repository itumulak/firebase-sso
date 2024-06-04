<?php
namespace Itumulak\WpSsoFirebase\Controller;

use Itumulak\WpSsoFirebase\Models\Providers_Model;
use Itumulak\WpSsoFirebase\Models\Scripts_Model;
use Itumulak\WpSsoFirebase\Models\UserProfileWP_Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class UserProfileWP_Controller extends Base_Controller {
	private Scripts_Model $js;
	private UserProfileWP_Model $user_profile_model;
	private Providers_Model $providers_model;
	public array $providers;

	public function __construct() {
		$this->user_profile_model = new UserProfileWP_Model();
		$this->providers_model    = new Providers_Model();
		$this->providers          = $this->providers_model->get_all();
		$this->js                 = new Scripts_Model();
	}

	public function init() : void {
		add_action( 'show_user_profile', array( $this, 'provider_user_profile_links' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'wp_ajax_' . $this->user_profile_model::AJAX_HANDLE, array( $this, 'provider_link_callback' ) );
		add_action( 'wp_ajax_' . $this->user_profile_model::AJAX_UNLINK_HANDLE, array( $this, 'provider_unlink_callback' ) );
	}

	public function scripts(): void {
		wp_enqueue_style( 'toast', $this->user_profile_model->get_plugin_url() . 'lib/toast/jquery.toast.min.css', array(), '1.0.0' );
		wp_enqueue_style(
			$this->user_profile_model->get_handle(),
			$this->user_profile_model->get_plugin_url() . 'src/View/UserProfileWP/assets/styles/linked-providers.css',
			array( 'toast' ),
			$this->user_profile_model->get_version()
		);

		$this->js->register(
			'toast',
			$this->user_profile_model->get_plugin_url() . 'lib/toast/jquery.toast.min.js',
			array( 'jquery' )
		);

		$this->js->register(
			$this->user_profile_model->get_handle(),
			$this->user_profile_model->get_plugin_url() . 'src/View/UserProfileWP/assets/js/provider-actions.js',
			array( 'toast' ),
			array(
				'strategy'  => 'defer',
				'is_module' => true,
			)
		);

		$this->js->register_localization(
			$this->user_profile_model->get_handle(),
			$this->user_profile_model->get_handle_object(),
			$this->user_profile_model->get_object_data()
		);

		$this->js->enqueue_all();
	}

	public function provider_user_profile_links(): void {
		echo $this->user_profile_model->get_template(
			'UserProfileWP',
			'template-linked-providers',
			array(
				'providers' => $this->providers,
				'linked'    => $this->user_profile_model->get_linked_providers( get_current_user_id() ),
				'model'     => $this->user_profile_model,
			)
		);
	}

	public function provider_link_callback() : void {
		if (
			isset( $_POST ) &&
			isset( $_POST['nonce'] ) &&
			$this->user_profile_model->verify_nonce( $_POST['nonce'], $this->user_profile_model::AJAX_NONCE )
		) {
			$user_id  = $_POST['user_id'];
			$uid      = $_POST['uid'];
			$provider = $_POST['provider'];

			$process_linking = $this->user_profile_model->check_uid_availability( $uid, $provider );

			if ( is_bool( $process_linking ) && $process_linking === true ) {
				$meta = $this->user_profile_model->link_provider( $user_id, $uid, $provider );

				if ( is_bool( $meta ) && $meta === true ) {
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
		}

		wp_die();
	}

	public function provider_unlink_callback() : void {
		if (
			isset( $_POST ) &&
			isset( $_POST['nonce'] ) &&
			$this->user_profile_model->verify_nonce( $_POST['nonce'], $this->user_profile_model::AJAX_NONCE )
		) {
			$user_id  = $_POST['user_id'];
			$provider = $_POST['provider'];

			$meta = $this->user_profile_model->unlink_provider( $user_id, $provider );

			if ( is_bool( $meta ) && $meta === true ) {
				wp_send_json_success(
					array(
						'unlinked' => true,
						'meta'   => $meta,
					)
				);
			} else {
				wp_send_json_error(
					array(
						'unlinked' => false,
						'errors' => $meta->get_error_messages(),
					)
				);
			}
		}

		wp_die();
	}
}
