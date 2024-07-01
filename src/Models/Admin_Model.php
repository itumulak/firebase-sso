<?php
/**
 * Admin model class.
 *
 * @package firebase-sso
 */

namespace Itumulak\WpSsoFirebase\Models;

use Itumulak\WpSsoFirebase\Models\Providers_Model;

/**
 * Admin_Model
 */
class Admin_Model extends Base_Model {
	/**
	 * Holds the API and authorize domain data from firebase.
	 *
	 * @var array
	 */
	private array $configuration_data;

	/**
	 * Holds the available the supported firebase providers.
	 *
	 * @var array
	 */
	private array $providers_data;

	/**
	 * Holds the configuration model class.
	 *
	 * @var Configuration_Model
	 */
	private Configuration_Model $configuration_model;

	/**
	 * Holds the providers model class.
	 *
	 * @var Providers_Model
	 */
	private Providers_Model $providers_model;

	const PROVIDER_ACTION = 'provider_action';
	const CONFIG_ACTION   = 'config_action';

	/**
	 * Constructor.
	 * Initialize default data.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->configuration_model = new Configuration_Model();
		$this->providers_model     = new Providers_Model();

		$this->configuration_data = array(
			'apiKey'     => array(
				'label' => 'API Key',
				'value' => '',
			),
			'authDomain' => array(
				'label' => 'Authorized Domain',
				'value' => '',
			),
		);

		$this->providers_data = array(
			$this->providers_model::PROVIDER_GOOGLE   => array(
				'id'        => $this->providers_model::PROVIDER_GOOGLE,
				'icon'      => esc_url( $this->get_plugin_url() . 'src/View/Admin/assets/images/google-logo.svg' ),
				'label'     => __( 'Google', 'firebase-sso' ),
				'is_active' => false,
			),
			$this->providers_model::PROVIDER_FACEBOOK => array(
				'id'        => $this->providers_model::PROVIDER_FACEBOOK,
				'icon'      => esc_url( $this->get_plugin_url() . 'src/View/Admin/assets/images/facebook-logo.svg' ),
				'label'     => __( 'Facebook', 'firebase-sso' ),
				'is_active' => false,
			),
		);
	}

	/**
	 * Save Firebase Config
	 *
	 * @param array $configuration_data Configuration data.
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function save_config( array $configuration_data ): bool {
		return $this->configuration_model->save( $configuration_data );
	}

	/**
	 * Fetch Settings configuration.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function get_config(): array {
		// $data = $this->configuration_model->get_all();

		// foreach ( $data as $key => $datum ) {
		// 	$this->configuration_data[ $key ]['value'] = strlen( $datum ) > 0 ? $this->spoof_datum() : '';
		// }

		// return $this->configuration_data;
		return $this->configuration_model->get_all();
	}

	/**
	 * Fetch saved Sign-in Providers
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function get_providers(): array {
		$data = $this->providers_model->get_all();

		foreach ( $data as $key => $datum ) {
			$this->providers_data[ $key ]['is_active'] = $datum;
		}

		return $this->providers_data;
	}

	/**
	 * Save Firebase Sign-in Providers
	 *
	 * @param array $data Data providers.
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function save_providers( array $data ): bool {
		return $this->providers_model->save( $data );
	}

	/**
	 * Return an imitating "•" for extra security.
	 *
	 * @return string
	 */
	private function spoof_datum(): string {
		return str_repeat( '•', 30 );
	}
}
