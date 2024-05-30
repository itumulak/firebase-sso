<?php
namespace Itumulak\WpSsoFirebase\Controller;

use Itumulak\WpSsoFirebase\Models\Admin_Model;
use Itumulak\WpSsoFirebase\Models\Providers_Model;
use Itumulak\WpSsoFirebase\Models\UserProfileWP_Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class UserProfileWP_Controller extends Base_Controller {
	private UserProfileWP_Model $user_profile_model;
	private Providers_Model $providers_model;
	public array $providers;

	public function __construct() {
		$this->user_profile_model = new UserProfileWP_Model();
		$this->providers_model = new Providers_Model();	
		$this->providers = $this->providers_model->get_all();
	}

	public function init() : void {
		add_action('show_user_profile', array($this, 'provider_user_profile_links'));
	}

	public function scripts(): void {
		
	}

	public function provider_user_profile_links(): void
	{
		echo $this->user_profile_model->get_template(
			'UserProfileWP',
			'template-linked-providers',
			array(
				'providers' => $this->providers,
				'model' => $this->user_profile_model,
			)
		);
	}
}