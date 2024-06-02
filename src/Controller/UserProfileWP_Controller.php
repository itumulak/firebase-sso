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
		$this->js      = new Scripts_Model();
	}

	public function init() : void {
		add_action( 'show_user_profile', array( $this, 'provider_user_profile_links' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
	}

	public function scripts(): void {
		wp_enqueue_style(
			$this->user_profile_model->get_handle(),
			$this->user_profile_model->get_plugin_url() . 'src/View/UserProfileWP/assets/styles/linked-providers.css',
			array(),
			$this->user_profile_model->get_version()
		);

		$this->js->register(
			$this->user_profile_model->get_handle(),
			$this->user_profile_model->get_plugin_url() . 'src/View/UserProfileWP/assets/js/link-providers.js',
			array(),
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

		// wp_enqueue_script_module(
		// 	$this->user_profile_model->get_handle(),
		// 	$this->user_profile_model->get_plugin_url() . 'src/View/UserProfileWP/assets/js/link-providers.js',
		// 	array(),
		// 	$this->user_profile_model->get_version()
		// );

		// wp_enqueue_script(
		// 	$this->user_profile_model->get_handle(),
		// 	$this->user_profile_model->get_plugin_url() . 'src/View/UserProfileWP/assets/js/link-providers.js',
		// 	array(),
		// 	$this->user_profile_model->get_version()
		// );

		// wp_localize_script(
		// 	$this->user_profile_model->get_handle(),
		// 	'firebase_sso',
		// 	array(
		// 		'providers' => array_keys( $this->providers ),
		// 	)
		// );
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

	public function provider_auth_callback() : void {
		$token    = $_POST['token'];
		$provider = $_POST['provider'];
		$user_id  = $_POST['user_id'];

		$process_linking = $this->user_profile_model->check_token_availability( $token, $provider );

		if ( is_bool( $process_linking ) && $process_linking === true ) {
			$meta = $this->user_profile_model->link_provider( $user_id, $token, $provider );

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

		wp_die();
	}
}
