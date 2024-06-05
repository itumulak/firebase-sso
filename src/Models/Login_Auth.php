<?php
/**
 * Login Auth class.
 *
 * @package firebase-sso
 */

namespace Itumulak\WpSsoFirebase\Models;

/**
 * Login_Auth
 */
class Login_Auth extends Base_Model {
	/**
	 * Set cookie logout.
	 *
	 * @use Hook/Action
	 * @since 1.0.0
	 */
	public function set_cookie_logout() {
		setcookie( self::COOKIE_LOGOUT, 1, time() + 3600, COOKIEPATH, COOKIE_DOMAIN );
	}
}
