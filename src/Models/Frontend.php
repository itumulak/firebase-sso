<?php
namespace Itumulak\WpSsoFirebase\Models;

class Frontend extends Factory {
	public array $enabled_providers;
	const LOGIN_STYLE_HANDLE = 'firebase_login';

	/**
	 * WP login constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$admin_model             = new Admin();
		$this->enabled_providers = $admin_model->get_providers();
	}
}
